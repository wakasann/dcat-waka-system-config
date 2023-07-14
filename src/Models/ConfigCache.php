<?php

namespace Wakazunn\DASystemConfig\Models;

use Dcat\Admin\Admin;
use Wakazunn\DASystemConfig\DASystemConfigServiceProvider;
use Illuminate\Support\Facades\Cache;

trait ConfigCache
{

    protected $cacheKey = 'waka-sys-config-%s';

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @param \Closure $builder
     *
     * @return mixed
     */
    public function remember(\Closure $builder)
    {
        if (! $this->enableCache()) {
            return $builder();
        }

        return $this->getStore()->remember($this->getCacheKey(), null, $builder);
    }

    /**
     * @return bool|void
     */
    public function flushCache()
    {
        if (! $this->enableCache()) {
            return;
        }

        return $this->getStore()->delete($this->getCacheKey());
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return sprintf($this->cacheKey, Admin::app()->getName());
    }

    /**
     * @return bool
     */
    public function enableCache()
    {
        return DASystemConfigServiceProvider::setting('wakazunn_system_config_cache_enable',false);
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

    public function getCachePrefix()
    {
        return DASystemConfigServiceProvider::setting('wakazunn_system_config_cache_prefix','waka:system:config');
    }
}