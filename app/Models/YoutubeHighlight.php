<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeHighlight extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
        ];
    }

    public function getThumbnailAttribute(): string
    {
        return $this->thumbnail_url
            ?: "https://i.ytimg.com/vi/{$this->youtube_id}/hqdefault.jpg";
    }
}
