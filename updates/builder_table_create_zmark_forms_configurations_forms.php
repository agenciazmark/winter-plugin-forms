<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableCreateZmarkFormsConfigurationsForms extends Migration
{
    public function up()
    {
        Schema::create('zmark_forms_configurations_forms', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('configuration_id');
            $table->integer('form_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('zmark_forms_configurations_forms');
    }
}