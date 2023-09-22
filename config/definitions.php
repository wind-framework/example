<?php
use function DI\{create,autowire, factory};
return [
	\Wind\View\ViewInterface::class => create(\Wind\View\Twig::class),
    \Psr\SimpleCache\CacheInterface::class => autowire(\Wind\Cache\RedisCache::class),

    //全局 HttpClient
	\Amp\Http\Client\HttpClient::class => factory(fn () => \Amp\Http\Client\HttpClientBuilder::buildDefault()),
];
