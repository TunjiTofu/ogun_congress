<?php
return [
    'route_path' => 'admin/log-viewer',

    'middleware' => ['web', 'auth'],

    'require_auth_when_public_accessible' => true,

    // Only super_admin can access
    'allow_access' => function () {
        return auth()->check()
            && auth()->user()->hasRole('super_admin');
    },

    'max_log_size_formatted' => '50 MB',
    'max_chunk_size' => 50 * 1024 * 1024,

    'shorter_stack_traces' => true,
    'exclude_patterns' => [
        '#vendor/livewire#',
    ],
];
