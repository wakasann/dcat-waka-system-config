<?php

namespace Wakazunn\DASystemConfig\Http\Controllers;


use App\Models\Coin\Plan;
use Wakazunn\DASystemConfig\Actions\Grid\BackToConfigTab;
use Wakazunn\DASystemConfig\Repositories\Config;
use Wakazunn\DASystemConfig\Models\ConfigTab;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Tab;
use Wakazunn\DASystemConfig\Actions\Grid\CopyRecordAction;
use Illuminate\Http\Request;
use Wakazunn\DASystemConfig\DASystemConfigServiceProvider;
use Illuminate\Support\Facades\Cache;
use Wakazunn\DASystemConfig\Events\ConfigSaved;
use Wakazunn\DASystemConfig\Events\SubTabForm;
use Wakazunn\DASystemConfig\Facades\WakaSystemConfig;

class ConfigController extends AdminController
{

    public function title2($title)
    {
        return sprintf(DASystemConfigServiceProvider::trans('config.labels.Config'),$title);
    }

    public function index(Content $content)
    {
        $tab_id = (int)\request()->get('tab_id');
        $configTagInfo = ConfigTab::where('id',$tab_id)->first();
        if(!$configTagInfo){
            return redirect(admin_url('/wakazunn/config/tabs'));
        }

        return $content
            ->translation($this->translation())
            ->title($this->title2($configTagInfo['title']))
            ->description($this->description()['index'] ?? trans('admin.list'))
            ->body($this->grid($tab_id));
    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($tab_id)
    {
        return Grid::make(new Config(), function (Grid $grid) use ($tab_id) {

            $grid->model()->where('config_tab_id',$tab_id);
            $grid->model()->orderBy('order', 'DESC');

            $grid->model()->setConstraints([
                'tab_id' => $tab_id
            ]);

            $grid->disableViewButton();

//            $grid->enableDialogCreate();

            $grid->column('id')->sortable();
            $grid->column('info')->editable();
            $grid->column('menu_name')->editable();
            $grid->column('input_type');
            $grid->column('order')->editable();
//            $grid->column('value',trans('config.fields.list_value'));
            $grid->column('status')->switch();
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
            $grid->tools(new BackToConfigTab());

            $grid->actions([new CopyRecordAction('\Wakazunn\DASystemConfig\Models\Config')]);
        });
    }


    public function edit($id, Content $content)
    {
        $payInfo = (new Config())->model()->where('id',$id)->first();
        $type = $payInfo['type']??0;
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['edit'] ?? trans('admin.edit'))
            ->body($this->createFormRule($type)->edit($id));
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $tab = Tab::make();
        $tab->add('文本框',$this->createFormRule(0));
        $tab->add('多行文本框', $this->createFormRule(1));
        $tab->add('单选框', $this->createFormRule(2));
        $tab->add('文件上传', $this->createFormRule(3));
        $tab->add('多选框', $this->createFormRule(4));
        $tab->add('下拉框', $this->createFormRule(5));
        $tab->add('JSON', $this->createFormRule(6));

        return $tab->withCard();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        $submitType = \request()->post('type');
        return $this->createFormRule($submitType)->store();
    }

    public function update($id)
    {
        $payInfo = (new Config())->model()->where('id',$id)->first();
        return $this->createFormRule($payInfo['type'])->update($id);
    }

