<?php

namespace App\Controller;

use Amp\TimeoutException;
use App\Job\TouchableJob;
use Wind\Base\TouchableTimeoutToken;
use Wind\Queue\QueueFactory;
use Wind\Web\Controller;

use function Amp\delay;

class TimeoutController extends Controller
{

    public function touchable()
    {
        $touchable = new TouchableTimeoutToken();

        delay(4500)->onResolve(function() use ($touchable) {
            echo "First touch\n";
            $touchable->touch();

            yield delay(4500);
            echo "Second touch\n";
            $touchable->touch();

            yield delay(1000);
            echo "Third touch\n";
            $touchable->touch();
        });

        $begin = microtime(true);

        try {
            yield touchableTimeout(delay(60000), 5000, $touchable);
            $timeUsed = microtime(true) - $begin;
            return "end after $timeUsed seconds\n";
        } catch (TimeoutException $e)  {
            //Will be 15 seconds (4.5 + 4.5 + 1 + 5)
            $timeUsed = microtime(true) - $begin;
            return "timeout after $timeUsed seconds\n";
        }
    }

    public function touchableJob(QueueFactory $queueFactory)
    {
        $queueFactory->get()->put(new TouchableJob());
    }

}
