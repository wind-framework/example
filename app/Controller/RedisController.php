<?php


namespace App\Controller;


use Wind\Redis\Redis;
use function Amp\delay;
use function Amp\Promise\all;

class RedisController extends \Wind\Web\Controller
{

    /**
     * Redis 事务并发问题测试
     *
     * 期望：["aaa 123","bbb 234","ccc 345","ccc 456"]
     *
     * @param Redis $redis
     * @return \Generator
     */
    public function transaction(Redis $redis)
    {
        $redis->transaction(function($transaction) {
            yield $transaction->set('aaa', 'aaa 123', 5);
            yield $transaction->set('bbb', 'bbb 234', 5);
        });

        $redis->transaction(function($transaction) {
            yield delay(100);
            yield $transaction->set('ccc', 'ccc 345', 5);
        });

        $ps = yield all([$redis->get('aaa'), $redis->get('bbb'), $redis->get('ccc')]);

        $redis->transaction(function($transaction) {
            $transaction->set('ccc', 'ccc 456', 5);
        });

        $ps[] = yield $redis->get('ccc');

        return $ps;
    }

}
