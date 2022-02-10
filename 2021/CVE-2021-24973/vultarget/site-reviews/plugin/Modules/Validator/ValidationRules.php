<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Str;
use InvalidArgumentException;

/**
 * @see \Illuminate\Validation\Validator (5.3)
 */
trait ValidationRules
{
    /**
     * Get the size of an attribute.
     * @param string $attribute
     * @param mixed $value
     * @return mixed
     */
    abstract protected function getSize($attribute, $value);

    /**
     * Replace all placeholders.
     * @param string $message
     * @return string
     */
    protected function replace($message, array $parameters)
    {
        if (!Str::contains('%s', $message)) {
            return $message;
        }
        return preg_replace_callback('/(%s)/', function () use (&$parameters) {
            foreach ($parameters as $key => $value) {
                return array_shift($parameters);
            }
        }, $message);
    }

    /**
     * Validate that an attribute value was "accepted".
     * This validation rule implies the attribute is "required".
     * @param mixed $value
     * @return bool
     */
    public function validateAccepted($value)
    {
        $acceptable = ['yes', 'on', '1', 1, true, 'true'];
        return $this->validateRequired($value) && in_array($value, $acceptable, true);
    }

    /**
     * Validate the size of an attribute is between a set of values.
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function validateBetween($value, $attribute, array $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'between');
        $size = $this->getSize($attribute, $value);
        return $size >= $parameters[0] && $size <= $parameters[1];
    }

    /**
     * Validate that an attribute value is a valid e-mail address.
     * @param mixed $value
     * @return bool
     */
    public function validateEmail($value)
    {
        return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate the size of an attribute is less than a maximum value.
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function validateMax($value, $attribute, array $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'max');
        return $this->getSize($attribute, $value) <= $parameters[0];
    }

    /**
     * Validate the size of an attribute is greater than a minimum value.
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function validateMin($value, $attribute, array $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'min');
        return $this->getSize($attribute, $value) >= $parameters[0];
    }

    /**
     * Validate that an attribute is numeric.
     * @param mixed $value
     * @return bool
     */
    public function validateNumber($value)
    {
        return is_numeric($value);
    }

    /**
     * Validate that a required attribute exists.
     * @param mixed $value
     * @return bool
     */
    public function validateRequired($value)
    {
        return is_null($value)
            || (is_string($value) && in_array(trim($value), ['', '[]']))
            || (is_array($value) && empty($value))
            ? false
            : true;
    }

    /**
     * Require a certain number of parameters to be present.
     * @param int $count
     * @param string $rule
     * @return void
     * @throws InvalidArgumentException
     */
    protected function requireParameterCount($count, array $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }
}
