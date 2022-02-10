<?php
/**
 * This is an extract of the symfony/polyfill-mbstring package.
 * 
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package symfony/polyfill-mbstring v1.18.1
 */

namespace GeminiLabs\Symfony\Polyfill\Mbstring;

/**
 * Partial mbstring implementation in PHP, iconv based, UTF-8 centric.
 *
 * Implemented:
 * - mb_convert_case         - Perform case folding on a string
 * - mb_strlen               - Get string length
 * - mb_strtolower           - Make a string lowercase
 * - mb_strtoupper           - Make a string uppercase
 * - mb_strwidth             - Return width of string
 * - mb_substr               - Get part of string
 *
 * Not implemented:
 * - mb_chr                  - Returns a specific character from its Unicode code point
 * - mb_convert_encoding     - Convert character encoding
 * - mb_convert_kana         - Convert "kana" one from another ("zen-kaku", "han-kaku" and more)
 * - mb_convert_variables    - Convert character code in variable(s)
 * - mb_decode_mimeheader    - Decode string in MIME header field
 * - mb_decode_numericentity - Decode HTML numeric string reference to character
 * - mb_detect_encoding      - Detect character encoding
 * - mb_encode_mimeheader    - Encode string for MIME header XXX NATIVE IMPLEMENTATION IS REALLY BUGGED
 * - mb_encode_numericentity - Encode character to HTML numeric string reference
 * - mb_ereg_*               - Regular expression with multibyte support
 * - mb_get_info             - Get internal settings of mbstring
 * - mb_http_input           - Detect HTTP input character encoding
 * - mb_http_output          - Set/Get HTTP output character encoding
 * - mb_internal_encoding    - Set/Get internal character encoding
 * - mb_list_encodings       - Returns an array of all supported encodings
 * - mb_ord                  - Returns the Unicode code point of a character
 * - mb_output_handler       - Callback function converts character encoding in output buffer
 * - mb_parse_str            - Parse GET/POST/COOKIE data and set global variable
 * - mb_preferred_mime_name  - Get MIME charset string
 * - mb_regex_encoding       - Returns current encoding for multibyte regex as string
 * - mb_regex_set_options    - Set/Get the default options for mbregex functions
 * - mb_scrub                - Replaces ill-formed byte sequences with substitute characters
 * - mb_send_mail            - Send encoded mail
 * - mb_split                - Split multibyte string using regular expression
 * - mb_str_split            - Convert a string to an array
 * - mb_strcut               - Get part of string
 * - mb_strimwidth           - Get truncated string with specified width
 * - mb_stripos              - Finds position of first occurrence of a string within another, case insensitive
 * - mb_stristr              - Finds first occurrence of a string within another, case insensitive
 * - mb_strpos               - Find position of first occurrence of string in a string
 * - mb_strrchr              - Finds the last occurrence of a character in a string within another
 * - mb_strrichr             - Finds the last occurrence of a character in a string within another, case insensitive
 * - mb_strripos             - Finds position of last occurrence of a string within another, case insensitive
 * - mb_strrpos              - Find position of last occurrence of a string in a string
 * - mb_strstr               - Finds first occurrence of a string within another
 * - mb_substitute_character - Set/Get substitution character
 * - mb_substr_count         - Count the number of substring occurrences
 */
final class Mbstring
{
    const MB_CASE_FOLD = PHP_INT_MAX;

    private static $encodingList = array('ASCII', 'UTF-8');
    private static $language = 'neutral';
    private static $internalEncoding = 'UTF-8';
    private static $caseFold = array(
        array('µ', 'ſ', "\xCD\x85", 'ς', "\xCF\x90", "\xCF\x91", "\xCF\x95", "\xCF\x96", "\xCF\xB0", "\xCF\xB1", "\xCF\xB5", "\xE1\xBA\x9B", "\xE1\xBE\xBE"),
        array('μ', 's', 'ι',        'σ', 'β',        'θ',        'φ',        'π',        'κ',        'ρ',        'ε',        "\xE1\xB9\xA1", 'ι'),
    );

