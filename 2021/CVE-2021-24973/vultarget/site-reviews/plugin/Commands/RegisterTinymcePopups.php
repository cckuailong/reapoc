<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helper;

class RegisterTinymcePopups implements Contract
{
    public $popups;

    public function __construct($input)
    {
        $this->popups = $input;
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->popups as $slug => $label) {
            $buttonClass = Helper::buildClassName([$slug, 'tinymce'], 'Tinymce');
            if (!class_exists($buttonClass)) {
                glsr_log()->error(sprintf('Tinymce Popup class missing (%s)', $buttonClass));
                continue;
            }
            $shortcode = glsr($buttonClass)->register($slug, [
                'label' => $label,
                'title' => $label,
            ]);
            glsr()->append('mce', $shortcode->properties, $slug);
        }
    }
}
