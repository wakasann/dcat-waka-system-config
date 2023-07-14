<?php

namespace Wakazunn\DASystemConfig\Repositories;

use Wakazunn\DASystemConfig\Models\Config as Model;
use Dcat\Admin\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Cache;

class Config extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;



    public function getConfigChildrenTabAll($pid = 0){
        $configAll = (new PayConfigTab())->getChildrenTab($pid);
        $config_tab = [];
        foreach ($configAll as $k => $v) {
            if (!$v['info']) {
                $config_tab[$k]['id'] = $v['id'];
                $config_tab[$k]['label'] = $v['title'];
                $config_tab[$k]['icon'] = $v['icon'];
                $config_tab[$k]['type'] = $v['type'];
                $config_tab[$k]['parent_id'] = $v['parent_id'];
            }
        }
        return $config_tab;
    }

    public function getConfigFieldAll($tabId){
        return $this->model()->where('config_tab_id',$tabId)->orderBy('order','desc')->orderBy('id','asc')->get();
    }

    /**
     *  初始化支付配置缓存
     */
    public function initConfigCache(){
        // if(!Cache::has('pay_system_config') || !Cache::has('pay_config')){
        //     $data =  \Wakazunn\DASystemConfig\Models\PayConfig::getAllConfig();
        //     foreach($data )
        //     Cache::forever('pay_system_config',$data);
        // }

    }

    /**
     *  重置支付配置信息
     */
    public function restoreConfig(){
        // if(Cache::has('pay_system_config')){
        //     Cache::forget('pay_system_config');
        // }
        // if(Cache::has('pay_config')){
        //     Cache::forget('pay_config');
        // }
        $this->initConfigCache();
    }

}
