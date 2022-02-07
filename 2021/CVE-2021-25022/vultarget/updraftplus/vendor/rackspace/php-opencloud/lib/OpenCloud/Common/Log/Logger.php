<?php
/**
 * Copyright 2012-2014 Rackspace US, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace OpenCloud\Common\Log;

use OpenCloud\Common\Exceptions\LoggingException;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Basic logger for OpenCloud which extends FIG's PSR-3 standard logger.
 *
 * @link https://github.com/php-fig/log
 */
class Logger extends AbstractLogger
{
    /**
     * Is this debug class enabled or not?
     *
     * @var bool
     */
    private $enabled;

    /**
     * These are the levels which will always be outputted - regardless of
     * user-imposed settings.
     *
     * @var array
     */
    private $urgentLevels = array(
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL
    );

    /**
     * Logging options.
     *
     * @var array
     */
    private $options = array(
        'outputToFile' => false,
        'logFile'      => null,
        'dateFormat'   => 'd/m/y H:I',
        'delimeter'    => ' - '
    );

    public function __construct($enabled = false)
    {
        $this->enabled = $enabled;
    }

    public static function newInstance()
    {
        return new static();
    }

    /**
     * Determines whether a log level needs to be outputted.
     *
     * @param  string $logLevel
     * @return bool
     */
    private function outputIsUrgent($logLevel)
    {
        return in_array($logLevel, $this->urgentLevels);
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message
     * @param array  $context
     * @return type
     */
    private function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * Enable or disable the debug class.
     *
     * @param  bool $enabled
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Is the debug class enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled === true;
    }

    /**
     * Set an array of options.
     *
     * @param array $options
     */
    public function setOptions(array $options = array())
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }

        return $this;
    }

    /**
     * Get all options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set an individual option.
     *
     * @param string $key
     * @param string $value
     */
    public function setOption($key, $value)
    {
        if ($this->optionExists($key)) {
            $this->options[$key] = $value;

            return $this;
        }
    }

    /**
     * Get an individual option.
     *
     * @param  string $key
     * @return string|null
     */
    public function getOption($key)
    {
        if ($this->optionExists($key)) {
            return $this->options[$key];
        }
    }

    /**
     * Check whether an individual option exists.
     *
     * @param  string $key
     * @return bool
     */
    private function optionExists($key)
    {
        return array_key_exists($key, $this->getOptions());
    }

    /**
     * Outputs a log message if necessary.
     *
     * @param string $logLevel
     * @param string $message
     * @param string $context
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->outputIsUrgent($level) || $this->isEnabled()) {
            $this->dispatch($message, $context);
        }
    }

    /**
     * Used to format the line outputted in the log file.
     *
     * @param  string $string
     * @return string
     */
    private function formatFileLine($string)
    {
        $format = $this->getOption('dateFormat') . $this->getOption('delimeter');

        return date($format) . $string;
    }

    /**
     * Dispatch a log output message.
     *
     * @param string $message
     * @param array  $context
     * @throws LoggingException
     */
    private function dispatch($message, $context)
    {
        $output = $this->interpolate($message, $context) . PHP_EOL;

        if ($this->getOption('outputToFile') === true) {
            $file = $this->getOption('logFile');

            if (!is_writable($file)) {
                throw new LoggingException(
                    'The log file either does not exist or is not writeable'
                );
            }

            // Output to file
            file_put_contents($file, $this->formatFileLine($output), FILE_APPEND);
        } else {
            echo $output;
        }
    }

    /**
     * Helper method, use PSR-3 warning function for deprecation warnings
     * @see http://www.php-fig.org/psr/psr-3/
     */
    public static function deprecated($method, $new)
    {
        return sprintf('The %s method is deprecated, please use %s instead', $method, $new);
    }
}
