<?php

namespace App\Models;

use Wind\Db\Model;

use function Amp\call;

/**
 * @property int $id
 * @property string $title
 * @property int $hits
 */
class Soul extends Model
{

    const TABLE = 'soul';

    public function getNameText()
    {
        return '['.$this->title.']';
    }

    public function events()
    {
        return [
            static::EVENT_INSERT => [

            ]
        ];
    }

    /**
     * 随机获取一条毒鸡汤
     * @return \Amp\Promise<static|null>
     */
    public static function random()
    {
        return call(function() {
            $count = yield self::query()->count();
            $offset = mt_rand(0, $count-1);
            return yield self::query()->limit(1, $offset)->fetchOne();
        });
    }

}
