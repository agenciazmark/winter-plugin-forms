<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableCreateZmarkContentForms extends Migration
{
    public function up()
    {
        Schema::create('zmark_forms_forms', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title', 255);
            $table->boolean('status')->default(1);
            $table->string('type', 255);
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->boolean('required')->default(1);
            $table->integer('sort_order')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('zmark_forms_forms');
    }
}
