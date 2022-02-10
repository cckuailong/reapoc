<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface PartialContract
{
    /**
     * @return string|void
     */
    public function build(array $args = []);
}
