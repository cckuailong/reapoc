<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class FormSubmitButtonTag extends FormTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $value = $this->submitButton();
        if (glsr(OptionManager::class)->isRecaptchaEnabled()) {
            $value .= $this->recaptchaHolder();
        }
        return $value;
    }

    /**
     * @return string
     */
    protected function recaptchaHolder()
    {
        return glsr(Builder::class)->div([
            'class' => 'glsr-recaptcha-holder',
            'data-badge' => sanitize_text_field(glsr_get_option('submissions.recaptcha.position')),
            'data-sitekey' => sanitize_text_field(glsr_get_option('submissions.recaptcha.key')),
            'data-size' => 'invisible',
        ]);
    }

    /**
     * @return string
     */
    protected function submitButton()
    {
        return glsr(Template::class)->build('templates/form/submit-button', [
            'context' => [
                'text' => __('Submit your review', 'site-reviews'),
            ],
        ]);
    }
}
