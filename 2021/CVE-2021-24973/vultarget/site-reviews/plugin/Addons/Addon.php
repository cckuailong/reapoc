<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Contracts\DefaultsContract;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Plugin;
use ReflectionClass;

/**
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property bool $licensed
 * @property string $name
 * @property string $slug
 * @property string $testedTo
 * @property string $update_url
 * @property Updater $updater
 * @property string $version
 */
abstract class Addon
{
    use Plugin;

    const ID = '';
    const LICENSED = false;
    const NAME = '';
    const POST_TYPE = '';
    const SLUG = '';
    const UPDATE_URL = '';

    protected $updater;

    /**
     * @return void
     */
    public function init()
    {
        $reflection = new ReflectionClass($this);
        $className = Str::replaceLast($reflection->getShortname(), 'Hooks', $reflection->getName());
        if (class_exists($className)) {
            (new $className())->run();
        } else {
            glsr_log()->error('The '.static::NAME.' add-on is missing a Hooks class');
        }
    }

    public function make($class, array $parameters = [])
    {
        $class = Str::camelCase($class);
        $class = ltrim(str_replace([__NAMESPACE__, 'GeminiLabs\SiteReviews'], '', $class), '\\');
        $class = __NAMESPACE__.'\\'.$class;
        return glsr($class, $parameters);
    }

    /**
     * @param string $path
     * @param mixed $fallback
     * @param string $cast
     * @return mixed
     */
    public function option($path = '', $fallback = '', $cast = '')
    {
        $path = Str::removePrefix($path, 'settings.');
        $path = Str::prefix($path, 'addons.'.static::SLUG.'.');
        $path = Str::prefix($path, 'settings.');
        return glsr_get_option($path, $fallback, $cast);
    }

    /**
     * @param string $defaultsClass  The defaults class used to restrict the options
     * @return \GeminiLabs\SiteReviews\Arguments
     */
    public function options($defaultsClass = '')
    {
        $options = glsr_get_option('settings.addons.'.static::SLUG, [], 'array');
        if (is_a($defaultsClass, DefaultsContract::class, true)) {
            $options = glsr($defaultsClass)->restrict($options);
        }
        return glsr()->args($options);
    }

    /**
     * @param int $perPage
     * @return array
     */
    public function posts($perPage = 50)
    {
        if (empty(static::POST_TYPE)) {
            return [];
        }
        $posts = get_posts([
            'order' => 'ASC',
            'orderby' => 'post_title',
            'post_type' => static::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => $perPage,
        ]);
        $results = wp_list_pluck($posts, 'post_title', 'ID');
        foreach ($results as $id => &$title) {
            if (empty(trim($title))) {
                $title = _x('Untitled', 'admin-text', 'site-reviews');
            }
            $title = sprintf('%s (ID: %s)', $title, $id);
        }
        natsort($results);
        return $results;
    }

    /**
     * @return void
     */
    public function update()
    {
        $doingCron = defined('DOING_CRON') && DOING_CRON;
        if (!current_user_can('manage_options') && !$doingCron) {
            return;
        }
        $this->updater = new Updater(static::UPDATE_URL, $this->file, [
            'license' => glsr_get_option('licenses.'.static::ID),
        ]);
        $this->updater->init();
    }
}
