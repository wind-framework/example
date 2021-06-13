<?php

namespace App\Process;

use Wind\Base\Channel;
use Wind\Process\Process;
use Workerman\Timer;

class WindStatProcess extends Process
{

    private $onlineCount = 0;
    private $stats = [];

    public function run() {
        echo "WindStatProcess Start\n";
        $channel = di()->get(Channel::class);

        $channel->on('wind.stat.online', function($data) {
            echo "Received wind.stat.online\n";
            print_r($data);
            $this->onlineCount++;
            echo $this->onlineCount."\n";
        });

        $channel->on('wind.stat.report', function($data) {
            if (isset($data['group'])) {
                $this->stats[$data['type']][$data['group']][$data['pid']] = $data['stat'];
            } else {
                $this->stats[$data['type']]['_'][$data['pid']] = $data['stat'];
            }
        });

        $channel->on('wind.stat.get', function($data) use ($channel) {
            echo "Received wind.stat.get\n";
            print_r($data);
            print_r($this->stats);
            $channel->publish('wind.stat.return.'.$data['id'], $this->stats);
        });

        Timer::add(1, function() use ($channel) {
            $channel->publish('wind.stat.tick', null);
        });
    }

}
