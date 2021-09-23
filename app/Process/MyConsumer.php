<?php

namespace App\Process;

use Wind\Queue\ConsumerProcess;

class MyConsumer extends ConsumerProcess
{

    protected $queue = 'lan2';

}