    /**
     * @param Form $form
     * @param int $type  0 文本框 1 多行文本框 2单选框 3 文本上传  4 多选框 5下拉列表 6 json
     * @param int $tab_id
     */
    public function createFormRule($type){
        return Form::make(new Config(),function (Form $form) use ($type){
            $form_type = '';
            $menuOptions = ConfigTab::selectOptions(null,DASystemConfigServiceProvider::trans('config.options.root_empty'));
            $form->select('config_tab_id',DASystemConfigServiceProvider::trans('config.fields.config_tab_id'))->options($menuOptions )->required();
            $form->text('info',DASystemConfigServiceProvider::trans('config.fields.info'))->required();
            $form->text('menu_name',DASystemConfigServiceProvider::trans('config.fields.menu_name'))->required();
            // if($form->isCreating()){

            // }else{
            //     $form->text('menu_name',DASystemConfigServiceProvider::trans('config.fields.menu_name'))->disable(true);
            // }

            if(is_string($type)){
                switch ($type){
                    case 'text';
                        $type = 0;
                        break;
                    case 'textarea';
                        $type = 1;
                        break;
                    case 'radio';
                    $type = 2;
                    break;
                    case 'upload';
                        $type = 3;
                        break;

                    case 'checkbox';
                        $type = 4;
                        break;

                    case 'select';
                        $type = 5;
                        break;
                    case 'json';
                        $type = 6;
                        break;
                }
            }


            switch ($type) {
                case 0://文本框
                    $form_type = 'text';
                    if($form->isCreating()){
                        $form->select('input_type', DASystemConfigServiceProvider::trans('config.fields.input_type'))->options($this->textType())->required();
                    }else{
                        $form->hidden('input_type');
                    }
                    $form->text('value',DASystemConfigServiceProvider::trans('config.fields.value'));
                    $form->number('width', DASystemConfigServiceProvider::trans('config.fields.input_w'))->default(100);
                    $form->text('required',DASystemConfigServiceProvider::trans('config.fields.required'))->placeholder('多个请用,隔开例如：required:true,url:true');
                    break;
                case 1://多行文本框
                    $form_type = 'textarea';
                    $form->textarea('value',  DASystemConfigServiceProvider::trans('config.fields.value'));
                    $form->number('width', DASystemConfigServiceProvider::trans('config.fields.input_w'))->default(100);
                    $form->number('high', DASystemConfigServiceProvider::trans('config.fields.textarea_h'))->default(5);
                    break;
                case 2://单选框
                    $form_type = 'radio';
                    $form->textarea('parameter',DASystemConfigServiceProvider::trans('config.fields.parameter'))->placeholder("参数方式例如:\n1=>男\n2=>女\n3=>保密");
                    $form->text('value',DASystemConfigServiceProvider::trans('config.fields.value'));
                    break;
                case 3://文件上传
                    $form_type = 'upload';
                    $form->radio('upload_type',DASystemConfigServiceProvider::trans('config.fields.upload_type'))->options($this->uploadType())->default(1);
                    break;
                case 4://多选框
                    $form_type = 'checkbox';
                    $form->textarea('parameter',DASystemConfigServiceProvider::trans('config.fields.parameter'))->placeholder("参数方式例如:\n1=>白色\n2=>红色\n3=>黑色");
                    $form->text('value',DASystemConfigServiceProvider::trans('config.fields.value'));
                    break;
                case 5://下拉框
                    $form_type = 'select';
                    $form->textarea('parameter',DASystemConfigServiceProvider::trans('config.fields.parameter'))->placeholder("参数方式例如:\n1=>白色\n2=>红色\n3=>黑色");
                    $form->text('value',DASystemConfigServiceProvider::trans('config.fields.value'));
                    break;
                case 6://下拉框
                    $form_type = 'json';
                    $form->select('input_type', DASystemConfigServiceProvider::trans('config.fields.input_type'))->options($this->jsonType())->required();
                    $form->textarea('parameter',DASystemConfigServiceProvider::trans('config.fields.parameter'))->placeholder("参数方式例如:\n1=>白色\n2=>红色\n3=>黑色");
                    $form->text('value',DASystemConfigServiceProvider::trans('config.fields.value'));
                break;
                case 7://分割线
                    $form_type = 'json';
                    $form->select('input_type', DASystemConfigServiceProvider::trans('config.fields.input_type'))->options($this->jsonType())->default(7)->required();
                    $form->text('value',DASystemConfigServiceProvider::trans('config.fields.value'))->default(1);
                break;
            }


            $form->text('desc',DASystemConfigServiceProvider::trans('config.fields.desc'));
            $form->text('order',DASystemConfigServiceProvider::trans('config.fields.order'))->default(0);
            $form->radio('status',DASystemConfigServiceProvider::trans('config.fields.status'))->options($this->formStatus())->default(1);
            $form->hidden('type')->value($form_type);
//            $form->hidden('submit_type',$type);

            $form->footer(function (Form\Footer $footer){
                $footer->disableCreatingCheck();
                $footer->disableEditingCheck();
                $footer->disableViewCheck();
            });

            $form->tools(function (Form\Tools $tools) use ($form){
                // 去掉跳转列表按钮
                $tools->disableList();
                // 去掉跳转详情页按钮
                $tools->disableView();

                if($form->isEditing()){
                    $tabId = $form->model()->get('config_tab_id');

                    $url =  $form->resource().'?tab_id='.$tabId[0]['config_tab_id'];
//                    $url = '';
                    $tools->prepend('<a class="btn btn-sm btn-primary" href="'.$url.'" style="margin-right: 5px;"><i class="feather icon-list"></i>&nbsp;&nbsp;列表</a>');
//                $tools->append(new ConfigTabListBack());
                }

            });

            $form->saving(function (Form $form){
//                 $form->deleteInput('submit_type');
            });
        });
    }

    /**
     * 选择文本框类型
     * @return array
     */
    public function textType()
    {
        return [
            'input' => DASystemConfigServiceProvider::trans('config.text_type.input'),
            'dateTime' => DASystemConfigServiceProvider::trans('config.text_type.datetime'),
            'color' => DASystemConfigServiceProvider::trans('config.text_type.color'),
            'number' => DASystemConfigServiceProvider::trans('config.text_type.number'),
            'password' => DASystemConfigServiceProvider::trans('config.text_type.password'),
            'switch' => DASystemConfigServiceProvider::trans('config.text_type.switch'),
            'rate'  => DASystemConfigServiceProvider::trans('config.text_type.rate'),
            'divider'  => DASystemConfigServiceProvider::trans('config.text_type.divider'),
        ];
    }

