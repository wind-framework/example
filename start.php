<?php

use Wind\Base\Application;
use Workerman\Worker;

require __DIR__.'/vendor/autoload.php';

define('BASE_DIR', __DIR__);
define('RUNTIME_DIR', __DIR__.'/runtime');

Worker::$logFile = RUNTIME_DIR.'/log/workerman.log';
Worker::$pidFile = RUNTIME_DIR.'/workerman-amphp.pid';

if (!is_dir(RUNTIME_DIR)) {
    mkdir(RUNTIME_DIR, 0775);
}

Application::start();

//start with xdebug: /opt/php81/bin/php -dzend_extension=xdebug -dxdebug.mode=debug -dxdebug.start_with_request=yes start.php start
