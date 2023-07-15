<?php

namespace App\Listener;

use Monolog\Level;
use Wind\Base\Event\SystemError;
use Wind\Collector\CollectorEvent;
use Wind\Crontab\CrontabEvent;
use Wind\Db\Event\QueryError;
use Wind\Db\Event\QueryEvent;
use Wind\Event\Event;
use Wind\Log\LogFactory;
use Wind\Queue\QueueJobEvent;
use Wind\Task\TaskExecuteEvent;

class AppLogListener extends \Wind\Event\Listener
{

    private $logFactory;

    public function __construct(LogFactory $logFactory)
    {
        $this->logFactory = $logFactory;
    }

    public function listen(): array
    {
        return [
            QueryEvent::class,
            QueryError::class,
            // TaskExecuteEvent::class,
            //CollectorEvent::class,
            CrontabEvent::class,
            QueueJobEvent::class,
            SystemError::class
        ];
    }

    public function handle(Event $event)
    {
        $class = get_class($event);
        $name = substr($class, strrpos($class, '\\')+1);
        $logger = $this->logFactory->get($name);
        $level = Level::Info;

        if ($event instanceof CrontabEvent) {
            if ($event->result instanceof \Throwable) {
                $level = Level::Error;
            }
        } elseif ($event instanceof QueueJobEvent) {
            if ($event->error || $event->state == QueueJobEvent::STATE_ERROR || $event->state == QueueJobEvent::STATE_FAILED) {
                $level = Level::Error;
            }
        } elseif ($event instanceof SystemError) {
            $level = Level::Error;
        } elseif ($event instanceof QueryError) {
            $logger->log(Level::Error, $event->exception->getMessage()."\r\n".$event->sql);
            return;
        }

        $logger->log($level, $event->__toString());
    }
}
