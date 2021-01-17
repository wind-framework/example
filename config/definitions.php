<?php
use function DI\{create,autowire};
return [
	\Wind\View\ViewInterface::class => create(\Wind\View\Twig::class),
    \Psr\SimpleCache\CacheInterface::class => autowire(\Wind\Cache\RedisCache::class),
];
