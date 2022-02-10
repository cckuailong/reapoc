<?php

namespace Aventura\Wprss\Core\Model\Regex;

use Aventura\Wprss\Core\Model\Collection;
use Aventura\Wprss\Core\Model\Set;

/**
 * Aids in adaprting regular expressions for use with HTML
 * by HTML-encodifying them.
 * HTML-encodifying transforms a regex in a way that HTML special chars in the expression get replaced
 * with all possibilities, each of which has the same meaning as the special char.
 * For example, encodifying "<div>" would produce "(?:\<|&lt;)div(?:\<|&lt;)", which would match both
 * the text "<div>" and the text "&lt;div&lt;", where the latter is the HTML-encoded version
 * of the original text.
 * While quotes are transformed in the same way, but by default they are considered do be "symmetrical",
 * i.e. an "opening" quote must be matched with an identical "closing" quote.
 *
 * @since 4.10
 */
class HtmlEncoder extends AbstractRegex
{
    const K_SYM_PREFIX = 'sym_prefix';
    const SYM_PREFIX = '_sym';
    const CHARC_TOKEN_PREFIX = '_charc';

    protected $synonymSets;
//    protected $htmlCharMap = null;
    protected $symChars = array();

    protected $occurrences = array();
    protected $replacementCount = 0;

    /**
     * @since 4.10
     */
    protected function _construct()
    {
        parent::_construct();
        $this->reset();

        $this->addHtmlChars(array('<', '>', '\''));
        $this->addHtmlCharSynonyms('"', array("'", '&#039;', '&apos;'));
        $this->addSymmetricalChars('"');
    }

    /**
     * Resets usage stats.
     *
     * @since 4.10
     *
     * @see getOccurrenceCount()
     * @see getReplacementCount()
     *
     * @return HtmlEncoder
     */
    public function reset()
    {
        $this->occurrences = array();
        $this->replacementCount = 0;

        return $this;
    }

    /**
     * @since 4.10
     *
     * @param string $expr The regex to encodify.
     *
     * @return string The regular expression that has been HTML-encodified.
     */
    public function encodify($expr)
    {
        $delimiter = $this->getDelimiter();
        $symPrefix = $this->getSymPrefix();
        $chars = $this->getSynonymSets();
        $symmetrical = $this->getSymmetricalChars();
        return $this->htmlEncodifyRegex($expr, $chars, $delimiter, $symmetrical, $symPrefix);
    }

    /**
     * Retrieve the prefix that is used to introduced named groups.
     *
     * Currently, named groups are used for matching symmetric quotes.
     *
     * @since 4.10
     *
     * @return string The group name prefix.
     */
    public function getSymPrefix()
    {
        return $this->_getDataOrConst(self::K_SYM_PREFIX);
    }

    /**
     * Retrieve all symmetrical chars.
     *
     * @since 4.10
     *
     * @return array Array of strings, each one being a char that must be matched in pairs by the transformed regex.
     */
    public function getSymmetricalChars()
    {
        return $this->symChars;
    }

    /**
     * Adds chars that must be symmetrical, e.g. must me matched in pairs by the transformed regex
     *
     * @since 4.10
     *
     * @param string|array $chars A char, string of chars, or array of chars, each of which to add.
     *
     * @return HtmlEncoder This instance.
     */
    public function addSymmetricalChars($chars)
    {
        $chars = $this->normalizeCharArray($chars);

        $chars = array_merge($this->symChars, $chars);
        $chars = array_keys(array_flip($chars));
        $this->_setSymmetricalChars($chars);

        return $this;
    }

    /**
     * @since 4.10
     *
     * @param type $chars
     *
     * @return HtmlEncoder
     */
    protected function _setSymmetricalChars($chars)
    {
        $this->symChars = (array) $chars;
        return $this;
    }

    /**
     * Retrieves the set of synonym sets used by this instance.
     *
     * Each inner set contains characters or sequences, all of which are synonymous with each other.
     *
     * @since 4.10
     *
     * @return Set\Synonym\Set The set of synonym sets.
     */
    public function getSynonymSets()
    {
        if (is_null($this->synonymSets)) {
            $this->_setSynonymSets(new Set\Synonym\Set());
        }

        return $this->synonymSets;
    }

