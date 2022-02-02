<?php

namespace wpdFormAttr\Login\twitter;

/**
 * @author Abraham Williams <abraham@abrah.am>
 */
class TwitterOAuthException extends \Exception {

    public function getOAuthMessage() {
        $message = json_decode($this->message);
        if (isset($message->errors) && is_array($message->errors)) {
            $oautException = "";
            foreach ($message->errors as $error) {
                $oautException .=  $error->message . '<br>';
            }
            return $oautException;
        }
        return $this->message;
    }

}
