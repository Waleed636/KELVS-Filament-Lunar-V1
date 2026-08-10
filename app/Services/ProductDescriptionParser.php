<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use Lunar\Models\Product;
use App\Models\ProductFaq;

class ProductDescriptionParser
{
    /**
     * Build comprehensive, structured sections for a given Product model.
     * Prioritizes dedicated backend fields & structured ProductFaq records,
     * with automatic fallback to parsing raw description HTML.
     *
     * @param Product $product
     * @return array
     */
    public static function parseForProduct(Product $product): array
    {
        $rawDesc = (string) $product->attr('description');
        $parsedSections = self::parse($rawDesc);

        // Map parsed sections by type for fallback retrieval
        $parsedByType = [];
        foreach ($parsedSections as $sec) {
            $parsedByType[$sec['type']] = $sec;
        }

        $finalSections = [];

        // 1. Overview & Key Benefits
        $overviewHtml = $parsedByType['overview']['content'] ?? $rawDesc;
        if (!empty(trim(strip_tags($overviewHtml)))) {
            $finalSections[] = self::buildSection(
                'Description & Key Benefits',
                $overviewHtml,
                'overview',
                'sparkles',
                'Formulation overview, clinical targets & key benefits'
            );
        }

        // 2. How to Use & Routine
        $dedicatedUsage = (string) $product->attr('how_to_use');
        $usageHtml = !empty(trim(strip_tags($dedicatedUsage)))
            ? $dedicatedUsage
            : ($parsedByType['usage']['content'] ?? null);

        if (!empty(trim(strip_tags((string) $usageHtml)))) {
            $finalSections[] = self::buildSection(
                'How to Use & Routine Steps',
                $usageHtml,
                'usage',
                'droplet',
                'Application directions, frequency & routine advice'
            );
        }

        // 3. Formula Science & Ingredients
        $dedicatedIngredients = (string) $product->attr('ingredients_list');
        $ingredientsHtml = !empty(trim(strip_tags($dedicatedIngredients)))
            ? $dedicatedIngredients
            : ($parsedByType['ingredients']['content'] ?? null);

        if (!empty(trim(strip_tags((string) $ingredientsHtml)))) {
            $finalSections[] = self::buildSection(
                'Formula Science & Ingredients',
                $ingredientsHtml,
                'ingredients',
                'beaker',
                'Key actives, pH stability & full INCI ingredients'
            );
        }

        // 4. Frequently Asked Questions (Prioritize dedicated ProductFaq table)
        $dbFaqs = ProductFaq::where('product_id', $product->id)
            ->active()
            ->ordered()
            ->get();

        $faqItems = [];
        if ($dbFaqs->isNotEmpty()) {
            foreach ($dbFaqs as $faq) {
                $faqItems[] = [
                    'question' => $faq->question,
                    'answer'   => $faq->answer,
                ];
            }
        } elseif (!empty($parsedByType['faq']['faq_items'])) {
            $faqItems = $parsedByType['faq']['faq_items'];
        }

        if (!empty($faqItems)) {
            $count = count($faqItems);
            $faqSection = self::buildSection(
                'Frequently Asked Questions',
                '',
                'faq',
                'question',
                'Common questions about results, safety & routine compatibility',
                "{$count} Questions"
            );
            $faqSection['faq_items'] = $faqItems;
            $finalSections[] = $faqSection;
        }

        // Fallback: If no sections were built, show simple description
        if (empty($finalSections) && !empty(trim(strip_tags($rawDesc)))) {
            $finalSections[] = self::buildSection(
                'Product Details',
                $rawDesc,
                'overview',
                'sparkles',
                'Detailed product information'
            );
        }

        return $finalSections;
    }

