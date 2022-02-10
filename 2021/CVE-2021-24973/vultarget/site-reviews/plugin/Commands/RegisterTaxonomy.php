<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Defaults\TaxonomyDefaults;

class RegisterTaxonomy implements Contract
{
    public $args;

    public function __construct(array $input = [])
    {
        $this->args = glsr(TaxonomyDefaults::class)->merge($input);
    }

    /**
     * @return void
     */
    public function handle()
    {
        register_taxonomy(glsr()->taxonomy, glsr()->post_type, $this->args);
        register_taxonomy_for_object_type(glsr()->taxonomy, glsr()->post_type);
    }
}
