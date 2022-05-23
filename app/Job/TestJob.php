<?php

namespace App\Job;

use Wind\Db\Db;
use Wind\Queue\Job;

use function Amp\delay;

class TestJob extends Job
{

    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function handle()
    {
        echo "Handle job get value: {$this->value}\n";

        if (mt_rand(0, 100) < 30) {
            throw new \Exception("Error example.");
        }

        echo "---END---\r\n";
    }

    public function fail($message, $ex)
    {
        echo "\n\n\n";
        // echo $ex->getMessage();
        echo "\n\n\n";
        return false;
    }

}
