<?php

namespace App\Controller;

use Wind\Web\WebSocketInterface;
use function Amp\delay;

class WebSocketController implements WebSocketInterface
{

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
