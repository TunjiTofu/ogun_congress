<?php
return [
    'route_path' => 'admin/log-viewer',

    'middleware' => ['web', 'auth', \Illuminate\Session\Middleware\StartSession::class],

    'max_log_size_formatted' => '50 MB',

    'shorter_stack_traces' => true,

    'exclude_patterns' => [
        '#vendor/livewire#',
    ],
];
