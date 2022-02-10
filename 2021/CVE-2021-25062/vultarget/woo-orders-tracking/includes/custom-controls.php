<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	require_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
}
if ( class_exists( 'WP_Customize_Control' ) ) {
if ( !class_exists( 'VI_WOT_Customize_Radio_Control' ) ) {
	class VI_WOT_Customize_Radio_Control extends WP_Customize_Control {
		public $type = 'vi_wot_shipment_icon';
		protected $data = array();

		public function enqueue() {
			wp_enqueue_script( 'jquery-ui-button' );
			wp_enqueue_script( 'vi_wot_customize-customize-preview-control', VI_WOO_ORDERS_TRACKING_JS . 'admin-custom-control.js', array(
				'jquery'
			), VI_WOO_ORDERS_TRACKING_VERSION, true );
		}

		public function render_content() {
			?>
            <label>
				<?php
				if ( ! empty( $this->label ) ) {
					?>
                    <span class="customize-control-title"><?php esc_html_e( $this->label ); ?></span>
					<?php
				}
				foreach ( $this->choices as $choice => $value ) {
					?>
                    <div class="vi_wot_radio_button_img">
                        <input type="radio" value="<?php echo esc_attr( $choice ); ?>"
                               name="_customize-<?php echo esc_attr( $this->type ) . '-' . esc_attr( $this->id ); ?>"
                               id="<?php echo esc_attr( $this->id ) . '-choice' . esc_attr( $choice ); ?>"
							<?php
							$this->link();
							echo ( $this->value() == $choice ) ? ' checked="checked" ' : ''; ?> />
                        <label for="<?php echo esc_attr( $this->id ) . '-choice' . esc_attr( $choice ); ?>"><i
                                    class="<?php esc_attr_e( $value ) ?>"></i></label>
                    </div>
					<?php
				}
				?>
            </label>
			<?php
		}
	}
	}
}