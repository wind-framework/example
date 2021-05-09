<?php

namespace App\Middleware;

use Wind\Web\MiddlewareInterface;
use Wind\Web\RequestInterface;
use Wind\Web\Response;

class TestMiddleware implements MiddlewareInterface
{

    public function process(RequestInterface $request, callable $handler)
    {
        /**
         * @var Response $response
         */
        $response = yield $handler($request);
        return $response->withAddedHeader('X-Test', 'This is a test')
            ->withHeader('Server', 'Wind-Http-Server');
    }
}