    public function jsonType(){
        return [
            'keyValue' => DASystemConfigServiceProvider::trans('config.json_type.keyValue'),
            'list' => DASystemConfigServiceProvider::trans('config.json_type.list'),
            'table'=> DASystemConfigServiceProvider::trans('config.json_type.table'),
            'embeds'=> DASystemConfigServiceProvider::trans('config.json_type.embeds'),
            
        ];
    }

    /**
     * 选择文文件类型
     * @return array
     */
    public function uploadType()
    {
        return [
            1 => DASystemConfigServiceProvider::trans('config.upload_type.single'),
            2 => DASystemConfigServiceProvider::trans('config.upload_type.multi'),
            3 => DASystemConfigServiceProvider::trans('config.upload_type.file'),
        ];
    }

    /**
     * 字段状态
     * @return array
     */
    public function formStatus()
    {
        return [
            1 => DASystemConfigServiceProvider::trans('config.status.show'),
            0 => DASystemConfigServiceProvider::trans('config.status.hide'),
        ];
    }


    public function configs(Request $request,Content $content){
        $type = $request->get('type',0);
        $pid= $request->get('pid',0);
        // 获取1级菜单
        $list = (new \Wakazunn\DASystemConfig\Repositories\ConfigTab())->getConfigTab($pid);
    //    dump($list);
        $tab = Tab::make();
        foreach ($list as $k1=>$v1){
            if(!empty($v1['children'])){
                $tab->add($v1['label'],$this->subTab($v1['children']));
            }else{
                $tab->add($v1['label'],$this->subTabForm($v1['id']));
            }

        }
        //获取2级菜单
        return $content->translation($this->translation())->title(DASystemConfigServiceProvider::trans('config.labels.basic'))->body($tab);
    }

    public function subTab($children){
        $tab = Tab::make();

        foreach ($children as $k1=>$v1){
            // print_r($v1);
//            echo $v1['label'];
//            echo $v1['id'];
            $tab->add($v1['label'],$this->subTabForm($v1['id']));
        }
        return $tab;
    }

