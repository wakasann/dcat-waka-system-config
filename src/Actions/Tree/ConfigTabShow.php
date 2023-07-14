<?php

namespace Wakazunn\DASystemConfig\Actions\Tree;

use Dcat\Admin\Tree\RowAction;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Traits\HasPermissions;
use Wakazunn\DASystemConfig\Models\ConfigTab;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ConfigTabShow extends RowAction
{

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $key = $this->getKey();
        $menu = ConfigTab::find($key);

        $menu->update(['status' => $menu->status ? 0 : 1]);

        return $this
            ->response()
            ->success(trans('admin.update_succeeded'))
            ->location('wakazunn/config/tabs');
    }

    public function title()
    {
        $icon = $this->getRow()->status ? 'icon-eye-off' : 'icon-eye';

        return "&nbsp;<i class='feather $icon'></i>&nbsp;";
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
