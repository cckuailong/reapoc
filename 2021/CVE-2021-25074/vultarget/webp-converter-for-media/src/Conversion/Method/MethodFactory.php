<?php

namespace WebpConverter\Conversion\Method;

use WebpConverter\Conversion\Format\FormatFactory;
use WebpConverter\Conversion\SkipCrashed;
use WebpConverter\Conversion\SkipLarger;
use WebpConverter\Repository\TokenRepository;
use WebpConverter\Service\ServerConfigurator;

/**
 * Adds support for all conversion methods and returns information about them.
 */
class MethodFactory {

	/**
	 * @var SkipCrashed
	 */
	private $skip_crashed;

	/**
	 * @var SkipLarger
	 */
	private $skip_larger;

	/**
	 * @var TokenRepository
	 */
	private $token_repository;

	/**
	 * @var ServerConfigurator
	 */
	private $server_configurator;

	/**
	 * Objects of supported conversion methods.
	 *
	 * @var MethodInterface[]
	 */
	private $methods = [];

	public function __construct(
		SkipCrashed $skip_crashed = null,
		SkipLarger $skip_larger = null,
		TokenRepository $token_repository = null,
		ServerConfigurator $server_configurator = null
	) {
		$this->skip_crashed        = $skip_crashed ?: new SkipCrashed();
		$this->skip_larger         = $skip_larger ?: new SkipLarger();
		$this->token_repository    = $token_repository ?: new TokenRepository();
		$this->server_configurator = $server_configurator ?: new ServerConfigurator();

		$this->set_integration( new ImagickMethod( $this->skip_crashed, $this->skip_larger, $this->server_configurator ) );
		$this->set_integration( new GdMethod( $this->skip_crashed, $this->skip_larger, $this->server_configurator ) );
		$this->set_integration( new RemoteMethod( $this->skip_larger, $this->token_repository, $this->server_configurator ) );
	}

	/**
	 * Sets integration for method.
	 *
	 * @param MethodInterface $method .
	 *
	 * @return void
	 */
	private function set_integration( MethodInterface $method ) {
		$this->methods[ $method->get_name() ] = $method;
	}

	/**
	 * Returns objects of conversion methods.
	 *
	 * @return MethodInterface[] .
	 */
	public function get_methods_objects(): array {
		$values = [];
		foreach ( $this->methods as $method ) {
			$values[ $method->get_name() ] = $method;
		}
		return $values;
	}

	/**
	 * Returns list of conversion methods.
	 *
	 * @return string[] Names of conversion methods with labels.
	 */
	public function get_methods(): array {
		$values = [];
		foreach ( $this->get_methods_objects() as $method_name => $method ) {
			$values[ $method_name ] = $method->get_label();
		}
		return $values;
	}

	/**
	 * Returns list of installed conversion methods.
	 *
	 * @return string[] Names of conversion methods with labels.
	 */
	public function get_available_methods(): array {
		$token_status = $this->token_repository->get_token()->get_valid_status();
		$values       = [];
		foreach ( $this->get_methods_objects() as $method_name => $method ) {
			if ( ! $method::is_method_installed()
				|| ( ! ( new FormatFactory() )->get_available_formats( $method_name ) ) ) {
				continue;
			}

			if ( ( $token_status && $method::is_pro_feature() ) || ( ! $token_status && ! $method::is_pro_feature() ) ) {
				$values[ $method_name ] = $method->get_label();
			}
		}
		return $values;
	}

	/**
	 * Returns status if conversion method is active.
	 *
	 * @param string $method_name   Name of conversion method.
	 * @param string $output_format Extension of output format.
	 *
	 * @return bool Is method active?
	 */
	public function is_method_available( string $method_name, string $output_format ): bool {
		return ( isset( $this->methods[ $method_name ] )
			&& $this->methods[ $method_name ]->is_method_active( $output_format ) );
	}
}
