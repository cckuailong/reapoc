<?php

namespace Aventura\Wprss\Core;

/**
 * Responsible for autoloading classes.
 */
class Loader {

	const WHITESPACE_CHARS = " \t\n\r\0\x0B";

	protected $_psr4 = array();
	protected $_isRegistered = false;
	protected $_ext = array();

	public function __construct() {
		$this->_construct();
	}

	protected function _construct() {
		$this->addExtension('php');
	}

	/**
	 * Registers this instance for handling autoloading, if not already registered.
	 *
	 * @return \Aventura\Wprss\Core\Loader This instance.
	 */
	public function register() {
		if (!$this->isRegistered()) {
			$this->_register();
		}

		return $this;
	}

	/**
	 * Low level method for registering the autoload function.
	 *
	 * No checks performed.
	 *
	 * @see spl_autoload_register()
	 * @return \Aventura\Wprss\Core\Loader This instance.
	 */
	protected function _register() {
		spl_autoload_register(array($this, 'autoload'));
		return $this;
	}

	/**
	 * Checks whether or not this class is already registered for handling autoloading.
	 *
	 * @see register()
	 * @return boolean True if already registered; false otherwise.
	 */
	public function isRegistered() {
		return (bool)$this->_isRegistered;
	}

	/**
	 * Implementation of an autoloading function.
	 *
	 * @see spl_autoload()
	 * @param string $class Name of the class to load.
	 */
	public function autoload($class) {
		if (!($basePath = $this->match($class))) {
			return;
		}

		$relativePath = $this->getClassRelativePath($class);
		if ($path = $this->findClassFile($relativePath, $basePath)) {
			include_once($path);
		}
	}

	/**
	 * Alias of addPsr4().
	 *
	 * @see addPsr4
	 * @return \Aventura\Wprss\Core\Loader This instance.
	 */
	public function add($namespace, $basePath) {
		$this->addPsr4($namespace, $basePath);
		return $this;
	}

	/**
	 * Tells the loader to load classes in specified namespace from specified directory.
	 *
	 * This is according to the PSR-4 standard.
	 *
	 * @param string $namespace The namespace, files for which should be loaded from the path.
	 * @param string $basePath Path to the directory, which is the base directory for class files.
	 * @return \Aventura\Wprss\Core\Loader This instance.
	 */
	public function addPsr4($namespace, $basePath) {
		$namespace = $this->normalizeClassName($namespace);
		$basePath = $this->normalizeBasePath($basePath);

		$this->_psr4[$namespace] = $basePath;
		return $this;
	}

	/**
	 * Matches the class name with all registered namespaces.
	 *
	 * Used to find the base path, where the class should be loaded from.
	 *
	 * @param string $class Name of the class to find the base path for.
	 * @return string|null The basepath that corresponds to the registered namespace of the class, if such a namespace is found.
	 *  Otherwise, null.
	 */
	public function match($class) {
		if ($basePath = $this->matchPsr4($class)) {
			return $basePath;
		}

		return null;
	}

	/**
	 * Matches the class agains the registered PSR-4 namespaces.
	 *
	 * @see match()
	 * @param string $class Class name to match.
	 * @return string|null Basepath for PSR-4 namespace, or null if no match.
	 */
	public function matchPsr4($class) {
		foreach ($this->getPsr4Namespaces() as $_ns => $_basePath) {
			if ( $this->matchNamespace( $_ns, $class ) ) {
				return $_basePath;
			}
		}

		return null;
	}

	/**
	 * Get all registered PSR-4 namespaces.
	 *
	 * They will be sorted by longest first. This allows to avoid instances, where
	 * class `My\Namespace\Cool\Class` will be loaded from the basepath that
	 * corresponds to `My\Namespace` even if `My\Namespace\Cool` is registered.
	 *
	 * @see sortNamespaces()
	 * @return array Array of registered PSR-4 namespaces, sorted by longest first.
	 */
	public function getPsr4Namespaces() {
		return $this->sortNamespaces($this->_psr4);
	}