    /**
     * Assigns the set of synonym sets.
     *
     * @since 4.10
     *
     * @param Set\Synonym\Set $set The synonym set.
     *
     * @return HtmlEncoder This instance.
     */
    protected function _setSynonymSets(Set\Synonym\Set $set)
    {
        $this->synonymSets = $set;

        return $this;
    }

    /**
     * Checks whether or not a char is registered as being an HTML char.
     *
     * @since 4.10
     *
     * @param string $char The char to check for.
     *
     * @return boolean True if this HTML char is registered; false otherwise.
     */
    public function hasHtmlChar($char)
    {
        $char = $this->normalizeChar($char);
        return $this->_hasHtmlChar($char);
    }

    /**
     * Determines if this instance's char map contains the specified character.
     *
     * @since 4.10
     *
     * @param string $char The character to check.
     *
     * @return bool True if the char map has the specified character; false otherwise.
     */
    protected function _hasHtmlChar($char)
    {
        return isset($this->htmlCharMap[$char]);
    }

    /**
     * Get the set of HTML char synonyms for a specific character.
     *
     * @see [*next-char*]
     *
     * @param string $char The char, for which to get the synonym set.
     *
     * @return Set\Synonym\Set The synonym set. This set might only contain the original char.
     */
    public function getHtmlCharSynonymSet($char)
    {
        $char = $this->normalizeChar($char);
        return $this->getSynonymSets()->getSetForTerm($char);
    }

    /**
     * Retrieve synonyms for the specified character.
     *
     * @since 4.10
     *
     * @param string $char The character, for which to get synonyms.
     * @param array $other Additional characters to include in the result.
     *
     * @return An array of unique characters or sequences, all of which are synonymous with each other.
     *  Contains the original character.
     */
    public function getHtmlCharSynonyms($char, $other = null)
    {
        if (is_null($other)) {
            $other = array();
        }

        $set = $this->getHtmlCharSynonymSet($char);
        $synonyms = $set->items();

        return array_keys(array_flip(array_merge($synonyms, $other)));
    }

    /**
     * Adds synonyms to a specified character.
     *
     * If the character does not exist, it will be added.
     *
     * @since 4.10
     *
     * @param string $char The character, for which to add synonyms.
     *
     * @param array $synonyms The synonyms to add.
     * @return HtmlEncoder This instance.
     */
    public function addHtmlCharSynonyms($char, $synonyms)
    {
        $set = $this->getHtmlCharSynonymSet($char);
        $set->addMany($synonyms);

        return $this;
    }

    /**
     * Removes a character from the set of chars to encodify.
     *
     * @since 4.10
     *
     * @todo This method may be redundant or broken. Verify.
     *
     * @param string $char The character to remove.
     * @return HtmlEncoder This instance.
     */
    public function removeHtmlChar($char)
    {
        $synonyms = array_flip($this->getHtmlCharSynonyms($char));
        if (array_key_exists($char, $synonyms)) {
            unset($synonyms[$char]);
        }
        $synonyms = array_keys($synonyms);
        $this->_mapHtmlChar($char, $synonyms);

        return $this;
    }

    /**
     * Adds a character to be encodified.
     *
     * @since 4.10
     *
     * @todo This method may be incomplete. Confirm and fix.
     *
     * @param string $char The character.
     *
     * @return HtmlEncoder This instance.
     */
    public function addHtmlChar($char, $synonyms = null)
    {
        $this->getHtmlCharSynonymSet($char);

        return $this;
    }

    /**
     * Adds multiple HTML characters to be encodified.
     *
     * @since 4.10
     *
     * @param string[] $chars The characters to be added.
     *
     * @return HtmlEncoder
     */
    public function addHtmlChars($chars)
    {
        foreach ($chars as $_char) {
            $this->getHtmlCharSynonymSet($_char);
        }

        return $this;
    }

    /**
     * Normalizes input to a single character.
     *
     * If array, uses the first element.
     * If string, uses the first character.
     *
     * @since 4.10
     *
     * @param type $char
     *
     * @return string A string of exactly 1 character in length.
     * @throws \Aventura\Wprss\Core\Exception If input could not be normalized.
     */
    public function normalizeChar($char)
    {
        if (is_array($char)) {
            if (isset($char[0])) {
                return $char[0];
            }
            throw $this->exception('Could not normalize char: input is array, but index 0 is empty');
        }
        return substr($char, 0, 1);
    }

