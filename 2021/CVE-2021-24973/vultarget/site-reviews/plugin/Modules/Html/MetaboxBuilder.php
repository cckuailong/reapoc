<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;

class MetaboxBuilder extends Builder
{
    /**
     * @return array
     */
    protected function normalize(array $args, $type)
    {
        if (class_exists($className = $this->getFieldClassName($type))) {
            $args = $className::merge($args, 'metabox');
        }
        return $args;
    }
}
