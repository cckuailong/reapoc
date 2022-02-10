<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Style;
use ReflectionClass;

abstract class Shortcode implements ShortcodeContract
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var array
     */
    public $dataAttributes;

    /**
     * @var string
     */
    public $shortcode;

    public function __construct()
    {
        $this->shortcode = Str::snakeCase($this->getShortClassName());
    }

    public function __get($parameter)
    {
        // @compat provides backwards compatibility with unsupported add-ons
    }

    /**
     * @param string|array $atts
     * @param string $type
     * @return string
     */
    public function build($atts, array $args = [], $type = 'shortcode')
    {
        $args = $this->normalizeArgs($args, $type);
        $atts = $this->normalizeAtts($atts, $type);
        $template = $this->buildTemplate($atts->toArray());
        if (!empty($atts->title)) {
            $title = $args->before_title.$atts->title.$args->after_title;
            $atts->title = $title;
        }
        $attributes = wp_parse_args($this->dataAttributes, [
            'class' => glsr(Style::class)->styleClasses(),
            'id' => $atts->id,
            'text' => $template,
        ]);
        $attributes = glsr()->filterArray('shortcode/'.$this->shortcode.'/attributes', $attributes, $this);
        $html = glsr(Builder::class)->div($attributes);
        return $args->before_widget.$atts->title.$html.$args->after_widget;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBlock($atts = [])
    {
        return $this->build($atts, [], 'block');
    }

    /**
     * {@inheritdoc}
     */
    public function buildShortcode($atts = [])
    {
        return $this->build($atts, [], 'shortcode');
    }

    /**
     * @return string|void
     */
    public function buildTemplate(array $args = [])
    {
        return; // @todo make this abstract in v6.0
    }

    /**
     * @return array
     */
    public function getHideOptions()
    {
        $options = $this->hideOptions();
        return glsr()->filterArray('shortcode/hide-options', $options, $this->shortcode, $this);
    }

    /**
     * @return string
     */
    public function getShortClassName($replace = '', $search = 'Shortcode')
    {
        return str_replace($search, $replace, (new ReflectionClass($this))->getShortName());
    }

    /**
     * @return string
     */
    public function getShortcodeDefaultsClassName()
    {
        $classname = str_replace('Shortcodes\\', 'Defaults\\', get_class($this));
        return str_replace('Shortcode', 'Defaults', $classname);
    }

    /**
     * @param array|string $args
     * @param string $type
     * @return Arguments
     */
    public function normalizeArgs($args, $type = 'shortcode')
    {
        $args = wp_parse_args($args, [
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h2 class="glsr-title">',
            'after_title' => '</h2>',
        ]);
        $args = glsr()->filterArray('shortcode/args', $args, $type, $this->shortcode);
        return glsr()->args($args);
    }

    /**
     * @param array|string $atts
     * @param string $type
     * @return Arguments
     */
    public function normalizeAtts($atts, $type = 'shortcode')
    {
        $atts = wp_parse_args($atts);
        $atts = glsr()->filterArray('shortcode/atts', $atts, $type, $this->shortcode);
        $atts = glsr($this->getShortcodeDefaultsClassName())->unguardedRestrict($atts);
        $atts = glsr()->args($atts);
        foreach ($atts as $key => &$value) {
            $method = Helper::buildMethodName($key, 'normalize');
            if (method_exists($this, $method)) {
                $value = call_user_func([$this, $method], $value, $atts);
            }
        }
        $this->setDataAttributes($atts, $type);
        return $atts;
    }

    /**
     * @return array
     */
    abstract protected function hideOptions();

    /**
     * @param string $postIds
     * @return string
     */
    protected function normalizeAssignedPosts($postIds, Arguments $atts)
    {
        $postIds = glsr(Sanitizer::class)->sanitizePostIds($postIds);
        $postIds = glsr(Multilingual::class)->getPostIds($postIds);
        return implode(',', $postIds);
    }

    /**
     * @param string $termIds
     * @return string
     */
    protected function normalizeAssignedTerms($termIds)
    {
        return implode(',', glsr(Sanitizer::class)->sanitizeTermIds($termIds));
    }

    /**
     * @param string $userIds
     * @return string
     */
    protected function normalizeAssignedUsers($userIds)
    {
        return implode(',', glsr(Sanitizer::class)->sanitizeUserIds($userIds));
    }

    /**
     * @param string|array $hide
     * @return array
     */
    protected function normalizeHide($hide)
    {
        $hideKeys = array_keys($this->getHideOptions());
        return array_filter(Cast::toArray($hide), function ($value) use ($hideKeys) {
            return in_array($value, $hideKeys);
        });
    }

    /**
     * @param string $labels
     * @return array
     */
    protected function normalizeLabels($labels)
    {
        $defaults = [
            __('Excellent', 'site-reviews'),
            __('Very good', 'site-reviews'),
            __('Average', 'site-reviews'),
            __('Poor', 'site-reviews'),
            __('Terrible', 'site-reviews'),
        ];
        $maxRating = (int) glsr()->constant('MAX_RATING', Rating::class);
        $defaults = array_pad(array_slice($defaults, 0, $maxRating), $maxRating, '');
        $labels = array_map('trim', explode(',', $labels));
        foreach ($defaults as $i => $label) {
            if (!empty($labels[$i])) {
                $defaults[$i] = $labels[$i];
            }
        }
        return array_combine(range($maxRating, 1), $defaults);
    }

    /**
     * @param string $type
     * @return void
     */
    protected function setDataAttributes(Arguments $atts, $type)
    {
        $this->dataAttributes = wp_parse_args(
            glsr($this->getShortcodeDefaultsClassName())->dataAttributes($atts->toArray()),
            ["data-{$type}" => '']
        );
    }
}
