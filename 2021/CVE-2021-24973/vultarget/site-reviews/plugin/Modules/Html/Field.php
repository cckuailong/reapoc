<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\FieldDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Attributes;
use GeminiLabs\SiteReviews\Modules\Style;

/**
 * @property string $id
 */
class Field
{
    /**
     * @var array
     */
    public $field;

    public function __construct(array $field = [])
    {
        $this->field = wp_parse_args($field, [
            'errors' => false,
            'is_multi' => false,
            'is_raw' => false,
            'is_valid' => true,
            'path' => '',
            'raw_type' => '',
        ]);
        $this->normalize();
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->field)) {
            return $this->field[$key];
        }
    }

    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->field)) {
            $this->field[$key] = $value;
        }
    }

    public function __toString()
    {
        return (string) $this->build();
    }

    /**
     * @return void|string
     */
    public function build()
    {
        if (!$this->field['is_valid']) {
            return;
        }
        if ($this->field['is_raw']) {
            return $this->builder()->{$this->field['type']}($this->field);
        }
        if ($this->field['is_multi']) {
            return $this->buildMultiField();
        }
        return $this->buildField();
    }

    /**
     * @return Builder
     */
    public function builder()
    {
        return glsr(Builder::class);
    }

    /**
     * @return string
     */
    public function choiceType()
    {
        return Helper::ifTrue('toggle' === $this->field['raw_type'], 
            $this->field['raw_type'],
            $this->field['type']
        );
    }

    /**
     * @return string
     */
    public function fieldType()
    {
        $isChoice = in_array($this->field['raw_type'], ['checkbox', 'radio', 'toggle']);
        return Helper::ifTrue($isChoice, 'choice', $this->field['raw_type']);
    }

    /**
     * @param string $key
     * @return array
     */
    public function getBaseClasses($key)
    {
        return [
            glsr(Style::class)->classes($key),
            Str::suffix(glsr(Style::class)->defaultClasses($key), '-'.$this->fieldType()),
        ];
    }

    /**
     * @return string
     */
    public function getField()
    {
        if ('choice' === $this->fieldType()) {
            return $this->buildFieldChoiceOptions();
        }
        return $this->builder()->raw($this->field);
    }

    /**
     * @return string
     */
    public function getFieldClasses()
    {
        $classes = $this->getBaseClasses('field');
        if (!empty($this->field['errors'])) {
            $classes[] = glsr(Style::class)->validation('field_error');
        }
        if (!empty($this->field['required'])) {
            $classes[] = glsr(Style::class)->validation('field_required');
        }
        $classes = glsr()->filterArray('rendered/field/classes', $classes, $this->field);
        return implode(' ', $classes);
    }

    /**
     * @return void|string
     */
    public function getFieldErrors()
    {
        return glsr(Template::class)->build('templates/form/field-errors', [
            'context' => [
                'class' => glsr(Style::class)->validation('field_message'),
                'errors' => implode('<br>', Cast::toArray($this->field['errors'])), // because <br> is used in validation.js
            ],
            'field' => $this->field,
        ]);
    }

    /**
     * @return string|void
     */
    public function getFieldLabel()
    {
        if (!empty($this->field['label'])) {
            return $this->builder()->label([
                'class' => implode(' ', $this->getBaseClasses('label')),
                'for' => $this->field['id'],
                'text' => $this->builder()->span($this->field['label']),
            ]);
        }
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return glsr()->id;
    }

    /**
     * @return void
     */
    public function render()
    {
        echo $this->build();
    }

    /**
     * @return string
     */
    protected function buildFieldChoiceOptions()
    {
        $index = 0;
        return array_reduce(array_keys($this->field['options']), function ($carry, $value) use (&$index) {
            $args = glsr()->args($this->field);
            $type = $this->choiceType();
            $inputField = [
                'checked' => in_array($value, $args->cast('value', 'array')),
                'class' => $args->class,
                'id' => Helper::ifTrue(!empty($args->id), $args->id.'-'.++$index),
                'name' => $args->name,
                'required' => $args->required,
                'tabindex' => $args->tabindex,
                'type' => $args->type,
                'value' => $value,
            ];
            $html = glsr(Template::class)->build('templates/form/type-'.$type, [
                'context' => [
                    'class' => glsr(Style::class)->defaultClasses('field').'-'.$type,
                    'id' => $inputField['id'],
                    'input' => $this->builder()->raw($inputField),
                    'text' => $args->options[$value],
                ],
                'field' => $this->field,
                'input' => $inputField,
            ]);
            $html = glsr()->filterString('rendered/field', $html, $type, $inputField);
            return $carry.$html;
        });
    }

    /**
     * @return string
     */
    protected function buildField()
    {
        $field = glsr(Template::class)->build('templates/form/field_'.$this->field['raw_type'], [
            'context' => [
                'class' => $this->getFieldClasses(),
                'errors' => $this->getFieldErrors(),
                'field' => $this->getField(),
                'field_name' => $this->field['path'],
                'for' => $this->field['id'],
                'label' => $this->getFieldLabel(),
                'label_text' => $this->field['label'],
            ],
            'field' => $this->field,
        ]);
        return glsr()->filterString('rendered/field', $field, $this->field['raw_type'], $this->field);
    }

    /**
     * @return string
     */
    protected function buildMultiField()
    {
        return $this->buildField();
    }

    /**
     * @return bool
     */
    protected function isFieldValid()
    {
        $missingValues = [];
        $requiredValues = [
            'name', 'type',
        ];
        foreach ($requiredValues as $value) {
            if (!isset($this->field[$value])) {
                $missingValues[] = $value;
                $this->field['is_valid'] = false;
            }
        }
        if (!empty($missingValues)) {
            glsr_log()
                ->warning('Field is missing: '.implode(', ', $missingValues))
                ->debug($this->field);
        }
        return $this->field['is_valid'];
    }

    /**
     * @param string $className
     * @return array
     */
    protected function mergeFieldArgs($className)
    {
        return $className::merge($this->field);
    }

    /**
     * @return void
     */
    protected function normalizeFieldArgs()
    {
        $className = Helper::buildClassName($this->field['type'], __NAMESPACE__.'\Fields');
        $className = glsr()->filterString('builder/field/'.$this->field['type'], $className);
        if (class_exists($className)) {
            $this->field = $this->mergeFieldArgs($className);
        }
    }

    /**
     * @return void
     */
    protected function normalize()
    {
        if ($this->isFieldValid()) {
            $this->field['path'] = $this->field['name'];
            $this->field['raw_type'] = $this->field['type']; // save the original type before it's normalized
            $this->field = glsr(FieldDefaults::class)->merge($this->field);
            $this->normalizeFieldArgs();
            $this->normalizeFieldId();
            $this->normalizeFieldName();
            $this->field = glsr()->filterArray('field/'.$this->field['raw_type'], $this->field);
        }
    }

    /**
     * @return void
     */
    protected function normalizeFieldId()
    {
        if (!empty($this->field['id']) || $this->field['is_raw']) {
            return;
        }
        $this->field['id'] = Str::convertPathToId(
            $this->field['path'],
            $this->getFieldPrefix()
        );
    }

    /**
     * @return void
     */
    protected function normalizeFieldName()
    {
        $name = Str::convertPathToName($this->field['path'], $this->getFieldPrefix());
        if (count($this->field['options']) > 1 && 'checkbox' === $this->field['type']) {
            $name = Str::suffix($name, '[]'); // @todo is it necessary to do this both here and in the defaults?
        }
        $this->field['name'] = $name;
    }
}
