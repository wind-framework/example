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
        delay(1);
        echo "1\n";
        $this->touch();
        delay(1);
        echo "2\n";
        $this->touch();
        delay(1);
        echo "3\n";
        $this->touch();
        delay(1);
        echo "4\n";
        $this->touch();
        delay(1);
        echo "5\n";
        echo "TimeoutJob end..\n";
    }

    public function fail($message, $ex)
    {
        return false;
    }

}
