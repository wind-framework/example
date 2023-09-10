<?php
//Global Config
return [
    'default_timezone' => 'Asia/Shanghai',
    'debug' => true,
    'max_stack_trace' => 50,
    'annotation' => [
        'scan_ns_paths' => [
            'App\\Controller@server' => BASE_DIR.'/app/Controller',
            'App\\Command@console' => BASE_DIR.'/app/Command'
        ]
    ]
];
