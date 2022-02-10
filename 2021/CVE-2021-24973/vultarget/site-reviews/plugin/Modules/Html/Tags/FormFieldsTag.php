<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Honeypot;
use GeminiLabs\SiteReviews\Modules\Html\Attributes;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Form;

class FormFieldsTag extends FormTag
{
    /**
     * @return array
     */
    protected function fields()
    {
        $fields = glsr()->config('forms/review-form');
        $fields = glsr()->filterArray('review-form/fields', $fields, $this->args);
        foreach ($fields as $name => &$field) {
            $field = new Field(wp_parse_args($field, ['name' => $name]));
        }
        return $this->normalizeFields($fields);
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $fields = $this->fields();
        $hiddenFields = array_merge($this->hiddenFields(), [
            'honeypot' => glsr(Honeypot::class)->build($this->args->id)
        ]);
        foreach ($fields as $name => $field) {
            unset($hiddenFields[$name]);
        }
        return new Form($fields, $hiddenFields);
    }

    /**
     * @return array
     */
    protected function hiddenFields()
    {
        $fields = [];
        $referer = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $referer = glsr()->filterString('review-form/referer', $referer);
        $hiddenFields = [
            '_action' => 'submit-review',
            '_counter' => null,
            '_nonce' => wp_create_nonce('submit-review'),
            '_post_id' => get_the_ID(),
            '_referer' => wp_unslash($referer),
            'assigned_posts' => $this->args->assigned_posts,
            'assigned_terms' => $this->args->assigned_terms,
            'assigned_users' => $this->args->assigned_users,
            'excluded' => $this->args->hide,
            'form_id' => $this->args->id,
            'terms_exist' => Cast::toInt(!in_array('terms', $this->args->hide)),
        ];
        foreach ($hiddenFields as $name => $value) {
            $fields[$name] = new Field([
                'name' => $name,
                'type' => 'hidden',
                'value' => $value,
            ]);
        }
        return glsr()->filterArray('review-form/fields/hidden', $fields, $this->args);
    }

    /**
     * @return void
     */
    protected function normalizeFieldClasses(Field &$field)
    {
        if ('hidden' === $field->fieldType()) {
            return;
        }
        $fieldClasses = [
            'input' => ['glsr-input', 'glsr-input-'.$field->choiceType()],
            'choice' => ['glsr-input-'.$field->choiceType()],
            'other' => ['glsr-'.$field->field['type']],
        ];
        if ('choice' === $field->fieldType()) {
            $classes = $fieldClasses['choice'];
        } else if (in_array($field->field['type'], Attributes::INPUT_TYPES)) {
            $classes = $fieldClasses['input'];
        } else {
            $classes = $fieldClasses['other'];
        }
        $classes[] = trim(Arr::get($field->field, 'class'));
        $field->field['class'] = implode(' ', $classes);
    }

    /**
     * @return void
     */
    protected function normalizeFieldId(Field &$field)
    {
        if (!empty($this->args->id) && !empty($field->field['id'])) {
            $field->field['id'] .= '-'.$this->args->id;
        }
    }

    /**
     * @return void
     */
    protected function normalizeFieldErrors(Field &$field)
    {
        if (array_key_exists($field->field['path'], $this->with->errors)) {
            $field->field['errors'] = $this->with->errors[$field->field['path']];
        }
    }

    /**
     * @return void
     */
    protected function normalizeFieldRequired(Field &$field)
    {
        if (in_array($field->field['path'], $this->with->required)) {
            $field->field['required'] = true;
        }
    }

    /**
     * @return array
     */
    protected function normalizeFields($fields)
    {
        $normalizedFields = [];
        foreach ($fields as $name => $field) {
            if (!in_array($field->field['path'], $this->args->hide)) {
                $this->normalizeFieldClasses($field);
                $this->normalizeFieldErrors($field);
                $this->normalizeFieldRequired($field);
                $this->normalizeFieldValue($field);
                $this->normalizeFieldId($field);
                $normalizedFields[$name] = $field;
            }
        }
        return glsr()->filterArray('review-form/fields/normalized', $normalizedFields, $this->args);
    }

    /**
     * @return void
     */
    protected function normalizeFieldValue(Field $field)
    {
        if (!array_key_exists($field->field['path'], $this->with->values)) {
            return;
        }
        if (in_array($field->field['type'], ['radio', 'checkbox'])) {
            $field->field['checked'] = $field->field['value'] == $this->with->values[$field->field['path']];
        } else {
            $field->field['value'] = $this->with->values[$field->field['path']];
        }
    }
}
