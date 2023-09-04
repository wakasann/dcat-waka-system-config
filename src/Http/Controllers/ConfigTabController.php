<?php

namespace Wakazunn\DASystemConfig\Http\Controllers;


use Wakazunn\DASystemConfig\Repositories\ConfigTab;
use Wakazunn\DASystemConfig\Actions\Tree\{ConfigTabShow,LinkToPayConfig};
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Http\Controllers\AdminController;

use Dcat\Admin\Http\Actions\Menu\Show;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Tree;
use Dcat\Admin\Widgets\Box;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Wakazunn\DASystemConfig\Models\ConfigTab as ModelsPayConfigTab;

use Wakazunn\DASystemConfig\DASystemConfigServiceProvider;

class ConfigTabController extends AdminController
{

    const tabTypes = [
        0 => '系统',
        1 => '应用',
        2 => '支付',
        3 => '其它',
        4 => '配置组',
    ];

    public function index(Content $content)
    {
        return $content
            ->title(DASystemConfigServiceProvider::trans('config-tab.labels.configTab'))
            ->description(DASystemConfigServiceProvider::trans('config-tab.list'))
            ->body(function (Row $row) {
                $row->column(7, $this->treeView()->render());


                $row->column(5, function (Column $column) {
                    $form = new WidgetForm();
                    $form->action(admin_url('wakazunn/config/tabs'));



                    $form->select('parent_id', trans('admin.parent_id'))->options(ModelsPayConfigTab::selectOptions());
                    $form->text('title', trans('admin.title'))->required();
                    $form->text('eng_title', DASystemConfigServiceProvider::trans('config-tab.fields.eng_title'));
                    $form->icon('icon', trans('admin.icon'))->help($this->iconHelp());

                    $form->radio('type',DASystemConfigServiceProvider::trans('config-tab.fields.type'))
                    ->options(self::tabTypes)
                    ->default(0)->help('配置组会应用英文标题为配置前缀');

                    $form->radio('status',DASystemConfigServiceProvider::trans('config-tab.fields.status'))->options([
                        1 => '显示',
                        0 => '隐藏',
                    ])->default(1);

                    $form->number('order')->default(0);

                    $form->width(9, 2);

                    $column->append(Box::make(trans('admin.new'), $form));
                });

            });
    }

    /**
     * @return \Dcat\Admin\Tree
     */
    protected function treeView()
    {


        return new Tree(new ModelsPayConfigTab(), function (Tree $tree) {
            $tree->disableCreateButton();
            $tree->disableQuickCreateButton();
            $tree->disableEditButton();
            $tree->maxDepth(3);

            $tree->actions(function (Tree\Actions $actions) {
                $actions->prepend(new LinkToPayConfig());
                $actions->prepend(new ConfigTabShow());
            });

            $tree->branch(function ($branch) {
                $topEngTitle = $branch['eng_title']?$branch['eng_title']:'';
                $topTitle = $branch['title'];
                if(!empty($topEngTitle)){
                    $topTitle .= '('.$topEngTitle.')';
                }
                $payload = "<i class='fa {$branch['icon']}'></i>&nbsp;<strong>{$topTitle}</strong>";

                return $payload;
            });
        });
    }

    /**
     * Help message for icon field.
     *
     * @return string
     */
    protected function iconHelp()
    {
        return 'For more icons please see <a href="http://fontawesome.io/icons/" target="_blank">http://fontawesome.io/icons/</a>';
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ConfigTab(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('parent_id');
            $grid->column('order');
            $grid->column('title');
            $grid->column('eng_title');
            $grid->column('icon');
            $grid->column('info');
            $grid->column('status');
            $grid->column('type');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new ConfigTab(), function (Show $show) {
            $show->field('id');
            $show->field('parent_id');
            $show->field('order');
            $show->field('title');
            $show->field('eng_title');
            $show->field('icon');
            $show->field('info');
            $show->field('status');
            $show->field('type');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new ConfigTab(), function (Form $form) {
            $form->display('id');

            $form->select('parent_id', trans('admin.parent_id'))->options(ModelsPayConfigTab::selectOptions());

            $form->text('title', trans('admin.title'))->required();
            $form->text('eng_title', DASystemConfigServiceProvider::trans('config-tab.fields.eng_title'));
            $form->icon('icon', trans('admin.icon'))->help($this->iconHelp());

            $form->radio('type',DASystemConfigServiceProvider::trans('config-tab.fields.type'))->options(self::tabTypes)->default(0)->help('配置组会应用英文标题为配置前缀');

            $form->radio('status',DASystemConfigServiceProvider::trans('config-tab.fields.status'))->options([
                1 => '显示',
                0 => '隐藏',
            ])->default(1);

            $form->number('order')->default(0);

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}