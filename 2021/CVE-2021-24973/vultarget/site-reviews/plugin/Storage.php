<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

trait Storage
{
    /**
     * @var Arguments
     */
    protected $storage;

    /**
     * @param string $property
     * @param mixed $value
     * @param string $key
     * @return false|array
     */
    public function append($property, $value, $key = null)
    {
        $stored = $this->retrieve($property, []);
        if (!is_array($stored)) {
            return false;
        }
        if ($key) {
            $stored[$key] = $value;
        } else {
            $stored[] = $value;
        }
        $this->store($property, $stored);
        return $stored;
    }

    /**
     * @param string $property
     * @return void
     */
    public function discard($property)
    {
        unset($this->storage()->$property);
    }

    /**
     * @param string $property
     * @param mixed $fallback
     * @return mixed
     */
    public function retrieve($property, $fallback = null)
    {
        return $this->storage()->get($property, $fallback);
    }

    /**
     * @param string $cast
     * @param string $property
     * @param mixed $fallback
     * @return mixed
     */
    public function retrieveAs($cast, $property, $fallback = null)
    {
        return Cast::to($cast, $this->storage()->get($property, $fallback));
    }

    /**
     * @return Arguments
     */
    public function storage()
    {
        if (!$this->storage instanceof Arguments) {
            $this->storage = new Arguments([]);
        }
        return $this->storage;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function store($property, $value)
    {
        $this->storage()->set($property, $value);
    }
}
