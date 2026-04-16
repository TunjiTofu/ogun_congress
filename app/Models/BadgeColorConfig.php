<?php

namespace App\Models;

use App\Enums\CamperCategory;
use Illuminate\Database\Eloquent\Model;

class BadgeColorConfig extends Model
{
    protected $table = 'badge_color_config';

    protected $fillable = ['category', 'color_hex', 'label'];

    protected function casts(): array
    {
        return [
            'category' => CamperCategory::class,
        ];
    }

    /**
     * Resolve the hex colour for a given category.
     * Falls back to the config default if no DB record exists.
     */
    public static function colorFor(CamperCategory $category): string
    {
        return static::where('category', $category->value)->value('color_hex')
            ?? config("camp.badge_colors.{$category->value}", '#1B3A6B');
    }
}
