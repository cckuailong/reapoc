<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * @method int getPost(int|string $postId)
 * @method array getPostIds(array $postIds)
 * @method bool isActive()
 * @method bool isEnabled()
 * @method bool isSupported()
 */
class Multilingual
{
    protected $integration;

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args = [])
    {
        if ($this->isIntegrated() && method_exists($this->integration, $method)) {
            return call_user_func_array([$this->integration, $method], $args);
        }
        return Arr::get($args, 0, false);
    }

    /**
     * @param string $integration
     * @return false|\GeminiLabs\SiteReviews\Modules\Multilingual\Polylang|\GeminiLabs\SiteReviews\Modules\Multilingual\Wpml
     */
    public function getIntegration($integration = '')
    {
        if (empty($integration)) {
            $integration = glsr(OptionManager::class)->get('settings.general.multilingual');
        }
        if (!empty($integration)) {
            $integrationClass = 'GeminiLabs\SiteReviews\Modules\Multilingual\\'.ucfirst($integration);
            if (class_exists($integrationClass)) {
                return glsr($integrationClass);
            }
            glsr_log()->error($integrationClass.' does not exist');
        }
        return false;
    }

    /**
     * return bool
     */
    public function isIntegrated()
    {
        if (!empty($this->integration)) {
            return true;
        }
        if ($integration = $this->getIntegration()) {
            $this->integration = $integration;
            return true;
        }
        return false;
    }
}
