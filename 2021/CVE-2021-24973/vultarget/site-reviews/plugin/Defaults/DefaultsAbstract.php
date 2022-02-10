<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Contracts\DefaultsContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use ReflectionClass;
use ReflectionException;

/**
 * @method array dataAttributes(array $values = [])
 * @method array defaults():
 * @method array filter(array $values = [])
 * @method array merge(array $values = [])
 * @method array restrict(array $values = [])
 * @method array unguardedDataAttributes(array $values = [])
 * @method array unguardedDefaults():
 * @method array unguardedFilter(array $values = [])
 * @method array unguardedMerge(array $values = [])
 * @method array unguardedRestrict(array $values = [])
 */
abstract class DefaultsAbstract implements DefaultsContract
{
    /**
     * The values that should be cast.
     * @var array
     */
    public $casts = [];

    /**
     * The values that should be concatenated.
     * @var array
     */
    public $concatenated = [];

    /**
     * The keys which should be restricted to specific values
     * @return array
     * @todo Not yet implemented!
     */
    public $enum = [];

    /**
     * The values that should be guarded.
     * @var array
     */
    public $guarded = [];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are sanitized!
     * Note: Mapped keys should not be included in the defaults!
     * @var array
     */
    public $mapped = [];

    /**
     * The values that should be sanitized.
     * @var array
     */
    public $sanitize = [];

    /**
     * The methods that are callable.
     * @var array
     */
    protected $callable = [
        'dataAttributes', 'defaults', 'filter', 'merge', 'restrict',
    ];

    /**
     * @var string
     */
    protected $called;

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * The string that should be used for concatenation.
     * @var string
     */
    protected $glue = '';

    /**
     * @var string
     */
    protected $hook;

    /**
     * @var string
     */
    protected $method;

    public function __construct()
    {
        $hook = 'defaults/'.$this->currentHook().'/defaults';
        $this->defaults = $this->app()->filterArray($hook, $this->defaults());
    }

    /**
     * @param string $name
     * @return array
     */
    public function __call($name, array $args = [])
    {
        $this->called = $name;
        $this->method = Helper::buildMethodName(Str::removePrefix($name, 'unguarded'));
        $values = $this->normalize(Arr::consolidate(array_shift($args)));
        $values = $this->mapKeys($values);
        array_unshift($args, $values);
        if (in_array($this->method, $this->callable)) { // this also means that the method exists
            return $this->callMethod($args);
        }
        glsr_log()->error("Invalid method [$this->method].");
        return $args;
    }

    /**
     * @return \GeminiLabs\SiteReviews\Application|\GeminiLabs\SiteReviews\Addons\Addon
     */
    protected function app()
    {
        return glsr();
    }

    protected function callMethod(array $args)
    {
        $this->hook = $this->currentHook();
        $this->app()->action('defaults', $this, $this->hook, $this->method);
        $values = 'defaults' === $this->method
            ? $this->defaults // use the filtered defaults (these have not been normalized!)
            : call_user_func_array([$this, $this->method], $args);
        if ('dataAttributes' !== $this->method) {
            $values = $this->sanitize($values);
            $values = $this->guard($values);
        }
        $args = array_shift($args);
        return $this->app()->filterArray('defaults/'.$this->hook, $values, $this->method, $args);
    }

    /**
     * @return string
     */
    protected function currentHook()
    {
        $hookName = (new ReflectionClass($this))->getShortName();
        $hookName = Str::replaceLast('Defaults', '', $hookName);
        return Str::dashCase($hookName);
    }

    /**
     * @return string
     */
    protected function concatenate($key, $value)
    {
        if (in_array($key, $this->property('concatenated'))) {
            $default = glsr()->args($this->defaults)->$key;
            return trim($default.$this->glue.$value);
        }
        return $value;
    }

