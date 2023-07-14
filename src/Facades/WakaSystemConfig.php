<?php

namespace Wakazunn\DASystemConfig\Facades;

use Illuminate\Support\Facades\Facade;
use Wakazunn\DASystemConfig\Contracts\WakaSystemConfig as RealClass;

/**
 * @method static mixed|null getConfig(string $key,$default = null) get config key value
 * @method static mixed|null saveConfig(string $key,$value = null) save config key
 * 
 * @see \Wakazunn\DASystemConfig\Contracts\WakaSystemConfig
 */
class WakaSystemConfig extends Facade{

        /**
     *
     * Get the registered name of the component.
     *
     * @return string
     *
     */
    protected static function getFacadeAccessor()
    {
//        return 'Receivable';
        return RealClass::class;
    }
}