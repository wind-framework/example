<?php

namespace App\Controller;

use Amp\Promise;
use Amp\Sql\QueryError;
use App\Models\Soul;
use App\Models\Test;
use ErrorException;
use Wind\Web\Controller;
use Wind\Db\Db;
use Wind\View\ViewInterface;

class DbController extends Controller
{

    public function soul(ViewInterface $view)
    {
        /** @var Soul $soul */
        $soul = yield Soul::random();

        if ($soul) {
            yield $soul->increment('hits');
            return $view->render('soul.twig', ['title'=>$soul->title]);
        } else {
            return "今天不丧。";
        }
    }

    public function soulFind($id)
    {
        $row = yield Soul::find($id);

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

    public function souls()
    {
        return Db::table('soul')->limit(10)->indexBy('id')->fetchAll();
    }

}