    /**
     * Restrict provided values to defaults, remove empty and unchanged values,
     * and return data attribute keys with JSON encoded values.
     * @return array
     */
    protected function dataAttributes(array $values = [])
    {
        $defaults = $this->flattenArrayValues($this->defaults);
        $values = $this->flattenArrayValues(shortcode_atts($defaults, $values));
        $filtered = array_filter(array_diff_assoc($values, $defaults));  // remove all empty values
        $filtered = $this->sanitize($filtered);
        $filtered = $this->guard($filtered); // this after sanitize for a more unique id
        $filteredJson = [];
        foreach ($filtered as $key => $value) {
            $filteredJson['data-'.$key] = !is_scalar($value)
                ? json_encode((array) $value, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : $value;
        }
        return $filteredJson;
    }

    /**
     * The default values.
     * @return array
     */
    protected function defaults()
    {
        return [];
    }

    /**
     * Remove empty values from the provided values and merge with the defaults.
     * @return array
     */
    protected function filter(array $values = [])
    {
        return $this->merge(array_filter($values, Helper::class.'::isNotEmpty'));
    }

    /**
     * @return array
     */
    protected function flattenArrayValues(array $values)
    {
        array_walk($values, function (&$value) {
            if (is_array($value)) {
                $value = implode(',', array_filter($value, 'is_scalar'));
            }
        });
        return $values;
    }

    /**
     * Remove guarded keys from the provided values.
     * @return array
     */
    protected function guard(array $values)
    {
        if (!Str::startsWith('unguarded', $this->called)) {
            return array_diff_key($values, array_flip($this->property('guarded')));
        }
        return $values;
    }

    /**
     * Map old or deprecated keys to new keys.
     * @return array
     */
    protected function mapKeys(array $args)
    {
        foreach ($this->property('mapped') as $old => $new) {
            if (!empty($args[$old])) { // old always takes precedence
                $args[$new] = $args[$old];
            }
            unset($args[$old]);
        }
        return $args;
    }

    /**
     * Merge provided values with the defaults.
     * @return array
     */
    protected function merge(array $values = [])
    {
        return $this->parse($values, $this->defaults);
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        return $values;
    }

    /**
     * @param mixed $values
     * @param mixed $defaults
     * @return array
     */
    protected function parse($values, $defaults)
    {
        $values = Cast::toArray($values);
        if (!is_array($defaults)) {
            return $values;
        }
        $parsed = $defaults;
        foreach ($values as $key => $value) {
            if (!is_scalar($value) && isset($parsed[$key])) {
                $parsed[$key] = Arr::unique($this->parse($value, $parsed[$key])); // does not reindex
                continue;
            }
            $parsed[$key] = $this->concatenate($key, $value);
        }
        return $parsed;
    }

    /**
     * @param mixed $values
     * @return array
     */
    protected function parseRestricted($values)
    {
        $values = Cast::toArray($values);
        $parsed = [];
        foreach ($this->defaults as $key => $default) {
            if (!array_key_exists($key, $values)) {
                $parsed[$key] = $default;
                continue;
            }
            if (is_array($default)) { // if the default value is supposed to be an array
                $parsed[$key] = $this->parse($values[$key], $default);
                continue;
            }
            $parsed[$key] = $this->concatenate($key, $values[$key]);
        }
        return $parsed;
    }

    /**
     * @return array|void
     */
    protected function property($key)
    {
        try {
            $reflection = new ReflectionClass($this);
            $property = $reflection->getProperty($key);
            $value = $property->getValue($this);
            if ($property->isPublic()) { // all public properties are expected to be an array
                $hook = 'defaults/'.$this->hook.'/'.$key;
                return $this->app()->filterArray($hook, $value, $this->method);
            }
        } catch (ReflectionException $e) {
            glsr_log()->error("Invalid or protected property [$key].");
        }
    }

    /**
     * Merge the provided values with the defaults and remove any non-default keys.
     * @return array
     */
    protected function restrict(array $values = [])
    {
        return $this->parseRestricted($values);
    }

    /**
     * @return array
     */
    protected function sanitize(array $values = [])
    {
        foreach ($this->property('casts') as $key => $cast) {
            if (array_key_exists($key, $values)) {
                $values[$key] = Cast::to($cast, $values[$key]);
            }
        }
        return (new Sanitizer($values, $this->property('sanitize')))->run();
    }

    /**
     * @return array
     */
    protected function unmapKeys(array $args)
    {
        foreach ($this->property('mapped') as $old => $new) {
            if (array_key_exists($new, $args)) {
                $args[$old] = $args[$new];
                unset($args[$new]);
            }
        }
        return $args;
    }
}
