<?php

namespace Aventura\Wprss\Core\Model;

/**
 * Common functionality for all commands.
 *
 * @since [*next-version]
 */
abstract class CommandAbstract extends ModelAbstract implements CommandInterface
{
	protected $_callable;
	protected $_args = array();


	/**
	 * @since 4.8.1
	 * @param callable $data The {@link set_function() function} to give to the command.
	 */
	public function __construct( $data = array() )
    {
        if (is_callable($data, true)) {
            $data = array('function' => $data);
        }

        if (isset($data['function'])) {
            $this->setFunction($data['function']);
            unset($data['function']);
        }

        if (isset($data['args'])) {
            $this->setArgs($data['args']);
            unset($data['args']);
        }

		parent::__construct($data);
	}


	/**
	 * Sets the function to be called with this command.
	 *
	 * @since 4.8.1
	 * @param callable $function The function or method to be called.
	 * @return CommandAbstract This instance.
	 * @throws CommandException If passed function is not a valid callable.
	 */
	public function setFunction( $function ) {
		if ( !is_callable( $function, true ) )
			throw $this->exception( 'Could not set function: function is not a valid callable', array(__NAMESPACE__, 'CommandException') );

		$this->_callable = $function;
		return $this;
	}


	/**
	 * Sets the argument or arguments for this command.
	 *
	 * If index is null or omitted, all arguments will be set to the value of $args.
	 * If in this case $args is not an array, args will be an array where $args is
	 * the only element.
	 * In any case, the indexes of $args do not matter, but the order does.
	 *
	 * @since 4.8.1
	 * @param array $args The argument or arguments to set for this command.
	 * @param int $index The index, at which to set the argument.
	 * @return CommandAbstract This instance.
	 */
	public function setArgs( $args, $index = null ) {
		if ( is_null( $index ) ) {
			$this->_args = array_values( (array) $args );
			return $this;
		}

		$index = (int) $index;
		$this->_args[ $index ] = $args;

		return $this;
	}


	/**
	 * @since 4.8.1
	 * @return callable The function of the command.
	 */
	public function getFunction() {
		return $this->_callable;
	}


	/**
	 * Gets the argument or arguments for this command.
	 *
	 * @since 4.8.1
	 * @param int|null $index The index of the argument to return.
	 * @return array|mixed|null The argument, or arguments, or null if not found.
	 */
	public function getArgs( $index = null ) {
		if ( is_null( $index ) )
			return $this->_args;

		$index = (int) $index;
		return isset( $this->_args[ $index ] ) ? $this->_args[ $index ] :  null;
	}


	/**
	 * Calls the function of this command with the given arguments.
	 *
	 * @since 4.8.1
	 * @param array $args_override A different set of arguments that will override the original.
	 * @return mixed The return value of the function of the command.
	 * @throws CommandException If the function of the command is not callable.
	 */
	public function call($args_override = array()) {
		$args = $this->getArgs();
		$args = array_merge_recursive_distinct( $args, $args_override );

		if ( !is_callable( $callable = $this->getFunction() ) )
				throw $this->exception( 'Could not call function: function must be callable', array(__NAMESPACE__, 'CommandException') );

		$result = call_user_func_array( $callable, $args);
		return $result;
	}

    /**
     * Allows instances of this class and its descendants to be called like a function.
     *
     * @since 4.8.1
     * @return type
     */
    public function __invoke()
    {
        $args = func_get_args();
        return $this->call($args);
    }
}