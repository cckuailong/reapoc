<?php

class DLM_Admin_Fields_Field_Title extends DLM_Admin_Fields_Field {

	/** @var string */
	private $link;

	/** @var string */
	private $title;

	/**
	 * DLM_Admin_Fields_Field_Title constructor.
	 *
	 * @param String $title
	 */
	public function __construct( $title ) {
		$this->title = $title;
		parent::__construct( '', '', '' );
	}

	/**
	 * Renders field
	 */
	public function render() {
		?>
        <h3><?php echo esc_html( $this->title ); ?></h3>
		<?php
	}

}