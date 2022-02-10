<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Arguments;

class FormTag extends Tag
{
    /**
     * @param mixed $with
     * @return bool
     */
    protected function validate($with)
    {
        return $with instanceof Arguments;
    }
}
