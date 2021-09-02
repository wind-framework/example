<?php

namespace App\Task;

use Wind\Db\Db;

use function Amp\await;

class TestTask
{

    public function abc() {
        echo "TestTask::abc() run~\n";
    }

    public function query() {
        // $row = await(Db::fetchOne("SELECT NOW()"));
        $row = 'aa';
        print_r($row);
    }

}
