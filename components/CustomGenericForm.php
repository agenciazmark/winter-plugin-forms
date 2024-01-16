<?php

namespace Zmark\Forms\Components;

use Martin\Forms\Components\GenericForm;
use Cms\Classes\ComponentBase;
use Martin\Forms\Models\Record;
use Martin\Forms\Models\Settings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Martin\Forms\Classes\BackendHelpers;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Exception\AjaxException;
use Martin\Forms\Classes\FilePond\FilePond;
use Winter\Storm\Support\Facades\Validator;
use Martin\Forms\Classes\Mails\AutoResponse;
use Zmark\Forms\Classes\Notification;
use Winter\Storm\Exception\ValidationException;

use Zmark\Forms\Models\Configuration;

class CustomGenericForm extends GenericForm
{
    use \Martin\Forms\Classes\ReCaptcha;
    use \Martin\Forms\Classes\SharedProperties;

    public function onFormSubmit()
    {
        // FLASH PARTIAL
        $flash_partial = $this->property('messages_partial', '@flash.htm');

        // CSRF CHECK
        if (Config::get('cms.enableCsrfProtection') && (Session::token() != post('_token'))) {
            throw new AjaxException(['#' . $this->alias . '_forms_flash' => $this->renderPartial($flash_partial, [
                'status'  => 'error',
                'type'    => 'danger',
                'content' => Lang::get('martin.forms::lang.components.shared.csrf_error'),
            ])]);
        }

        // LOAD TRANSLATOR PLUGIN
        if (BackendHelpers::isTranslatePlugin()) {
            $translator = \Winter\Translate\Classes\Translator::instance();
            $translator->loadLocaleFromSession();
            $locale = $translator->getLocale();
            \Winter\Translate\Models\Message::setContext($locale);
        }

        // FILTER ALLOWED FIELDS
        $allow = $this->property('allowed_fields');
        if (is_array($allow) && !empty($allow)) {
            foreach ($allow as $field) {
                $post[$field] = post($field);
            }
            if ($this->isReCaptchaEnabled()) {
                $post['g-recaptcha-response'] = post('g-recaptcha-response');
            }
        } else {
            $post = post();
        }

        // VERIFICA QUAL O FORMULÁRIO QUE ESTÁ ENVIANDO
        if($post['form_id']) {
            $formId = $post['form_id'];
        } else {
            $formId = null;
        }

        // SANITIZE FORM DATA
        if ($this->property('sanitize_data') == 'htmlspecialchars') {
            $post = $this->array_map_recursive(function ($value) {
                return htmlspecialchars($value, ENT_QUOTES);
            }, $post);
        }

        // VALIDATION PARAMETERS
        $rules = (array)$this->property('rules');
        $msgs  = (array)$this->property('rules_messages');
        $custom_attributes = (array)$this->property('custom_attributes');

        // TRANSLATE CUSTOM ERROR MESSAGES
        if (BackendHelpers::isTranslatePlugin()) {
            foreach ($msgs as $rule => $msg) {
                $msgs[$rule] = \Winter\Translate\Models\Message::trans($msg);
            }
        }

        // ADD reCAPTCHA VALIDATION
        if ($this->isReCaptchaEnabled() && $this->property('recaptcha_size') != 'invisible') {
            $rules['g-recaptcha-response'] = 'required';
        }

        // DO FORM VALIDATION
        $validator = Validator::make($post, $rules, $msgs, $custom_attributes);

        // NICE reCAPTCHA FIELD NAME
        if ($this->isReCaptchaEnabled()) {
            $fields_names = ['g-recaptcha-response' => 'reCAPTCHA'];
            $validator->setAttributeNames(array_merge($fields_names, $custom_attributes));
        }

        // VALIDATE ALL + CAPTCHA EXISTS
        if ($validator->fails()) {

            // GET DEFAULT ERROR MESSAGE
            $message = $this->property('messages_errors');

            // LOOK FOR TRANSLATION
            if (BackendHelpers::isTranslatePlugin()) {
                $message = \Winter\Translate\Models\Message::trans($message);
            }

            // THROW ERRORS
            if ($this->property('inline_errors') == 'display') {
                throw new ValidationException($validator);
            } else {
                throw new AjaxException($this->exceptionResponse($validator, [
                    'status'  => 'error',
                    'type'    => 'danger',
                    'title'   => $message,
                    'list'    => $validator->messages()->all(),
                    'errors'  => json_encode($validator->messages()->messages()),
                    'jscript' => $this->property('js_on_error'),
                ]));
            }
        }

        // IF FIRST VALIDATION IS OK, VALIDATE CAPTCHA vs GOOGLE
        // (this prevents to resolve captcha after every form error)
        if ($this->isReCaptchaEnabled()) {

            // PREPARE RECAPTCHA VALIDATION
            $rules   = ['g-recaptcha-response'           => 'recaptcha'];
            $err_msg = ['g-recaptcha-response.recaptcha' => Lang::get('martin.forms::lang.validation.recaptcha_error')];

            // DO SECOND VALIDATION
            $validator = Validator::make($post, $rules, $err_msg);

            // VALIDATE ALL + CAPTCHA EXISTS
            if ($validator->fails()) {

                // THROW ERRORS
                if ($this->property('inline_errors') == 'display') {
                    throw new ValidationException($validator);
                } else {
                    throw new AjaxException($this->exceptionResponse($validator, [
                        'status'  => 'error',
                        'type'    => 'danger',
                        'content' => Lang::get('martin.forms::lang.validation.recaptcha_error'),
                        'errors'  => json_encode($validator->messages()->messages()),
                        'jscript' => $this->property('js_on_error'),
                    ]));
                }
            }
        }

        // REMOVE EXTRA FIELDS FROM STORED DATA
        unset($post['_token'], $post['g-recaptcha-response'], $post['_session_key'], $post['files'], $post['registraAcesso'], $post['form_id']);

        // FIRE BEFORE SAVE EVENT
        Event::fire('martin.forms.beforeSaveRecord', [&$post, $this]);

        if (count($custom_attributes)) {
            $post = collect($post)->mapWithKeys(function ($val, $key) use ($custom_attributes) {
                return [array_get($custom_attributes, $key, $key) => $val];
            })->all();
        }

        $record = new Record;
        $record->ip        = $this->getIP();
        $record->created_at = date('Y-m-d H:i:s');

        // SAVE RECORD TO DATABASE
        if (!$this->property('skip_database')) {
            $record->form_data = json_encode($post, JSON_UNESCAPED_UNICODE);
            if ($this->property('group') != '') {
                $record->group = $this->property('group');
            }

            // attach files
            $this->attachFiles($record);

            $record->save(null, post('_session_key'));
        }


        //dados personalizados para o envio
        $properties = $this->getProperties();        

        // pega a configuração pelo ID do form enviado pelo frontend. Caso não seja enviado ou não encontre uma configuração vinculada, pega a primeira ativa.
        if($formId) {
            $configuration = Configuration::where('status',true)->whereHas('forms', function ($q) use($formId){
                $q->where('form_id',$formId);
            })->first();
        } 
        if(!$configuration) {
            $configuration = Configuration::where('status',true)->first();
        }
        
        if($configuration) {
            //emails
            foreach ($configuration->recipients as $recipient) {
                $properties['mail_recipients'][] = $recipient['email'];
            }       
            // configs gerais
            $properties['mail_subject'] = $configuration->subject;
            $properties['messages_success'] = $configuration->success;
            $message = $configuration->success;
            $properties['mail_enabled'] = $configuration->send;
        }


        // SEND NOTIFICATION EMAIL
        if ($properties['mail_enabled']) {            
            $notification = App::makeWith(Notification::class, [
                $properties, $post, $record, $record->files
            ]);
            $notification->send();
        }

        // SEND AUTORESPONSE EMAIL
        if ($this->property('mail_resp_enabled')) {
            $autoresponse = App::makeWith(AutoResponse::class, [
                $properties, $post, $record
            ]);
            $autoresponse->send();
        }

        // FIRE AFTER SAVE EVENT
        Event::fire('martin.forms.afterSaveRecord', [&$post, $this, $record]);

        // CHECK FOR REDIRECT
        if ($this->property('redirect')) {
            return Redirect::to($properties['redirect']);
        }

        // GET DEFAULT SUCCESS MESSAGE
        if(!$message) {
            $message = $this->property('messages_success');
            
            // LOOK FOR TRANSLATION
            if (BackendHelpers::isTranslatePlugin()) {
                $message = \Winter\Translate\Models\Message::trans($message);
            }
        }

        // DISPLAY SUCCESS MESSAGE
        return ['#' . $this->alias . '_forms_flash' => $this->renderPartial($flash_partial, [
            'status'  => 'success',
            'type'    => 'success',
            'content' => $message,
            'jscript' => $this->prepareJavaScript(),
        ])];
    }

    private function getIP()
    {
        if ($this->property('anonymize_ip') == 'full') {
            return '(Not stored)';
        }

        $ip = Request::getClientIp();

        if ($this->property('anonymize_ip') == 'partial') {
            return BackendHelpers::anonymizeIPv4($ip);
        }

        return $ip;
    }

    private function attachFiles(Record $record)
    {
        $files = post('files', null);

        if (!$files) {
            return;
        }

        foreach ($files as $file) {
            $filepond = App::make(FilePond::class);
            $filePath = $filepond->getPathFromServerId($file);

            $record->files()->create([
                'data' => $filePath
            ], post('_session_key'));
        }
    }

    private function prepareJavaScript()
    {
        $code = false;

        /* SUCCESS JS */
        if ($this->property('js_on_success') != '') {
            $code .= $this->property('js_on_success');
        }

        /* RECAPTCHA JS */
        if ($this->isReCaptchaEnabled()) {
            $code .= $this->renderPartial('@js/recaptcha.htm');
        }

        /* RESET FORM JS */
        if ($this->property('reset_form')) {
            $params = ['id' => '#' . $this->alias . '_forms_flash'];
            $code .= $this->renderPartial('@js/reset-form.htm', $params);
        }

        return $code;
    }
}
