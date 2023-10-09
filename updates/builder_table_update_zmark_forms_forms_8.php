<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateZmarkFormsForms8 extends Migration
{
    public function up()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->string('label', 255)->nullable();
            $table->text('width')->nullable();
            $table->text('height')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('zmark_forms_forms', function($table)
        {
            $table->dropColumn('label');
            $table->dropColumn('width');
            $table->dropColumn('height');
        });
    }
}
