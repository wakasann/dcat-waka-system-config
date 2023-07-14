<?php

namespace Wakazunn\DASystemConfig\Actions\Tree;

use Dcat\Admin\Tree\RowAction;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LinkToPayConfig extends RowAction
{

    public function title()
    {
        return "&nbsp;<i class='feather icon-list' title='配置列表'></i>&nbsp;";
    }

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
    	//
    	$key = $this->getKey();

        return $this->response()
            ->success('跳转成功')
            ->redirect(admin_url('/wakazunn/config/configs').'?tab_id='.$key);
    }

    /**
     * @return string|void
     */
    protected function href()
    {
        // return admin_url('auth/users');
    }

    /**
	 * @return string|array|void
	 */
	public function confirm()
	{
		// return ['Confirm?', 'contents'];
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
}
