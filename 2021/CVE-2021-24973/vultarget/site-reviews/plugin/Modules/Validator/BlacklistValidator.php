<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Request;

class BlacklistValidator extends ValidatorAbstract
{
    /**
     * @return bool
     */
    public function isValid()
    {
        $target = implode("\n", array_filter([
            $this->request->name,
            $this->request->content,
            $this->request->email,
            $this->request->ip_address,
            $this->request->title,
        ]));
        $isValid = $this->validateBlacklist($target);
        return glsr()->filterBool('validate/blacklist', $isValid, $target, $this->request);
    }

    /**
     * @return void
     */
    public function performValidation()
    {
        if (!$this->isValid()) {
            if ('reject' !== glsr_get_option('submissions.blacklist.action')) {
                $this->request->set('blacklisted', true);
                return;
            }
            $this->setErrors(
                __('Your review cannot be submitted at this time.', 'site-reviews'),
                'Blacklisted submission detected.'
            );
        }
    }

    /**
     * @return string
     */
    protected function blacklist()
    {
        return 'comments' === glsr_get_option('submissions.blacklist.integration')
            ? trim(glsr(OptionManager::class)->getWP('disallowed_keys'))
            : trim(glsr_get_option('submissions.blacklist.entries'));
    }

    /**
     * @param string $target
     * @return bool
     */
    protected function validateBlacklist($target)
    {
        if (empty($blacklist = $this->blacklist())) {
            return true;
        }
        $lines = explode("\n", $blacklist);
        foreach ((array) $lines as $line) {
            $line = trim($line);
            if (empty($line) || 256 < strlen($line)) {
                continue;
            }
            $pattern = sprintf('#%s#i', preg_quote($line, '#'));
            if (preg_match($pattern, $target)) {
                return false;
            }
        }
        return true;
    }
}
