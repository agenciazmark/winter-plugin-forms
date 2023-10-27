<?php namespace Zmark\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateZmarkFormsInputs extends Migration
{
    public function up()
    {
        Schema::rename('zmark_forms_forms', 'zmark_forms_inputs');
    }
    
    public function down()
    {
        Schema::rename('zmark_forms_inputs', 'zmark_forms_forms');
    }
}
