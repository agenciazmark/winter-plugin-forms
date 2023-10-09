<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateZmarkContentForms4 extends Migration
{
    public function up()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->boolean('status_crm')->nullable()->default(1);
            $table->text('name')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->dropColumn('status_crm');
            $table->dropColumn('name');
        });
    }
}
