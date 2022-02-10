<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Attributes
{
    const ATTRIBUTES_A = [
        'download', 'href', 'hreflang', 'ping', 'referrerpolicy', 'rel', 'target', 'type',
    ];

    const ATTRIBUTES_BUTTON = [
        'autofocus', 'disabled', 'form', 'formaction', 'formenctype', 'formmethod',
        'formnovalidate', 'formtarget', 'name', 'type', 'value',
    ];

    const ATTRIBUTES_FORM = [
        'accept', 'accept-charset', 'action', 'autocapitalize', 'autocomplete', 'enctype', 'method',
        'name', 'novalidate', 'target',
    ];

    const ATTRIBUTES_IMG = [
        'alt', 'crossorigin', 'decoding', 'height', 'ismap', 'loading', 'referrerpolicy', 'sizes', 
        'src', 'srcset', 'width', 'usemap',
    ];

    const ATTRIBUTES_INPUT = [
        'accept', 'autocomplete', 'autocorrect', 'autofocus', 'capture', 'checked', 'disabled',
        'form', 'formaction', 'formenctype', 'formmethod', 'formnovalidate', 'formtarget', 'height',
        'incremental', 'inputmode', 'list', 'max', 'maxlength', 'min', 'minlength', 'multiple',
        'name', 'pattern', 'placeholder', 'readonly', 'results', 'required', 'selectionDirection',
        'selectionEnd', 'selectionStart', 'size', 'spellcheck', 'src', 'step', 'tabindex', 'type',
        'value', 'webkitdirectory', 'width',
    ];

    const ATTRIBUTES_LABEL = [
        'for',
    ];

    const ATTRIBUTES_OPTGROUP = [
        'disabled', 'label',
    ];

    const ATTRIBUTES_OPTION = [
        'disabled', 'label', 'selected', 'value',
    ];

    const ATTRIBUTES_SELECT = [
        'autofocus', 'disabled', 'form', 'multiple', 'name', 'required', 'size',
    ];

    const ATTRIBUTES_TEXTAREA = [
        'autocapitalize', 'autocomplete', 'autofocus', 'cols', 'disabled', 'form', 'maxlength',
        'minlength', 'name', 'placeholder', 'readonly', 'required', 'rows', 'spellcheck', 'wrap',
    ];

    const BOOLEAN_ATTRIBUTES = [
        'autofocus', 'capture', 'checked', 'disabled', 'draggable', 'formnovalidate', 'hidden',
        'multiple', 'novalidate', 'readonly', 'required', 'selected', 'spellcheck',
        'webkitdirectory',
    ];

    const GLOBAL_ATTRIBUTES = [ // ie-style is used by https://github.com/nuxodin/ie11CustomProperties
        'accesskey', 'class', 'contenteditable', 'contextmenu', 'dir', 'draggable', 'dropzone',
        'hidden', 'id', 'ie-style', 'lang', 'spellcheck', 'style', 'tabindex', 'title',
    ];

    const GLOBAL_WILDCARD_ATTRIBUTES = [
        'aria-', 'data-', 'item', 'on',
    ];

    const INPUT_TYPES = [
        'button', 'checkbox', 'color', 'date', 'datetime-local', 'email', 'file', 'hidden', 'image',
        'month', 'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text',
        'time', 'url', 'week',
    ];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param string $method
     * @param array $args
     * @return static
     */
    public function __call($method, $args)
    {
        $constant = 'static::ATTRIBUTES_'.strtoupper($method);
        $allowedAttributeKeys = defined($constant)
            ? constant($constant)
            : [];
        $this->normalize(Arr::consolidate(Arr::get($args, 0)), $allowedAttributeKeys);
        $this->normalizeInputType($method);
        return $this;
    }

    /**
     * @return static
     */
    public function set(array $attributes)
    {
        $this->normalize($attributes);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $attributes = [];
        foreach ($this->attributes as $attribute => $value) {
            $quote = $this->getQuoteChar($attribute);
            $value = esc_attr(implode(',', (array) $value));
            $attributes[] = in_array($attribute, static::BOOLEAN_ATTRIBUTES)
                ? $attribute
                : $attribute.'='.$quote.$value.$quote;
        }
        return implode(' ', $attributes);
    }

    /**
     * @return array
     */
    protected function filterAttributes(array $allowedAttributeKeys)
    {
        return array_intersect_key($this->attributes, array_flip($allowedAttributeKeys));
    }

    /**
     * @return array
     */
    protected function filterGlobalAttributes()
    {
        $globalAttributes = $this->filterAttributes(static::GLOBAL_ATTRIBUTES);
        $wildcards = [];
        foreach (static::GLOBAL_WILDCARD_ATTRIBUTES as $wildcard) {
            $newWildcards = array_filter($this->attributes, function ($key) use ($wildcard) {
                return Str::startsWith($wildcard, $key);
            }, ARRAY_FILTER_USE_KEY);
            $wildcards = array_merge($wildcards, $newWildcards);
        }
        return array_merge($globalAttributes, $wildcards);
    }

    /**
     * @return array
     */
    protected function getPermanentAttributes()
    {
        $permanentAttributes = [];
        if (array_key_exists('value', $this->attributes)) {
            $permanentAttributes['value'] = $this->attributes['value'];
        }
        return $permanentAttributes;
    }

    /**
     * @param string $attribute
     * @return string
     */
    protected function getQuoteChar($attribute)
    {
        return Str::startsWith('data-', $attribute) ? '\'' : '"';
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    protected function isAttributeKeyNumeric($key, $value)
    {
        return is_string($value)
            && is_numeric($key)
            && !array_key_exists($value, $this->attributes);
    }

    /**
     * @return void
     */
    protected function normalize(array $args, array $allowedAttributeKeys = [])
    {
        $this->attributes = array_change_key_case($args, CASE_LOWER);
        $this->normalizeBooleanAttributes();
        $this->normalizeDataAttributes();
        $this->normalizeStringAttributes();
        $this->removeEmptyAttributes();
        $this->removeIndexedAttributes();
        $this->attributes = array_merge(
            $this->filterGlobalAttributes(),
            $this->filterAttributes($allowedAttributeKeys)
        );
    }

    /**
     * @return void
     */
    protected function normalizeBooleanAttributes()
    {
        foreach ($this->attributes as $key => $value) {
            if ($this->isAttributeKeyNumeric($key, $value)) {
                $key = $value;
                $value = true;
            }
            if (!in_array($key, static::BOOLEAN_ATTRIBUTES)) {
                continue;
            }
            $this->attributes[$key] = wp_validate_boolean($value);
        }
    }

    /**
     * @return void
     */
    protected function normalizeDataAttributes()
    {
        foreach ($this->attributes as $key => $value) {
            if ($this->isAttributeKeyNumeric($key, $value)) {
                $key = $value;
                $value = '';
            }
            if (!Str::startsWith('data-', $key)) {
                continue;
            }
            if (is_array($value)) {
                $value = json_encode($value, JSON_HEX_APOS | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
            $this->attributes[$key] = $value;
        }
    }

    /**
     * @return void
     */
    protected function normalizeStringAttributes()
    {
        foreach ($this->attributes as $key => $value) {
            if (is_string($value)) {
                $this->attributes[$key] = trim($value);
            }
        }
    }

    /**
     * @param string $method
     * @return void
     */
    protected function normalizeInputType($method)
    {
        if ('input' != $method) {
            return;
        }
        $attributes = wp_parse_args($this->attributes, ['type' => '']);
        if (!in_array($attributes['type'], static::INPUT_TYPES)) {
            $this->attributes['type'] = 'text';
        }
    }

    /**
     * @return void
     */
    protected function removeEmptyAttributes()
    {
        $attributes = $this->attributes;
        $permanentAttributes = $this->getPermanentAttributes();
        foreach ($this->attributes as $key => $value) {
            if (in_array($key, static::BOOLEAN_ATTRIBUTES) && !$value) {
                unset($attributes[$key]);
            }
            if (Str::startsWith('data-', $key)) {
                $permanentAttributes[$key] = $value;
                unset($attributes[$key]);
            }
        }
        $this->attributes = array_merge(Arr::removeEmptyValues($attributes), $permanentAttributes);
    }

    /**
     * @return void
     */
    protected function removeIndexedAttributes()
    {
        $this->attributes = array_diff_key(
            $this->attributes,
            array_filter($this->attributes, 'is_numeric', ARRAY_FILTER_USE_KEY)
        );
    }
}