    /**
     * Normalizes input to an array of characters.
     *
     * @since 4.10
     *
     * @param string|array $chars The character set to normalize.
     *
     * @return string[] An array of 1-char long strings.
     */
    public function normalizeCharArray($chars)
    {
        if (is_array($chars)) {
            return $chars;
        }
        return str_split($chars);
    }

    /**
     * @access protected
     * @since 4.10
     *
     * @param string $char
     * @return HtmlEncoder
     */
    public function _incrementOccurrence($char)
    {
        $char = $this->normalizeChar($char);
        $current = $this->getOccurrenceCount($char);
        $this->occurrences[$char] = ++$current;

        return $this;
    }

    /**
     * Retrieve the number of occurrences of the specified encodified character since last reset, or since instantiation.
     *
     * @since 4.10
     *
     * @param string $char The char to retrieve the occurrence count for.
     * @return int The number of occurrences.
     */
    public function getOccurrenceCount($char)
    {
        return isset($this->occurrences[$char])
            ? (int) $this->occurrences[$char]
            : 0;
    }

    /**
     * @access protected
     * @since 4.10
     */
    public function _incrementReplacement()
    {
        $this->replacementCount++;
    }

    /**
     * Retrieve the total number of replacements of encodifiable characters performed since last reset, or since instantiation.
     *
     * @since 4.10
     *
     * @return int
     */
    public function getReplacementCount()
    {
        return $this->replacementCount;
    }

    /**
     * Get auto-generated synonyms for the specified character.
     *
     * @since 4.10
     *
     * @param string $char The character to get synonyms for.
     *
     * @return string[] The array of synonyms, excluding the original character.
     */
    public function getAutoSynonyms($char)
    {
        return array(htmlspecialchars($char, /* ENT_HTML401 | */ ENT_QUOTES));
    }

    /**
     * Replaces character classes in a regex expression with a token, and remembers the values.
     *
     * Caters for escaped "\[" and "\]";
     *
     * @since 4.10
     *
     * @param string $string The regex expression.
     * @param array $classes This will be populated with a token-value map, where value is the character class that was replaced by token.
     *
     * @return string The string with character classes replaced by tokens.
     */
    protected function _extractCharClass($string, &$classes = array())
    {
        // Matches "[.*]", and allows escaping "\]"
        $expr = '(?<!\\\)\[.*(?<!\\\)\]';
        $prefix = static::CHARC_TOKEN_PREFIX;
        $index = 0;
        $string = $this->replaceCallback('~' . $expr . '~U', function($matches) use ($prefix, &$classes, &$index)
        {
            $replacement = $prefix . $index;
            $classes[$replacement] = $matches[0];
            $index++;

            return $replacement;
        }, $string);

        return $string;
    }

    /**
     * Puts the char classes back in place of their corresponding tokens.
     *
     * @since 4.10
     *
     * @see _extractCharClass()
     * @param string $string The regular expression.
     * @param array $classes The token-value map, where value is the char class definition.
     *
     * @return string The regular expression with tokens replaced by char classes from map.
     */
    protected function _injectCharClass($string, $classes)
    {
        return str_replace(array_keys($classes), array_values($classes), $string);
    }

