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
            'get /db/concurrent' => 'DbController::concurrent',
            'get /sleep' => 'IndexController::sleep',
            'get /block' => 'IndexController::block',
            'get /exception' => 'IndexController::exception',
            'get /phpinfo' => 'IndexController::phpinfo',
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
                    'get stat' => 'TestController::stat'
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
