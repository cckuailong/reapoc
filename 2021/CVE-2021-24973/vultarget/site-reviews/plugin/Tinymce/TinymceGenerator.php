<?php

namespace GeminiLabs\SiteReviews\Tinymce;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

abstract class TinymceGenerator
{
    /**
     * @var array
     */
    public $properties;

    /**
     * @var string
     */
    public $tag;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $required = [];

    /**
     * @return array
     */
    abstract public function fields();

    /**
     * @param string $tag
     * @return static
     */
    public function register($tag, array $args)
    {
        $this->tag = $tag;
        $this->properties = wp_parse_args($args, [
            'btn_close' => _x('Close', 'admin-text', 'site-reviews'),
            'btn_okay' => _x('Insert Shortcode', 'admin-text', 'site-reviews'),
            'errors' => $this->errors,
            'fields' => $this->getFields(),
            'label' => '['.$tag.']',
            'required' => $this->required,
            'title' => _x('Shortcode', 'admin-text', 'site-reviews'),
        ]);
        return $this;
    }

    /**
     * @return array
     */
    protected function generateFields(array $fields)
    {
        $generatedFields = array_map(function ($field) {
            if (empty($field)) {
                return;
            }
            $field = $this->normalize($field);
            $method = Helper::buildMethodName($field['type'], 'normalize');
            if (!method_exists($this, $method)) {
                return;
            }
            return $this->$method($field);
        }, $fields);
        return array_values(array_filter($generatedFields));
    }

    /**
     * @param string $tooltip
     * @return array
     */
    protected function getCategories($tooltip = '')
    {
        $terms = glsr(Database::class)->terms();
        if (empty($terms)) {
            return [];
        }
        return [
            'label' => _x('Category', 'admin-text', 'site-reviews'),
            'name' => 'assigned_terms',
            'options' => $terms,
            'tooltip' => $tooltip,
            'type' => 'listbox',
        ];
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        $fields = $this->generateFields($this->fields());
        if (!empty($this->errors)) {
            $errors = [];
            foreach ($this->required as $name => $alert) {
                if (false === Arr::searchByKey($name, $fields, 'name')) {
                    $errors[] = $this->errors[$name];
                }
            }
            $this->errors = $errors;
        }
        return empty($this->errors)
            ? $fields
            : $this->errors;
    }

    /**
     * @return array
     */
    protected function getHideOptions()
    {
        $classname = str_replace('Tinymce\\', 'Shortcodes\\', get_class($this));
        $classname = str_replace('Tinymce', 'Shortcode', $classname);
        $hideOptions = glsr($classname)->getHideOptions();
        $options = [];
        foreach ($hideOptions as $name => $tooltip) {
            $options[] = [
                'name' => 'hide_'.$name,
                'text' => $name,
                'tooltip' => $tooltip,
                'type' => 'checkbox',
            ];
        }
        return $options;
    }

    /**
     * @param string $tooltip
     * @return array
     */
    protected function getTypes($tooltip = '')
    {
        if (count($reviewTypes = glsr()->retrieveAs('array', 'review_types')) < 2) {
            return [];
        }
        return [
            'label' => _x('Type', 'admin-text', 'site-reviews'),
            'name' => 'type',
            'options' => $reviewTypes,
            'tooltip' => $tooltip,
            'type' => 'listbox',
        ];
    }

    /**
     * @return array
     */
    protected function normalize(array $field)
    {
        return wp_parse_args($field, [
            'items' => [],
            'type' => '',
        ]);
    }

    /**
     * @return void|array
     */
    protected function normalizeCheckbox(array $field)
    {
        return $this->normalizeField($field, [
            'checked' => false,
            'label' => '',
            'minHeight' => '',
            'minWidth' => '',
            'name' => false,
            'text' => '',
            'tooltip' => '',
            'type' => '',
            'value' => '',
        ]);
    }

    /**
     * @return void|array
     */
    protected function normalizeContainer(array $field)
    {
        if (!array_key_exists('html', $field) && !array_key_exists('items', $field)) {
            return;
        }
        $field['items'] = $this->generateFields($field['items']);
        return $field;
    }

    /**
     * @return void|array
     */
    protected function normalizeField(array $field, array $defaults)
    {
        if ($this->validate($field)) {
            return array_filter(shortcode_atts($defaults, $field), function ($value) {
                return '' !== $value;
            });
        }
    }

    /**
     * @return void|array
     */
    protected function normalizeListbox(array $field)
    {
        $listbox = $this->normalizeField($field, [
            'label' => '',
            'minWidth' => '',
            'name' => false,
            'options' => [],
            'placeholder' => esc_attr_x('- Select -', 'admin-text', 'site-reviews'),
            'tooltip' => '',
            'type' => '',
            'value' => '',
        ]);
        if (!is_array($listbox)) {
            return;
        }
        if (!array_key_exists('', $listbox['options'])) {
            $listbox['options'] = Arr::prepend($listbox['options'], $listbox['placeholder'], '');
        }
        foreach ($listbox['options'] as $value => $text) {
            $listbox['values'][] = [
                'text' => $text,
                'value' => $value,
            ];
        }
        return $listbox;
    }

    /**
     * @return void|array
     */
    protected function normalizePost(array $field)
    {
        if (!is_array($field['query_args'])) {
            $field['query_args'] = [];
        }
        $posts = get_posts(wp_parse_args($field['query_args'], [
            'order' => 'ASC',
            'orderby' => 'title',
            'post_type' => 'post',
            'posts_per_page' => 30,
        ]));
        if (!empty($posts)) {
            $options = [];
            foreach ($posts as $post) {
                $options[$post->ID] = esc_html($post->post_title);
            }
            $field['options'] = $options;
            $field['type'] = 'listbox';
            return $this->normalizeListbox($field);
        }
        $this->validate($field);
    }

    /**
     * @return void|array
     */
    protected function normalizeTextbox(array $field)
    {
        return $this->normalizeField($field, [
            'hidden' => false,
            'label' => '',
            'maxLength' => '',
            'minHeight' => '',
            'minWidth' => '',
            'multiline' => false,
            'name' => false,
            'size' => '',
            'text' => '',
            'tooltip' => '',
            'type' => '',
            'value' => '',
        ]);
    }

    /**
     * @return bool
     */
    protected function validate(array $field)
    {
        $args = shortcode_atts([
            'label' => '',
            'name' => false,
            'required' => false,
        ], $field);
        if (!$args['name']) {
            return false;
        }
        return $this->validateErrors($args) && $this->validateRequired($args);
    }

    /**
     * @return bool
     */
    protected function validateErrors(array $args)
    {
        if (!isset($args['required']['error'])) {
            return true;
        }
        $this->errors[$args['name']] = $this->normalizeContainer([
            'html' => $args['required']['error'],
            'type' => 'container',
        ]);
        return false;
    }

    /**
     * @return bool
     */
    protected function validateRequired(array $args)
    {
        if (false == $args['required']) {
            return true;
        }
        $alert = _x('Some of the shortcode options are required.', 'admin-text', 'site-reviews');
        if (isset($args['required']['alert'])) {
            $alert = $args['required']['alert'];
        } elseif (!empty($args['label'])) {
            $alert = sprintf(
                _x('The "%s" option is required.', 'the option label (admin-text)', 'site-reviews'),
                str_replace(':', '', $args['label'])
            );
        }
        $this->required[$args['name']] = $alert;
        return false;
    }
}
