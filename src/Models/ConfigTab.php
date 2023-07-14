<?php

namespace Wakazunn\DASystemConfig\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Dcat\Admin\Traits\ModelTree;

class ConfigTab extends Model implements Sortable
{
    use HasDateTimeFormatter,
        ConfigTabCache,
        ModelTree {
        allNodes as treeAllNodes;
        ModelTree::boot as treeBoot;
    }

    protected $table = 'waka_system_config_tab';

    /**
     * @var array
     */
    protected $sortable = [
        'sort_when_creating' => true,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'order', 'title', 'icon', 'eng_title', 'info', 'status', 'type'];


    /**
     * Get options for Select field in form.
     *
     * @param \Closure|null $closure
     * @param string        $rootText
     *
     * @return array
     */
    public static function selectOptions2(\Closure $closure = null, $rootText = null,$needRoot = true)
    {
        $rootText = $rootText ?: admin_trans_label('root');

        $options = (new static())->withQuery($closure)->buildSelectOptions();

        $options = collect($options);
        if($needRoot){
            $options = $options->prepend($rootText, 0);
        }

        return $options->all();
    }


    /**
     * Get all elements.
     *
     * @param bool $force
     *
     * @return static[]|\Illuminate\Support\Collection
     */
    public function allNodes($force = false)
    {
        if ($force || $this->queryCallbacks) {
            return $this->fetchAll();
        }

        return $this->remember(function () {
            return $this->fetchAll();
        });
    }

    /**
     * Fetch all elements.
     *
     * @return static[]|\Illuminate\Support\Collection
     */
    public function fetchAll()
    {
        return $this->treeAllNodes();
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        static::treeBoot();

        static::deleting(function ($model) {
            $model->flushCache();
        });

        static::saved(function ($model) {
            $model->flushCache();
        });
    }





}