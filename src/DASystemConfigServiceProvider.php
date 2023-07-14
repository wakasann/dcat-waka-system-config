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
		$this->register_routes();
	}
    
     public function register_routes(){
        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        app('router')->group($attributes, function ($router) {
            /* @var \Illuminate\Routing\Router $router */
			$router->resource('wakazunn/config/tabs', 'Wakazunn\DASystemConfig\Http\Controllers\PayConfigTabController');
			$router->resource('wakazunn/config/configs', 'Wakazunn\DASystemConfig\Http\Controllers\PayConfigController');
			$router->get('wakazunn/config/list', 'Wakazunn\DASystemConfig\Http\Controllers\PayConfigController@configs')->name('waka-syste-config-list');
			$router->post('wakazunn/config/save_basics', 'Wakazunn\DASystemConfig\Http\Controllers\PayConfigController@save_basics')->name('waka-syste-config-list-save');
        });
    }
    
	public function init()
	{
		parent::init();
		
	}

	public function settingForm()
	{
		return new Setting($this);
	}
}
