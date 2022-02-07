<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Validation;

/**
 * Class NumericValidator
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Validation
 */
class NumericValidator
{

    /**
     * Helper method for validating an argument if it is numeric
     *
     * @param mixed     $argument
     * @param string|null $argumentName
     * @return bool
     */
    public static function validate($argument, $argumentName = null)
    {
        if (trim($argument) != null && !is_numeric($argument)) {
            throw new \InvalidArgumentException("$argumentName is not a valid numeric value");
        }
        return true;
    }
}
