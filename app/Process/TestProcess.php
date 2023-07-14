<?php

namespace App\Process;

use Wind\Base\Context;
use Wind\Base\TouchableTimeoutCancellation;
use Wind\Db\Db;
use Wind\Log\LogFactory;
use Wind\Process\Process;

use function Amp\async;
use function Amp\delay;
use function Amp\Future\await;

class TestProcess extends Process
{

    public function run()
    {
        $logger = di()->get(LogFactory::class)->get('test');

        $methods = get_class_methods($this);

        $success = $fail = 0;

        foreach ($methods as $method) {
            if (str_starts_with($method, 'test')) {
                $logger->info("Test $method..");
                try {
                    $this->{$method}();
                    $logger->info("Test $method passed.");
                    ++$success;
                } catch (\Throwable $e) {
                    $ex = $e::class;
                    $logger->error("Test $method failed.\nTest: $method().\nFile: {$e->getFile()}:{$e->getLine()}\n{$ex}: {$e->getMessage()}\n".$e->getTraceAsString());
                    ++$fail;
                }
            }
        }

        if ($fail == 0) {
            $logger->info('Congratulations! ----- √√√√√√√√√√ ALL TEST PASSED. √√√√√√√√√√');
        } else {
            $logger->error("TEST RESULT: $success TEST PASSED, ×××××××××× $fail TEST FAILURE!!! ××××××××××");
        }
    }

    protected function testIndexByConcurrency()
    {
        //Test indexBy() concurrency problem
        $rows = await([
            async(fn() => Db::table('soul')->where(['id'=>11])->indexBy('id')->fetchAll()),
            async(fn() => Db::table('soul')->where(['id'=>12])->indexBy('id')->fetchAll())
        ]);

        //indexBy 并发时，应该互相保持独立，多个查询获得的索引应该不受影响
        assert(array_keys($rows[0])[0] == current($rows[0])['id']);
        assert(array_keys($rows[1])[0] == current($rows[1])['id']);
    }

    /**
     * 测试内部 Promise 的异常状态是否正常抛出
     *
     * 在之前的实现中，如果内部的 Promise 较快完成，则返回的 Promise 实际上就是个 new Success() 对象，导致外部无法获得正确的值和异常。
     */
    protected function testTouchableTimeoutThrow()
    {
        $cancellation = new TouchableTimeoutCancellation(0.5);

        try {
            async(function() {
                throw new \ErrorException('Code exception.');
            })->await($cancellation);
            throw new \Exception('Should throw an exception.');
        } catch (\ErrorException $e) {
            assert($e->getMessage() === 'Code exception.');
        }
    }

    protected function testTouchableReturn()
    {
        $cancellation = new TouchableTimeoutCancellation(0.5);

        $ret = async(function() {
            delay(0.1);
            return 'Hello';
        })->await($cancellation);

        assert($ret === 'Hello');
    }

    protected function testContext()
    {
        $test = function($id) {
            delay(0.1);
            assert(Context::get('id') === $id, "Context::get('id') === $id, expect $id, actual: ".Context::get('id'));
        };

        await([
            async(function() use ($test) {
                Context::set('id', 111);
                $test(111);
            }),
            async(function() use ($test) {
                Context::set('id', 222);
                $test(222);
            }),
        ]);
    }

}
