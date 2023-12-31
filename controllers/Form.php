<?php namespace Zmark\Forms\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Form extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'   ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Zmark.Forms', 'main-menu-forms', 'side-menu-forms');
    }
}
