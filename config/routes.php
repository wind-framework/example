<?php
/**
 * Router Config
 *
 * groups[]
 * - namespace
 * - prefix
 * - middlewares
 * - routes
 *    - name
 *    - middlewares
 *    - handler
 */
return [
    //app group
    [
        'namespace' => 'App\Controller',
        'routes' => [
            'get /' => 'IndexController::index',
            'get /gc-status' => 'IndexController::gcStatus',
            'get /gc-recycle' => 'IndexController::gcRecycle',
            'get /cache' => 'IndexController::cache',
            'get /soul' => 'DbController::soul',
            'get /soul/{id:\d+}' => 'DbController::soulFind',
            'get /souls' => 'DbController::souls',
            'get /db/concurrent' => 'DbController::concurrent',
            'get /db/post/{id:\d+}' => 'DbController::post',
            'get /db/post2/{id:\d+}' => 'DbController::postByEloquent',
            'get /sleep' => 'IndexController::sleep',
            'get /block' => 'IndexController::block',
            'get /exception' => 'IndexController::exception',
            'get /phpinfo' => 'IndexController::phpinfo',

            'get /queue' => 'QueueController::index',
            'get /queue/peek/{status}' => 'QueueController::peek',
            'get /queue/wakeup' => 'QueueController::wakeup',
            'get /queue/drop' => 'QueueController::drop'
        ],
        'groups' => [
            //test group (group can also have a key name)
            'g1' => [
                'prefix' => '/test',
                'middlewares' => [\App\Middleware\TestMiddleware::class],
                'routes' => [
                    'get task' => 'TestController::taskCall',
                    'get client-ip' => [
                        'name' => 'test.cache',
                        'middlewares' => [\App\Middleware\TestMiddleware::class],
                        'handler' => 'TestController::clientIp'
                    ],
                    'get|post closure' => function(\Workerman\Protocols\Http\Request $req) {
                        return $req->uri();
                    },
                    'get queue' => 'TestController::queue',
                    'get http' => 'TestController::http',
                    'get log' => 'TestController::log',
                    'get file' => 'TestController::sendFile',
                    'post upload' => 'TestController::uploadFile',
                    'get db/insert' => 'DbController::insert',
                    'get stat' => 'TestController::stat',
                    'get websocket' => 'TestController::websocket',

                    'get /redis/get' => 'RedisController::get',
                    'get /redis/transaction' => 'RedisController::transaction',
                    'get /redis/eval' => 'RedisController::script'
                ]
            ],
            //g2 group
            [
                'prefix' => 'g2'
            ]
        ]
    ],

    //static group
    [
        'namespace' => 'Wind\Web',
        'routes' => [
            'get /static/{filename:.+}' => 'FileServer::sendStatic',
            'get /{filename:favicon\.ico}' => 'FileServer::sendStatic'
        ]
    ]
];
