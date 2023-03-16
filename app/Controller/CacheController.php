<?php

namespace App\Controller;

use Psr\SimpleCache\CacheInterface;
use Wind\Web\Controller;

class CacheController extends Controller
{

    public function get(CacheInterface $cache)
    {
        return $cache->get('test');
    }

    public function set(CacheInterface $cache)
    {
        return $cache->set('test', 'Hello World', 30);
    }

}
