<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Post extends \LaraZeus\Sky\Models\Post
{
    /**
     * Get the post image.
     */
    public function image(string $collection = 'posts'): Collection | string | null
    {
        $image = parent::image($collection);

        if (is_string($image) && !empty($image) && !filter_var($image, FILTER_VALIDATE_URL)) {
            return Storage::disk('public')->url($image);
        }

        return $image;
    }
}
