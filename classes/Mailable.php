<?php

namespace Zmark\Forms\Classes;

interface Mailable
{
    /**
     * Returns email template to use
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * Parse custom subject and modify using form variables and custom settings
     *
     * @return void
     */
    public function prepareCustomSubject();
}
