<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateZmarkContentForms6 extends Migration
{
    public function up()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->string('type_crm')->nullable();
            $table->dropColumn('name');
        });
    }
    
    public function down()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->dropColumn('type_crm');
            $table->text('name')->nullable();
        });
    }
}
