<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Text
{
    /**
     * @param string $text
     * @param int $limit
     * @param bool $splitWords
     * @return string
     */
    public static function excerpt($text, $limit = 55, $splitWords = true)
    {
        $text = static::normalize($text);
        $splitLength = $limit;
        if ($splitWords) {
            $splitLength = extension_loaded('intl')
                ? static::excerptIntlSplit($text, $limit)
                : static::excerptSplit($text, $limit);
        }
        $hiddenText = mb_substr($text, $splitLength);
        if (!empty($hiddenText)) {
            $showMore = glsr(Builder::class)->span($hiddenText, [
                'class' => 'glsr-hidden glsr-hidden-text',
                'data-show-less' => __('Show less', 'site-reviews'),
                'data-show-more' => __('Show more', 'site-reviews'),
                'data-trigger' => glsr_get_option('reviews.excerpts_action') ?: 'excerpt',
            ]);
            $text = ltrim(mb_substr($text, 0, $splitLength)).$showMore;
        }
        $text = nl2br($text);
        $text = wptexturize($text);
        $text = preg_replace('/(\v|\s){1,}/u', ' ', $text); // replace all multiple-space and carriage return characters with a space
        return $text;
    }

    /**
     * @param string $text
     * @return string
     */
    public static function name($text)
    {
        return Str::convertName($text,
            glsr_get_option('reviews.name.format'),
            glsr_get_option('reviews.name.initial')
        );
    }

    /**
     * @param string $text
     * @return string
     */
    public static function normalize($text)
    {
        $allowedHtml = wp_kses_allowed_html();
        $allowedHtml['mark'] = []; // allow using the <mark> tag to highlight text
        $text = wp_kses($text, $allowedHtml);
        $text = strip_shortcodes($text);
        $text = excerpt_remove_blocks($text); // just in case...
        $text = convert_smilies($text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = preg_replace('/(\v){2,}/u', '$1', $text);
        return $text;
    }

    /**
     * @param string $text
     * @return string
     */
    public static function text($text)
    {
        $text = static::normalize($text);
        $text = nl2br($text);
        $text = wptexturize($text);
        $text = preg_replace('/(\v|\s){1,}/u', ' ', $text); // replace all multiple-space and carriage return characters with a space
        return $text;
    }

    /**
     * @param string $text
     * @param int $limit
     * @return int
     */
    protected static function excerptIntlSplit($text, $limit)
    {
        $words = \IntlRuleBasedBreakIterator::createWordInstance('');
        $words->setText($text);
        $count = 0;
        foreach ($words as $offset) {
            if (\IntlRuleBasedBreakIterator::WORD_NONE === $words->getRuleStatus()) {
                continue;
            }
            ++$count;
            if ($count != $limit) {
                continue;
            }
            return $offset;
        }
        return strlen($text);
    }

    /**
     * @param string $text
     * @param int $limit
     * @return int
     */
    protected static function excerptSplit($text, $limit)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = array_keys(str_word_count($text, 2));
            return $words[$limit];
        }
        return strlen($text);
    }
}
