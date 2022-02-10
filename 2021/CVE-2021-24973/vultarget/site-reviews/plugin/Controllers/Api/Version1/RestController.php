<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

class RestController
{
    /**
     * @return void
     */
    public function registerRoutes()
    {
        (new RestSummaryController())->register_routes();
        (new RestTypeController())->register_routes();
    }
}
