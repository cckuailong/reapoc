<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class Arguments extends \ArrayObject
{
    /**
     * @param mixed $args
     */
    public function __construct($args)
    {
        if ($args instanceof Arguments) {
            $args = $args->toArray();
        } else {
            $args = Arr::consolidate($args);
        }
        parent::__construct($args, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return serialize($this->toArray());
    }

    /**
     * @param mixed $key
     * @param string $cast
     * @return mixed
     */
    public function cast($key, $cast)
    {
        return Cast::to($cast, $this->get($key));
    }

    /**
     * @param mixed $key
     * @param mixed $fallback
     * @return mixed
     */
    public function get($key, $fallback = null)
    {
        $value = Arr::get($this->toArray(), $key, $fallback);
        return isset($fallback)
            ? Helper::ifEmpty($value, $fallback)
            : $value;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->toArray());
    }

    /**
     * @return self
     */
    public function merge(array $data = [])
    {
        $storage = wp_parse_args($data, $this->toArray());
        $this->exchangeArray($storage);
        return $this;
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @param mixed $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $storage = $this->toArray();
        unset($storage[$key]);
        $this->exchangeArray($storage);
    }

    /**
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public function set($path, $value)
    {
        $storage = Arr::set($this->toArray(), $path, $value);
        $this->exchangeArray($storage);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }
}
