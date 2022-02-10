<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Application;

interface ProviderContract
{
    /**
     * @return void
     */
    public function register(Application $app);
}
