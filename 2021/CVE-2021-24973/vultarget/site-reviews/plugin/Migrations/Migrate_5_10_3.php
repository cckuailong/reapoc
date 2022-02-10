<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Database\CountManager;

class Migrate_5_10_3
{
    /**
     * @return bool
     */
    public function run()
    {
        glsr(CountManager::class)->recalculate();
        return true;
    }
}
