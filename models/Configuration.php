<?php namespace Zmark\Forms\Models;

use Model;

/**
 * Model
 */
class Configuration extends Model
{
    use \Winter\Storm\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'zmark_forms_configurations';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
    
    /**
     * @var array Attribute names to encode and decode using JSON.
     */
    public $jsonable = ['recipients'];
}
