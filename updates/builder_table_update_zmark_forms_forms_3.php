<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateZmarkContentForms3 extends Migration
{
    public function up()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->text('options')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->dropColumn('options');
        });
    }
}
