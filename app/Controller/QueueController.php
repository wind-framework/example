<?php

namespace App\Controller;

use Wind\Queue\QueueFactory;
use Wind\View\ViewInterface;
use Wind\Web\Controller;
use Wind\Web\RequestInterface;
use Wind\Web\Response;

class QueueController extends Controller
{

    protected function instance(RequestInterface $request)
    {
        $name = $request->get('queue', 'default');
        return di()->get(QueueFactory::class)->get($name);
    }

    /**
     * 队列服务器状态
     *
     * @param ViewInterface $view
     * @param RequestInterface $request
     * @return void
     */
    public function index(ViewInterface $view, RequestInterface $request)
    {
        $currentKey = $request->get('queue', 'default');
        $queueKeys = array_keys(config('queue'));
        $stats = yield $this->instance($request)->stats();
        return $view->render('queue/index.twig', compact('stats', 'currentKey', 'queueKeys'));
    }

    public function peek(ViewInterface $view, RequestInterface $request, $status)
    {
        $queue = $this->instance($request);

        switch ($status) {
            case 'ready': $message = yield $queue->peekReady(); break;
            case 'delayed': $message = yield $queue->peekDelayed(); break;
            case 'fail': $message = yield $queue->peekFail(); break;
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

        $count = yield $this->instance($request)->wakeup($num);

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

        $count = yield $this->instance($request)->drop($num);

        if ($count == 0) {
            return "No failed job to drop.";
        } else {
            return "Success drop $count failed jobs.";
        }
    }

}
