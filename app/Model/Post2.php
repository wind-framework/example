<?php

namespace App\Model;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Post2 extends Model
{

    protected $table = 'posts';

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function soul()
    {
        return $this->hasOne(Soul2::class, 'id');
    }

}
