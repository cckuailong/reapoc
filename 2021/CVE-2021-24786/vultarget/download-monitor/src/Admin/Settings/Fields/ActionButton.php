<?php

class DLM_Admin_Fields_Field_ActionButton extends DLM_Admin_Fields_Field {

	/** @var string */
	private $link;

	/** @var string */
	private $label;

	/**
	 * DLM_Admin_Fields_Field constructor.
	 *
	 * @param String $name
	 * @param String $link
	 * @param String $label
	 */
	public function __construct( $name, $link, $label ) {
		$this->link  = $link;
		$this->label = $label;
		parent::__construct( $name, '', '' );
	}

	/**
     * Generate nonce
     *
	 * @return string
	 */
	private function generate_nonce() {
		return wp_create_nonce( $this->get_name() );
	}

	/**
     * Get prepped URL
     *
	 * @return string
	 */
	private function get_url() {
		return add_query_arg( array(
			'dlm_action' => $this->get_name(),
			'dlm_nonce'  => $this->generate_nonce()
		), $this->link );
    }

	/**
	 * Renders field
	 *
	 * The Button is quite an odd 'field'. It's basically just an a tag.
	 */
	public function render() {
		?>
        <a class="button" href="<?php echo $this->get_url(); ?>"><?php echo $this->label; ?></a>
		<?php
	}

}