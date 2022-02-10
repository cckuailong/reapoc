<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class QUBELY_Settings
{

    public $options;
    public $fields;

    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'init_settings' ) );
        add_action( 'wp_ajax_update_qubely_options', array( $this, 'ajax_update_qubely_options' ) );
    }


    /**
     * Settings Init
     * @since 1.5.2
     */
    public function init_settings() {
        require __DIR__ . '/Fields.php';
        $this->save_options();
        $this->option_setter();
    }

    /**
	 * @param array $input
	 *
	 * @return array
	 *
	 * Sanitize input array
	 */
	public function sanitize_settings_array( $input = array() ) {
		$array = array();

		if ( is_array( $input ) && count( $input ) ) {
			foreach ( $input as $key => $value ) {
				if ( is_array( $value ) ) {
					$array[ $key ] = $this->sanitize_array( $value );
				} else {
					$key           = sanitize_text_field( $key );
					$value         = sanitize_text_field( $value );
					$array[ $key ] = $value;
				}
			}
		}

		return $array;
	}

    /**
     * Update option using qubely
     * @since 1.5.2
     */
    public function ajax_update_qubely_options() {
        $new_options = isset( $_POST['options'] ) && is_array( $_POST['options'] ) ? $this->sanitize_settings_array( $_POST['options'] ) : array();
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'qubely_nonce' ) || ! $new_options ) {
            wp_send_json_error( 'No data or nonce failed' );
        };
        $options = get_option( 'qubely_options' );
        $updated_options = wp_parse_args( $new_options, $options );
        update_option( 'qubely_options', $updated_options );
        wp_send_json_success( $updated_options );
    }

    /**
     * Set options to the Class
     * @since 1.3.91
     */
    public function option_setter()
    {
        $this->options = (array) maybe_unserialize( get_option( 'qubely_options' ) );
        $this->fields = $this->fields();
    }

    /**
     * Save options to database
     * @since 1.3.91
     */
    public function save_options()
    {
        if (
            ! isset( $_POST['qubely_option_save'] ) ||
            ! isset( $_POST['_wpnonce'] ) ||
            ! wp_verify_nonce( $_POST['_wpnonce'], 'qubely_option_save' )
        ) return;

        $option = (array) isset( $_POST['qubely_options'] ) ? $this->sanitize_settings_array( $_POST['qubely_options'] ) : array();
        $option = apply_filters( 'qubely_options_input', $option );

        do_action( 'qubely_options_before_save', $option );
        update_option( 'qubely_options', $option );
        do_action( 'qubely_options_after_save', $option );
    }

    /**
     * @param null $key
     * @param bool $default
     * @return bool|mixed|void
     * Get option by key
     */
    public function get_option( $key = null, $default = false )
    {
        $options = $this->options;
        if ( empty( $options ) || ! is_array( $options ) || ! $key ) {
            return $default;
        }

        if ( array_key_exists( $key, $options ) ) {
            return apply_filters( $key, $options[ $key ] );
        }

        return $default;
    }

    /**
     * @return mixed|void
     * Settings Fields
     * @since 1.3.91
     */
    public function fields()
    {
        /**
         * Available Fields
         *
         * @text,
         * @number,
         * @date,
         * @email,
         * @month,
         * @search,
         * @url,
         * @time,
         * @tel,
         * @week,
         * @color,
         * @select
         * @checkbox
         */
        $skeleton = array(
            // Tab General
            'general' => array(
                'label' => 'General',
                'field_groups' => array(
                    "gmap" => array(
                        'label' => 'Google MAP',
                        'fields' => array(
                            'qubely_gmap_api_key' => array(
                                'type' => 'text',
                                'label' => __( 'Google Map API Keys', 'qubely' ),
                                'default' => '',
                                'desc' => sprintf( __( 'Enter your Google map api key, %1$s Generate API key %2$s', 'qubely' ), '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">', '</a>' ),
                                'placeholder' => '',
                                'suffix' => '',
                                'size' => 'regular',
                            )
                        )
                    ),
                    "recaptcha" => array(
                        'label' => 'Google ReCaptcha',
                        'fields' => array(
                            'qubely_recaptcha_site_key' => array(
                                'type' => 'text',
                                'label' => __( 'ReCaptcha site key', 'qubely' ),
                                'default' => '',
                                'desc' => __( 'Enter your ReCaptcha site key', 'qubely' ),
                                'placeholder' => '',
                                'class' => '',
                                'size' => 'regular',
                            ),
                            'qubely_recaptcha_secret_key' => array(
                                'type' => 'text',
                                'label' => __( 'ReCaptcha secret key', 'qubely' ),
                                'default' => '',
                                'desc' => sprintf( __( 'Enter your ReCaptcha secret key,  %1$s Get reCAPTCHA(v2) keys %2$s', 'qubely' ), "<a href='//www.google.com/recaptcha/admin/' >", "</a>" ),
                                'placeholder' => '',
                                'suffix' => '',
                                'size' => 'regular',
                            )
                        )
                    ),
                    "mailchimp" => array(
                        'label' => 'MailChimp',
                        'fields' => array(
                            'mailchimp_api_key' => array(
                                'type' => 'text',
                                'label' => __( 'Default Form Action', 'qubely' ),
                                'default' => '',
                                'desc' => sprintf( __( 'Enter your MailChimp Form Action, %1$s or Create a Signup form here %2$s', 'qubely' ), '<a href="https://mailchimp.com/help/add-a-signup-form-to-your-website/" target="_blank">', '</a>' ),
                                'placeholder' => '',
                                'suffix' => '',
                                'size' => 'regular',
                            ),
                        ),
                    ),
                    "qubely_email" => array(
                        'label' => 'Contact Form Settings',
                        'fields' => array(
                            'form_from_name' => array(
                                'type' => 'text',
                                'label' => __( 'From Name', 'qubely' ),
                                'default' => sanitize_text_field( get_option( 'blogname' ) ),
                                'desc' => __( 'Set the default "From Name" for contact forms' ),
                                'placeholder' => 'Qubely',
                                'suffix' => '',
                                'size' => 'regular',
                            ),
                            'form_from_email' => array(
                                'type' => 'text',
                                'label' => __( 'From Email', 'qubely' ),
                                'default' => sanitize_email( get_option( 'admin_email' ) ),
                                'desc' => __( 'Set the default "From Email" for contact forms' ),
                                'placeholder' => 'admin@example.com',
                                'suffix' => '',
                                'size' => 'regular',
                            ),
                        ),
                    )
                )
            ),
            // Tab Advanced
            'advanced' => array(
                'label' => 'Advanced',
                'fields' => array(
                    'css_save_as' => array(
                        'type' => 'select',
                        'label' => __( 'CSS location', 'qubely' ),
                        'default' => '',
                        'desc' => __( 'Select where you want to save CSS', 'qubely' ),
                        'options' => array(
                            'wp_head'   => __( 'Header', 'qubely' ),
                            'filesystem' => __( 'File System', 'qubely' ),
                        ),
                        'suffix' => '',
                        'size' => 'regular',
                    ),
                    'import_with_global_settings' => array(
                        'type' => 'select',
                        'label' => __( 'Use global settings with Import layouts/section', 'qubely' ),
                        'default' => 'manual',
                        'desc' => __( 'Apply global settings while importing layouts/sections', 'qubely' ),
                        'options' => array(
                            'manually'   => __( 'Manually', 'qubely' ),
                            'always' => __( 'Always', 'qubely' ),
                            'never' => __( 'Never', 'qubely' ),
                        ),
                        'suffix' => '',
                        'size' => 'regular',
                    ),
                    'load_font_awesome_CSS' => array(
                        'type' => 'select',
                        'label' => __( 'Load Font Awesome CSS', 'qubely' ),
                        'default' => 'yes',
                        'desc' => __( 'Select Yes if you want to load Font Awesome from Qubely', 'qubely' ),
                        'options' => array(
                            'yes'   => __( 'Yes', 'qubely' ),
                            'no' => __( 'No', 'qubely' ),
                        ),
                        'suffix' => '',
                        'size' => 'regular',
                    ),
                    'load_google_fonts' => array(
                        'type' => 'select',
                        'label' => __( 'Load Google Fonts', 'qubely' ),
                        'default' => 'yes',
                        'desc' => __( 'Select Yes if you want to load Google Fonts from Qubely', 'qubely' ),
                        'options' => array(
                            'yes'   => __( 'Yes', 'qubely' ),
                            'no' => __( 'No', 'qubely' ),
                        ),
                        'suffix' => '',
                        'size' => 'regular',
                    ),
                )
            )
        );

        return apply_filters( 'qubely_options', $skeleton );
    }

    /**
     * Setting Page Markup
     * @since 1.3.91
     */
    public function markup()
    {
?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Qubely Settings', 'qubely' ); ?></h1>
            <div id="qubely-settings-tabs" class="nav-tab-wrapper">
                <?php
                $index = 0;
                foreach ( $this->fields() as $key => $options ) {
                    $index++;

                    // if (!isset($options['fields']) || !is_array($options['fields'])) continue;
                    $options['label'] = ! empty( $options['label'] ) ? $options['label'] : $key;
                ?>
                    <a class="nav-tab <?php echo esc_attr( $index === 0 ? 'nav-tab-active' : '' )  ?>" href="#<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $options['label'] ) ?></a>
                <?php
                }
                ?>
            </div>
            <form id="qubely-settings-tabs-content" method="POST">
                <?php wp_nonce_field( 'qubely_option_save' ) ?>
                <?php
                $index = 0;
                foreach ( $this->fields() as $key => $options ) {
                    $index++;
                ?>
                    <div class="qubely-settings-inner" id="<?php echo esc_attr( $key ); ?>">
                        <?php if ( isset( $options['fields'] ) && is_array( $options['fields'] ) && count( $options['fields'] ) ) { ?>
                            <table class="form-table">
                                <tbody>
                                    <?php
                                    foreach ( $options['fields'] as $field_key => $field ) {
                                        $field['key'] = $field_key;
                                        $field['value'] = $this->get_option( $field_key, $field['default'] );
                                        QUBELY_Fields::get( $field['type'], $field );
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php } ?>
                        <?php
                            if (
                                isset( $options['field_groups'] ) &&
                                is_array( $options['field_groups'] ) &&
                                count( $options['field_groups'] )
                            ) {
                                foreach ( $options['field_groups'] as $group_key => $group ) {
                                    $label = isset( $group['label'] ) ? $group['label'] : null;
                                    $description = isset( $group['description'] ) ? $group['description'] : null;
                                    echo $label ? '<h2>' . esc_html( $label ) . '</h2>' : '';
                                    echo $description ? wp_kses_post( $description ) : "";
                                    echo "<table class='form-table'><tbody>";
                                    foreach ( $group['fields'] as $field_key => $field ) {
                                        $field['key'] = $field_key;
                                        $field['value'] = $this->get_option( $field_key, $field['default'] );
                                        QUBELY_Fields::get( $field['type'], $field );
                                    }
                                    echo "</tbody></table>";
                                }
                            }
                        ?>
                    </div>
                <?php
                }
                submit_button( 'Save changes', 'primary', 'qubely_option_save' );
                ?>
            </form>
        </div>
<?php
    }
}