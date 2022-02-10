<?php

namespace Aventura\Wprss\Core\Model;

use Aventura\Wprss\Core;

/**
 * @since 4.8.1
 */
abstract class ModelAbstract extends Core\DataObject implements ModelInterface
{
    const PREFIX_OVERRIDE = '!';

    /**
     * @since 4.8.1
     */
    protected function _construct()
    {
        // Default depth
        if (!$this->hasBaseNamespaceDepth()) {
            $this->setBaseNamespaceDepth(3);
        }
        parent::_construct();
    }

    /**
     * Translates a string of text.
     * 
     * If text is an array, it will be used as an array to a format function,
     * such as {@see sprintf()}.
     * Formatting always takes place after translation; only the first element
     * of the text array will be translated.
     * If the text is a scalar value other than string, it will be returned unmodified.
     *
     * @since 4.8.1
     * @param string|array $text The text to translate, or an array of arguments to a format function.
     * @param string|mixed $translator A translator instance or some kind of identifier.
     * @return string Translated string.
     * @throws \InvalidArgumentException If trying to translate an untranslatable value.
     */
    public function __($text, $translator = null)
    {
        // Allowing array to designate message formatting
        $args = (array)$text;
        $text = array_shift($args);
        $isString = is_string($text);
        $isScalar = is_scalar($text);
        $canStringify = $isString || $isScalar;

        if (!$canStringify) {
            throw new \InvalidArgumentException('Could not process text: Text must be a string, or at least of a scalar type');
        }

        $text = (string)$text;
        if (!$isString) {
            return $text;
        }

        // Allowing to skip message translation
        if ($translator !== false) {
            $text = $this->_translate($text, $translator);
        }

        array_unshift($args, $text);

        if (count($args) > 1) {
            // Formatting message
            $result = call_user_func_array('sprintf', $args);

            // Detecting format error
            if (!strlen($result) && strlen($text)) {
                error_log($text);
                throw new \InvalidArgumentException(sprintf('Could not process text: It seems there was an error with formatting, '
                .'perhaps wrong parameter count (string should contain %1$d).', count($args) - 1));
            }
        }
        else {
            $result = $args[0];
        }


        return $result;
    }

    /**
     * Translates a string.
     *
     * @since 4.8.1
     * @param string $text The text to translate.
     * @param string|mixed $translator A translator instance or some kind of identifier.
     * @return string Translated string.
     */
    protected function _translate($text, $translator = null) {
        return $text;
    }

    /**
     * Get the namespace of this or other class
     *
     * @since 4.8.1
     * @see wprss_get_namespace()
     * @param int|null $depth The depth of the namespace to retrieve.
     *  If omitted, the whole namespace will be retrieved.
     * @param string|object|null $class The class name or instance, for which to get the namespace.
     *  If omitted, the current class will be used.
     * @param bool $asString If true, the result will be a string; otherwise, array with namespace parts.
     * @return array|string The namespace of the class.
     */
    public static function getNamespace($depth = null, $class = null,
                                        $asString = false)
    {
        // Default to this class
        if (is_null($class)) {
            $className = trim(get_called_class(), $ns);
        }
        return wprss_get_namespace($class, $depth, $asString);
    }

    /**
     * Check if a namespace is a root namespace.
     *
     * @since 4.8.1
     * @see wprss_is_root_namespace()
     * @param string $namespace The namespace to check.
     * @param bool $isCheckClass If true, and a class or interface with the name of the specified namespace exists,
     *  will make this function return true. Otherwise, the result depends purely on the namespace string.
     * @return boolean True if the namespace is a root namespace; false otherwise.
     */
    public static function isRootNamespace($namespace, $isCheckClass = true)
    {
        return wprss_is_root_namespace($namespace, $isCheckClass);
    }

    /**
     * Get the base namespace of this plugin.
     *
     * At least most of the classes in a plugin will be relative to that plugin's namespace.
     * The default namespace is the namespace of this class, of an optional depth which is determined by
     * {@see getBaseNamespaceDepth()}.
     *
     * @since 4.8.1
     * @return string The base namespace of this plugin.
     */
    public function getBaseNamespace()
    {
        if ($namespace = $this->getData('base_namespace')) {
            return $namespace;
        }

        // Fall back to auto namespace
        return static::getNamespace($this->getBaseNamespaceDepth(), $this, true);
    }

    /**
     * Creates an exception instance.
     *
     * @since 4.8.1
     * @param string|array $text The text to translate.
     *  If array, then the result will be formatted with `sprintf()`, using
     *   the first argument as the format string, and any that follow as arguments.
     * @param string $className The name of the exception's class.
     *  If it doesn't start with a backslash '\', will be treated as relative to
     *   the plugin base namespace.
     *  Defaults to 'Exception', which results in class [base_namespace]\Exception.
     *  {@see getExceptionClassName()}
     * @param string|bool|null $translate The text domain to use for translation.
     *  If false, no translation will be made.
     *  Defaults to the current plugin's text domain.
     *  {@see __()}
     * @return \Exception The new exception instance.
     * @throws Aventura\Wprss\SpinnerChief\Plugin\Exception If the exception class does not exist.
     */
    public function exception($text, $className = null, $translate = null)
    {
        $text      = $this->__($text, $translate);
        $className = $this->getExceptionClassName($className);
        if (!class_exists($className)) {
            throw new Core\Exception(sprintf('Could not create exception: Class "%1$s" does not exist', $className));
        }

        $exception = new $className($text);
        return $exception;
    }

