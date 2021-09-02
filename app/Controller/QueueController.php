<?php

namespace App\Controller;

use Wind\Queue\QueueFactory;
use Wind\View\ViewInterface;
use Wind\Web\Controller;
use Wind\Web\RequestInterface;
use Wind\Web\Response;

class QueueController extends Controller
{

    /**
     * @var \Wind\Queue\Queue
     */
    private $queue;

    public function __construct(QueueFactory $queueFactory)
    {
        $this->queue = $queueFactory->get();
    }

    /**
     * 队列服务器状态
     *
     * @param ViewInterface $view
     * @param QueueFactory $queueFactory
     * @return void
     */
    public function index(ViewInterface $view)
    {
        $stats = $this->queue->stats();
        return $view->render('queue/index.twig', compact('stats'));
    }

    public function peek(ViewInterface $view, $status)
    {
        switch ($status) {
            case 'ready': $message = $this->queue->peekReady(); break;
            case 'delayed': $message = $this->queue->peekDelayed(); break;
            case 'fail': $message = $this->queue->peekFail(); break;
            default: return new Response(400, "Unknown status $status to peek.");
        }

        if (!$message) {
            return new Response(404, "Not message to peek for status $status.");
        }

        $jobClass = get_class($message->job);

        return $view->render('queue/peek.twig', compact('message', 'jobClass', 'status'));
    }

    public function wakeup(RequestInterface $request)
    {
        $num = (int)$request->get('num', 1);

        if ($num < 1) {
            return new Response(400, 'Invalid num param.');
        }

        $count = $this->queue->wakeup($num);

        if ($count == 0) {
            return "No failed job to wakeup.";
        } else {
            return "Success wakeup $count failed jobs.";
        }
    }

    public function drop(RequestInterface $request)
    {
        $num = (int)$request->get('num', 1);

        if ($num < 1) {
            return new Response(400, 'Invalid num param.');
        }

        $count = $this->queue->drop($num);

        if ($count == 0) {
            return "No failed job to drop.";
        } else {
            return "Success drop $count failed jobs.";
        }
    }

}
