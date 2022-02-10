<?php

namespace GeminiLabs\SiteReviews;

use BadMethodCallException;
use GeminiLabs\SiteReviews\Helpers\Arr;
use ReflectionClass;

trait Deprecated
{
    /**
     * @var array
     */
    protected $mappedDeprecatedMethods;

    public function __call($oldMethod, $args)
    {
        $newMethod = Arr::get(Arr::consolidate($this->mappedDeprecatedMethods), $oldMethod);
        if (empty($newMethod) || !method_exists($this, $newMethod)) {
            throw new BadMethodCallException("Method [$oldMethod] does not exist.");
        }
        $className = (new ReflectionClass($this))->getShortName();
        $message = sprintf(
            _x('The [%s] method has been deprecated and will be soon be removed, please use the [%s] method instead.', 'admin-text', 'site-reviews'), 
            sprintf('%s::%s()', $className, $oldMethod),
            sprintf('%s::%s()', $className, $newMethod)
        );
        glsr()->append('deprecated', $message);
        return call_user_func_array([$this, $newMethod], $args);
    }
}
