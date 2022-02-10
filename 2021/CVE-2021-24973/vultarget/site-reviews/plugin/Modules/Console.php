<?php

namespace GeminiLabs\SiteReviews\Modules;

use BadMethodCallException;
use DateTime;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Backtrace;
use ReflectionClass;
use Throwable;

/**
 * @method static debug($message, $context = [])
 * @method static info($message, $context = [])
 * @method static notice($message, $context = [])
 * @method static warning($message, $context = [])
 * @method static error($message, $context = [])
 * @method static critical($message, $context = [])
 * @method static alert($message, $context = [])
 * @method static emergency($message, $context = [])
 */
class Console
{
    const DEBUG = 0;      // Detailed debug information
    const INFO = 1;       // Interesting events
    const NOTICE = 2;     // Normal but significant events
    const WARNING = 4;    // Exceptional occurrences that are not errors
    const ERROR = 8;      // Runtime errors that do not require immediate action
    const CRITICAL = 16;  // Critical conditions
    const ALERT = 32;     // Action must be taken immediately
    const EMERGENCY = 64; // System is unusable

    protected $file;
    protected $log;
    protected $logOnceKey = 'glsr_log_once';

    public function __construct()
    {
        $this->setLogFile();
        $this->reset();
    }

    public function __call($method, $args)
    {
        $constant = 'static::'.strtoupper($method);
        if (defined($constant)) {
            $args = Arr::prepend($args, constant($constant));
            return call_user_func_array([$this, 'log'], array_slice($args, 0, 3));
        }
        throw new BadMethodCallException("Method [$method] does not exist.");
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->log = '';
        file_put_contents($this->file, $this->log);
    }

    /**
     * @return string
     */
    public function get()
    {
        return esc_html(
            Helper::ifEmpty($this->log, _x('Console is empty', 'admin-text', 'site-reviews'))
        );
    }

    /**
     * @return string
     */
    public function getRaw()
    {
        return htmlspecialchars_decode($this->get(), ENT_QUOTES);
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return glsr()->filterInt('console/level', static::INFO);
    }

    /**
     * @return array
     */
    public function getLevels()
    {
        $constants = (new ReflectionClass(__CLASS__))->getConstants();
        return array_map('strtolower', array_flip($constants));
    }

    /**
     * @return string
     */
    public function humanLevel()
    {
        $level = $this->getLevel();
        return sprintf('%s (%d)', strtoupper(Arr::get($this->getLevels(), $level, 'unknown')), $level);
    }

    /**
     * @return string
     */
    public function humanSize()
    {
        return Str::replaceLast(' B', ' bytes', Cast::toString(size_format($this->size())));
    }

    /**
     * @param int $level
     * @param mixed $message
     * @param array $context
     * @param string $backtraceLine
     * @return static
     */
    public function log($level, $message, $context = [], $backtraceLine = '')
    {
        if (empty($backtraceLine)) {
            $backtraceLine = glsr(Backtrace::class)->line();
        }
        if ($this->canLogEntry($level, $backtraceLine)) {
            $levelName = Arr::get($this->getLevels(), $level);
            $backtraceLine = glsr(Backtrace::class)->normalizeLine($backtraceLine);
            $message = $this->interpolate($message, $context);
            $entry = $this->buildLogEntry($levelName, $message, $backtraceLine);
            file_put_contents($this->file, $entry.PHP_EOL, FILE_APPEND | LOCK_EX);
            apply_filters('console', $message, $levelName, $backtraceLine); // Show in Blackbar plugin if installed
            $this->reset();
        }
        return $this;
    }

    /**
     * @return void
     */
    public function logOnce()
    {
        $once = Arr::consolidate(glsr()->{$this->logOnceKey});
        $levels = $this->getLevels();
        foreach ($once as $entry) {
            $levelName = Arr::get($entry, 'level');
            if (in_array($levelName, $levels)) {
                $level = Arr::get(array_flip($levels), $levelName);
                $message = Arr::get($entry, 'message');
                $backtraceLine = Arr::get($entry, 'backtrace');
                $this->log($level, $message, [], $backtraceLine);
            }
        }
        glsr()->{$this->logOnceKey} = [];
    }

