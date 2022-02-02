<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/**
 * Validate variables
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package Duplicator
 * @subpackage classes/utilities
 * @copyright (c) 2017, Snapcreek LLC
 *
 */
// Exit if accessed directly
if (!defined('DUPLICATOR_VERSION')) {
    exit;
}

class DUP_Validator
{
    /**
     * @var array $patterns
     */
    private static $patterns = array(
        'fdir' => '/^([a-zA-Z]:[\\\\\/]|\/|\\\\\\\\|\/\/)[^<>\0]+$/',
        'ffile' => '/^([a-zA-Z]:[\\\\\/]|\/|\\\\\\\\|\/\/)[^<>\0]+$/',
        'fext' => '/^\.?[^\\\\\/*:<>\0?"|\s\.]+$/',
        'email' => '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_\`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
        'empty' => '/^$/',
        'nempty' => '/^.+$/',
    );

    const FILTER_VALIDATE_IS_EMPTY  = 'empty';
    const FILTER_VALIDATE_NOT_EMPTY = 'nempty';
    const FILTER_VALIDATE_FILE      = 'ffile';
    const FILTER_VALIDATE_FOLDER    = 'fdir';
    const FILTER_VALIDATE_FILE_EXT  = 'fext';
    const FILTER_VALIDATE_EMAIL     = 'email';

    /**
     * @var array $errors [ ['key' => string field key,
     *                      'msg' => error message ] , [] ]
     */
    private $errors = array();

    /**
     *
     */
    public function __construct()
    {
        $this->errors = array();
    }

    /**
     *
     */
    public function reset()
    {
        $this->errors = array();
    }

    /**
     *
     * @return bool
     */
    public function isSuccess()
    {
        return empty($this->errors);
    }

    /**
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     *
     * @return array return errors messages
     */
    public function getErrorsMsg()
    {
        $result = array();
        foreach ($this->errors as $err) {
            $result[] = $err['msg'];
        }
        return $result;
    }

    /**
     *
     * @param string $format printf format message where %s is the variable content default "%s\n"
     * @param bool $echo if false return string
     * @return void|string
     */
    public function getErrorsFormat($format = "%s\n", $echo = true)
    {
        $msgs = $this->getErrorsMsg();

        ob_start();
        foreach ($msgs as $msg) {
            printf($format, $msg);
        }

        if ($echo) {
            ob_end_flush();
        } else {
            return ob_get_clean();
        }
    }

    /**
     *
     * @param string $key
     * @param string $msg
     */
    protected function addError($key, $msg)
    {
        $this->errors[] = array(
            'key' => $key,
            'msg' => $msg
        );
    }

    /**
     * filter_var function wrapper see http://php.net/manual/en/function.filter-var.php
     *
     * additional options
     * valkey => key of field
     * errmsg => error message; % s will be replaced with the contents of the variable  es. "<b>%s</b> isn't a valid field"
     * acc_vals => array of accepted values that skip validation
     *
     * @param mixed $variable
     * @param int $filter
     * @param array $options
     * @return mixed
     */
    public function filter_var($variable, $filter = FILTER_DEFAULT, $options = array())
    {
        $success = true;
        $result  = null;

        if (isset($options['acc_vals']) && in_array($variable, $options['acc_vals'])) {
            return $variable;
        }

        if ($filter === FILTER_VALIDATE_BOOLEAN) {
            $options['flags'] = FILTER_NULL_ON_FAILURE;

            $result = filter_var($variable, $filter, $options);

            if (is_null($result)) {
                $success = false;
            }
        } else {
            $result = filter_var($variable, $filter, $options);

            if ($result === false) {
                $success = false;
            }
        }

        if (!$success) {
            $key = isset($options['valkey']) ? $options['valkey'] : '';

            if (isset($options['errmsg'])) {
                $msg = sprintf($options['errmsg'], $variable);
            } else {
                $msg = sprintf('%1$s isn\'t a valid value', $variable);
            }

            $this->addError($key, $msg);
        }

        return $result;
    }

    /**
     * validation of predefined regular expressions
     *
     * @param mixed $variable
     * @param string $filter
     * @param array $options
     * @return type
     * @throws Exception
     */
    public function filter_custom($variable, $filter, $options = array())
    {

        if (!isset(self::$patterns[$filter])) {
            throw new Exception('Filter not valid');
        }

        $options = array_merge($options, array(
            'options' => array(
                'regexp' => self::$patterns[$filter])
            )
        );

        //$options['regexp'] = self::$patterns[$filter];

        return $this->filter_var($variable, FILTER_VALIDATE_REGEXP, $options);
    }

    /**
     * it explodes a string with a delimiter and validates every element of the array
     *
     * @param string $variable
     * @param string $delimiter
     * @param string $filter
     * @param array $options
     */
    public function explode_filter_custom($variable, $delimiter, $filter, $options = array())
    {
        if (empty($variable)) {
            return array();
        }

        $vals = explode($delimiter, trim($variable, $delimiter));
        $res  = array();

        foreach ($vals as $val) {
            $res[] = $this->filter_custom($val, $filter, $options);
        }

        return $res;
    }
}