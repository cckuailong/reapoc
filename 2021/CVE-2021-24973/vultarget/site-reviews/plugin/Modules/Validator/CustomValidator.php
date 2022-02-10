<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Cast;

class CustomValidator extends ValidatorAbstract
{
    /**
     * @return bool
     */
    public function isValid()
    {
        return Cast::toBool(glsr()->filter('validate/custom', true, $this->request));
    }

    /**
     * @return void
     */
    public function performValidation()
    {
        $validated = glsr()->filter('validate/custom', true, $this->request); // value may be a string
        if (true === Cast::toBool($validated)) {
            return;
        }
        if (!is_string($validated)) {
            $validated = __('The review submission failed. Please notify the site administrator.', 'site-reviews');
        }
        $this->setErrors($validated);
    }
}
