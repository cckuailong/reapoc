<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Request;

abstract class ValidatorAbstract
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->errors = [];
        $this->request = $request;
    }

    /**
     * @return void
     */
    abstract public function performValidation();

    /**
     * @return Request
     */
    public function validate()
    {
        if (!$this->alreadyFailed()) {
            $this->performValidation();
        }
        return $this->request;
    }

    /**
     * @return bool
     */
    protected function alreadyFailed()
    {
        return is_array(glsr()->session()->get($this->sessionKey('errors')));
    }

    /**
     * @param string $type
     * @return string
     */
    protected function sessionKey($type)
    {
        return $this->request->form_id.'_'.$type;
    }

    /**
     * @param string $message
     * @param string $loggedMessage
     * @return void
     */
    protected function setErrors($message, $loggedMessage = null)
    {
        glsr()->sessionSet($this->sessionKey('errors'), $this->errors);
        glsr()->sessionSet($this->sessionKey('message'), $message);
        glsr()->sessionSet($this->sessionKey('values'), $this->request->toArray());
        if (!empty($loggedMessage)) {
            glsr_log()->warning($loggedMessage)->debug($this->request->toArray());
        }
    }
}
