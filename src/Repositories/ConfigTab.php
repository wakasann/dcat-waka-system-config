<?php

namespace Wakazunn\DASystemConfig\Repositories;

use Wakazunn\DASystemConfig\Models\ConfigTab as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class ConfigTab extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

    public function getConfigTab($pid){
        $model = $this->model();
        $list = $model->where(['status' => 1])->when($pid <= 0,function($q){
                return $q->whereIn('type',[0,4]);
            })->select(['id', 'id as value', 'title as label', 'parent_id', 'icon', 'type'])->orderBy('order','asc')->orderBy('id','asc')->get();

        if (is_object($list)) {
            $list = $list->toArray();
        }
        foreach ($list as &$item) {
            $item['children'] = (new Config())->getConfigChildrenTabAll($item['value']);
        }
        unset($item);
        return $list;
//        return get_tree_children($list);
    }

    /**
     * @param int $type
     * @return \think\Collection
     */
    public  function getChildrenTab($pid)
    {
        $where['status'] = 1;
        $where['parent_id'] = $pid;
        return $this->model()->where($where)->orderByRaw('`order` asc,`id` asc')->get();
    }


    public function getInfo($id){
        return $this->model()->where('id',$id)->first();
    }
}
