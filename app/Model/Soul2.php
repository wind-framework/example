<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Soul2 extends Model
{

    protected $table = 'soul';

    public function post()
    {
        return $this->belongsTo(Post2::class, 'id', 'id');
    }

}
