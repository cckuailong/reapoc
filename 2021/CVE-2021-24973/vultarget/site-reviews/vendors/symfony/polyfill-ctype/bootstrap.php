<?php

/**
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package symfony/polyfill-ctype v1.22.1
 */

use GeminiLabs\Symfony\Polyfill\Ctype as p;

if (!function_exists('ctype_digit')) {
    function ctype_digit($text) { return p\Ctype::ctype_digit($text); }
}
if (!function_exists('ctype_lower')) {
    function ctype_lower($text) { return p\Ctype::ctype_lower($text); }
}
