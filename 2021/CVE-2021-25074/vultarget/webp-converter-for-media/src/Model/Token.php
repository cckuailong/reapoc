<?php

namespace WebpConverter\Model;

/**
 * Stores token information for the PRO version.
 */
class Token {

	/**
	 * @var string|null
	 */
	private $token_value;

	/**
	 * @var bool
	 */
	private $valid_status;

	/**
	 * @var int
	 */
	private $images_usage;

	/**
	 * @var int
	 */
	private $images_limit;

	public function __construct(
		string $token_value = null,
		bool $valid_status = false,
		int $images_usage = 0,
		int $images_limit = 0
	) {
		$this->token_value  = $token_value;
		$this->valid_status = $valid_status;
		$this->images_usage = $images_usage;
		$this->images_limit = $images_limit;
	}

	public function set_token_value( string $token_value = null ): self {
		$this->token_value = $token_value;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function get_token_value() {
		return $this->token_value;
	}

	public function set_valid_status( bool $status ): self {
		$this->valid_status = $status;
		return $this;
	}

	public function get_valid_status(): bool {
		return $this->valid_status;
	}

	public function set_images_usage( int $count ): self {
		$this->images_usage = $count;
		return $this;
	}

	public function get_images_usage(): int {
		return $this->images_usage;
	}

	public function set_images_limit( int $count ): self {
		$this->images_limit = $count;
		return $this;
	}

	public function get_images_limit(): int {
		return $this->images_limit;
	}
}
