<?php

namespace App\Process;

use Wind\Crontab\CrontabDispatcherProcess;

class MyMergedProcess extends \Wind\Process\MergedProcess
{

    public $processes = [
        CrontabDispatcherProcess::class,
        MyProcess::class
    ];

}
