<?php

use Amp\Dns\Record;
use Amp\Http\Client\Connection\DefaultConnectionFactory;
use Amp\Http\Client\Connection\UnlimitedConnectionPool;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Socket\ConnectContext;

use function DI\{create, autowire, factory};

return [
    \Wind\View\ViewInterface::class => create(\Wind\View\Twig::class),
    \Psr\SimpleCache\CacheInterface::class => autowire(\Wind\Cache\RedisCache::class),
    //全局 HttpClient
    \Amp\Http\Client\HttpClient::class => factory(function () {
        $connectContext = (new ConnectContext())->withDnsTypeRestriction(Record::A); //限定只查询 IPv4 的
        $pool = new UnlimitedConnectionPool(new DefaultConnectionFactory(null, $connectContext));
        return (new HttpClientBuilder)->usingPool($pool)->build();
        // return \Amp\Http\Client\HttpClientBuilder::buildDefault();
    })
];
