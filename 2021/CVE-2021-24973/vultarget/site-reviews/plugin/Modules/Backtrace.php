<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use Throwable;

class Backtrace
{
    /**
     * @return string
     */
    public function buildLine(array $backtrace)
    {
        return sprintf('%s:%s', $this->getClassName($backtrace), $this->getLineNumber($backtrace));
    }

    /**
     * @param int $limit
     * @return void|string
     */
    public function line($limit = 10)
    {
        return $this->buildLine(array_slice($this->trace($limit), 4));
    }

    /**
     * @param \Throwable|mixed $data
     * @return string
     */
    public function lineFromData($data)
    {
        $backtrace = $data instanceof Throwable
            ? $data->getTrace()
            : debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        return $this->buildLine($backtrace);
    }

    /**
     * @param string $line
     * @return string
     */
    public function normalizeLine($line)
    {
        $search = [
            'GeminiLabs\\SiteReviews\\',
            glsr()->path('plugin/'),
            glsr()->path('plugin/', false),
            trailingslashit(glsr()->path()),
            trailingslashit(glsr()->path('', false)),
            WP_CONTENT_DIR,
            ABSPATH,
        ];
        return str_replace(array_unique($search), '', $line);
    }

    /**
     * @param int $limit
     * @return array
     */
    public function trace($limit = 6)
    {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $limit);
    }

    /**
     * @return string
     */
    protected function getClassName(array $backtrace)
    {
        $file = Arr::get($backtrace, '0.file');
        $class = Arr::get($backtrace, '1.class');
        $search = Arr::searchByKey('glsr_log', $backtrace, 'function');
        if (false !== $search) {
            $class = Arr::get($search, 'class', Arr::get($search, 'file'));
        } elseif (Str::endsWith('helpers.php', $file)) {
            $file = Arr::get($backtrace, '1.file');
            $class = Arr::get($backtrace, '2.class');
        } elseif (Str::endsWith('BlackHole.php', $file) && 'WP_Hook' !== Arr::get($backtrace, '2.class')) {
            $class = Arr::get($backtrace, '2.class');
        }
        return Helper::ifEmpty($class, $file);
    }

    /**
     * @return string
     */
    protected function getLineNumber(array $backtrace)
    {
        $search = Arr::searchByKey('glsr_log', $backtrace, 'function');
        if (false !== $search) {
            return Arr::get($search, 'line');
        }
        $file = Arr::get($backtrace, '0.file');
        $line = Arr::get($backtrace, '0.line');
        if (Str::endsWith('helpers.php', $file)) {
            return Arr::get($backtrace, '1.line');
        }
        elseif (Str::endsWith('BlackHole.php', $file) && 'WP_Hook' !== Arr::get($backtrace, '2.class')) {
            return Arr::get($backtrace, '1.line');
        }
        return $line;
    }
}
