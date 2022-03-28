<?php

namespace App\Controller;

use Wind\Web\WebSocketInterface;
use function Amp\delay;

class WebSocketController implements WebSocketInterface
{

    public static function onWorkerStart()
    {
        echo "WebSocket Worker Start.\n";
    }

    public static function onWorkerStop()
    {
        echo "WebSocket Worker Stopped.\n";
    }

    /**
     * @inheritDoc
     */
    public function onConnect($connection, $vars) {
        echo "Connection {$connection->id} connected.\n";
        $connection->send('Welcome to WebSocketServer.');
    }

    /**
     * @inheritDoc
     */
    public function onMessage($connection, $data) {
        yield delay(1000);
        $connection->send('You have sent: '.$data);
    }

    /**
     * @inheritDoc
     */
    public function onClose($connection) {
        echo "Connection {$connection->id} closed.\n";
    }

}
