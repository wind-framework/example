<?php

namespace App\Controller;

use Amp\Promise;
use Amp\Sql\QueryError;
use ErrorException;
use Wind\Web\Controller;
use Wind\Db\Db;
use Wind\View\ViewInterface;

class DbController extends Controller
{

    public function soul(ViewInterface $view)
    {
        $count = yield Db::table('soul')->count();
        $offset = mt_rand(0, $count-1);

        $row = yield Db::table('soul')->limit(1, $offset)->fetchOne();

        if ($row) {
            yield Db::table('soul')->where(['id' => $row['id']])->update(['^hits'=>'hits+1']);
            return $view->render('soul.twig', ['title'=>$row['title']]);
        } else {
            return "今天不丧。";
        }
    }

    public function soulFind($id)
    {
        $row = yield Db::table('soul')->where(['id' => $id])->fetchOne();

        if ($row) {
            return print_r($row, true);
        } else {
            return "无该丧。";
        }
    }

    public function concurrent()
    {
        $a = Db::execute("SELECT SLEEP(3)");
        $b = Db::execute("SELECT SLEEP(3)");

        yield Promise\all([$a, $b]);

        return 'concurrent';
    }

    public function insert()
    {
        return yield Db::table('posts')->insert([
            'id' => 19,
            'title' => 'Test insert ignore',
            'body' => 'Test insert ignore\'s body content.'
        ], [
            'body' => 'Test insertOrUpdate is updated 2.'
        ]);
    }

    public function transaction()
    {
        /**
         * @var \Wind\Db\Transaction $transaction
         */
        $transaction = yield Db::beginTransaction();
        yield $transaction->table('posts')->where(['id'=>2])->update(['^views'=>'views+1']);
        yield $transaction->table('posts')->where(['id'=>3])->update(['^views'=>'views+1']);
        $data = yield $transaction->table('posts')->where(['id'=>1])->fetchOne();
        yield $transaction->rollback();

        yield Db::table('posts')->where(['id'=>1])->update(['^views'=>'views+1']);

        return $data;
    }

}
