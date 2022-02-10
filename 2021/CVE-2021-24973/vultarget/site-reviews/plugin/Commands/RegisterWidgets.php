<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helper;

class RegisterWidgets implements Contract
{
    /**
     * @return void
     */
    public function handle()
    {
        $dir = glsr()->path('plugin/Widgets');
        if (!is_dir($dir)) {
            return;
        }
        $iterator = new \DirectoryIterator($dir);
        foreach ($iterator as $fileinfo) {
            if ('file' !== $fileinfo->getType()) {
                continue;
            }
            $className = str_replace('.php', '', $fileinfo->getFilename());
            $widgetClass = Helper::buildClassName($className, 'Widgets');
            if (class_exists($widgetClass) && !(new \ReflectionClass($widgetClass))->isAbstract()) {
                register_widget($widgetClass);
            }
        }
    }
}
