<?php

namespace GeminiLabs\SiteReviews;

use BadMethodCallException;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use ReflectionClass;

/**
 * @property string $id
 * @property string $name
 * @method array filterArray($hook, ...$args)
 * @method bool filterBool($hook, ...$args)
 * @method float filterFloat($hook, ...$args)
 * @method int filterInt($hook, ...$args)
 * @method object filterObject($hook, ...$args)
 * @method string filterString($hook, ...$args)
 */
trait Plugin
{
    /**
     * @var static|null
     */
    protected static $instance;

    protected $file;
    protected $languages;
    protected $testedTo;
    protected $version;

    public function __call($method, $args)
    {
        $isFilter = Str::startsWith('filter', $method);
        $to = Helper::buildMethodName(Str::removePrefix($method, 'filter'), 'to');
        if ($isFilter && method_exists(Cast::class, $to)) {
            $filtered = call_user_func_array([$this, 'filter'], $args);
            return Cast::$to($filtered);
        }
        throw new BadMethodCallException("Method [$method] does not exist.");
    }

    public function __construct()
    {
        $file = wp_normalize_path((new ReflectionClass($this))->getFileName());
        $this->file = str_replace('plugin/Application', $this->id, $file);
        $plugin = get_file_data($this->file, [
            'languages' => 'Domain Path',
            'name' => 'Plugin Name',
            'testedTo' => 'Tested up to',
            'version' => 'Version',
        ], 'plugin');
        array_walk($plugin, function ($value, $key) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        });
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        $constant = 'static::'.strtoupper($property);
        if (defined($constant)) {
            return constant($constant);
        }
    }

    /**
     * @param string $hook
     * @param mixed ...$args
     * @return void
     */
    public function action($hook, ...$args)
    {
        do_action_ref_array($this->id.'/'.$hook, $args);
    }

    /**
     * @param mixed $args
     * @return Arguments
     */
    public function args($args = [])
    {
        return new Arguments($args);
    }

    /**
     * @param string $view
     * @return string
     */
    public function build($view, array $data = [])
    {
        ob_start();
        $this->render($view, $data);
        return ob_get_clean();
    }

    /**
     * @return void
     */
    public function catchFatalError()
    {
        $error = error_get_last();
        if (E_ERROR === Arr::get($error, 'type') && Str::contains($this->path(), Arr::get($error, 'message'))) {
            glsr_log()->error($error['message']);
        }
    }

    /**
     * @param string $name
     * @param bool $filtered
     * @return array
     */
    public function config($name, $filtered = true)
    {
        $path = $this->filterString('config', 'config/'.$name.'.php');
        $configFile = $this->path($path);
        $config = file_exists($configFile)
            ? include $configFile
            : [];
        return $filtered
            ? $this->filterArray('config/'.$name, $config)
            : $config;
    }

    /**
     * @param string $property
     * @return string
     */
    public function constant($property, $className = 'static')
    {
        $property = strtoupper($property);
        $constant = $className.'::'.$property;
        return defined($constant)
            ? $this->filterString('const/'.$property, constant($constant))
            : '';
    }

    /**
     * @param string $view
     * @return void|string
     */
    public function file($view)
    {
        $view .= '.php';
        $filePaths = [];
        if (Str::startsWith('templates/', $view)) {
            $filePaths[] = $this->themePath(Str::removePrefix($view, 'templates/'));
        }
        $filePaths[] = $this->path($view);
        $filePaths[] = $this->path('views/'.$view);
        foreach ($filePaths as $file) {
            if (file_exists($file)) {
                return $file;
            }
        }
    }

    /**
     * @param string $hook
     * @param mixed ...$args
     * @return mixed
     */
    public function filter($hook, ...$args)
    {
        return apply_filters_ref_array($this->id.'/'.$hook, $args);
    }

    /**
     * @param string $hook
     * @param mixed ...$args
     * @return array
     */
    public function filterArrayUnique($hook, ...$args)
    {
        $filtered = apply_filters_ref_array($this->id.'/'.$hook, $args);
        return array_unique(array_filter(Cast::toArray($filtered)));
    }

    /**
     * @return static
     */
    public static function load()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param string $file
     * @return string
     */
    public function path($file = '', $realpath = true)
    {
        $path = plugin_dir_path($this->file);
        if (!$realpath) {
            $path = trailingslashit(WP_PLUGIN_DIR).basename(dirname($this->file));
        }
        $path = trailingslashit($path).ltrim(trim($file), '/');
        return $this->filterString('path', $path, $file);
    }

    /**
     * @param string $view
     * @return void
     */
    public function render($view, array $data = [])
    {
        $view = $this->filterString('render/view', $view, $data);
        $file = $this->filterString('views/file', $this->file($view), $view, $data);
        if (!file_exists($file)) {
            glsr_log()->error(sprintf('File not found: (%s) %s', $view, $file));
            return;
        }
        $data = $this->filterArray('views/data', $data, $view);
        extract($data);
        include $file;
    }

    /**
     * @param mixed $args
     * @return Request
     */
    public function request($args = [])
    {
        return new Request($args);
    }

    /**
     * @param string $className
     * @return mixed|false
     */
    public function runIf($className, ...$args)
    {
        return class_exists($className)
            ? call_user_func_array([glsr($className), 'handle'], $args)
            : false;
    }

    /**
     * @param string $file
     * @return string
     */
    public function themePath($file = '')
    {
        return get_stylesheet_directory().'/'.$this->id.'/'.ltrim(trim($file), '/');
    }

    /**
     * @param string $path
     * @return string
     */
    public function url($path = '')
    {
        $url = esc_url(plugin_dir_url($this->file).ltrim(trim($path), '/'));
        return $this->filterString('url', $url, $path);
    }

    /**
     * @param string $versionLevel
     * @return string
     */
    public function version($versionLevel = '')
    {
        $pattern = '/^v?(\d{1,5})(\.\d++)?(\.\d++)?(.+)?$/i';
        preg_match($pattern, $this->version, $matches);
        switch ($versionLevel) {
            case 'major':
                $version = Arr::get($matches, 1);
                break;
            case 'minor':
                $version = Arr::get($matches, 1).Arr::get($matches, 2);
                break;
            case 'patch':
                $version = Arr::get($matches, 1).Arr::get($matches, 2).Arr::get($matches, 3);
                break;
        }
        return empty($version)
            ? $this->version
            : $version;
    }
}
