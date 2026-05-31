<?php

return [
    'cloud_name'    => env('CLOUDINARY_CLOUD_NAME'),
    'api_key'       => env('CLOUDINARY_API_KEY'),
    'api_secret'    => env('CLOUDINARY_API_SECRET'),
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', 'ogun_congress'),

    // Default folder structure
    'folders' => [
        'media'     => 'ogun-congress/' . date('Y') . '/media',
        'officials' => 'ogun-congress/' . date('Y') . '/officials',
    ],

    // Upload constraints
    'max_image_size_mb' => 10,
    'max_video_size_mb' => 100,
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/heic'],
    'allowed_video_types' => ['video/mp4', 'video/quicktime', 'video/avi', 'video/webm'],
];