    /**
     * Parse HTML product description into structured, categorized sections.
     *
     * @param string|null $html
     * @return array
     */
    public static function parse(?string $html): array
    {
        if (empty($html) || !trim(strip_tags($html))) {
            return [];
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        // Load with UTF-8 encoding declaration
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $container = $dom->getElementsByTagName('div')->item(0);
        if (!$container) {
            return [
                self::buildSection('Description & Overview', $html, 'overview', 'sparkles')
            ];
        }

        $rawSections = [];
        $currentHeading = 'Description & Overview';
        $currentNodes = [];

        foreach ($container->childNodes as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE && in_array(strtolower($node->nodeName), ['h2', 'h3'])) {
                $headingText = trim(strip_tags($node->textContent));

                // Check if this heading is an individual FAQ question (e.g. Q: ...)
                $isFaqQuestion = (bool) preg_match('/^Q\s*[:\?]/i', $headingText);

                if (!$isFaqQuestion && !empty($headingText)) {
                    if (!empty($currentNodes)) {
                        $content = self::renderNodesHtml($dom, $currentNodes);
                        if (trim(strip_tags($content))) {
                            $rawSections[] = [
                                'title' => $currentHeading,
                                'content' => $content,
                            ];
                        }
                        $currentNodes = [];
                    }
                    $currentHeading = $headingText;
                    continue;
                }
            }

            $currentNodes[] = $node;
        }

        if (!empty($currentNodes)) {
            $content = self::renderNodesHtml($dom, $currentNodes);
            if (trim(strip_tags($content))) {
                $rawSections[] = [
                    'title' => $currentHeading,
                    'content' => $content,
                ];
            }
        }

        if (empty($rawSections)) {
            return [
                self::buildSection('Description & Overview', $html, 'overview', 'sparkles')
            ];
        }

        return self::groupIntoMasterSections($rawSections);
    }

    /**
     * Group raw parsed sections into cohesive e-commerce accordion categories.
     *
     * @param array $rawSections
     * @return array
     */
    protected static function groupIntoMasterSections(array $rawSections): array
    {
        $overviewContent = [];
        $usageContent = [];
        $ingredientsContent = [];
        $faqHtml = '';

        foreach ($rawSections as $sec) {
            $type = self::detectType($sec['title']);
            $titleClean = trim(preg_replace('/\s+/', ' ', $sec['title']));

            switch ($type) {
                case 'faq':
                    $faqHtml .= $sec['content'];
                    break;

                case 'usage':
                    if (str_starts_with(strtolower($sec['title']), 'how to use') || str_starts_with(strtolower($sec['title']), 'directions')) {
                        $usageContent[] = $sec['content'];
                    } else {
                        $usageContent[] = '<h4 class="text-xs font-extrabold uppercase tracking-wider text-[#111111] mb-2 mt-4">' . e($titleClean) . '</h4>' . $sec['content'];
                    }
                    break;

                case 'ingredients':
                    if (str_starts_with(strtolower($sec['title']), 'full ingredient') || str_starts_with(strtolower($sec['title']), 'ingredients')) {
                        $ingredientsContent[] = $sec['content'];
                    } else {
                        $ingredientsContent[] = '<h4 class="text-xs font-extrabold uppercase tracking-wider text-[#111111] mb-2 mt-4">' . e($titleClean) . '</h4>' . $sec['content'];
                    }
                    break;

                case 'overview':
                default:
                    if ($titleClean === 'Description & Overview' || str_starts_with(strtolower($titleClean), 'about')) {
                        $overviewContent[] = $sec['content'];
                    } else {
                        $overviewContent[] = '<h4 class="text-xs font-extrabold uppercase tracking-wider text-[#111111] mb-2 mt-4">' . e($titleClean) . '</h4>' . $sec['content'];
                    }
                    break;
            }
        }

        $masterSections = [];

        // 1. Overview & Key Benefits
        if (!empty($overviewContent)) {
            $masterSections[] = self::buildSection(
                'Description & Key Benefits',
                implode("\n", $overviewContent),
                'overview',
                'sparkles',
                'Formulation overview, clinical targets & key benefits'
            );
        }

        // 2. How to Use & Routine
        if (!empty($usageContent)) {
            $masterSections[] = self::buildSection(
                'How to Use & Routine Steps',
                implode("\n", $usageContent),
                'usage',
                'droplet',
                'Application directions, frequency & routine advice'
            );
        }

        // 3. Formula & Ingredients
        if (!empty($ingredientsContent)) {
            $masterSections[] = self::buildSection(
                'Formula Science & Ingredients',
                implode("\n", $ingredientsContent),
                'ingredients',
                'beaker',
                'Key actives, pH stability & full INCI ingredients'
            );
        }

        // 4. Frequently Asked Questions
        if (!empty($faqHtml)) {
            $faqItems = self::extractFaqItems($faqHtml);
            $count = count($faqItems);
            $badge = $count > 0 ? "{$count} Questions" : null;

            $faqSection = self::buildSection(
                'Frequently Asked Questions',
                $faqHtml,
                'faq',
                'question',
                'Common questions about results, safety & routine compatibility',
                $badge
            );
            $faqSection['faq_items'] = $faqItems;
            $masterSections[] = $faqSection;
        }

        // Fallback: If grouping resulted in only 1 section or empty, wrap everything safely
        if (empty($masterSections)) {
            $masterSections[] = self::buildSection(
                'Product Details',
                implode("\n", array_column($rawSections, 'content')),
                'overview',
                'sparkles',
                'Detailed product information'
            );
        }

        return $masterSections;
    }