    /**
     * @param string $levelName
     * @param string $handle
     * @param mixed $data
     * @return void
     */
    public function once($levelName, $handle, $data)
    {
        $once = Arr::consolidate(glsr()->{$this->logOnceKey});
        $filtered = array_filter($once, function ($entry) use ($levelName, $handle) {
            return Arr::get($entry, 'level') == $levelName
                && Arr::get($entry, 'handle') == $handle;
        });
        if (empty($filtered)) {
            $once[] = [
                'backtrace' => glsr(Backtrace::class)->lineFromData($data),
                'handle' => $handle,
                'level' => $levelName,
                'message' => '[RECURRING] '.$this->getMessageFromData($data),
            ];
            glsr()->{$this->logOnceKey} = $once;
        }
    }

    /**
     * @return int
     */
    public function size()
    {
        return file_exists($this->file)
            ? filesize($this->file)
            : 0;
    }

    /**
     * @param string $levelName
     * @param mixed $message
     * @param string $backtraceLine
     * @return string
     */
    protected function buildLogEntry($levelName, $message, $backtraceLine = '')
    {
        return sprintf('[%s] %s [%s] %s',
            current_time('mysql'),
            strtoupper($levelName),
            $backtraceLine,
            $message
        );
    }

    /**
     * @param int $level
     * @return bool
     */
    protected function canLogEntry($level, $backtraceLine)
    {
        $levelExists = array_key_exists($level, $this->getLevels());
        if (!Str::contains(glsr()->path(), $backtraceLine)) {
            return $levelExists; // ignore level restriction if triggered outside of the plugin
        }
        return $levelExists && $level >= $this->getLevel();
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function getMessageFromData($data)
    {
        return $data instanceof Throwable
            ? $this->normalizeThrowableMessage($data->getMessage())
            : print_r($data, true);
    }

    /**
     * Interpolates context values into the message placeholders.
     * @param mixed $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, $context = [])
    {
        $context = Arr::consolidate($context);
        if (!is_scalar($message) || empty($context)) {
            return print_r($message, true);
        }
        $replace = [];
        foreach ($context as $key => $value) {
            $replace['{'.$key.'}'] = $this->normalizeValue($value);
        }
        return strtr($message, $replace);
    }

    /**
     * @param string $message
     * @return string
     */
    protected function normalizeThrowableMessage($message)
    {
        $calledIn = strpos($message, ', called in');
        return false !== $calledIn
            ? substr($message, 0, $calledIn)
            : $message;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function normalizeValue($value)
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif (!is_scalar($value)) {
            $value = json_encode($value);
        }
        return (string) $value;
    }

    /**
     * @return void
     */
    protected function reset()
    {
        if ($this->size() <= wp_convert_hr_to_bytes('512kb')) {
            return;
        }
        $this->clear();
        file_put_contents($this->file,
            $this->buildLogEntry(static::NOTICE,
                _x('Console was automatically cleared (512KB maximum size)', 'admin-text', 'site-reviews')
            )
        );
    }

    /**
     * @return void
     */
    protected function setLogFile()
    {
        require_once ABSPATH.WPINC.'/pluggable.php';
        $uploads = wp_upload_dir();
        if (!file_exists($uploads['basedir'])) {
            $uploads = wp_upload_dir(null, true, true); // maybe the site has been moved, so refresh the cached uploads path
        }
        $base = trailingslashit($uploads['basedir'].'/'.glsr()->id);
        $this->file = $base.'logs/'.sanitize_file_name('console-'.wp_hash(glsr()->id).'.log');
        $files = [
            $base.'index.php' => '<?php',
            $base.'logs/.htaccess' => 'deny from all',
            $base.'logs/index.php' => '<?php',
            $this->file => '',
        ];
        foreach ($files as $file => $contents) {
            if (wp_mkdir_p(dirname($file)) && !file_exists($file)) {
                file_put_contents($file, $contents);
            }
        }
        $this->log = file_get_contents($this->file);
    }
}
