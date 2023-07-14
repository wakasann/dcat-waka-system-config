<?php

namespace Wakazunn\DASystemConfig\Models;

use \Illuminate\Database\Eloquent\Model;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

use Dcat\Admin\Traits\ModelTree;

class Config extends Model
{
    use HasDateTimeFormatter,
        ConfigCache,
        ModelTree {
        ModelTree::boot as treeBoot;
    }


    /** @var  string 父级ID */
    public $parentColumn = 'config_tab_id';
    protected $table = 'waka_system_config';
    protected $guarded = [];

    protected $casts = [
        'value' => AsArrayObject::class,
    ];


    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        static::treeBoot();


        // static::deleting(function ($model) {
        //     $model->flushCache();
        // });

        // static::saved(function ($model) {
        //     $model->flushCache();
        // });
    }

    /**
     * 获取单个参数配置
     * @param $menu
     * @return bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getConfigValue($menu)
    {
        if (empty($menu) || !($config_one = self::where('menu_name', $menu)->first())) return false;
        return $config_one['value'];
    }

    /**
     * 获得多个参数
     * @param $menus
     * @return array
     */
    public static function getMore($menus)
    {
        $menus = is_array($menus) ? $menus: $menus;
        $list = self::whereIn('menu_name', $menus)->pluck('value', 'menu_name') ?: [];
        foreach ($list as $menu => $value) {
            $list[$menu] = $value;
        }
        return $list;
    }

    /**
     * @return array
     */
    public static function getAllConfig()
    {
        $list = self::where('status',1)->pluck('value', 'menu_name')->toArray() ?: [];
        foreach ($list as $menu => $value) {
            $list[$menu] = $value;
        }
        return $list;
    }
}