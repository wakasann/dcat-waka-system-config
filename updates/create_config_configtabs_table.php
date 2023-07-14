<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigConfigtabsTable extends Migration
{
    public function getConnection()
    {
        return config('database.connection') ?: config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('waka_system_config_tab')){
            Schema::create('waka_system_config_tab', function (Blueprint $table) {
                $table->bigIncrements('id')->unsigned()->comment('配置分类id');
                $table->bigInteger('parent_id')->default(0)->comment('上级分类id');
                $table->integer('order')->default(0)->comment('排序');
                $table->string('title', 50)->comment('配置分类标题');
                $table->string('eng_title', 50)->nullable()->comment('配置分类标题');
                $table->string('icon')->nullable();
                $table->tinyInteger('info')->default(0)->comment('配置分类是否显示');
                $table->tinyInteger('status')->default(1)->comment('配置分类状态 1 显示 0 隐藏');
                $table->integer('type')->default(0)->comment('配置类型 0 系统 1 应用 2 支付 3 其它');
                $table->timestamps();
    //            $table->comment = 'Paypal config tab';
            });
    
            Schema::create('waka_system_config', function (Blueprint $table) {
                $table->bigIncrements('id')->unsigned()->comment('配置id');
                $table->string('menu_name')->comment('字段名称');
                $table->string('type')->comment('类型(文本框,单选按钮...)');
                $table->string('input_type')->default('input')->comment('表单类型');
                $table->bigInteger('config_tab_id')->unsigned()->comment('配置分类id');
                $table->string('parameter')->nullable()->comment('规则 单选框和多选框');
                $table->tinyInteger('upload_type')->nullable()->unsigned()->comment('上传文件格式1单图2多图3文件');
                $table->string('required')->nullable()->comment('规则');
                $table->integer('width')->unsigned()->nullable()->comment('多行文本框的宽度');
                $table->integer('high')->unsigned()->nullable()->comment('多行文框的高度');
                $table->string('value',5000)->nullable()->comment('默认值');
                $table->string('info')->default('')->comment('配置名称');
                $table->string('desc')->nullable()->comment('配置简介');
                $table->integer('order')->default(0)->comment('排序');
                $table->tinyInteger('status')->unsigned()->default(0)->comment('是否隐藏 1是 0否');
                $table->timestamps();
    //            $table->comment = 'Paypal config';
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waka_system_config_tab');
        Schema::dropIfExists('waka_system_config');
    }
}
