<?php

use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Wind\Log\LogFactory;

return [
    'default' => [
        'handlers' => [
            [
                'class' => \Monolog\Handler\RotatingFileHandler::class,
                'args' => [
                    'filename' => RUNTIME_DIR.'/log/app.log',
                    'maxFiles' => 15,
                    'level' => Level::Info
                ],
                'async' => true //启用 TaskWorker 模式异步写入
            ],
            [
                'class' => \Wind\Log\StdoutHandler::class,
                'args' => [
                    'level' => Level::Info
                ],
                //为 handler 设置独立的 formatter
                'formatter' => [
                    'class' => LineFormatter::class,
                    'args' => [
                        'dateFormat' => 'Y-m-d H:i:s',
                        'allowInlineLineBreaks' => true
                    ]
                ]
            ]
        ],
        //整个组默认的 formatter
        'formatter' => [
            'class' => LineFormatter::class,
            'args' => [
                'dateFormat' => 'Y-m-d H:i:s v',
                'allowInlineLineBreaks' => true
            ]
        ]
    ],
    'task' => [
        'handlers' => [
            [
                'class' => \Monolog\Handler\RotatingFileHandler::class,
                'args' => [
                    'filename' => RUNTIME_DIR.'/log/task.log',
                    'maxFiles' => 15,
                    'level' => Level::Info
                ],
                'async' => LogFactory::ASYNC_LOG_WRITER //启用 LogWriter 模式异步写入
            ],
            [
                'class' => \Wind\Log\StdoutHandler::class,
                'args' => [
                    'level' => Level::Info
                ],
                //为 handler 设置独立的 formatter
                'formatter' => [
                    'class' => LineFormatter::class,
                    'args' => [
                        'dateFormat' => 'Y-m-d H:i:s',
                        'allowInlineLineBreaks' => true
                    ]
                ]
            ]
        ],
        //整个组默认的 formatter
        'formatter' => [
            'class' => LineFormatter::class,
            'args' => [
                'dateFormat' => 'Y-m-d H:i:s v',
                'allowInlineLineBreaks' => true
            ]
        ]
    ]

];
