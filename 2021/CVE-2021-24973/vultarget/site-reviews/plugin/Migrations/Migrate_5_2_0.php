<?php

namespace GeminiLabs\SiteReviews\Migrations;

class Migrate_5_2_0
{
    /**
     * @return bool
     */
    public function run()
    {
        wp_clear_scheduled_hook('site-reviews/schedule/session/purge');
        return true;
    }
}
