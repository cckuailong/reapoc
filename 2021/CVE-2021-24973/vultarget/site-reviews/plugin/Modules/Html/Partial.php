<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;

class Partial
{
    /**
     * @return \GeminiLabs\SiteReviews\Application|\GeminiLabs\SiteReviews\Addons\Addon
     */
    public function app()
    {
        return glsr();
    }

    /**
     * @param string $partialPath
     * @return string|void
     */
    public function build($partialPath, array $args = [])
    {
        $className = Helper::buildClassName($partialPath, 'Modules\Html\Partials');
        $className = $this->app()->filterString('partial/classname', $className, $partialPath, $args);
        if (!class_exists($className)) {
            glsr_log()->error('Partial missing: '.$className);
            return;
        }
        $args = $this->app()->filterArray('partial/args/'.$partialPath, $args);
        $partial = glsr($className)->build($args);
        $partial = $this->app()->filterString('rendered/partial', $partial, $partialPath, $args);
        $partial = $this->app()->filterString('rendered/partial/'.$partialPath, $partial, $args);
        return $partial;
    }
    /**
     * @param string $partialPath
     * @return void
     */
    public function render($partialPath, array $args = [])
    {
        echo $this->build($partialPath, $args);
    }
}
