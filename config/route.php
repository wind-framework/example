<?php
use FastRoute\RouteCollector;
use Wind\Web\Route;

/**
 * groups[]
 * - namespaces
 * - prefix
 * - middleware
 * - middlewares
 * - routes
 *    - name
 *    - middleware
 *    - middlewares
 *    - handler
 */
$routes = [
    //app group
    [
        'namespaces' => 'App\Controller',
        'routes' => [
            'get /' => 'IndexController::index',
            'get /gc-status' => 'IndexController::gcStatus',
            'get /gc-recycle' => 'IndexController::gcRecycle',
            'get /cache' => 'IndexController::cache',
            'get /soul' => 'IndexController::soul',
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
                    'get /cache' => [
                        'name' => 'test.cache',
                        'middleware' => \Wind\Web\Middleware\EmptyMiddleware::class,
                        'handler' => 'TestController::newRoute'
                    ],
                    'get|post closure' => function(\Workerman\Protocols\Http\Request $req) {
                        return $req->uri();
                    },
                    'get queue' => 'TestController::queue',
                    'get http' => 'TestController::http',
                    'get log' => 'TestController::log',
                    'get file' => 'TestController::sendFile',
                    'post upload' => 'TestController::uploadFile',
                    'get client-ip' => 'TestController::clientIp'
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
        'namespaces' => 'Wind\Web',
        'routes' => [
            'get /static/{filename:.+}' => 'FileServer::sendStatic',
            'get /{filename:favicon\.ico}' => 'FileServer::sendStatic'
        ]
    ]
];

return function(RouteCollector $r) {
	$r->addRoute('GET', '/', 'App\Controller\IndexController::index');
	$r->addRoute('GET', '/cache', 'App\Controller\IndexController::cache');
	$r->addRoute('GET', '/soul', 'App\Controller\DbController::soul');
	$r->addRoute('GET', '/soul/{id:\d+}', 'App\Controller\DbController::soulFind');
	$r->addRoute('GET', '/db/concurrent', 'App\Controller\DbController::concurrent');
	$r->addRoute('GET', '/sleep', 'App\Controller\IndexController::sleep');
	$r->addRoute('GET', '/block', 'App\Controller\IndexController::block');
	$r->addRoute('GET', '/exception', 'App\Controller\IndexController::exception');
	$r->addRoute('GET', '/gc-status', 'App\Controller\IndexController::gcStatus');
	$r->addRoute('GET', '/gc-recycle', 'App\Controller\IndexController::gcRecycle');
	$r->addRoute('GET', '/phpinfo', 'App\Controller\IndexController::phpinfo');

	$r->addGroup('/test/', function(RouteCollector $r) {
		$r->get('task', 'App\Controller\TestController::taskCall');
		$r->addRoute('GET', 'request/{id:\d}', 'App\Controller\TestController::request');
		$r->addRoute(['GET', 'POST'], 'closure', function(\Workerman\Protocols\Http\Request $req) {
			return $req->uri();
		});
		
		$r->addRoute('GET', 'queue', 'App\Controller\TestController::queue');
		$r->addRoute('GET', 'http', 'App\Controller\TestController::http');
		$r->get('log', 'App\Controller\TestController::log');
		$r->get('file', 'App\Controller\TestController::sendFile');
		$r->post('upload', 'App\Controller\TestController::uploadFile');
		$r->get('client-ip', 'App\Controller\TestController::clientIp');
	});

	$r->addRoute('GET', '/static/{filename:.+}', '\Wind\Web\FileServer::sendStatic');
	$r->addRoute('GET', '/{filename:favicon\.ico}', '\Wind\Web\FileServer::sendStatic');
};
