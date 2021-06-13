<?php

return [
    'default2' => [
        'driver' => Wind\Queue\Driver\BeanstalkDriver::class,
        'host' => '192.168.4.2',
        'port' => 11300,
        'tube' => 'wind-queue',
        'reserve_timeout' => null,
        'processes' => 2,
        'concurrent' => 8
    ],
    'default' => [
        'driver' => Wind\Queue\Driver\RedisDriver::class,
        'key' => 'wind:queue',
        'processes' => 2,
        'concurrent' => 16
    ]
];
