<?php

namespace Wakazunn\DASystemConfig\Actions\Grid;

use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BackToConfigTab extends AbstractTool
{

    /**
     * 按钮样式定义，默认 btn btn-white waves-effect
     *
     * @var string
     */
    protected $style = 'btn btn-primary';


    /**
     * @return string
     */
	protected $title = '<i class="feather icon-rotate-ccw"></i>配置分类';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        return $this->response()
            ->redirect(admin_url('wakazunn/config/tabs'));
    }

    /**
     * @return string|void
     */
    protected function href()
    {
//        return admin_url('pay_config_tab');
    }


    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }

    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }
}
