<?php

namespace GeminiLabs\SiteReviews\Modules\Multilingual;

use GeminiLabs\SiteReviews\Contracts\MultilingualContract as Contract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Polylang implements Contract
{
    public $pluginName = 'Polylang';
    public $supportedVersion = '2.3';

    /**
     * {@inheritdoc}
     */
    public function getPostId($postId)
    {
        $postId = trim($postId);
        if (!is_numeric($postId)) {
            return 0;
        }
        if ($this->isEnabled()) {
            $polylangPostId = pll_get_post($postId, pll_get_post_language(get_the_ID()));
        }
        if (!empty($polylangPostId)) {
            $postId = $polylangPostId;
        }
        return intval($postId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPostIds(array $postIds)
    {
        if (!$this->isEnabled()) {
            return $postIds;
        }
        $newPostIds = [];
        foreach (Arr::uniqueInt($postIds) as $postId) {
            $newPostIds = array_merge($newPostIds,
                array_values(pll_get_post_translations($postId))
            );
        }
        return Arr::uniqueInt($newPostIds);
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return function_exists('PLL')
            && function_exists('pll_get_post')
            && function_exists('pll_get_post_language')
            && function_exists('pll_get_post_translations');
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->isActive()
            && 'polylang' == glsr(OptionManager::class)->get('settings.general.multilingual');
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported()
    {
        return defined('POLYLANG_VERSION')
            && Helper::isGreaterThanOrEqual(POLYLANG_VERSION, $this->supportedVersion);
    }
}
