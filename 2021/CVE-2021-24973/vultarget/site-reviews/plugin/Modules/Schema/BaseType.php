<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

use ArrayAccess;
use DateTime;
use DateTimeInterface;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Schema\Exceptions\InvalidProperty;
use JsonSerializable;
use ReflectionClass;

abstract class BaseType implements ArrayAccess, JsonSerializable, Type
{
    /**
     * @var array
     */
    public $allowed = [];

    /**
     * @var array
     */
    public $parents = [];

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $method
     * @return static
     */
    public function __call($method, array $arguments)
    {
        return $this->setProperty($method, Arr::get($arguments, 0));
    }

    /**
     * @param string $type
     */
    public function __construct($type = null)
    {
        $this->type = !is_string($type)
            ? (new ReflectionClass($this))->getShortName()
            : $type;
        $this->setAllowedProperties();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toScript();
    }

    /**
     * @return static
     */
    public function addProperties(array $properties)
    {
        foreach ($properties as $property => $value) {
            $this->setProperty($property, $value);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return 'https://schema.org';
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string $property
     * @param mixed $default
     * @return mixed
     */
    public function getProperty($property, $default = null)
    {
        return Arr::get($this->properties, $property, $default);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param bool $condition
     * @param mixed $callback
     * @return static
     */
    public function doIf($condition, $callback)
    {
        if ($condition) {
            $callback($this);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->properties);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getProperty($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->setProperty($offset, $value);
    }

    /**
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->properties[$offset]);
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return static
     */
    public function setProperty($property, $value)
    {
        if (!in_array($property, $this->allowed)
            && 'UnknownType' != (new ReflectionClass($this))->getShortName()) {
            glsr_log()->warning($this->getType().' does not allow the "'.$property.'" property');
            return $this;
        }
        if (!Helper::isEmpty($value)) {
            $this->properties[$property] = $value;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $this->serializeIdentifier();
        $properties = $this->serializeProperty($this->getProperties());
        $array = [
            '@context' => $this->getContext(),
            '@type' => $this->getType(),
        ];
        return is_array($properties)
            ? $array + $properties
            : $array;
    }

    /**
     * @return string
     */
    public function toScript()
    {
        return sprintf('<script type="application/ld+json">%s</script>',
            json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * @param array|null $parents
     * @return array
     */
    protected function getParents($parents = null)
    {
        if (!isset($parents)) {
            $parents = $this->parents;
        }
        $newParents = $parents;
        foreach ($parents as $parent) {
            $parentClass = Helper::buildClassName($parent, __NAMESPACE__);
            if (!class_exists($parentClass)) {
                continue;
            }
            $newParents = array_merge($newParents, $this->getParents((new $parentClass())->parents));
        }
        return array_values(array_unique($newParents));
    }

    /**
     * @return void
     */
    protected function setAllowedProperties()
    {
        $parents = $this->getParents();
        foreach ($parents as $parent) {
            $parentClass = Helper::buildClassName($parent, __NAMESPACE__);
            if (!class_exists($parentClass)) {
                continue;
            }
            $this->allowed = array_values(array_unique(array_merge((new $parentClass())->allowed, $this->allowed)));
        }
    }

    /**
     * @return void
     */
    protected function serializeIdentifier()
    {
        if (isset($this['identifier'])) {
            $this->setProperty('@id', $this['identifier']);
            unset($this['identifier']);
        }
    }

    /**
     * @param mixed $property
     * @return array|string
     */
    protected function serializeProperty($property)
    {
        if (is_array($property)) {
            return array_map([$this, 'serializeProperty'], $property);
        }
        if ($property instanceof Type) {
            $property = $property->toArray();
            unset($property['@context']);
        }
        if ($property instanceof DateTimeInterface) {
            $property = $property->format(DateTime::ATOM);
        }
        if (is_object($property) && method_exists($property, '__toString')) {
            $property = (string) $property;
        }
        if (is_object($property)) {
            throw new InvalidProperty();
        }
        return $property;
    }
}
