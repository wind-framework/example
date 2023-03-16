<?php

namespace App\Job;

use Wind\Log\LogFactory;
use Wind\Queue\Job;

use function Amp\delay;

class TouchableJob extends Job
{

    public $ttr = 5;

    public function handle() {
        $logger = di()->get(LogFactory::class)->get('TouchableJob');

        for ($i=1; $i<=10; $i++) {
            yield delay(4000);
            $logger->info("Touch $i after 4 seconds\n");
            yield $this->touch();
        }

        $logger->info('Job End');
    }

}
