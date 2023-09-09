<?php

namespace App\Model;

use Wind\Db\Behaviors\DatetimeBehavior;
use Wind\Db\Model;
use Wind\Db\Modifier\DatetimeModifier;

class Post extends Model
{

    use DatetimeModifier;

    protected $table = 'posts';

    protected $datetimeAttributes = [
        Model::EVENT_BEFORE_CREATE => ['created_at', 'updated_at'],
        Model::EVENT_BEFORE_UPDATE => ['updated_at']
    ];

    protected static function boot()
    {
        // self::addBehavior(DatetimeBehavior::class);
    }

}
