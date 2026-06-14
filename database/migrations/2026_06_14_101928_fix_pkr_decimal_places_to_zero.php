<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * PKR decimal_places: 1 → 0
     *
     * WHY: PKR (Pakistani Rupee) has no subunit (no paisa in everyday use).
     * Lunar was set up with decimal_places=1 causing stored values to be 10x
     * the real price (e.g. Rs 4,499 stored as 44,990). This migration corrects
     * all stored integers by dividing by 10 and sets decimal_places=0 so that
     * stored values match real prices directly (4499 = Rs 4,499).
     *
     * REVERSIBLE: down() multiplies all values by 10 and restores decimal_places=1.
     */

    // Conversion factor: old decimal_places=1, new decimal_places=0
    // Dividing stored values by 10^(1-0) = 10
    private const DIVISOR = 10;

    public function up(): void
    {
        // Get PKR currency id
        $pkr = DB::table('lunar_currencies')->where('code', 'PKR')->first();

        if (! $pkr) {
            return; // Nothing to do if PKR doesn't exist
        }

        $pkrId = $pkr->id;

        // ── 1. Prices ─────────────────────────────────────────────────────────
        // lunar_prices stores price and compare_price as integers
        DB::table('lunar_prices')
            ->where('currency_id', $pkrId)
            ->update([
                'price'         => DB::raw('ROUND(price / ' . self::DIVISOR . ')'),
                'compare_price' => DB::raw(
                    'CASE WHEN compare_price IS NOT NULL THEN ROUND(compare_price / ' . self::DIVISOR . ') ELSE NULL END'
                ),
            ]);

        // ── 2. Orders ─────────────────────────────────────────────────────────
        // lunar_orders stores all monetary values as integers
        DB::table('lunar_orders')
            ->where('currency_code', 'PKR')
            ->update([
                'sub_total'       => DB::raw('ROUND(sub_total / ' . self::DIVISOR . ')'),
                'discount_total'  => DB::raw('ROUND(discount_total / ' . self::DIVISOR . ')'),
                'shipping_total'  => DB::raw('ROUND(shipping_total / ' . self::DIVISOR . ')'),
                'tax_total'       => DB::raw('ROUND(tax_total / ' . self::DIVISOR . ')'),
                'total'           => DB::raw('ROUND(total / ' . self::DIVISOR . ')'),
            ]);

        // ── 3. Order Lines ────────────────────────────────────────────────────
        // Update lines belonging to PKR orders
        $pkrOrderIds = DB::table('lunar_orders')
            ->where('currency_code', 'PKR')
            ->pluck('id');

        if ($pkrOrderIds->isNotEmpty()) {
            DB::table('lunar_order_lines')
                ->whereIn('order_id', $pkrOrderIds)
                ->update([
                    'unit_price'      => DB::raw('ROUND(unit_price / ' . self::DIVISOR . ')'),
                    'sub_total'       => DB::raw('ROUND(sub_total / ' . self::DIVISOR . ')'),
                    'discount_total'  => DB::raw('ROUND(discount_total / ' . self::DIVISOR . ')'),
                    'tax_total'       => DB::raw('ROUND(tax_total / ' . self::DIVISOR . ')'),
                    'total'           => DB::raw('ROUND(total / ' . self::DIVISOR . ')'),
                ]);

            // ── 4. Transactions ───────────────────────────────────────────────
            DB::table('lunar_transactions')
                ->whereIn('order_id', $pkrOrderIds)
                ->update([
                    'amount' => DB::raw('ROUND(amount / ' . self::DIVISOR . ')'),
                ]);
        }

        // ── 5. Update Currency decimal_places ─────────────────────────────────
        DB::table('lunar_currencies')
            ->where('code', 'PKR')
            ->update(['decimal_places' => 0]);
    }

    public function down(): void
    {
        // Reverse: multiply everything by 10, restore decimal_places=1
        $pkr = DB::table('lunar_currencies')->where('code', 'PKR')->first();

        if (! $pkr) {
            return;
        }

        $pkrId = $pkr->id;

        DB::table('lunar_prices')
            ->where('currency_id', $pkrId)
            ->update([
                'price'         => DB::raw('price * ' . self::DIVISOR),
                'compare_price' => DB::raw(
                    'CASE WHEN compare_price IS NOT NULL THEN compare_price * ' . self::DIVISOR . ' ELSE NULL END'
                ),
            ]);

        DB::table('lunar_orders')
            ->where('currency_code', 'PKR')
            ->update([
                'sub_total'      => DB::raw('sub_total * ' . self::DIVISOR),
                'discount_total' => DB::raw('discount_total * ' . self::DIVISOR),
                'shipping_total' => DB::raw('shipping_total * ' . self::DIVISOR),
                'tax_total'      => DB::raw('tax_total * ' . self::DIVISOR),
                'total'          => DB::raw('total * ' . self::DIVISOR),
            ]);

        $pkrOrderIds = DB::table('lunar_orders')
            ->where('currency_code', 'PKR')
            ->pluck('id');

        if ($pkrOrderIds->isNotEmpty()) {
            DB::table('lunar_order_lines')
                ->whereIn('order_id', $pkrOrderIds)
                ->update([
                    'unit_price'     => DB::raw('unit_price * ' . self::DIVISOR),
                    'sub_total'      => DB::raw('sub_total * ' . self::DIVISOR),
                    'discount_total' => DB::raw('discount_total * ' . self::DIVISOR),
                    'tax_total'      => DB::raw('tax_total * ' . self::DIVISOR),
                    'total'          => DB::raw('total * ' . self::DIVISOR),
                ]);

            DB::table('lunar_transactions')
                ->whereIn('order_id', $pkrOrderIds)
                ->update([
                    'amount' => DB::raw('amount * ' . self::DIVISOR),
                ]);
        }

        DB::table('lunar_currencies')
            ->where('code', 'PKR')
            ->update(['decimal_places' => 1]);
    }
};
