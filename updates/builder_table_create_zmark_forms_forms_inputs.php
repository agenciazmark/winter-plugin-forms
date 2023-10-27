<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableCreateZmarkFormsFormsInputs extends Migration
{
    public function up()
    {
        Schema::create('zmark_forms_forms_inputs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('form_id');
            $table->integer('input_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('zmark_forms_forms_inputs');
    }
}
