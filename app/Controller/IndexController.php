<?php

namespace App\Controller;

use App\Collect\GcRecycle;
use App\Collect\GcStatusCollect;
use Psr\SimpleCache\CacheInterface;
use Revolt\EventLoop;
use Wind\Collector\Collector;
use Wind\Utils\FileUtil;
use Wind\View\ViewInterface;
use Wind\Web\Controller;
use Wind\Web\Response;
use function Amp\delay;

class IndexController extends Controller
{

    public function index()
    {
        return 'Hello World'.\env('APP');
    }

    public function cache(CacheInterface $cache)
    {
        $ret = $cache->get("lastvisit", "None");

        $cache->setMultiple(['a'=>111, 'b'=>222, 'c'=>333]);
        $b = $cache->getMultiple(['a', 'b', 'c', 'd']);

        $cache->set("lastvisit", ["last"=>date('Y-m-d H:i:s'), "timestamp"=>time()], 86400);

        $b['lastvisit'] = $ret;

        return json_encode($b);
    }

    public function sleep()
    {
        delay(5000);
        return 'Sleep 5 seconds.';
    }

    public function block()
    {
        sleep(5);
        return 'Block sleep 5 seconds.';
    }

    public function exception()
    {
        throw new \Exception('Test something wrong!');
    }

    public function gcStatus(ViewInterface $view)
    {
        //运行时间
        $runSeconds = time() - getApp()->startTimestamp;
        $running = \floor($runSeconds / 86400) . ' 天 '
            . \floor(($runSeconds % 86400) / 3600) . ' 小时 '
            . \floor(($runSeconds % 3600) / 60) . ' 分 '
            . \floor($runSeconds % 60) . ' 秒';

        $driver = EventLoop::getDriver();
        $event = substr(explode('\\', get_class($driver))[3], 0, -6);

        if ($event == 'StreamSelect') {
            $event = 'Native';
        }

        //内存回收统计
        /** @var GcStatusCollect[] $info */
        $info = Collector::get(GcStatusCollect::class);

        usort($info, function($a, $b) {
            return $a->pid <=> $b->pid;
        });

        foreach ($info as &$r) {
            $r->memoryUsage = FileUtil::formatSize($r->memoryUsage);
            $r->memoryUsageOccupy = FileUtil::formatSize($r->memoryUsageOccupy);
            $r->memoryUsagePeak = FileUtil::formatSize($r->memoryUsagePeak);
        }

        return $view->render('gc-status.twig', compact('info', 'running', 'event'));
    }

    public function gcRecycle()
    {
    	$info = Collector::get(GcRecycle::class);
    	return new Response(302, '', ['Location'=>'/gc-status']);
    }

    public function phpinfo(ViewInterface $view)
    {
        return \PhpInfoCliParser\Parser::render();
    }

}
