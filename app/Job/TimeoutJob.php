<?php

namespace App\Job;

use Wind\Queue\Job;

use function Amp\delay;

class TimeoutJob extends Job
{

    public $ttr = 2;
    public $maxAttempts = 0;

    public function handle()
    {
        echo "TimeoutJob start..\n";
        delay(10);
        echo "TimeoutJob end..\n";
    }

    public function fail($message, $ex)
    {
        return false;
    }

}
