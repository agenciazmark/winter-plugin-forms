<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateZmarkContentForms extends Migration
{
    public function up()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->dropColumn('name');
        });
    }
    
    public function down()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->string('name');
        });
    }
}
