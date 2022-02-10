<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Style;

class Honeypot
{
    /**
     * @param string $formId
     * @return string
     */
    public function build($formId)
    {
        $honeypot = new Field([
            'class' => 'glsr-input glsr-input-text',
            'label' => esc_html__('Your review', 'site-reviews'),
            'name' => $this->hash($formId),
            'type' => 'text',
        ]);
        $honeypot->id = $honeypot->id.'-'.$formId;
        return glsr(Builder::class)->div([
            'class' => glsr(Style::class)->classes('field'),
            'style' => 'display:none;',
            'text' => $honeypot->getFieldLabel().$honeypot->getField(),
        ]);
    }

    /**
     * @param string $formId
     * @return string
     */
    public function hash($formId)
    {
        require_once ABSPATH.WPINC.'/pluggable.php';
        return substr(wp_hash($formId, 'nonce'), -12, 8);
    }

    /**
     * @param string $hash
     * @param string $formId
     * @return bool
     */
    public function verify($hash, $formId)
    {
        return hash_equals($this->hash($formId), $hash);
    }
}
