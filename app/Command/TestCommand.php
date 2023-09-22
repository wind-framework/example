<?php

namespace App\Command;

use Amp\DeferredFuture;
use Amp\Http\Client\HttpClient;
use Amp\Http\Client\Request;
use Amp\Socket\ConnectContext;
use App\Model\Soul;
use Psr\SimpleCache\CacheInterface;
use Revolt\EventLoop;
use SplQueue;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wind\Beanstalk\Beanstalk as BeanstalkClient;
use Wind\Console\Annotation\Command as AnnotationCommand;
use Wind\Redis\Redis;

use function Amp\async;
use function Amp\delay;
use function Amp\Future\await;
use function Amp\Socket\connect;

#[AnnotationCommand]
#[AsCommand('test:playground')]
class TestCommand extends Command
{

    protected function configure()
    {
        $methods = [];

        $internalMethods = ['configure', 'execute'];

        $ref = new \ReflectionClass($this);
        foreach ($ref->getMethods() as $method) {
            if (!in_array($method->getName(), $internalMethods) && $method->getDeclaringClass()->getName() == self::class) {
                $methods[] = $method->getName();
            }
        }

        $this
            ->addArgument('method', InputArgument::REQUIRED, '要执行测试的具体方法,支持列表: '.join(', ', $methods));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $method = $input->getArgument('method');

        if (is_callable([$this, $method])) {
            di()->call($this->$method(...), ['input' => $input, 'output' => $output]);
            return self::SUCCESS;
        } else {
            $output->writeln("<error>Method $method not found for test.</>");
            return self::FAILURE;
        }
    }

    private function ampSocket(OutputInterface $output)
    {
        $connectContext = (new ConnectContext)
            ->withConnectTimeout(3);

        try {
            $socket = connect('192.168.4.2:6378', $connectContext);

            $socket->write("INFO\n");

            $buffer = $socket->read();

            echo $buffer;

            $socket->close();
        } catch (\Throwable $e) {
            $class = $e::class;
            $output->writeln("<error>$class: {$e->getMessage()}</error>");
        }
    }

    private function newRedis(OutputInterface $output)
    {
        $redis = new Redis([
            'host' => '192.168.4.3',
            // 'auth' => '93SQSqn20Ybf'
        ]);

        // $keys = $redis->keys('*');
        // $output->writeln($keys);

        // $output->writeln($redis->type('requestbin-requests'));

        // $futures = [];
        // for ($i=0; $i<100; $i++) {
        //     $futures[] = async(function() use ($redis, $output) {
        //         $redis->set('test-expire', \time(), 5);
        //         $output->writeln($redis->get('test-expire'));
        //     });
        // }
        // await($futures);

        $arr = $redis->transaction(function($transaction) {
            $transaction->set('test2', '123123123213213', 60);
            $transaction->set('test3', '123123123213213', 60);
            $transaction->get('test3');
        });

        print_r($arr);

        delay(5);

        try {
            $output->writeln($redis->get('test3'));
        } catch (\Throwable $e) {
            echo $e;
        }

        delay(5);

        try {
            $output->writeln($redis->get('test3'));
        } catch (\Throwable $e) {
            echo $e;
        }

        // $output->writeln($redis->get('test2'));
    }

    private function beanstalkPut(OutputInterface $output)
    {
        $client = new BeanstalkClient('192.168.4.2');

        $stats = $client->stats();

        $table = new Table($output);
        $table->setHeaders(['Key', 'Value']);
        $table->setRows(array_map(null, array_keys($stats), array_values($stats)));
        $table->render();

        $id = $client->put('Hello World');
        $output->writeln(sprintf("Put job id: %s", $id));
    }

    private function beanstalkReserve(OutputInterface $output)
    {
        $client = new BeanstalkClient('192.168.4.2');

        while (true) {
            $data = $client->reserve();
            $output->writeln(print_r($data, true));
            $client->delete($data['id']);
        }
    }

}
