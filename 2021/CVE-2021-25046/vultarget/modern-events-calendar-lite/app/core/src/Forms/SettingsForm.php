<?php

namespace MEC\Forms;

use MEC\Singleton;

class SettingsForm extends Singleton {

	/**
	 * @var \MEC_Main
	 */
	public $main;

	public $enqueue;

	/**
	 * @var \MEC\Forms\FormFields
	 */
	private $fieldFactory;

	public function __construct() {

		$this->main = new \MEC_Main();
	}

	public function enqueue() {

		if ( true !== $this->enqueue ) {

			wp_enqueue_style( 'mec-backend', plugin_dir_url( __FILE__ ) . 'backend.css' );
			wp_enqueue_script( 'mec-backend', plugin_dir_url( __FILE__ ) . 'backend.js', array( 'jquery' ) );
			$this->enqueue = true;
		}
	}

	/**
	 * Get Form Fields
	 *
	 * @param $group_id
	 *
	 * @return array
	 */
	public function get_fields( $group_id ) {

		return CustomForm::getInstance()->get_reg_fields( $group_id );
	}

	/**
	 * @param $group_id
	 *
	 * @return array
	 */
	public function get_fixed_fields( $group_id ) {

		$fixed_fields = CustomForm::getInstance()->get_fixed_fields( $group_id );
		if ( !is_array( $fixed_fields ) ) {
			$fixed_fields = array();
		}

		return $fixed_fields;
	}

