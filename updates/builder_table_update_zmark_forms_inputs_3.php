<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateZmarkFormsInputs3 extends Migration
{
    public function up()
    {
        Schema::table('zmark_forms_inputs', function($table)
        {
            $table->integer('form_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('zmark_forms_inputs', function($table)
        {
            $table->dropColumn('form_id');
        });
    }
}
