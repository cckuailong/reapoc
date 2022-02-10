<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\FieldDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * This class generates raw HTML tags without additional DOM markup.
 *
 * @method string a(string|array ...$params)
 * @method string button(string|array ...$params)
 * @method string div(string|array ...$params)
 * @method string i(string|array ...$params)
 * @method string img(string|array ...$params)
 * @method string input(string|array ...$params)
 * @method string label(string|array ...$params)
 * @method string option(string|array ...$params)
 * @method string p(string|array ...$params)
 * @method string select(string|array ...$params)
 * @method string small(string|array ...$params)
 * @method string span(string|array ...$params)
 * @method string textarea(string|array ...$params)
 */
class Builder
{
    const INPUT_TYPES = [
        'checkbox', 'date', 'datetime-local', 'email', 'file', 'hidden', 'image', 'month',
        'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text', 'time',
        'url', 'week',
    ];

    const TAGS_FORM = [
        'input', 'select', 'textarea',
    ];

    const TAGS_SINGLE = [
        'img',
    ];

    const TAGS_STRUCTURE = [
        'div', 'form', 'nav', 'ol', 'section', 'ul',
    ];

    const TAGS_TEXT = [
        'a', 'button', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'i', 'label', 'li', 'option', 'optgroup',
        'p', 'pre', 'small', 'span',
    ];

    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $args;

    /**
     * @var bool
     */
    public $render = false;

    /**
     * @var string
     */
    public $tag;

    /**
     * @var string
     */
    public $type;

