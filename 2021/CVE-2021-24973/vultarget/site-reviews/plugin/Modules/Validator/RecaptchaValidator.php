<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;

class RecaptchaValidator extends ValidatorAbstract
{
    const RECAPTCHA_API_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';
    const RECAPTCHA_DISABLED = 0;
    const RECAPTCHA_EMPTY = 1;
    const RECAPTCHA_FAILED = 2;
    const RECAPTCHA_INVALID = 3;
    const RECAPTCHA_VALID = 4;

    protected $status;

    /**
     * @return bool
     */
    public function isValid()
    {
        if (in_array($this->status, [static::RECAPTCHA_DISABLED, static::RECAPTCHA_VALID])) {
            return true;
        }
        if (static::RECAPTCHA_EMPTY === $this->status) {
            glsr()->sessionSet($this->sessionKey('recaptcha'), 'unset');
            return true;
        }
        glsr()->sessionSet($this->sessionKey('recaptcha'), 'reset');
        return false;
    }

    /**
     * @return void
     */
    public function performValidation()
    {
        $this->status = $this->recaptchaStatus();
        if (!$this->isValid()) {
            $message = Helper::ifTrue($this->status === static::RECAPTCHA_FAILED,
                __('The reCAPTCHA failed to load, please refresh the page and try again.', 'site-reviews'),
                __('The reCAPTCHA verification failed, please try again.', 'site-reviews')
            );
            $this->setErrors($message);
        }
    }

    /**
     * @return int
     */
    protected function recaptchaStatus()
    {
        if (!glsr(OptionManager::class)->isRecaptchaEnabled()) {
            return static::RECAPTCHA_DISABLED;
        }
        if (empty($this->request['_recaptcha-token'])) {
            return Cast::toInt($this->request->_counter) < glsr()->filterInt('recaptcha/timeout', 5)
                ? static::RECAPTCHA_EMPTY
                : static::RECAPTCHA_FAILED;
        }
        return $this->recaptchaTokenStatus();
    }

    /**
     * @return int
     */
    protected function recaptchaTokenStatus()
    {
        $endpoint = add_query_arg([
            'remoteip' => $this->request->ip_address,
            'response' => $this->request['_recaptcha-token'],
            'secret' => glsr_get_option('submissions.recaptcha.secret'),
        ], static::RECAPTCHA_API_ENDPOINT);
        if (is_wp_error($response = wp_remote_get($endpoint))) {
            glsr_log()->error($response->get_error_message());
            return static::RECAPTCHA_FAILED;
        }
        $response = json_decode(wp_remote_retrieve_body($response));
        if (!empty($response->success)) {
            return static::RECAPTCHA_VALID;
        }
        foreach ($response->{'error-codes'} as $error) {
            glsr_log()->error('reCAPTCHA error: '.$error);
        }
        return static::RECAPTCHA_INVALID;
    }
}