    /**
     * HTML-encodify a regex expresion to match HTML-encoded variants of the strings.
     *
     * @since 4.10
     *
     * @param string $expr The expression to HTML-encodify.
     * @param Set\Synonym\Set $synonymSets A set of synonym sets.
     *  Each char in any of the synonym sets will be encodified.
     * @param string|null $delimiter The delimiter used by the regex. Default: ! (exclamation mark).
     * @param array|string|null $symmetricChars Chars that come in pairs
     *
     * @return type
     */
    public function htmlEncodifyRegex($expr, $synonymSets, $delimiter, $symmetricChars, $symPrefix)
    {
        $chars = iterator_to_array($synonymSets->getAllItems());
        $chars = $this->quoteAll($chars, $delimiter);
        // Preserving character classes
        $classes = array();
        $expr = $this->_extractCharClass($expr, $classes);
        $charsRegex = sprintf('%2$s(%1$s)%2$s', implode('|', $chars), $delimiter);
        $me = $this;

        $result = $this->replaceCallback(
            $charsRegex, // Replace potentially encoded symbols that have significance in HTML
            function($matches) use ($delimiter, $symmetricChars, $symPrefix, $synonymSets, $me) {
                if (!isset($matches[0])) {
                    return null;
                }
                $char = $matches[0]; // Only one character will match
                /* The return value should be a regular expression that
                 * represents the HTML-significant char and all of its
                 * HTML-encoded alternatives.
                 */
                $me->_incrementOccurrence($char);
                $currentOccurrence = $me->getOccurrenceCount($char);
                $synonymSet = $synonymSets->getSetForTerm($char);
                $synonyms = $synonymSet->getSynonyms($char);
                $synonyms = array_merge(array($char), $synonyms, $me->getAutoSynonyms($char));
                $synonyms = array_unique($synonyms);
                $synonyms = $me->quoteAll($synonyms, $delimiter);

                // Dealing with symmetric chars
                $return = '';
                $isSymmetric = $synonymSet->hasOneOf($symmetricChars);
                if ($isSymmetric && $currentOccurrence) {
                    // if even occurrence, replace with lookbehind of the previous occurrence
                    $return = $currentOccurrence % 2
                        ? sprintf('(?<%2$s%3$s>%1$s)', implode('|', $synonyms), $symPrefix, $currentOccurrence)
                        : sprintf('\\g{%1$s%2$s}', $symPrefix, $currentOccurrence-1);
                }
                else {
                    // The final substitute regex, which by default is non-capturing
                    $return = sprintf('(?:%1$s)', implode('|', $synonyms));
                }

                $me->_incrementReplacement();
                return $return;
            },
            $expr);

        // Restoring character classes
        $result = $this->_injectCharClass($result, $classes);

        return $result;
    }

    /**
     * Cleans an array of matches from symmetric char matches.
     *
     * Those matches are added as a result of HTML-encodifying a regular expressions,
     * and may cause desired matches to not appear at their normal indexes.
     *
     * @since 4.10
     *
     * @param array $matches The matches array to clean.
     * @param string $symPrefix The symmetric char match name prefix used by the matcher function.
     *
     * @return array The matches array with symmetrical character matches removed.
     */
    public function cleanMatches(array $matches, $symPrefix = null)
    {
        if (is_null($symPrefix)) {
            $symPrefix = $this->getSymPrefix();
        }

        if (count($matches) === 0) {
            return $matches;
        }

        /**
         * Iterating through all matches.
         * http://php.net/manual/en/control-structures.foreach.php#88578
         */
        reset($matches);
        do {
            $key = key($matches);
            $value = current($matches);
            // Is this a symmetrical char match key?
            if (!$this->isSymKey($key, $symPrefix)) {
                continue;
            }

            $keys = array_keys($matches);
            // The index of the current key
            $keyIndex = array_search($key, $keys, true);
            // The next key
            $nextKey = $keys[$keyIndex+1];

            if (array_key_exists($nextKey, $matches)) {
                // Remove the numeric copy of the symmetric char match
                unset($matches[$nextKey]);
            }
            // Remove the symmetric char match
            unset($matches[$key]);
        } while (next($matches) !== false && key($matches) !== null);

        $matches = array_merge($matches);
        return $matches;
    }

    /**
     * Determines is a key could represent a symmetric character synonym group.
     *
     * @since 4.10
     *
     * @see getSymPrefix()
     *
     * @param string $key The key to check.
     * @param string|null $symPrefix The prefix to check for. Default: default prefix.
     * @return bool True if the specified key could be a sym key; false otherwise.
     */
    public function isSymKey($key, $symPrefix = null)
    {

        if (is_null($symPrefix)) {
            $symPrefix = $this->getSymPrefix();
        }

        return $this->_isStringStartsWith($key, $symPrefix);
    }

    /**
     * Determines whether a string starts with the specified prefix.
     *
     * @since 4.10
     *
     * @param string $string The string to check.
     * @param string $start The prefix to check for.
     * @return bool True if the specified string starts with the specified prefix; false otherwise.
     */
    protected function _isStringStartsWith($string, $start)
    {
        $startLength = strlen($start);
        return ($actualStart = substr($string, 0, $startLength)) === $start;
    }
}