    /**
     * @param string $method
     * @param array $methodArgs
     * @return string|void
     */
    public function __call($method, $methodArgs)
    {
        $instance = new static();
        $args = call_user_func_array([$instance, 'prepareArgs'], $methodArgs);
        $tag = Str::dashCase($method);
        $result = $instance->build($tag, $args);
        if (!$instance->render) {
            return $result;
        }
        echo $result;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set($property, $value)
    {
        $method = Helper::buildMethodName($property, 'set');
        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $value);
        }
    }

    /**
     * @return string
     */
    public function build($tag, array $args = [])
    {
        $this->setArgs($args, $tag);
        $this->setTag($tag);
        glsr()->action('builder', $this);
        $result = $this->isHtmlTag($this->tag)
            ? $this->buildElement()
            : $this->buildCustom($tag);
        return glsr()->filterString('builder/result', $result, $this);
    }

    /**
     * @return void|string
     */
    public function buildClosingTag()
    {
        return '</'.$this->tag.'>';
    }

    /**
     * @param string $tag
     * @return void|string
     */
    public function buildCustom($tag)
    {
        if (class_exists($className = $this->getFieldClassName($tag))) {
            return (new $className($this))->build();
        }
        glsr_log()->error("Field [$className] missing.");
    }

    /**
     * @return string
     */
    public function buildDefaultElement($text = '')
    {
        $text = Helper::ifEmpty($text, $this->args->text, $strict = true);
        return $this->buildOpeningTag().$text.$this->buildClosingTag();
    }

    /**
     * @return void|string
     */
    public function buildElement()
    {
        if (in_array($this->tag, static::TAGS_SINGLE)) {
            return $this->buildOpeningTag();
        }
        if (in_array($this->tag, static::TAGS_FORM)) {
            return $this->buildFormElement();
        }
        return $this->buildDefaultElement();
    }

    /**
     * @return void|string
     */
    public function buildFormElement()
    {
        $method = Helper::buildMethodName($this->tag, 'buildForm');
        return $this->$method();
    }

    /**
     * @return void|string
     */
    public function buildOpeningTag()
    {
        $attributes = glsr(Attributes::class)->{$this->tag}($this->args->toArray())->toString();
        return '<'.trim($this->tag.' '.$attributes).'>';
    }

    /**
     * @return string
     */
    public function raw(array $field)
    {
        unset($field['label']);
        return $this->{$field['type']}($field);
    }

    /**
     * @param array $args
     * @param string $type
     * @return void
     */
    public function setArgs($args = [], $type = '')
    {
        $args = Arr::consolidate($args);
        if (!empty($args)) {
            $args = $this->normalize($args, $type);
            $options = glsr()->args($args)->options;
            $args = glsr(FieldDefaults::class)->merge($args);
            if (is_array($options)) {
                // Merging reindexes the options array, this may not be desirable
                // if the array is indexed so here we restore the original options array.
                // It's a messy hack, but it will have to do for now.
                $args['options'] = $options;
            }
        }
        $args = glsr()->filterArray('builder/'.$type.'/args', $args, $this);
        $this->args = glsr()->args($args);
    }

    /**
     * @param bool $bool
     * @return void
     */
    public function setRender($bool)
    {
        $this->render = Cast::toBool($bool);
    }

    /**
     * @param string $tag
     * @return void
     */
    public function setTag($tag)
    {
        $tag = Cast::toString($tag);
        $this->tag = Helper::ifTrue(in_array($tag, static::INPUT_TYPES), 'input', $tag);
    }

    /**
     * @return string|void
     */
    protected function buildFormInput()
    {
        if (!in_array($this->args->type, ['checkbox', 'radio'])) {
            return $this->buildFormLabel().$this->buildOpeningTag();
        }
        return empty($this->args->options)
            ? $this->buildFormInputChoice()
            : $this->buildFormInputChoices();
    }

    /**
     * @return string|void
     */
    protected function buildFormInputChoice()
    {
        if ($label = Helper::ifEmpty($this->args->text, $this->args->label)) {
            return $this->buildFormLabel([
                'text' => $this->buildOpeningTag().' '.$label,
            ]);
        }
        return $this->buildOpeningTag();
    }

    /**
     * @return string|void
     */
    protected function buildFormInputChoices()
    {
        $index = 0;
        return array_reduce(array_keys($this->args->options), function ($carry, $value) use (&$index) {
            return $carry.$this->input([
                'checked' => in_array($value, $this->args->cast('value', 'array')),
                'class' => $this->args->class,
                'disabled' => $this->args->disabled,
                'id' => $this->indexedId(++$index),
                'label' => $this->args->options[$value],
                'name' => $this->args->name,
                'required' => $this->args->required,
                'tabindex' => $this->args->tabindex,
                'type' => $this->args->type,
                'value' => $value,
            ]);
        });
    }

    /**
     * @return void|string
     */
    protected function buildFormLabel(array $customArgs = [])
    {
        if (!empty($this->args->label) && 'hidden' !== $this->args->type) {
            return $this->label(wp_parse_args($customArgs, [
                'for' => $this->args->id,
                'text' => $this->args->label,
            ]));
        }
    }

    /**
     * @return string|void
     */
    protected function buildFormSelect()
    {
        return $this->buildFormLabel().$this->buildDefaultElement($this->buildFormSelectOptions());
    }

    /**
     * @return string|void
     */
    protected function buildFormSelectOptions()
    {
        $options = $this->args->cast('options', 'array');
        $optgroupEnabled = glsr()->filterBool('builder/enable/optgroup', false);
        if ($this->args->placeholder) {
            $options = Arr::prepend($options, $this->args->placeholder, '');
        }
        return array_reduce(array_keys($options), function ($carry, $key) use ($options, $optgroupEnabled) {
            if ($optgroupEnabled && is_array($options[$key])) {
                return $carry.$this->buildFormSelectOptGroup($options[$key], $key);
            }
            return $carry.$this->option([
                'selected' => $this->args->cast('value', 'string') === Cast::toString($key),
                'text' => $options[$key],
                'value' => $key,
            ]);
        });
    }

    /**
     * @return string
     */
    protected function buildFormSelectOptGroup($options, $label)
    {
        $children = array_reduce(array_keys($options), function ($carry, $key) use ($options) {
           return $carry.glsr(Builder::class)->option([
                'selected' => $this->args->cast('value', 'string') === Cast::toString($key),
                'text' => $options[$key],
                'value' => $key,
            ]);
        });
        return glsr(Builder::class)->optgroup([
            'label' => $label,
            'text' => $children,
        ]);
    }

    /**
     * @return string|void
     */
    protected function buildFormTextarea()
    {
        return $this->buildFormLabel().$this->buildDefaultElement($this->args->cast('value', 'string'));
    }

    /**
     * @return string
     */
    protected function indexedId($index)
    {
        return Helper::ifTrue(count($this->args->options) > 1,
            $this->args->id.'-'.$index,
            $this->args->id
        );
    }

    /**
     * @param string $tag
     * @return bool
     */
    protected function isHtmlTag($tag)
    {
        return in_array($tag, array_merge(
            static::TAGS_FORM,
            static::TAGS_SINGLE,
            static::TAGS_STRUCTURE,
            static::TAGS_TEXT
        ));
    }

    /**
     * @param string $tag
     * @return string
     */
    protected function getFieldClassName($tag)
    {
        $className = Helper::buildClassName($tag, __NAMESPACE__.'\Fields');
        return glsr()->filterString('builder/field/'.$tag, $className);
    }

    /**
     * @return array
     */
    protected function normalize(array $args, $type)
    {
        if (class_exists($className = $this->getFieldClassName($type))) {
            $args = $className::merge($args);
        }
        return $args;
    }

    /**
     * @param string|array ...$params
     * @return array
     */
    protected function prepareArgs(...$params)
    {
        if (is_array($parameter1 = array_shift($params))) {
            return $parameter1;
        }
        $parameter2 = Arr::consolidate(array_shift($params));
        if (is_scalar($parameter1)) {
            $parameter2['text'] = $parameter1;
        }
        return $parameter2;
    }
}
