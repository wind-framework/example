<?php

return [
    Wind\Log\LogWriterProcess::class,
    Wind\Crontab\CrontabDispatcherProcess::class,
    App\Process\MyProcess::class,
    Wind\Queue\ConsumerProcess::class,
    // App\Process\MyConsumer::class,
    App\Process\TestProcess::class
];
