<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Defaults\PaginationDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleClassesDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleValidationDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

/**
 * @method string classes(string $key)
 * @method string defaultClasses(string $key)
 * @method string defaultValidation(string $key)
 * @method string validation(string $key)
 */
class Style
{
    /**
     * @var array
     */
    public $classes;

    /**
     * @var string
     */
    public $style;

    /**
     * @var array
     */
    public $pagination;

    /**
     * @var array
     */
    public $validation;

    /**
     * The methods that are callable.
     * @var array
     */
    protected $callable = [
        'classes', 'validation',
    ];

    public function __construct()
    {
        $this->style = glsr_get_option('general.style', 'default');
        $config = shortcode_atts(
            array_fill_keys(['classes', 'pagination', 'validation'], []),
            glsr()->config('styles/'.$this->style)
        );
        $this->classes = glsr(StyleClassesDefaults::class)->restrict($config['classes']);
        $this->pagination = glsr(PaginationDefaults::class)->restrict($config['pagination']);
        $this->validation = glsr(StyleValidationDefaults::class)->restrict($config['validation']);
    }

    public function __call($method, $args)
    {
        $property = strtolower(Str::removePrefix($method, 'default'));
        if (!in_array($property, $this->callable)) {
            return;
        }
        $key = Arr::get($args, 0);
        if (Str::startsWith('default', $method)) {
            $className = Helper::buildClassName(['style', $property, 'defaults'], 'Defaults');
            return glsr()->args(glsr($className)->defaults())->$key;
        }
        return glsr()->args($this->$property)->$key;
    }

    /**
     * @param string $view
     * @return string
     */
    public function filterView($view)
    {
        $styledViews = glsr()->filterArray('style/views', [
            'templates/form/field',
            'templates/form/response',
            'templates/form/submit-button',
            'templates/form/type-checkbox',
            'templates/form/type-radio',
            'templates/form/type-toggle',
            'templates/reviews-form',
        ]);
        if (!preg_match('('.implode('|', $styledViews).')', $view)) {
            return $view;
        }
        $views = $this->generatePossibleViews($view);
        foreach ($views as $possibleView) {
            if (file_exists(glsr()->file($possibleView))) {
                return Str::removePrefix($possibleView, 'views/');
            }
        }
        return $view;
    }

    /**
     * @return void
     */
    public function modifyField(Builder $instance)
    {
        if ($this->isPublicInstance($instance)) {
            call_user_func_array([$this, 'customize'], [$instance]);
        }
    }

    /**
     * This allows us to override the pagination config in /config/styles instead of using a filter hook.
     * @return array
     */
    public function paginationArgs(array $args)
    {
        return wp_parse_args($args, $this->pagination);
    }

    /**
     * @return string
     */
    public function styleClasses()
    {
        return glsr()->filterString('style', 'glsr glsr-'.$this->style);
    }

    /**
     * Add the custom form classes.
     * @return void
     */
    protected function customize(Builder $instance)
    {
        if (array_key_exists($instance->tag, $this->classes)) {
            $key = $instance->tag.'_'.$instance->args->type;
            $classes = Arr::get($this->classes, $key, Arr::get($this->classes, $instance->tag));
            $classes = trim($instance->args->class.' '.$classes);
            $classes = implode(' ', Arr::unique(explode(' ', $classes))); // remove duplicate classes
            $instance->args->class = $classes;
            glsr()->action('customize/'.$this->style, $instance);
        }
    }

    /**
     * @param string $view
     * @return array
     */
    protected function generatePossibleViews($view)
    {
        $basename = basename($view);
        $basepath = rtrim($view, $basename);
        $customPath = 'views/partials/styles/'.$this->style.'/';
        $parts = explode('_', $basename);
        $views = [
            $customPath.$basename,
            $customPath.$parts[0],
            $view,
            $basepath.$parts[0],
        ];
        return array_filter($views);
    }

    /**
     * @return bool
     */
    protected function isPublicInstance(Builder $instance)
    {
        $args = glsr()->args($instance->args)->merge(['is_raw' => false]);
        return !glsr()->isAdmin() && !Cast::toBool($args->is_raw);
    }
}
