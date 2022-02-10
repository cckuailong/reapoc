<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Modules\Honeypot;

class HoneypotValidator extends ValidatorAbstract
{
    /**
     * @return bool
     */
    public function isValid()
    {
        $hash = glsr(Honeypot::class)->hash($this->request->form_id);
        $isValid = isset($this->request[$hash]) && empty($this->request[$hash]);
        return glsr()->filterBool('validate/honeypot', $isValid, $this->request);
    }

    /**
     * @return void
     */
    public function performValidation()
    {
        if (!$this->isValid()) {
            $this->setErrors(
                __('This review has been flagged as possible spam and cannot be submitted.', 'site-reviews'),
                'The Honeypot caught a bad submission.'
            );
        }
    }
}
