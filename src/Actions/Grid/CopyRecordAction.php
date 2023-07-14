<?php

namespace Wakazunn\DASystemConfig\Actions\Grid;

use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CopyRecordAction extends RowAction
{
    /**
     * @return string
     */
	protected $title = '复制';

    protected $model;

    public function __construct(string $model = null)
    {
        $this->model = $model;
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
        // dump($this->getKey());

         // 获取当前行ID
         $id = $this->getKey();

         // 获取 parameters 方法传递的参数
         $model = $request->get('model');
 
         // 复制数据
         $model::find($id)->replicate()->save();
 
         // 返回响应结果并刷新页面
         return $this->response()->success("复制成功")->refresh();
 
    }

    /**
	 * @return string|array|void
	 */
	public function confirm()
	{
		return [
            // 确认弹窗 title
            "提示",
            // 确认弹窗 content
            "您确定要复制这行数据吗？",
        ];
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
        return [
            // 把模型类名传递到接口
            'model' => $this->model,
        ];
    }
}
