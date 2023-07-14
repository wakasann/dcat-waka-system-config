<?php

namespace Wakazunn\DASystemConfig;

use Dcat\Admin\Extend\Setting as Form;

class Setting extends Form
{
    public function form()
    {
        $this->switch('wakazunn_system_config_cache_enable','缓存配置')->default(false);

        $keys = array_keys(config('cache.stores'));

        $options = [];
        foreach($keys as $v){
            $options[$v] = $v;
        }

        $this->select('wakazunn_system_config_cache_store','配置缓存存储')->default('redis')->options($options)->required();

        $this->select('wakazunn_system_config_cache_prefix','配置缓存存储前缀')->default('waka:system:config')->options($options)->required();
    }
}
