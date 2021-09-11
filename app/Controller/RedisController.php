<?php

namespace App\Controller;

use Wind\Redis\Redis;

use function Amp\async;
use function Amp\await;
use function Amp\defer;
use function Amp\delay;

class RedisController extends \Wind\Web\Controller
{

    /**
     * Redis 事务并发问题测试
     *
     * 期望：["aaa 123","bbb 234","ccc 345","ccc 456"]
     * 实际：["aaa 123","bbb 234","ccc 345","ccc 345"]
     *
     * 由于 defer 与 0.x 版本的机制不同，defer 是将内部的回调函数排到下个异步的 Tick 执行，
     * 而基于 call() 和 yield 的协程，或是直接调用返回 Promise 的方法是先执行到 Generator 返回时，
     * 所以导致此处的结果不同，在 0.x 中第三个 transaction 会先于后面的 get('ccc') 执行，
     * 而此处由于 defer 的特性，其执行顺序不能保证，此测试它会排到后面执行，所以实际与原期望不同。
     *
     * 但这并不影响整个程序的执行和数据一致性，此处只是特意用 defer 产生并发的效果。
     *
     * @param Redis $redis
     * @return \Generator
     */
    public function transaction(Redis $redis)
    {
        defer([$redis, 'transaction'], function($transaction) {
            $transaction->set('aaa', 'aaa 123', 5);
            $transaction->set('bbb', 'bbb 234', 5);
        });

        defer([$redis, 'transaction'], function($transaction) {
            delay(100);
            $transaction->set('ccc', 'ccc 345', 5);
        });

        $ps = await([
            async([$redis, 'get'], 'aaa'),
            async([$redis, 'get'], 'bbb'),
            async([$redis, 'get'], 'ccc')
        ]);

        defer([$redis, 'transaction'], function($transaction) {
            $transaction->set('ccc', 'ccc 456', 5);
        });

        $ps[] = $redis->get('ccc');

        return $ps;
    }

    public function script(Redis $redis)
    {
        $num = $redis->eval("return redis.call('incr', KEYS[1])", 1, 'testincr');
        return $num;
    }

}
