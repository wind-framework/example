<?php

return [
    // App\Process\WindStatProcess::class,
    App\Process\MyProcess::class,
    Wind\Crontab\CrontabDispatcherProcess::class,
    Wind\Queue\ConsumerProcess::class,
    // App\Process\MyConsumer::class,
];
