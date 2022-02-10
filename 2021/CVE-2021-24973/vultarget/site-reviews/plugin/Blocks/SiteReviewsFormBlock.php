<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Attributes;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode as Shortcode;

class SiteReviewsFormBlock extends Block
{
    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'assign_to' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_posts' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_terms' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_users' => [
                'default' => '',
                'type' => 'string',
            ],
            'category' => [
                'default' => '',
                'type' => 'string',
            ],
            'className' => [
                'default' => '',
                'type' => 'string',
            ],
            'hide' => [
                'default' => '',
                'type' => 'string',
            ],
            'id' => [
                'default' => '',
                'type' => 'string',
            ],
            'user' => [
                'default' => '',
                'type' => 'string',
            ],
        ];
    }

    /**
     * @return string
     */
    public function render(array $attributes)
    {
        $attributes['class'] = $attributes['className'];
        $shortcode = glsr(Shortcode::class);
        if ('edit' == filter_input(INPUT_GET, 'context')) {
            $this->filterFormFields();
            $this->filterSubmitButton();
            if (!$this->hasVisibleFields($shortcode, $attributes)) {
                $this->filterInterpolation();
            }
        }
        return $shortcode->buildBlock($attributes);
    }

    /**
     * @return void
     */
    protected function filterFormFields()
    {
        add_filter('site-reviews/review-form/fields', function (array $fields) {
            array_walk($fields, function (&$field) {
                $field['class'] = $this->formFieldClass(Arr::get($field, 'type'));
                $field['disabled'] = true;
                $field['tabindex'] = '-1';
            });
            return $fields;
        });
    }

    /**
     * @return void
     */
    protected function filterInterpolation()
    {
        add_filter('site-reviews/interpolate/reviews-form', function ($context) {
            $context['class'] = 'glsr-block-disabled';
            $context['fields'] = _x('You have hidden all of the fields for this block.', 'admin-text', 'site-reviews');
            $context['response'] = '';
            $context['submit_button'] = '';
            return $context;
        });
    }

    /**
     * @return void
     */
    protected function filterSubmitButton()
    {
        add_filter('site-reviews/rendered/template/form/submit-button', function ($template) {
            $template = str_replace('type="submit"', 'tabindex="-1"', $template);
            $template = str_replace('glsr-button button btn', 'components-button is-secondary', $template);
            return $template;
        });
    }

    /**
     * @return string
     */
    protected function formFieldClass($type)
    {
        if (in_array($type, ['checkbox', 'radio', 'select', 'textarea'])) {
            return sprintf('components-%s-control__input', $type);
        }
        if (in_array($type, Attributes::INPUT_TYPES)) {
            return 'components-text-control__input';
        }
        return '';
    }
}
