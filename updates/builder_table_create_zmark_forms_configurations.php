<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableCreateZmarkFormsConfigurations extends Migration
{
    public function up()
    {
        Schema::create('zmark_forms_configurations', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->boolean('status');
            $table->text('recipients');
            $table->string('subject');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('zmark_forms_configurations');
    }
}