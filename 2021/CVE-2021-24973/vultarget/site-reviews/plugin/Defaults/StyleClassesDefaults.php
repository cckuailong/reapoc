<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class StyleClassesDefaults extends Defaults
{
    /**
     * @var string[]
     */
    public $concatenated = [
        'field',
        'form',
        'input',
        'input_checkbox',
        'input_radio',
        'label',
        'label_checkbox',
        'label_radio',
        'select',
        'textarea',
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
            'field' => 'glsr-field',
            'form' => 'glsr-form',
            'input' => '',
            'input_checkbox' => '',
            'input_radio' => '',
            'label' => 'glsr-label',
            'label_checkbox' => '',
            'label_radio' => '',
            'select' => '',
            'textarea' => '',
        ];
    }
}
