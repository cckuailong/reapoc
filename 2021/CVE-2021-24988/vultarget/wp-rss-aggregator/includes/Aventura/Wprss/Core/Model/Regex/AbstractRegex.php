<?php

namespace Aventura\Wprss\Core\Model\Regex;

use Aventura\Wprss\Core;

/**
 * Common regex functionality
 *
 * @since 4.10
 */
class AbstractRegex extends Core\Model\ModelAbstract
{
    const DELIMITER = '!';
    const K_DELIMITER = 'delimiter';
    const REPLACE_LIMIT = -1;
    const K_REPLACE_LIMIT = 'replace_limit';

    /**
     * Escapes all special regex characters in all supplied strings.
     *
     * @since 4.10
     *
     * @param array|string $string A string or array of strings to escape.
     * @param string|null $delimiter The delimiter, which, if specified, will also be quoted.
     *  Default: '!'.
     *
     * @return array All strings escaped for use in regex.
     */
    public function quoteAll($string, $delimiter = null)
    {
        // Default delimiter
        if (is_null($delimiter)) {
            $delimiter = $this->getDelimiter();
        }

        $string = (array)$string;
        $self = $this;
        return array_map(function ($string) use ($delimiter, $self) {
            if (is_array($string)) {
                return $self->quoteAll($string, $delimiter);
            }
            return preg_quote($string, $delimiter);
        }, $string);
    }

    /**
     * Gets the RegEx delimiter used by this instance.
     *
     * @since 4.10
     *
     * @return string
     */
    public function getDelimiter()
    {
        return $this->_getDataOrConst(self::K_DELIMITER);
    }

    /**
     * Get the limit used by this instance's RegEx replacement functions.
     *
     * @since 4.10
     *
     * @return int
     */
    public function getReplaceLimit()
    {
        return intval($this->_getDataOrConst(static::K_REPLACE_LIMIT));
    }

    /**
     * Uses a regex to replace parts of the input string.
     *
     * @since 4.10
     *
     * @param string|array $pattern See {@see preg_replace_callback()}
     * @param callable $callback See {@see preg_replace_callback()}
     * @param string|array $subject See {@see preg_replace_callback()}
     * @param int $limit See {@see preg_replace_callback()}
     * @param int $count See {@see preg_replace_callback()}
     *
     * @return array|string|null See {@see preg_replace_callback()}
     */
    public function replaceCallback($pattern, $callback, $subject, $limit = null, &$count = null)
    {
        if (is_null($limit)) {
            $limit = $this->getReplaceLimit();
        }

        return preg_replace_callback($pattern, $callback, $subject, $limit, $count);
    }

    /**
     * Attempts a RegEx match.
     *
     * @since 4.10
     *
     * @see preg_match()
     *
     * @param string $pattern
     * @param string $subject
     * @param array $matches
     * @param int $flags
     * @param int $offset
     *
     * @return int|bool Returns 1 if the pattern is found, 0 if not found, or false if an error occurred.
     */
    public function match($pattern, $subject, &$matches = null, $flags = 0, $offset = 0)
    {
        return preg_match($pattern, $subject, $matches, $flags, $offset);
    }
}
