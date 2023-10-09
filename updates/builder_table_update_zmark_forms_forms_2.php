<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateZmarkContentForms2 extends Migration
{
    public function up()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->string('slug', 255)->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->string('slug', 255)->nullable(false)->change();
        });
    }
}
