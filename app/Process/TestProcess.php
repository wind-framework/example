<?php

namespace App\Process;

use Wind\Db\Db;
use Wind\Log\LogFactory;
use Wind\Process\Process;

use function Amp\call;
use function Amp\Promise\all;

class TestProcess extends Process
{

    private $logger;

    public function run()
    {
        $this->logger = di()->get(LogFactory::class)->get('test');

        yield $this->testIndexByConcurrency();
    }

    protected function testIndexByConcurrency()
    {
        return call(function() {
            //Test indexBy() concurrency problem
            $rows = yield all([
                Db::table('soul')->where(['id'=>11])->indexBy('id')->fetchAll(),
                Db::table('soul')->where(['id'=>12])->indexBy('id')->fetchAll()
            ]);

            //indexBy 并发时，应该互相保持独立，多个查询获得的索引应该不受影响
            assert(array_keys($rows[0])[0] == current($rows[0])['id']);
            assert(array_keys($rows[1])[0] == current($rows[1])['id']);

            $this->logger->info("testIndexByConcurrency passed.");
        });
    }

}