    /**
     * Get a class name of an exception.
     * 
     * This will be based on its absolute or relative class name, or potentially
     * any other identifier that could be mapped to a class name.
     *
     * @since 4.8.1
     * @see getExceptionClassRoot()
     * @param string|array $className A name of the exception class, or array with namespace parts.
     *  If array, assumed to be root namespace.
     * @return string The fully qualified name of the exception class.
     */
    public function getExceptionClassName($className = null)
    {
        $defaultClassName = 'Exception';
        // Defaults to this namespace's root exception
        if (is_null($className)) {
            $className = $defaultClassName;
        }

        // Namespace specified as array of parts; assume root namespace
        if (is_array($className)) {
            $className = '\\' . trim(implode('\\', $className), '\\');
        }

        // Allowing explicit class name
        if (static::isRootNamespace($className, $className !== $defaultClassName)) {
            return $className;
        }

        // Relative to this class's root
        $rootNamespace = $this->getExceptionClassRoot();
        return sprintf('%1$s\\%2$s', $rootNamespace, $className);
    }

    /**
     * Get the root namespace for exception classes.
     * 
     * Unless altered, {@see getExceptionClassName} will process class names
     * relative to the value returned by this method.
     *
     * @since 4.8.1
     * @return string The root namespace of exception class names.
     */
    public function getExceptionClassRoot()
    {
        $key = 'exception_class_root';
        if (!$this->hasData($key)) {
            $this->setData($key, $this->getBaseNamespace());
        }

        return $this->getData($key);
    }

    /**
     *
     * @since 4.8.1
     * @param string $string The string to check.
     * @return bool True if the string had a prefix override, and it was removed;
     *  false otherwise;
     */
    public static function stringHadPrefix(&$string)
    {
        return string_had_prefix($string, static::PREFIX_OVERRIDE);
    }

    public static function classImplements($class, $interface = null, $autoload = true)
    {
        $interfaces = class_implements($class, $autoload);
        if (is_null($interface)) {
            return $interfaces;
        }

        return in_array($interface, $interfaces);
    }

    /**
     * Gets the data member with the specified key, or a corresponding constant if that key is not defined.
     *
     * Useful for accessing data, the default value of which was defined as a class constants.
     *
     * @since 4.8.1
     * @param string $key The data key to get.
     * @param mixed|null $default What to return if neither the data key or the constant are defined.
     * @return mixed|null The value of the data key or constant.
     */
    protected function _getDataOrConst($key, $default = null)
    {
        if ($this->hasData($key)) {
            return $this->getData($key);
        }

        $const = $this->constant($key);
        return empty($const)
            ? $default
            : $const;
    }

    /**
     * Gets the value of a constant with the specified name from this object's class.
     *
     * @since 4.8.1
     * @param string $key The key of the constant, which corresponds to it's name.
     * @param mixed|null $default What to return if the constant with the specified key is not defined.
     * @param bool $toUpper Whether or not to convert the key to uppercase.
     * @return int|string|null The value of the constant, or `null` if no such constant defined.
     */
    public function constant($key, $default = null, $toUpper = true)
    {
        if ($toUpper) {
            $key = strtoupper($key);
        }

        $constName = sprintf('%1$s::%2$s', get_class($this), $key);
        return defined($constName)
            ? constant($constName)
            : $default;
    }

    public function log($level, $message, array $context = array())
    {
        return false;
    }

    /**
     * Get this instance's event prefix, or a prefixed event name.
     *
     * An event prefix is a prefix that will by default be added to names of events
     * that are listened to or raised by this instance.
     *
     * The event prefix is by default the plugin code followed by an underscore "_", unless the code is
     * not set, in which case the prefix is empty.
     *
     * Override with setEventPrefix().
     *
     * @since 4.8.1
     * @param string|null $name An event name to prefix.
     * @return string This instance's event prefix, or a prefixed name.
     */
    public function getEventPrefix($name = null)
    {
        $prefix = $this->_getEventPrefix();
        return static::stringHadPrefix($name)
            ? $name
            : "{$prefix}{$name}";
    }

    /**
     * Get the actual event prefix, only.
     *
     * @since 4.8.1
     * @return string
     */
    protected function _getEventPrefix()
    {
        return $this->getData('event_prefix');
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function on($name, $listener, $data = null, $priority = null, $acceptedArgs = null)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function event($name, $data = array())
    {
        return null;
    }

    /**
     * Convenient wrapper for providing a default value to a parameter.
     *
     * Will return the default value if the parameter matches the criteria;
     * otherwise, returns the parameter unchanged.
     * The comparison is made in strict mode.
     *
     * @since 4.9
     * @param mixed $param The actual value.
     * @param mixed $default The default value.
     * @param mixed $criteria The condition.
     * @return mixed The parameter, or the default value.
     */
    public static function defaultParam($param, $default, $criteria = null)
    {
        if ($param === $criteria) {
            return $default;
        }

        return $param;
    }
}
