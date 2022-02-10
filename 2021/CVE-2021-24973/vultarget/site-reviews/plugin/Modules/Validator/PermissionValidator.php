<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Cast;

class PermissionValidator extends ValidatorAbstract
{
    /**
     * @return bool
     */
    public function isValid()
    {
        if (Cast::toBool(glsr_get_option('general.require.login'))) {
            return is_user_logged_in();
        }
        return true;
    }

    /**
     * @return void
     */
    public function performValidation()
    {
        if (!$this->isValid()) {
            $this->setErrors(__('You must be logged in to submit a review.', 'site-reviews'));
        }
    }
}
