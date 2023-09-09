<?php

namespace App\Model;

use Wind\Db\Model;

/**
 * @property int $id
 * @property string $title
 * @property int $hits
 */
class Soul extends Model
{

    protected $table = 'soul';

    public function getNameText()
    {
        return '['.$this->title.']';
    }

    /**
     * 随机获取一条毒鸡汤
     * @return self|null
     */
    public static function random()
    {
        $count = self::query()->count();
        $offset = mt_rand(0, $count-1);
        return self::query()->limit(1, $offset)->fetchOne();
    }

}