    public static function mb_convert_case($s, $mode, $encoding = null)
    {
        if (!self::isIconvLoaded()) {
            if (MB_CASE_UPPER == $mode) {
                return \strtoupper($s);
            }
            if (MB_CASE_LOWER == $mode) {
                return \strtolower($s);
            }
            return $s;
        }
        $s = (string) $s;
        if ('' === $s) {
            return '';
        }
        $encoding = self::getEncoding($encoding);
        if ('UTF-8' === $encoding) {
            $encoding = null;
            if (!preg_match('//u', $s)) {
                $s = @iconv('UTF-8', 'UTF-8//IGNORE', $s);
            }
        } else {
            $s = iconv($encoding, 'UTF-8//IGNORE', $s);
        }
        if (MB_CASE_TITLE == $mode) {
            static $titleRegexp = null;
            if (null === $titleRegexp) {
                $titleRegexp = self::getData('titleCaseRegexp');
            }
            $s = preg_replace_callback($titleRegexp, array(__CLASS__, 'title_case'), $s);
        } else {
            if (MB_CASE_UPPER == $mode) {
                static $upper = null;
                if (null === $upper) {
                    $upper = self::getData('upperCase');
                }
                $map = $upper;
            } else {
                if (self::MB_CASE_FOLD === $mode) {
                    $s = str_replace(self::$caseFold[0], self::$caseFold[1], $s);
                }
                static $lower = null;
                if (null === $lower) {
                    $lower = self::getData('lowerCase');
                }
                $map = $lower;
            }
            static $ulenMask = array("\xC0" => 2, "\xD0" => 2, "\xE0" => 3, "\xF0" => 4);
            $i = 0;
            $len = \strlen($s);
            while ($i < $len) {
                $ulen = $s[$i] < "\x80" ? 1 : $ulenMask[$s[$i] & "\xF0"];
                $uchr = substr($s, $i, $ulen);
                $i += $ulen;
                if (isset($map[$uchr])) {
                    $uchr = $map[$uchr];
                    $nlen = \strlen($uchr);
                    if ($nlen == $ulen) {
                        $nlen = $i;
                        do {
                            $s[--$nlen] = $uchr[--$ulen];
                        } while ($ulen);
                    } else {
                        $s = substr_replace($s, $uchr, $i - $ulen, $ulen);
                        $len += $nlen - $ulen;
                        $i += $nlen - $ulen;
                    }
                }
            }
        }
        if (null === $encoding) {
            return $s;
        }
        return iconv('UTF-8', $encoding.'//IGNORE', $s);
    }

    public static function mb_strlen($s, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);
        if (!self::isMultibyte($encoding)) {
            return \strlen($s);
        }
        return @iconv_strlen($s, $encoding);
    }

    public static function mb_strtolower($s, $encoding = null)
    {
        return self::mb_convert_case($s, MB_CASE_LOWER, $encoding);
    }

    public static function mb_strtoupper($s, $encoding = null)
    {
        return self::mb_convert_case($s, MB_CASE_UPPER, $encoding);
    }

    public static function mb_strwidth($s, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);
        if (!self::isIconvLoaded()) {
            return \strlen($s);
        }
        if ('UTF-8' !== $encoding) {
            $s = iconv($encoding, 'UTF-8//IGNORE', $s);
        }
        $s = preg_replace('/[\x{1100}-\x{115F}\x{2329}\x{232A}\x{2E80}-\x{303E}\x{3040}-\x{A4CF}\x{AC00}-\x{D7A3}\x{F900}-\x{FAFF}\x{FE10}-\x{FE19}\x{FE30}-\x{FE6F}\x{FF00}-\x{FF60}\x{FFE0}-\x{FFE6}\x{20000}-\x{2FFFD}\x{30000}-\x{3FFFD}]/u', '', $s, -1, $wide);
        return ($wide << 1) + iconv_strlen($s, 'UTF-8');
    }

    public static function mb_substr($s, $start, $length = null, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);
        if (!self::isMultibyte($encoding)) {
            return (string) \substr($s, $start, null === $length ? 2147483647 : $length);
        }
        if ($start < 0) {
            $start = iconv_strlen($s, $encoding) + $start;
            if ($start < 0) {
                $start = 0;
            }
        }
        if (null === $length) {
            $length = 2147483647;
        } elseif ($length < 0) {
            $length = iconv_strlen($s, $encoding) + $length - $start;
            if ($length < 0) {
                return '';
            }
        }
        return (string) iconv_substr($s, $start, $length, $encoding);
    }

    private static function getData($file)
    {
        if (file_exists($file = __DIR__.'/Resources/unidata/'.$file.'.php')) {
            return require $file;
        }
        return false;
    }

    private static function getEncoding($encoding)
    {
        if (null === $encoding) {
            return self::$internalEncoding;
        }
        if ('UTF-8' === $encoding) {
            return 'UTF-8';
        }
        $encoding = strtoupper($encoding);
        if ('8BIT' === $encoding || 'BINARY' === $encoding) {
            return 'CP850';
        }
        if ('UTF8' === $encoding) {
            return 'UTF-8';
        }
        return $encoding;
    }

    private static function isIconvLoaded()
    {
        return extension_loaded('iconv');
    }

    private static function isMultibyte($encoding)
    {
        return self::isIconvLoaded() && !('CP850' === $encoding || 'ASCII' === $encoding);
    }
}
