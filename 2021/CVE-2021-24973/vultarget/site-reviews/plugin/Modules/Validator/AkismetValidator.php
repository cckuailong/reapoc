<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use Akismet;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Request;

class AkismetValidator extends ValidatorAbstract
{
    /**
     * @return bool
     */
    public function isValid()
    {
        if (!$this->isActive()) {
            return true;
        }
        $submission = [
            'blog' => glsr(OptionManager::class)->getWP('home'),
            'blog_charset' => glsr(OptionManager::class)->getWP('blog_charset', 'UTF-8'),
            'blog_lang' => get_locale(),
            'comment_author' => $this->request->name,
            'comment_author_email' => $this->request->email,
            'comment_content' => $this->request->title."\n\n".$this->request->content,
            'comment_type' => 'review',
            'referrer' => filter_input(INPUT_SERVER, 'HTTP_REFERER'),
            'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
            'user_ip' => $this->request->ip_address,
            // 'user_role' => 'administrator',
            // 'is_test' => 1,
        ];
        foreach ($_SERVER as $key => $value) {
            if (!is_array($value) && !in_array($key, ['HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW'])) {
                $submission[$key] = $value;
            }
        }
        $submission = glsr()->filterArray('validate/akismet/submission', $submission, $this->request);
        return $this->validateAkismet($submission);
    }

    /**
     * @return void
     */
    public function performValidation()
    {
        if (!$this->isValid()) {
            $this->setErrors(
                __('This review has been flagged as possible spam and cannot be submitted.', 'site-reviews'),
                'Akismet caught a spam submission (consider adding the IP address to the blacklist).'
            );
        }
    }

    /**
     * @return string
     */
    protected function buildUrlQuery(array $data)
    {
        $query = [];
        foreach ($data as $key => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            if (false === $value) {
                $value = '0';
            }
            $value = trim($value);
            if (!strlen($value)) {
                continue;
            }
            $query[] = urlencode($key).'='.urlencode($value);
        }
        return implode('&', $query);
    }

    /**
     * @return bool
     */
    protected function isActive()
    {
        $check = !glsr_get_option('submissions.akismet', false, 'bool')
            || !is_callable(['Akismet', 'get_api_key'])
            || !is_callable(['Akismet', 'http_post'])
            ? false
            : !empty(Akismet::get_api_key());
        return glsr()->filterBool('validate/akismet/is-active', $check);
    }

    /**
     * @return bool
     */
    protected function validateAkismet(array $submission)
    {
        $response = Akismet::http_post($this->buildUrlQuery($submission), 'comment-check');
        $isValid = 'true' !== $response[1];
        return glsr()->filterBool('validate/akismet', $isValid, $submission, $response);
    }
}
