<?php

/**
 * Validates code prior to execution.
 *
 * @package Code_Snippets
 */
class Code_Snippets_Validator {

	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var array
	 */
	private $tokens;

	/**
	 * The index of the token currently being examined.
	 *
	 * @var int
	 */
	private $current;

	/**
	 * The total number of tokens.
	 *
	 * @var int
	 */
	private $length;

	/**
	 * Array to keep track of the various function, class and interface identifiers which have been defined.
	 *
	 * @var array
	 */
	private $defined_identifiers = array();

	/**
	 * Exclude certain tokens from being checked.
	 *
	 * @var array
	 */
	private $exceptions = array();

	/**
	 * Class constructor.
	 *
	 * @param string $code Snippet code for parsing.
	 */
	public function __construct( $code ) {
		$this->code = $code;
		$this->tokens = token_get_all( "<?php\n" . $this->code );
		$this->length = count( $this->tokens );
		$this->current = 0;
	}

	/**
	 * Determine whether the parser has reached the end of the list of tokens.
	 *
	 * @return bool
	 */
	private function end() {
		return $this->current === $this->length;
	}

	/**
	 * Retrieve the next token without moving the pointer
	 *
	 * @return string|array|null The current token if the list has not been expended, null otherwise.
	 */
	private function peek() {
		return $this->end() ? null : $this->tokens[ $this->current ];
	}

	/**
	 * Move the pointer to the next token, if there is one
	 *
	 * If the first argument is provided, only move the pointer if the tokens match
	 *
	 * @return bool Whether the pointer was advanced.
	 */
	private function next() {
		if ( $this->end() ) {
			return false;
		}

		$this->current++;

		return true;
	}

	/**
	 * Check whether a particular identifier has been used previously.
	 *
	 * @param string $type       Which type of identifier this is. Supports T_FUNCTION, T_CLASS and T_INTERFACE.
	 * @param string $identifier The name of the identifier itself.
	 *
	 * @return bool true if the identifier is not unique.
	 */
	private function check_duplicate_identifier( $type, $identifier ) {

		if ( ! isset( $this->defined_identifiers[ $type ] ) ) {
			switch ( $type ) {
				case T_FUNCTION:
					$defined_functions = get_defined_functions();
					$this->defined_identifiers[ T_FUNCTION ] = array_merge( $defined_functions['internal'], $defined_functions['user'] );
					break;

				case T_CLASS:
					$this->defined_identifiers[ T_CLASS ] = get_declared_classes();
					break;

				case T_INTERFACE:
					$this->defined_identifiers[ T_INTERFACE ] = get_declared_interfaces();
					break;

				default:
					return false;
			}
		}

		$duplicate = in_array( $identifier, $this->defined_identifiers[ $type ], true );
		array_unshift( $this->defined_identifiers[ $type ], $identifier );

		return $duplicate && ! ( isset( $this->exceptions[ $type ] ) && in_array( $identifier, $this->exceptions[ $type ], true ) );
	}

	/**
	 * Validate the given PHP code and return the result.
	 * @return array|bool Array containing message if an error was encountered, false if validation was successful.
	 */
	public function validate() {

		while ( ! $this->end() ) {
			$token = $this->peek();
			$this->next();

			if ( ! is_array( $token ) ) {
				continue;
			}

			// if this is a function or class exists check, then allow this function or class to be defined
			if ( T_STRING === $token[0] && 'function_exists' === $token[1] || 'class_exists' === $token[1] ) {
				$type = 'function_exists' === $token[1] ? T_FUNCTION : T_CLASS;

				// eat tokens until we find the function or class name
				while ( ! $this->end() && T_CONSTANT_ENCAPSED_STRING !== $token[0] ) {
					$token = $this->peek();
					$this->next();
				}

				// add the identifier to the list of exceptions
				$this->exceptions[ $type ] = isset( $this->exceptions[ $type ] ) ? $this->exceptions[ $type ] : array();
				$this->exceptions[ $type ][] = trim( $token[1], '\'"' );
				continue;
			}

			// if we have a double colon, followed by a class, then consume it before the next section
			if ( T_DOUBLE_COLON === $token[0] ) {
				$token = $this->peek();
				$this->next();

				if ( T_CLASS === $token[0] ) {
					$this->next();
					$token = $this->peek();
				}
			}

			// only look for class and function declaration tokens
			if ( T_CLASS !== $token[0] && T_FUNCTION !== $token[0] ) {
				continue;
			}

			$structure_type = $token[0];

			// continue eating tokens until we find the name of the class or function
			while ( ! $this->end() && T_STRING !== $token[0] &&
			        ( T_FUNCTION !== $structure_type || '(' !== $token ) && ( T_CLASS !== $structure_type || '{' !== $token ) ) {
				$token = $this->peek();
				$this->next();
			}

			// if we've eaten all of the tokens without discovering a name, then there must be a syntax error, so return appropriately
			if ( $this->end() ) {
				return array(
					'message' => __( 'Parse error: syntax error, unexpected end of snippet.', 'code-snippets' ),
					'line'    => $token[2],
				);
			}

			// if the function or class is anonymous, with no name, then no need to check
			if ( ! ( T_FUNCTION === $structure_type && '(' === $token ) && ! ( T_CLASS === $structure_type && '{' === $token ) ) {

				// check whether the name has already been defined
				if ( $this->check_duplicate_identifier( $structure_type, $token[1] ) ) {
					switch ( $structure_type ) {
						case T_FUNCTION:
							/* translators: %s: PHP function name */
							$message = __( 'Cannot redeclare function %s.', 'code-snippets' );
							break;
						case T_CLASS:
							/* translators: %s: PHP class name */
							$message = __( 'Cannot redeclare class %s.', 'code-snippets' );
							break;
						case T_INTERFACE:
							/* translators: %s: PHP interface name */
							$message = __( 'Cannot redeclare interface %s.', 'code-snippets' );
							break;
						default:
							/* translators: %s: PHP identifier name*/
							$message = __( 'Cannot redeclare %s.', 'code-snippets' );
					}

					return array(
						'message' => sprintf( $message, $token[1] ),
						'line'    => $token[2],
					);
				}
			}

			// if we have entered into a class, eat tokens until we find the closing brace
			if ( T_CLASS !== $structure_type ) {
				continue;
			}

			// find the opening brace for the class
			while ( ! $this->end() && '{' !== $token ) {
				$token = $this->peek();
				$this->next();
			}

			// continue traversing the class tokens until we have found the class closing brace
			$depth = 1;
			while ( ! $this->end() && $depth > 0 ) {
				$token = $this->peek();

				if ( '{' === $token ) {
					$depth++;
				} elseif ( '}' === $token ) {
					$depth--;
				}

				$this->next();
			}

			// if we did not make it out of the class, then there's a problem
			if ( $depth > 0 ) {
				return array(
					'message' => __( 'Parse error: syntax error, unexpected end of snippet', 'code-snippets' ),
					'line'    => $token[2],
				);
			}
		}

		return false;
	}
}
