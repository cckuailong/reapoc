<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class StyleValidationDefaults extends Defaults
{
    /**
     * @var string[]
     */
    public $concatenated = [
        'field_error',
        'field_message',
        'field_required',
        'field_valid',
        'form_error',
        'form_message',
        'form_message_failed',
        'form_message_success',
        'input_error',
        'input_valid',
    ];

    /**
     * @var string
     */
    protected $glue = ' ';

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'field_error' => 'glsr-field-is-invalid',
            'field_message' => 'glsr-field-error',
            'field_required' => 'glsr-required',
            'field_valid' => 'glsr-field-is-valid',
            'form_error' => 'glsr-form-is-invalid',
            'form_message' => 'glsr-form-message',
            'form_message_failed' => 'glsr-form-failed',
            'form_message_success' => 'glsr-form-success',
            'input_error' => 'glsr-is-invalid',
            'input_valid' => 'glsr-is-valid',
        ];
    }
}
