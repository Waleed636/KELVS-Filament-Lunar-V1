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
            // Local development fallback: if the file is missing locally, load from live server
            if (app()->environment('local') && !file_exists(public_path('storage/' . $image))) {
                return 'https://kelvsint.com/storage/' . $image;
            }

            return Storage::disk('public')->url($image);
        }

        return $image;
    }
}