    /**
     * Detect section type based on heading keywords.
     *
     * @param string $title
     * @return string
     */
    protected static function detectType(string $title): string
    {
        $lower = strtolower($title);

        if (str_contains($lower, 'faq') || str_contains($lower, 'frequently asked') || str_contains($lower, 'questions')) {
            return 'faq';
        }
        if (str_contains($lower, 'how to use') || str_contains($lower, 'directions') || str_contains($lower, 'usage') || str_contains($lower, 'wash-day routine') || str_contains($lower, 'ways to use')) {
            return 'usage';
        }
        if (str_contains($lower, 'ingredient') || str_contains($lower, 'inci') || str_contains($lower, 'formula') || str_contains($lower, 'ph-balanced') || str_contains($lower, 'optimal range for hair')) {
            return 'ingredients';
        }

        return 'overview';
    }

    /**
     * Extract individual Q&A pairs from FAQ HTML block.
     *
     * @param string $html
     * @return array
     */
    public static function extractFaqItems(string $html): array
    {
        $items = [];
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $container = $dom->getElementsByTagName('div')->item(0);
        if (!$container) {
            return [];
        }

        $currentQuestion = null;
        $currentAnswerNodes = [];

        foreach ($container->childNodes as $node) {
            $text = trim(strip_tags($node->textContent));
            $isQuestionNode = $node->nodeType === XML_ELEMENT_NODE &&
                (in_array(strtolower($node->nodeName), ['h2', 'h3', 'h4', 'strong', 'p']) &&
                preg_match('/^Q\s*[:\?](.*)$/i', $text, $matches));

            if ($isQuestionNode) {
                if ($currentQuestion && !empty($currentAnswerNodes)) {
                    $ans = self::renderNodesHtml($dom, $currentAnswerNodes);
                    if (trim(strip_tags($ans))) {
                        $items[] = [
                            'question' => $currentQuestion,
                            'answer'   => trim($ans),
                        ];
                    }
                    $currentAnswerNodes = [];
                }
                $currentQuestion = trim($matches[1] ?? $text);
            } else {
                if ($currentQuestion) {
                    $currentAnswerNodes[] = $node;
                }
            }
        }

        if ($currentQuestion && !empty($currentAnswerNodes)) {
            $ans = self::renderNodesHtml($dom, $currentAnswerNodes);
            if (trim(strip_tags($ans))) {
                $items[] = [
                    'question' => $currentQuestion,
                    'answer'   => trim($ans),
                ];
            }
        }

        return $items;
    }

    /**
     * Helper to render array of DOMNodes to HTML string.
     *
     * @param DOMDocument $dom
     * @param array $nodes
     * @return string
     */
    protected static function renderNodesHtml(DOMDocument $dom, array $nodes): string
    {
        $html = '';
        foreach ($nodes as $node) {
            $html .= $dom->saveHTML($node);
        }
        return $html;
    }

    /**
     * Helper to construct standard section data array.
     */
    protected static function buildSection(
        string $title,
        string $content,
        string $type,
        string $icon,
        ?string $subtitle = null,
        ?string $badge = null
    ): array {
        return [
            'title'     => $title,
            'subtitle'  => $subtitle,
            'content'   => $content,
            'type'      => $type,
            'icon'      => $icon,
            'badge'     => $badge,
        ];
    }
}
