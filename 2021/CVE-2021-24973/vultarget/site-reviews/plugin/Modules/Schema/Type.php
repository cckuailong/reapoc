<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

interface Type
{
    /**
     * Return an array representation of the type. If the array contains child types,
     * their context needs to be stripped if it's the same.
     * @return array
     */
    public function toArray();

    /**
     * Create a json-ld script tag for this type, built from the data that `toArray` returns.
     * @return string
     */
    public function toScript();

    /**
     * Create a json-ld script tag for this type, built from the data that `toArray` returns.
     * @return string
     */
    public function __toString();
}
