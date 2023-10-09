<?php namespace Zmark\Forms;

use Backend;
use Backend\Models\UserRole;
use System\Classes\PluginBase;

/**
 * Forms Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register(): void
    {

    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot(): void
    {

    }

    /**
     * Registers any frontend components implemented in this plugin.
     */
    public function registerComponents(): array
    {
        return [
            'Zmark\Forms\Components\CustomGenericForm' => 'customGenericForm'
        ];
    }

}
