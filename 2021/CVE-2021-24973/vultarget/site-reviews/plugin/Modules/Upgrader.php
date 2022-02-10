<?php

namespace GeminiLabs\SiteReviews\Modules;

/**
 * This class exists because versions prior to v4.3.8 triggered the 
 * upgrader_process_complete hook during a plugin update which had the 
 * undesired side-effect of running the upgrade process while the plugin was 
 * still in a "dirty" state. Since the Upgrader class was removed in v4.3.8, this
 * has the potential to throw a ReflectionException error.
 */
class Upgrader
{
    public function run()
    {}
}
