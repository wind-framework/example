<?php

namespace App\Controller;

use App\Model\Soul;
use Wind\Web\Controller;
use Wind\Db\Db;
use Wind\View\ViewInterface;

use function Amp\async;
use function Amp\Future\await;

class DbController extends Controller
{

    public function soul(ViewInterface $view)
    {
        $soul = Soul::random();

        if ($soul) {
            $soul->increment('hits');
            return $view->render('soul.twig', ['title'=>$soul->title]);
        } else {
            return "今天不丧。";
        }
    }

    public function soulFind(string $id)
    {
        $row = Soul::find($id);

        if ($row) {
            return $row;
        } else {
            return "无该丧。";
        }
    }

    public function souls()
    {
        $souls = Soul::query()->limit(10)->fetchAll();
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
