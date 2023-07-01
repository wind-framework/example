<?php

namespace App\Controller;

use Amp\Http\Client\Connection\DefaultConnectionFactory;
use Amp\Http\Client\Connection\UnlimitedConnectionPool;
use Amp\Http\Client\Connection\UnprocessedRequestException;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\HttpException as ClientHttpException;
use Amp\Http\Client\Request as HttpRequest;
use Amp\Http\Tunnel\Http1TunnelConnector;
use App\Helper\Invoker;
use App\Job\TestJob;
use App\Job\TimeoutJob;
use App\Task\TestTask;
use Wind\Base\Config;
use Wind\Log\LogFactory;
use Wind\Queue\Queue;
use Wind\Queue\QueueFactory;
use Wind\Task\Task;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Wind\Base\Channel;
use Wind\Base\Context;
use Wind\Event\EventDispatcher;
use Wind\Process\ProcessState;
use Wind\View\ViewInterface;
use Wind\Web\Annotation\Get;
use Wind\Web\Annotation\Post;
use Wind\Web\Request as WebRequest;
use Wind\Web\RequestInterface;
use Wind\Web\Response;
use Workerman\Protocols\Http\Request;
use Workerman\Timer;

use function Amp\async;
use function Amp\delay;

class TestController extends \Wind\Web\Controller
{

    public $invoker;

    // public function __construct(Invoker $invoker)
    // {
    //     $this->invoker = $invoker;
    // }

    public static function ex()
    {
        //$a = [
        //    Task::execute([$this->invoker, 'getCache'], 'ABCDEFG'),
        //    compute([$this->invoker, 'someBlock'], 'ABCDEFG')
        //];
        //
        //$b = yield Promise\all($a);

        echo "Hello World\n";

        Timer::add(1, asyncCallable(fn() => self::ex()), persistent: false);

        di()->get(EventDispatcher::class)->dispatch(new \Wind\Crontab\CrontabEvent('test', \Wind\Crontab\CrontabEvent::TYPE_SCHED, 666));

        self::bb();
        //Unsupported closure task call at 1.0.x-dev
        // $v = compute(function() {
        //     delay(2000);
        //     return 'Hello World '.time();
        // });

        // return $v;
        // $previous = new ClientHttpException('Bad Request', 400);
        // throw new UnprocessedRequestException($previous);
    }

    public static function bb()
    {
        di()->get(EventDispatcher::class)->dispatch(new \Wind\Crontab\CrontabEvent('test', \Wind\Crontab\CrontabEvent::TYPE_EXECUTE));

        async(static fn($arg) => \Wind\Task\Task::await($arg), [TestTask::class, 'query'])->map(function($e, $result) {
            if ($e) {
                echo $e->getMessage()."\n";
            } else {
                var_dump($result);
            }
        });
    }

    public function taskCall()
    {
        self::ex();

        // $a = Task::execute(self::class.'::'.'ex');

        // $a = compute([TestTask::class, 'abc']);

        // return $a;
    }

    public function request(Request $req, $id, ContainerInterface $container, CacheInterface $cache)
    {
        $hello = $container->get(Config::class)->get('components')[0];
        return 'Request, id=' . $id . ', name=' . $req->get('name') . (yield $cache->get('abc', 'def')) . $hello;
    }

    public function queue(QueueFactory $factory)
    {
        $queue = $factory->get('default');
        $ret = [];

        $job = new TestJob('Hello World [Low Priority] ' . date('Y-m-d H:i:s'));
        $ret[] = $queue->put($job, 2, Queue::PRI_LOW);

        $job = new TestJob('Hello World [Normal Priority] ' . date('Y-m-d H:i:s'));
        $ret[] = $queue->put($job);

        $job = new TestJob('Hello World [High Priority] ' . date('Y-m-d H:i:s'));
        $ret[] = $queue->put($job, 2, Queue::PRI_HIGH);

        $job = new TimeoutJob();
        $ret[] = $queue->put($job);

        return json_encode($ret);
    }

    public function http()
    {
        // $socketConnector = new Http1TunnelConnector('192.168.4.4:7890');
        // $client = (new HttpClientBuilder())
        //     ->usingPool(new UnlimitedConnectionPool(new DefaultConnectionFactory($socketConnector)))
        //     ->build();

        $client = HttpClientBuilder::buildDefault();

        $request = new HttpRequest('https://api64.ipify.org?format=json');

        $response = $client->request($request);


        $status = $response->getStatus();
        //print_r($response->getHeaders());
        $buffer = $response->getBody()->buffer();

        if ($status == 200) {
            // $json = substr($buffer, 19, -1);
            $data = json_decode($buffer, true);
            return "<p>IP：{$data['ip']}</p><p>Location：{$data['city']}</p>";
        } else {
            return 'Request ' . $status . ' Error!';
        }
    }

    public function log(LogFactory $logFactory)
    {
        $log = $logFactory->get('test');

        // $e = new \ErrorException('This is a error!');

        // add records to the log
        // $log->warning('Foo', [$e]);
        $log->error('Bar');

        // yield Task::execute([self::class, 'taskLog']);
        return 'Ok';
    }

    public static function taskLog()
    {
        di()->get(LogFactory::class)->get()->info("Log in task");
    }

    public static function clientIp(RequestInterface $request)
    {
        return 'Your IP Address is: ' . $request->getClientIp();
    }

    public function uploadFile(RequestInterface $request)
    {
        $u = $request->getUploadedFile('test');

        $to = RUNTIME_DIR . '/test-' . uniqid() . '.jpg';
        $u->moveTo($to);

        return (new Response())->withHeader('X-Workerman-Sendfile', $to);
    }

    public function sendFile()
    {
        return (new Response())
            ->withHeader('X-Workerman-Sendfile', BASE_DIR . '/static/workerman_logo.png');
    }

    public function stat(Channel $channel, ViewInterface $view)
    {
        $data = ProcessState::get();

        $queueConsumerHelp = [];

        if (isset($data['queue_consumer_concurrent'])) {
            foreach ($data['queue_consumer_concurrent'] as $group => $process) {
                $queueConsumerHelp[$group]['group'] = array_sum(array_map('count', $process));
                foreach ($process as $pid => $items) {
                    $queueConsumerHelp[$group][$pid] = count($items);
                }
            }
        }

        return $view->render('stat.twig', compact('data', 'queueConsumerHelp'));
    }

    public function websocket(ViewInterface $view)
    {
        return $view->render('websocket.twig');
    }

    #[Get('/hello/test123')]
    public function helloTest(LogFactory $logFactory)
    {
        $logger = $logFactory->get('test-helloTest', 'task');
        $logger->info('This is a test.');

        return "This is hello test";
    }

    #[Post('/hello/a2')]
    public function a2()
    {
        return "This is a2";
    }

    #[Get('/test/request-context-1')]
    public function requestContext1()
    {
        delay(5);

        $invoker = di()->get(Invoker::class);
        $userId = $invoker->getCurrentUser();

        Context::set('context-name', WebRequest::current()->getUri()->getPath());

        $contextName = $invoker->getContextName();

        return "Request context 1 get user id is: $userId, context name is: $contextName.\n";
    }

    #[Get('/test/request-context-2')]
    public function requestContext2()
    {
        $invoker = di()->get(Invoker::class);

        $userId = $invoker->getCurrentUser();

        Context::set('context-name', WebRequest::current()->getUri()->getPath());

        $contextName = $invoker->getContextName();

        return "Request context 2 get user id is: $userId, context name is: $contextName.\n";
    }

}
