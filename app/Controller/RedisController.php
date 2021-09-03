<?php

namespace App\Controller;

use Wind\Redis\Redis;
use Wind\Web\Controller;

class RedisController extends Controller
{

    public function script(Redis $redis)
    {
        $num = $redis->eval("return redis.call('incr', KEYS[1])", 1, 'testincr');
        return $num;
    }

}
