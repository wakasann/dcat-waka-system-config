<?php

namespace  Wakazunn\DASystemConfig\Contracts;

use  Wakazunn\DASystemConfig\Models\Config;
use Illuminate\Support\Facades\Cache;
use Wakazunn\DASystemConfig\DASystemConfigServiceProvider;

class WakaSystemConfig {

    private $prefix = 'waka:system:config:%s';

    public function getCacheKey($key){
        return sprintf($this->prefix,$key);
    }

    /**
     * Get cache store.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function getStore()
    {
        return Cache::store(DASystemConfigServiceProvider::setting('wakazunn_system_config_cache_store','file'));
    }

    /**
     * 保存Cache
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function saveConfig($key,$value){
        $this->getStore()->put($this->getCacheKey($key),$value);
    }

    /**
     * 获取配置
     *
     * @param string $key
     * @return mixed|null
     */
    public function getConfig($key,$default = null){
        $value = $this->getStore()->get($this->getCacheKey($key));
        if($value === false || $value == null){
            $configRecord = Config::where('menu_name',$key)->first('value');
            if($configRecord){
                $value = $configRecord->value;
            }else{
                return $default;
            }
        }
        return json_decode($value,true);
    }
}