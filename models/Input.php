<?php namespace Zmark\Forms\Models;

use Model;

/**
 * Model
 */
class Input extends Model
{
    use \Winter\Storm\Database\Traits\Validation;
    use \Winter\Storm\Database\Traits\Sortable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'zmark_forms_inputs';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
    
    /**
     * @var array Attribute names to encode and decode using JSON.
     */
    public $jsonable = ['options'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeBanner($query)
    {
        return $query->where('status', true)->whereHas('forms', function($q){
            $q->where('form_id', '1');
        });
    }

    public $belongsToMany = [
        'forms' => [
            'Zmark\Forms\Models\Form',
            'table' => 'zmark_forms_forms_inputs',
            'key' => 'input_id',
            'otherKey' => 'form_id'
        ], 
    ];

    public function beforeSave()
    {
        // garante que a slug está no formato correto e é única
        if($this->slug == null)
        {
            $this->slug = \Str::slug( $this->title );
            $record = Form::where('id','!=',$this->id)->where('form',$this->title)->where('slug',$this->slug)->first();
            if($record)
            {
                $this->slug = $this->slug.'-'.$this->id;
            }
        }
        else
        {
            $this->slug = \Str::slug( $this->slug);
            $record = Form::where('id','!=',$this->id)->where('form',$this->title)->where('slug',$this->slug)->first();
            if($record)
            {
                //verifica se é um cadastro novo ou antigo, se for novo (não tem ainda um id) pega o ultimo cadastro e soma 1.
                if($this->id) {
                    $id = $this->id;
                } else {
                    $ultimo = Form::orderBy('id','DESC')->first();
                    $id = $ultimo->id + 1;
                }
                $this->slug = $this->slug.'-'.$id;
            }
        }
    }

}
