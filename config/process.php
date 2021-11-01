<?php

return [
    //App\Process\MyProcess::class,
    //Wind\Crontab\CrontabDispatcherProcess::class,
    \App\Process\MyMergedProcess::class,
    Wind\Queue\ConsumerProcess::class,
    // App\Process\MyConsumer::class,
];
