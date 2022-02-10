<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class FormResponseTag extends FormTag
{
    /**
     * @return string|void
     */
    protected function contextClass()
    {
        $classes = [glsr(Style::class)->validation('form_message')];
        if (!empty($this->with->errors)) {
            $classes[] = glsr(Style::class)->validation('form_message_failed');
        }
        return trim(implode(' ', array_filter($classes)));
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return glsr(Template::class)->build('templates/form/response', [
            'context' => [
                'class' => $this->contextClass(),
                'message' => wpautop($this->with->message),
            ],
            'has_errors' => !empty($this->with->errors),
        ]);
    }
}
