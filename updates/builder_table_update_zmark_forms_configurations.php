<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateZmarkFormsConfigurations extends Migration
{
    public function up()
    {
        Schema::table('zmark_forms_configurations', function($table)
        {
            $table->boolean('send')->default(1);
            $table->string('success')->default('Enviado com Sucesso');
        });
    }
    
    public function down()
    {
        Schema::table('zmark_forms_configurations', function($table)
        {
            $table->dropColumn('send');
            $table->dropColumn('success');
        });
    }
}
