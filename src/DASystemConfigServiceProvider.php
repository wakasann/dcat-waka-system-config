<?php

namespace Wakazunn\DASystemConfig;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Admin;

class DASystemConfigServiceProvider extends ServiceProvider
{

	protected $js = [
        
    ];
	protected $css = [
		
	];

    protected $menu = [
        [
            'title' => 'System Config',
            'uri'   => '',
			'icon'  => 'fa-cogs'
        ],
		[
			'parent' => 'System Config', // 指定父级菜单
			'title'  => 'Config Group',
			'uri'    => 'wakazunn/config/tabs',
		],
		[
			'parent' => 'System Config', // 指定父级菜单
			'title'  => 'Config',
			'uri'    => 'wakazunn/config/configs',
		],
    ];

	public function register()
	{
		
	}
    
     public function register_routes(){
        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        app('router')->group($attributes, function ($router) {
            /* @var \Illuminate\Routing\Router $router */
			$router->resource('wakazunn/config/tabs', 'Wakazunn\DASystemConfig\Http\Controllers\ConfigTabController');
			$router->resource('wakazunn/config/configs', 'Wakazunn\DASystemConfig\Http\Controllers\ConfigController');
			$router->get('wakazunn/config/list', 'Wakazunn\DASystemConfig\Http\Controllers\ConfigController@configs')->name('waka-syste-config-list');
			$router->post('wakazunn/config/save_basics', 'Wakazunn\DASystemConfig\Http\Controllers\ConfigController@save_basics')->name('waka-syste-config-list-save');
        });
    }
    
	public function init()
	{
		parent::init();
		$this->register_routes();
		
	}

	public function settingForm()
	{
		return new Setting($this);
	}
}