    public function subTabForm($tabId){
//        dump($tabId);
        $listField = (new Config())->getConfigFieldAll($tabId);
        return Form::make(new Config(),function (Form $form) use ($listField){
            $form->disableViewCheck(true);
            $form->disableEditingCheck(true);
            $form->disableCreatingCheck(true);
            $form->disableListButton(true);
            $form->disableHeader(true);

            event(new SubTabForm($listField));

            foreach ($listField as $data){
                $required = $data['required'];
                $tempField = null;
                switch ($data['type']) {
                    case 'text'://文本框
                        switch ($data['input_type']) {
                            case 'input':
                                $data['value'] = json_decode($data['value'], true);
                                if($data['value'] == null){
                                    $data['value'] = '';
                                }
                                $form->text($data['menu_name'],$data['info'])->rules($required)->value($data['value'])->placeholder($data['desc']);
                            break;
                            case 'number':
                                $data['value'] = json_decode($data['value'], true) ?: 0;
                                if($data['desc']){
                                    $form->number($data['menu_name'], $data['info'])->rules($required)->value($data['value'])->help($data['desc']);
                                }else{
                                    $form->number($data['menu_name'], $data['info'])->rules($required)->value($data['value']);
                                }
                                
                                break;
                            case 'dateTime':
                                $form->datetime($data['menu_name'], $data['info'])->value($data['value']);
                                break;
                            case 'color':
                                $data['value'] = json_decode($data['value'], true) ?: '';
                                $form->color($data['menu_name'], $data['info'])->value($data['value']);
                                break;
                            case 'password':
                                $data['value'] = json_decode($data['value'], true) ?: '';
                                $form->password($data['menu_name'], $data['info'])->value($data['value']);
                            break;
                            case 'switch':
                                $data['value'] = json_decode($data['value'], true) ?: '';
                                $form->switch($data['menu_name'], $data['info'])->value($data['value']);
                            break;
                            case 'rate':
                                $data['value'] = json_decode($data['value'], true) ?: '';
                                $form->rate($data['menu_name'], $data['info'])->value($data['value']);
                            break;
                            case 'divider':
                                $form->divider();
                            break;
                            default:
                                $data['value'] = json_decode($data['value'], true) ?: '';
                                $form->text($data['menu_name'],$data['info'])->value($data['value'])->placeholder($data['desc']);
                                break;
                        }
                    break;

                    case 'textarea'://多行文本框
                        $data['value'] = json_decode($data['value'], true) ?: '';
                        $form->textarea($data['menu_name'], $data['info'])->value($data['value'])->rows(6)->addVariables(['cols'=>13])->placeholder($data['desc']);
                        break;
                    case 'radio'://单选框
                        $data['value'] = json_decode($data['value'], true) ?: '0';
                        $parameter = explode("\r\n", $data['parameter']);
                        $options = [];
                        $parameter = array_filter($parameter);
                        if ($parameter) {
                            foreach ($parameter as $v) {
                                $pdata = explode("=>", $v);
                                $options[$pdata[0]] = $pdata[1];
                            }
                            $form->radio($data['menu_name'], $data['info'])->options($options)->value($data['value']);
                        }
                        break;
                    case 'checkbox'://多选框
                        $data['value'] = json_decode($data['value'], true) ?: [];
                        $parameter = explode("\r\n", $data['parameter']);
                        $parameter = array_filter($parameter);
                        $options = [];
                        if ($parameter) {
                            foreach ($parameter as $v) {
                                $pdata = explode("=>", $v);
                                $options[$pdata[0]] = $pdata[1];
                            }
                            $form->checkbox($data['menu_name'], $data['info'])->options($options)->value($data['value']);
                        }
                        break;
                    case 'select'://多选框
                        $data['value'] = json_decode($data['value'], true) ?: '';
                        $parameter = explode("\r\n", $data['parameter']);
                        $parameter = array_filter($parameter);
                        $options = [];
                        if ($parameter) {
                            foreach ($parameter as $v) {
                                $pdata = explode("=>", $v);
                                $options[$pdata[0]] = $pdata[1];
                            }
                            $form->select($data['menu_name'], $data['info'])->options($options)->value($data['value']);
                        }
                        break;
                    case 'upload'://文件上传
                        switch ($data['upload_type']) {
                            case 1:
                                //上传一张图片
                                $data['value'] = json_decode($data['value'], true) ?: '';
                                $form->image($data['menu_name'], $data['info'])->autoUpload()->url(admin_url('/upload'))->value($data['value']);
                                break;
                            case 2:
                                //上传多张图片
                                $data['value'] = json_decode($data['value'], true) ?: [];
                                $form->multipleImage($data['menu_name'], $data['info'])->autoUpload()->url(admin_url('/upload'))->value($data['value']);
                                break;
                            case 3:
                                $data['value'] = json_decode($data['value'], true);
                                $form->file($data['menu_name'], $data['info'])->autoUpload()->url(admin_url('/upload'))->value($data['value']);
                                break;
                        }

                        break;
                    case 'json':
                        switch($data['input_type']){
                            case 'list':
                                $form->list($data['menu_name'], $data['info']);
                                break;
                            case 'table':

                                $parameter = explode("\r\n", $data['parameter']);
                                $parameter = array_filter($parameter);
                                $options = [];
                                if ($parameter) {
                                    foreach ($parameter as $v) {
                                        $pdata = explode("=>", $v);
                                        $options[$pdata[0]] = $pdata[1];
                                    }
                                }

                                $form->table($data['menu_name'], $data['info'], function ($table)  use($options) {
                                    // $table->text('key');
                                    // $table->text('value');
                                    // $table->text('desc');
                                    foreach($options as $k=>$v){
                                        $table->text($k)->required();
                                    }
                                })->help($data['desc']);
                                
                            

                                break;
                        }
                    break;
                }
            }

            $form->action(admin_url('/wakazunn/config/save_basics'));

        });
    }


    public function destroy($id)
    {
        $payInfo = (new Config())->model()->where('id',$id)->first();
        return $this->createFormRule($payInfo['type'])->destroy($id);
    }

    /**
     * 保存数据    true
     * */
    public function save_basics()
    {
        $post = \request()->post();

         //保存数据的事件
         event(new ConfigSaved($post));

        /** @var Config  $ConfigRes*/
        $ConfigRes = app()->make(Config::class);

        foreach ($post as $k => $v) {
            if (is_array($v)) {
                $res = $ConfigRes->model()->where('menu_name', $k)->pluck('upload_type', 'type');
                foreach ($res as $kk => $vv) {
                    if ($kk == 'upload') {
                        if ($vv == 1 || $vv == 3) {
                            $post[$k] = $v[0];
                        }
                    }
                }
            }
        }
        foreach ($post as $k => $v) {
            $exist = $ConfigRes->model()->where('menu_name',$k)->count();
            if($exist){
                $ConfigRes->model()->where('menu_name',$k)->update([
                    'value' => json_encode($v)
                ]);
                WakaSystemConfig::saveConfig($k,$v);
            }

        }

       

        return \Dcat\Admin\Http\JsonResponse::make()->success('成功！');

    }

}
