<?php

namespace App\Helper;

use Psr\SimpleCache\CacheInterface;
use Wind\Base\Context;
use Wind\Web\Request;

use function Amp\delay;

class Invoker
{

	public $cache;

	public function __construct(CacheInterface $cache) {
		$this->cache = $cache;
	}

	public function getCache($input)
	{
        $lastVisit = $this->cache->get('lastvisit');
        return 'Input: '.$input.', Output: '.json_encode($lastVisit);
	}

	public function someBlock($a)
    {
        $c = '';
        for ($i=0; $i<1000; $i++) {
            $c .= $i;
        }
        sleep(1);
        return $a.$c;
    }

    public function getCurrentUser()
    {
        // delay(0.01);
        return Request::current()->get('uid');
    }

    public function getContextName()
    {
        delay(0.01);
        return Context::get('context-name');
    }

}
