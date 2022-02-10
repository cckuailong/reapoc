<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;

trait Session
{
    /**
     * @var array
     */
    protected $session = [];

    /**
     * @return Arguments
     */
    public function session()
    {
        return glsr()->args($this->session);
    }

    /**
     * @return void
     */
    public function sessionClear()
    {
        $this->session = [];
    }

    /**
     * @return mixed
     */
    public function sessionGet($key, $fallback = '')
    {
        $value = Arr::get($this->session, $key, $fallback);
        unset($this->session[$key]);
        return $value;
    }

    /**
     * @return void
     */
    public function sessionSet($key, $value)
    {
        $this->session[$key] = $value;
    }
}
