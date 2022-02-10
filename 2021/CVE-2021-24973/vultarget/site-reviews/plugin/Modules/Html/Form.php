<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Form extends \ArrayObject
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $hidden;

    /**
     * @var array
     */
    protected $visible;

    public function __construct(array $visible, array $hidden = [])
    {
        $this->fields = array_merge($hidden, $visible);
        $this->hidden = $hidden;
        $this->visible = $visible;
        parent::__construct($this->fields, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return array_reduce($this->getArrayCopy(), function ($carry, $field) {
            return $carry.$field;
        });
    }

    /**
     * @return \GeminiLabs\SiteReviews\Modules\Html\Field|array|null
     */
    public function hidden($key = null)
    {
        return is_null($key) ? $this->hidden : Arr::get($this->hidden, $key, null);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->fields)) {
            return $this->fields[$key];
        }
    }

    /**
     * @return \GeminiLabs\SiteReviews\Modules\Html\Field|array|null
     */
    public function visible($key = null)
    {
        return is_null($key) ? $this->visible : Arr::get($this->visible, $key, null);
    }
}
