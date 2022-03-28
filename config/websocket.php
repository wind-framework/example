<?php

return [
    'callbacks' => [
        'on_worker_start' => '\App\Controller\WebSocketController::onWorkerStart',
        'on_worker_stop' => '\App\Controller\WebSocketController::onWorkerStop'
    ],
    'routes' => [
        '/chat' => \App\Controller\WebSocketController::class
    ]
];