	/**
	 * Sorts an array of strings by longest first.
	 *
	 * @param array $namespaces The array to sort.
	 * @return array Array of strings, sorted by longest first.
	 */
	public function sortNamespaces($namespaces) {
		uasort($namespaces, function($a, $b) {
			return strlen($b) - strlen($a);
		});

		return $namespaces;
	}

	/**
	 * Determines whether a class belongs to a namespace.
	 *
	 * @param string $namespace Namespace to match the class against.
	 * @param string $class Name of the class to match.
	 * @return boolean True if the specified class belongs to the specified namespace.
	 */
	public function matchNamespace($namespace, $class) {
		$namespace = $this->normalizeClassName($namespace);
		$class = $this->normalizeClassName($class);
		return strpos( $class, $namespace ) === 0;
	}

	/**
	 * Normalizes a class name.
	 *
	 * @param string $className Class name to normalize.
	 * @return string The class name with whitespace and namespace separators removed from beginning and end.
	 */
	public function normalizeClassName($className) {
		return trim($className, self::WHITESPACE_CHARS . '\\');
	}

	/**
	 * Normalizes a path in a way that is independent of platform's directory separator.
	 *
	 * @param string $path Path to normalize.
	 * @return string The path with whitespace and directory separators removed from end.
	 *  Whitespace will be removed from beginning as well.
	 */
	public function normalizeBasePath($path) {
		$path = ltrim($path);
		return rtrim($path, self::WHITESPACE_CHARS . '\\/' . DIRECTORY_SEPARATOR);
	}

	/**
	 * Normalizes a file extension.
	 *
	 * @param string $extension The extension to normalize.
	 * @return string The file extension with whitespace and period trimmed.
	 */
	public function normalizeExtension($extension) {
		return trim($extension, self::WHITESPACE_CHARS . '.');
	}

	/**
	 * Deduces the relative path to the file of a PSR-0 or PSR-4 class.
	 *
	 * @param string $class Name of the class.
	 * @return string The relative path deduced from the class name, without extension.
	 */
	public function getClassRelativePath($class) {
		$class = $this->normalizeClassName($class);
		$path = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class);

		return $path;
	}

	/**
	 * Looks for a file with all registered extensions in all specified paths.
	 *
	 * @see getClassRelativePath()
	 * @param string $path Relative path to a class file, without extension.
	 * @param string|array $basePath Path or array of paths to the base directory in which to search for the file.
	 * @return string|null Path to the class file, if found; otherwise, null.
	 */
	public function findClassFile($path, $basePath) {
		$basePath = (array)$basePath;
		$extensions = $this->getAllowedExtensions();
		foreach ($basePath as $_path) {
			foreach ($extensions as $_ext) {
				$classPath = implode(DIRECTORY_SEPARATOR, array($_path, "{$path}.{$_ext}"));
				if (file_exists($classPath)) {
					return $classPath;
				}
			}
		}

		return null;
	}

	/**
	 * Retrieves the list of all extensions, for which this loader will autoload class files.
	 *
	 * @return array Array of registered extensions.
	 */
	public function getAllowedExtensions() {
		return array_keys($this->_ext);
	}

	/**
	 * Allows an extension for class files.
	 *
	 * @param string $extension Extension to allow.
	 * @return \Aventura\Wprss\Core\Loader This instance.
	 *
	 */
	public function addExtension($extension) {
		$extension = $this->normalizeExtension($extension);
		$this->_ext[$extension] = true;

		return $this;
	}

	/**
	 * Disallows an extension for class files.
	 *
	 * @param string $extension Extension to disallow.
	 * @return \Aventura\Wprss\Core\Loader This instance.
	 */
	public function removeExtension($extension) {
		$extension = $this->normalizeExtension($extension);
		if (isset($this->_ext[$extension])) {
			unset($this->_ext[$extension]);
		}

		return $this;
	}
}
