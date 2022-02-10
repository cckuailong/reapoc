<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface CommandContract
{
    /**
     * @return mixed
     */
    public function handle();
}
