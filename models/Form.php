<?php namespace Zmark\Forms\Models;

use Model;

/**
 * Model
 */
class Form extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'zmark_forms_forms';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
    
    /**
     * @var array Attribute names to encode and decode using JSON.
     */
    public $jsonable = [];

    public $belongsToMany = [
        'inputs' => [
            'Zmark\Forms\Models\Form',
            'table' => 'zmark_forms_forms_inputs',
            'key' => 'form_id',
            'otherKey' => 'input_id'
        ], 
    ];


}
