<?php

namespace App\Controller;

use Wind\Process\ProcessStat;
use Wind\Web\WebSocketInterface;
use Workerman\Timer;

use function Amp\asyncCoroutine;
use function Amp\delay;

class WebSocketController implements WebSocketInterface
{

    public function onConnect($connection, $vars) {
        echo "Connection {$connection->id} connected.\n";
        $connection->send('Welcome to WebSocketServer.');
    }

    public function onMessage($connection, $data) {
        yield delay(1000);
        $connection->send('You have sent: '.$data);
    }

    public function onClose($connection) {
        echo "Connection {$connection->id} closed.\n";
    }

}
