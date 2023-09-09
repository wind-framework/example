<?php

define('BASE_DIR', realpath(__DIR__.'/..'));
define('RUNTIME_DIR', BASE_DIR.'/runtime');

if (!is_dir(RUNTIME_DIR)) {
    mkdir(RUNTIME_DIR, 0775);
}
