<?php

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\BlackHole;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Exceptions\BindingResolutionException;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Backtrace;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Request;

defined('ABSPATH') || exit;

/*
 * Alternate method of using the functions without having to use `function_exists()`
 * Example: apply_filters('glsr_get_reviews', [], ['assigned_posts' => 'post_id']);
 * @param mixed ...
 * @return mixed
 */
add_filter('plugins_loaded', function () {
    $hooks = [
        'glsr_create_review' => 2,
        'glsr_debug' => 10,
        'glsr_get' => 4,
        'glsr_get_option' => 4,
        'glsr_get_options' => 1,
        'glsr_get_ratings' => 2,
        'glsr_get_review' => 2,
        'glsr_get_reviews' => 2,
        'glsr_log' => 3,
        'glsr_star_rating' => 4,
        'glsr_trace' => 2,
        'glsr_update_review' => 3,
    ];
    foreach ($hooks as $function => $acceptedArgs) {
        add_filter($function, function () use ($function) {
            $args = func_get_args();
            array_shift($args); // remove the fallback value
            return call_user_func_array($function, $args);
        }, 10, $acceptedArgs);
    }
});

/**
 * @return mixed
 */
function glsr($alias = null, array $parameters = [])
{
    $app = Application::load();
    if (is_null($alias)) {
        return $app;
    }
    try {
        return $app->make($alias, $parameters);
    } catch (BindingResolutionException $e) {
        glsr_log()->error($e->getMessage());
        return $app->make(BlackHole::class, compact('alias'));
    }
}

/**
 * @param string $page
 * @param string $tab
 * @param string $sub
 * @return string
 */
function glsr_admin_url($page = '', $tab = '', $sub = '')
{
    if (!empty($page)) {
        $page = Str::dashCase(glsr()->prefix.$page);
    }
    $args = array_filter([
        'post_type' => glsr()->post_type,
        'page' => $page,
        'tab' => $tab,
        'sub' => $sub,
    ]);
    return add_query_arg($args, admin_url('edit.php'));
}

/**
 * @return \GeminiLabs\SiteReviews\Review|false
 */
function glsr_create_review($values = [])
{
    $values = Arr::removeEmptyValues(Arr::consolidate($values));
    $request = new Request($values);
    $command = new CreateReview($request);
    return $command->isValid()
        ? glsr(ReviewManager::class)->create($command)
        : false;
}

/**
 * @return \WP_Screen|object
 */
function glsr_current_screen()
{
    if (function_exists('get_current_screen')) {
        $screen = get_current_screen();
    }
    return empty($screen)
        ? (object) array_fill_keys(['action', 'base', 'id', 'post_type'], null)
        : $screen;
}

/**
 * @param mixed ...$vars
 * @return void
 */
function glsr_debug(...$vars)
{
    if (1 == count($vars)) {
        $value = htmlspecialchars(print_r($vars[0], true), ENT_QUOTES, 'UTF-8');
        printf('<div class="glsr-debug"><pre>%s</pre></div>', $value);
    } else {
        echo '<div class="glsr-debug-group">';
        foreach ($vars as $var) {
            glsr_debug($var);
        }
        echo '</div>';
    }
}

/**
 * @param array $data
 * @param string $path
 * @param mixed $fallback
 * @return mixed
 */
function glsr_get($array, $path = '', $fallback = '')
{
    return Arr::get($array, $path, $fallback);
}

/**
 * @param string $path
 * @param mixed $fallback
 * @param string $cast
 * @return mixed
 */
function glsr_get_option($path = '', $fallback = '', $cast = '')
{
    return is_string($path)
        ? glsr(OptionManager::class)->get(Str::prefix($path, 'settings.'), $fallback, $cast)
        : $fallback;
}

/**
 * @return array
 */
function glsr_get_options()
{
    return glsr(OptionManager::class)->get('settings');
}

/**
 * @return \GeminiLabs\SiteReviews\Arguments
 */
function glsr_get_ratings($args = [])
{
    $counts = glsr(RatingManager::class)->ratings(Arr::consolidate($args));
    return new Arguments([
        'average' => glsr(Rating::class)->average($counts),
        'maximum' => Cast::toInt(glsr()->constant('MAX_RATING', Rating::class)),
        'minimum' => Cast::toInt(glsr()->constant('MIN_RATING', Rating::class)),
        'ranking' => glsr(Rating::class)->ranking($counts),
        'ratings' => $counts,
        'reviews' => array_sum($counts),
    ]);
}

/**
 * @param int|\WP_Post $postId
 * @return \GeminiLabs\SiteReviews\Review
 */
function glsr_get_review($postId)
{
    return glsr(ReviewManager::class)->get($postId);
}

/**
 * @return \GeminiLabs\SiteReviews\Reviews
 */
function glsr_get_reviews($args = [])
{
    glsr()->sessionSet('glsr_get_reviews', true); // Tell Site Reviews that the helper function was used
    return glsr(ReviewManager::class)->reviews(Arr::consolidate($args));
}

/**
 * @return \GeminiLabs\SiteReviews\Modules\Console
 */
function glsr_log(...$args)
{
    $console = glsr(Console::class);
    return !empty($args)
        ? call_user_func_array([$console, 'debug'], $args)
        : $console;
}

/**
 * @param array $array
 * @param string $path
 * @param mixed $value
 * @return array
 */
function glsr_set(array $data, $path, $value)
{
    return Arr::set($data, $path, $value);
}

/**
 * @param mixed $rating
 * @param int|null $count
 * @return string
 */
function glsr_star_rating($rating, $count = 0, array $args = [])
{
    return glsr(Partial::class)->build('star-rating', [
        'args' => $args,
        'count' => $count,
        'rating' => $rating,
    ]);
}

/**
 * @param int $limit
 * @return void
 */
function glsr_trace($limit = 5)
{
    glsr_log(glsr(Backtrace::class)->trace($limit));
}

/**
 * @param int $postId
 * @return \GeminiLabs\SiteReviews\Review|false
 */
function glsr_update_review($postId, $values = [])
{
    return glsr(ReviewManager::class)->update($postId, Arr::consolidate($values));
}
