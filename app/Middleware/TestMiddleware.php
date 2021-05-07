<?php

namespace App\Middleware;

use Wind\Web\MiddlewareInterface;
use Wind\Web\RequestInterface;

class TestMiddleware implements MiddlewareInterface
{

    public function process(RequestInterface $request, callable $handler)
    {
        $response = yield $handler($request);
        return $response->withHeader('X-Test', 'This is a test');
    }
}
