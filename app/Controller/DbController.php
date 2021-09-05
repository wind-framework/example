<?php

namespace App\Controller;

use Wind\Web\Controller;
use Wind\Db\Db;
use Wind\View\ViewInterface;

use function Amp\async;
use function Amp\await;

class DbController extends Controller
{

    public function soul(ViewInterface $view)
    {
        $count = Db::table('soul')->count();
        $offset = mt_rand(0, $count-1);
        $row = Db::table('soul')->limit(1, $offset)->fetchOne();

        if ($row) {
            Db::table('soul')->where(['id' => $row['id']])->update(['^hits'=>'hits+1']);
            return $view->render('soul.twig', ['title'=>$row['title']]);
        } else {
            return "今天不丧。";
        }
    }

    public function soulFind($id)
    {
        $row = Db::table('soul')->where(['id' => $id])->fetchOne();

        if ($row) {
            return $row;
        } else {
            return "无该丧。";
        }
    }

    public function souls()
    {
        $souls = Db::table('soul')->limit(10)->fetchAll();
        return $souls;
    }

    /**
     * DB Concurrent
     *
     * /db/concurrent
     *
     * @return void
     */
    public function concurrent()
    {
        $begin = microtime(true);

        $a = async(fn() => Db::execute("SELECT SLEEP(3)"));
        $b = async(fn() => Db::execute("SELECT SLEEP(3)"));

        await([$a, $b]);

        $used = microtime(true) - $begin;

        return "concurrent($used)";
    }

}