	/**
	 * @param string $type
	 *
	 * @return mixed|void
	 */
	public function get_element_fields( $type = 'reg' ) {

		$elements = array(
				'first_name'      => array(
					'required' => true,
					'text'     => __( 'MEC First Name', 'modern-events-calendar-lite' ),
					'class'    => 'red',
				),
				'last_name'      => array(
					'required' => true,
					'text'     => __( 'MEC Last Name', 'modern-events-calendar-lite' ),
					'class'    => 'red',
				),
				'mec_email' => array(
						'required' => true,
						'text'     => __( 'MEC Email', 'modern-events-calendar-lite' ),
						'class'    => 'red',
				),
				'text'      => array(
						'required' => false,
						'text'     => __( 'Text', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
				'email'     => array(
						'required' => false,
						'text'     => __( 'Email', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
				'date'      => array(
						'required' => false,
						'text'     => __( 'Date', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
				'tel'       => array(
						'required' => false,
						'text'     => __( 'Tel', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
				'file'      => array(
						'required' => false,
						'text'     => __( 'File', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
				'textarea'  => array(
						'required' => false,
						'text'     => __( 'Textarea', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
				'checkbox'  => array(
						'required' => false,
						'text'     => __( 'Checkboxes', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
				'radio'     => array(
						'required' => false,
						'text'     => __( 'Radio Buttons', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
				'select'    => array(
						'required' => false,
						'text'     => __( 'Dropdown', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
				'agreement' => array(
						'required' => false,
						'text'     => __( 'Agreement', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
				'p'         => array(
						'required' => false,
						'text'     => __( 'Paragraph', 'modern-events-calendar-lite' ),
						'class'    => '',
				),
		);

		if ( 'reg' !== $type ) {

			unset( $elements['first_name'] );
			unset( $elements['last_name'] );
			unset( $elements['mec_email'] );
		}

		return apply_filters( 'mec_get_element_fields', $elements );
	}

	/**
	 * @param array  $fields
	 * @param string $group_id
	 * @param string $type reg|bfixed|$custom
	 */
	public function display_fields( $fields, $group_id, $type = 'reg' ) {

		$type_fields = $type;
		$type        = $group_id . '_' . $type;
		add_action( 'admin_footer', array( $this, 'enqueue' ) );
		?>
		<div class="mec-container">
			<?php do_action( 'before_mec_' . $type . '_fields_form' ); ?>
			<div class="mec-form-row" id="mec_<?php echo $type ?>_form_container" data-form-type="<?php echo $type; ?>">
				<?php do_action( 'mec_' . $type . '_fields_form_start' ); ?>
				<?php /** Don't remove this hidden field **/ ?>
				<input type="hidden" name="mec[<?php echo $type ?>_fields]" value=""/>

				<ul id="mec_<?php echo $type ?>_form_fields" class="mec_form_fields">
					<?php

					$i = 0;
					foreach ( $fields as $key => $field_args ) {
						if(in_array($key, [':i:',':fi:','_i_','_fi_',], true)){

							continue;
						}
						$i = max( $i, (int)$key );


						echo $this->display_field( $key, $field_args, $type );
					}

					?>
				</ul>
				<div id="mec_<?php echo $type ?>_form_field_types" class="mec_form_field_types">
					<?php
					$elements = $this->get_element_fields( $type_fields );
					foreach ( $elements as $element_id => $element ) {

						$text  = isset( $element['text'] ) ? $element['text'] : '';
						$class = isset( $element['class'] ) ? $element['class'] : '';
						echo '<button type="button" class="button ' . $class . '" data-type="' . $element_id . '">' . $text . '</button>';
					}

					?>
				</div>
				<?php do_action( 'mec_' . $type . '_fields_form_end' ); ?>
			</div>
			<?php do_action( 'after_mec_' . $type . '_fields_form' ); ?>

			<input type="hidden" id="mec_new_<?php echo $type ?>_field_key" value="<?php echo $i + 1; ?>"/>
			<div class="mec-util-hidden">
				<?php
				foreach ( $elements as $element_id => $element ) {
					$method = 'field_' . $element_id;
					if ( method_exists( FormFields::class, $method ) ) {

						echo '<div id="mec_' . $type . '_field_' . $element_id . '" class="mec_field_' . $element_id . '">' .
							 $this->display_field( ':i:', [ 'type' => $element_id ], $type )
							 . '</div>';
					}
				}

				?>
				<div id="mec_<?php echo $type ?>_option" class="mec_field_option">
					<?php echo FormFields::getInstance()->field_option( ':fi:', ':i:', array(), $type ); ?>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * @param string $key
	 * @param array  $field_args
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function display_field( $key, $field_args, $prefix = 'reg' ) {

		$type = isset( $field_args['type'] ) ? $field_args['type'] : false;

		if ( !$type ) {

			return '';
		}

		if ( is_null( $this->fieldFactory ) ) {

			$this->fieldFactory = FormFields::getInstance();
		}

		$html = '';
		switch ( $type ) {
			case 'text':
				$html .= $this->fieldFactory->field_text( $key, $field_args, $prefix );
				break;
			case 'name':
				$html .= $this->fieldFactory->field_first_name( $key, $field_args, $prefix );
				break;
			case 'first_name':
				$html .= $this->fieldFactory->field_first_name( $key, $field_args, $prefix );
				break;
			case 'last_name':
				$html .= $this->fieldFactory->field_last_name( $key, $field_args, $prefix );
				break;
			case 'mec_email':
				$html .= $this->fieldFactory->field_mec_email( $key, $field_args, $prefix );
				break;
			case 'email':
				$html .= $this->fieldFactory->field_email( $key, $field_args, $prefix );
				break;
			case 'date':
				$html .= $this->fieldFactory->field_date( $key, $field_args, $prefix );
				break;
			case 'file':
				$html .= $this->fieldFactory->field_file( $key, $field_args, $prefix );
				break;
			case 'tel':
				$html .= $this->fieldFactory->field_tel( $key, $field_args, $prefix );
				break;
			case 'textarea':
				$html .= $this->fieldFactory->field_textarea( $key, $field_args, $prefix );
				break;
			case 'p':
				$html .= $this->fieldFactory->field_p( $key, $field_args, $prefix );
				break;
			case 'checkbox':
				$html .= $this->fieldFactory->field_checkbox( $key, $field_args, $prefix );
				break;
			case 'radio':
				$html .= $this->fieldFactory->field_radio( $key, $field_args, $prefix );
				break;
			case 'select':
				$html .= $this->fieldFactory->field_select( $key, $field_args, $prefix );
				break;
			case 'agreement':
				$html .= $this->fieldFactory->field_agreement( $key, $field_args, $prefix );
				break;
		}

		return $html;
	}

	public function display_settings_form_fields( $group_id, $fields = null) {

		$type   = 'reg';
		if( is_null( $fields ) ){

			$fields = $this->get_fields( $group_id );
		}

		$this->display_fields( $fields, $group_id, $type );
	}

	public function display_settings_form_fixed_fields( $group_id, $fields = null ) {

		$type   = 'bfixed';
		if( is_null( $fields ) ){

			$fields = $this->get_fixed_fields( $group_id );
		}

		$this->display_fields( $fields, $group_id, $type );
	}

}