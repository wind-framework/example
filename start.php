<?php

use Wind\Base\Application;
use Workerman\Worker;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/bootstrap.php';

Worker::$logFile = RUNTIME_DIR.'/log/workerman.log';
Worker::$pidFile = RUNTIME_DIR.'/workerman-amphp.pid';

Application::start();

//start with xdebug: /opt/php81/bin/php -dzend_extension=xdebug -dxdebug.mode=debug -dxdebug.start_with_request=yes start.php start
