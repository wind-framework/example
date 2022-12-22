<?php
//Global Config
return [
    'default_timezone' => 'Asia/Shanghai',
    'debug' => true,
    'max_stack_trace' => 50,
    'annotation' => [
        'scan' => [
            'App\\Controller' => BASE_DIR.'/app/Controller'
        ]
    ]
];
