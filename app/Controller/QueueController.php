<?php

namespace App\Controller;

use Wind\Queue\QueueFactory;
use Wind\Web\Controller;

class QueueController extends Controller
{

    /**
     * 消息队列的统计
     *
     * @return void
     */
    public function index(QueueFactory $queueFactory)
    {
        $queue = $queueFactory->get();

        $stats = yield $queue->stats();
        $ready = yield $queue->peekReady();
        $fail = yield $queue->peekFail();
        $delayed = yield $queue->peekDelayed();

        // $dropped = yield $queue->drop(10);

        return compact('stats', 'ready', 'fail', 'delayed');
    }

    /**
     * 唤醒失败的队列
     *
     * @return void
     */
    public function wakeup(QueueFactory $queueFactory)
    {
        $queue = $queueFactory->get();

        $failed = yield $queue->peekFail();
        $count = yield $queue->wakeup(1);

        return compact('failed', 'count');
    }

    /**
     * 移除失败的队列
     *
     * @return void
     */
    public function drop(QueueFactory $queueFactory)
    {
        $queue = $queueFactory->get();

        $failed = yield $queue->peekFail();
        $count = yield $queue->drop(1);

        return compact('failed', 'count');
    }

}
