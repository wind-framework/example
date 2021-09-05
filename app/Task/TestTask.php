<?php

namespace App\Task;

use Wind\Db\Db;

class TestTask
{

    public function abc() {
        echo "TestTask::abc() run~\n";
    }

    public function query() {
        $row = Db::fetchOne("SELECT NOW()");
        print_r($row);
    }

}
