<?php
/**
 * Form block.
 *
 * @package ive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once IVE_DIR . 'src/blocks/form/field-attributes/index.php';
require_once IVE_DIR . 'src/blocks/form/field-label/index.php';
require_once IVE_DIR . 'src/blocks/form/field-description/index.php';

/**
 * Class IVE_Form_Block
 */
class IVE_Form_Block {
    /**
     * Unique ID.
     *
     * @var integer
     */
    public $form_id = 0;

    /**
     * Form post data after submit.
     *
     * @var array
     */
    public $form_post_data = array();

    /**
     * Default form block attribute values.
     *
     * @var array
     */
    public $default_attrs = array();

    /**
     * IVE_Form_Block constructor.
     */
    public function __construct() {
        add_action( 'template_redirect', array( $this, 'get_post_data' ) );
        add_action( 'wp_footer', array( $this, 'reset_post_data' ) );

        add_action( 'init', array( $this, 'init' ) );



        add_action( 'ive_form_email_before_send', array( $this, 'mail_before_send' ) );
        add_action( 'ive_form_email_after_send', array( $this, 'mail_after_send' ) );

        add_filter( 'render_block', array( $this, 'maybe_submit_form' ), 10, 2 );
    }

    /**
     * Save post data and redirect after form submitted.
     */
    public function get_post_data() {
        if ( ! is_admin() && ! session_id() ) {
          session_start();
        }

        // phpcs:disable
        if ( ! is_admin() && isset( $_POST['ive_form_id'] ) ) {
          $_SESSION['ive_form_submit_post'] = IVE_Loader::ive_sanitize_array( $_POST );
          wp_redirect( $_SERVER['REQUEST_URI'] );
          exit;
        }
        // phpcs:enable

        if ( isset( $_SESSION['ive_form_submit_post'] ) ) {
          $this->form_post_data = $_SESSION['ive_form_submit_post'];
        }
    }

    /**
     * Reset post data.
     */
    public function reset_post_data() {
        if ( ! empty( $this->form_post_data ) ) {
            $this->form_post_data = array();
        }
        if ( isset( $_SESSION['ive_form_submit_post'] ) ) {
            unset( $_SESSION['ive_form_submit_post'] );
        }
    }

    /**
     * Init.
     */
    public function init() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        $from_email  = '';
        $admin_email = get_option( 'admin_email' );
        $blogname    = get_option( 'blogname' );

        // phpcs:ignore
        if ( isset( $_SERVER[ 'SERVER_NAME' ] ) ) {
            // phpcs:ignore
            $sitename = strtolower( $_SERVER[ 'SERVER_NAME' ] );
        } else {
            $site_url       = site_url();
            $site_url_parts = wp_parse_url( $site_url );
            $sitename       = $site_url_parts['host'];
        }

        if ( substr( $sitename, 0, 4 ) === 'www.' ) {
            $sitename = substr( $sitename, 4 );
        }

        if ( strpbrk( $admin_email, '@' ) === '@' . $sitename ) {
            $from_email = $admin_email;
        } else {
            $from_email = 'wordpress@' . $sitename;
        }

        $this->default_attrs = array(
            'mailAllow'           => true,
            'mailTo'              => $admin_email,
            'mailSubject'         => $blogname . ' "{field_subject}"',
            'mailFrom'            => $blogname . ' ' . $from_email,
            'mailReplyTo'         => '{field_email}',
            'mailMessage'         => '{all_fields}',
            'confirmationType'    => 'message',
            'confirmationMessage' => esc_html__( 'Thank you for contacting us! We will be in touch with you shortly.', 'ibtana-visual-editor' ),
        );

        register_block_type(
            'ive/form',
            array(
                'render_callback' => array( $this, 'block_render' ),
                'attributes'      => array(
                    'mailAllow' => array(
                        'type'    => 'boolean',
                        'default' => $this->default_attrs['mailAllow'],
                    ),
                    'mailTo' => array(
                        'type'    => 'string',
                        'default' => $this->default_attrs['mailTo'],
                    ),
                    'mailSubject' => array(
                        'type'    => 'string',
                        'default' => $this->default_attrs['mailSubject'],
                    ),
                    'mailFrom' => array(
                        'type'    => 'string',
                        'default' => $this->default_attrs['mailFrom'],
                    ),
                    'mailReplyTo' => array(
                        'type'    => 'string',
                        'default' => $this->default_attrs['mailReplyTo'],
                    ),
                    'mailMessage' => array(
                        'type'    => 'string',
                        'default' => $this->default_attrs['mailMessage'],
                    ),

                    'confirmationType' => array(
                        'type'    => 'string',
                        'default' => $this->default_attrs['confirmationType'],
                    ),
                    'confirmationMessage' => array(
                        'type'    => 'string',
                        'default' => $this->default_attrs['confirmationMessage'],
                    ),
                    'confirmationRedirect' => array(
                        'type'    => 'string',
                    ),

                    'className' => array(
                        'type' => 'string',
                    ),
                ),
            )
        );
    }

    /**
     * Register gutenberg block output
     *
     * @param array  $attributes - block attributes.
     * @param string $inner_blocks - inner blocks.
     *
     * @return string
     */
    public function block_render( $attributes, $inner_blocks ) {
        ob_start();

        $class = 'ive-form';

        if ( isset( $attributes['className'] ) ) {
            $class .= ' ' . $attributes['className'];
        }

        $form_slug = 'ive_form_' . $this->form_id;
        $form_id   = 'gkt_form_' . hash( 'crc32b', $form_slug );

        $this->form_id += 1;

        $url  = add_query_arg( array() );
        $frag = strstr( $url, '#' );

        if ( $frag ) {
            $url = substr( $url, 0, -strlen( $frag ) );
        }

        $url .= '#' . $form_id;

        // Add unique slug to id and for attributes.
        $inner_blocks = str_replace( 'id="', 'id="' . $form_id . '_', $inner_blocks );
        $inner_blocks = str_replace( 'for="', 'for="' . $form_id . '_', $inner_blocks );

        // Google reCaptcha.
        $recaptcha_site_key   = get_option( 'ive_google_recaptcha_api_site_key' );
        $recaptcha_secret_key = get_option( 'ive_google_recaptcha_api_secret_key' );

        ?>

        <div class="<?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $form_id ); ?>">
          <form method="POST" name="ive-form" action="<?php echo esc_url( $url ); ?>">
            <?php
            // phpcs:ignore
            echo do_blocks( $inner_blocks );

            wp_nonce_field( 'ive_form', $form_id );
            ?>
            <input type="hidden" name="ive_form_id" value="<?php echo esc_attr( $form_id ); ?>">
            <?php
            if ( $recaptcha_site_key && $recaptcha_secret_key ) {
              ?>
              <input type="hidden" name="ive_form_google_recaptcha" />
              <?php
            }
            ?>
          </form>
        </div>

        <?php

        return ob_get_clean();
    }

    /**
     * Submit form and replace Form block output.
     *
     * @param string $block_content - block content.
     * @param array  $block - block attributes.
     *
     * @return string
     */
    public function maybe_submit_form( $block_content, $block ) {
        if ( ! isset( $block['blockName'] ) || 'ive/form' !== $block['blockName'] ) {
            return $block_content;
        }

        $form_id = '';

        preg_match( '/<input type="hidden" name="ive_form_id" value="(\w+)"/', $block_content, $parse_form_id );

        if ( $parse_form_id && isset( $parse_form_id[1] ) && $parse_form_id[1] ) {
            $form_id = $parse_form_id[1];
        }

        if ( ! $form_id ) {
            return $block_content;
        }

        $attributes = array_merge(
            $this->default_attrs,
            $block['attrs']
        );

        $class = 'ive-form';

        if ( isset( $attributes['className'] ) ) {
            $class .= ' ' . $attributes['className'];
        }

        $success     = false;
        $errors      = array();
        $new_content = '';

        // Form send.
        if ( isset( $this->form_post_data[ $form_id ] ) ) {
            $nonce = sanitize_text_field( wp_unslash( $this->form_post_data[ $form_id ] ) );

            if ( wp_verify_nonce( $nonce, 'ive_form' ) ) {
                // validate Google reCaptcha.
                if ( ! $this->verify_recaptcha() ) {
                    $errors[] = esc_html__( 'Google reCaptcha form verification failed.', 'ibtana-visual-editor' );
                }

                if ( ! count( $errors ) ) {
                    $success = true;

                    if ( $attributes['mailAllow'] ) {
                        do_action( 'ive_form_email_before_send', $form_id, $attributes );

                        $success = $this->process_mail( $attributes );

                        do_action( 'ive_form_email_after_send', $form_id, $attributes );
                    }

                    if ( ! $success ) {
                        $error    = error_get_last();
                        $errors[] = isset( $error['message'] ) ? $error['message'] : esc_html__( 'Something went wrong while trying to send the form.', 'ibtana-visual-editor' );
                    }
                }
            } else {
                $errors[] = esc_html__( 'Something went wrong while trying to send the form.', 'ibtana-visual-editor' );
            }

            $this->reset_post_data();
        }

        // Form submit errors.
        if ( ! empty( $errors ) ) {
          ob_start();
          ?>
          <div class="ive-alert ive-alert-form ive-alert-form-error">
            <div class="ive-alert-content">
              <?php
              foreach ( $errors as $error ) {
                echo '<p>' . esc_html( $error ) . '</p>';
              }
              ?>
            </div>
          </div>
          <?php
          $new_content = ob_get_clean();

            // Form submit success.
        } elseif ( $success ) {
            ob_start();
            if ( 'redirect' === $attributes['confirmationType'] ) {
                ?>
                <div class="ive-alert ive-alert-form ive-alert-form-success"><div class="ive-alert-content"><p>
                    <?php
                    echo wp_kses_post(
                        sprintf(
                            // translators: %s - redirect link.
                            __( 'Your form is successfully submitted. Redirecting to %s...', 'ibtana-visual-editor' ),
                            '<a href="' . esc_url( $attributes['confirmationRedirect'] ) . '">' . esc_url( $attributes['confirmationRedirect'] ) . '</a>'
                        )
                    );
                    ?>
                </p></div></div>
                <script>
                    setTimeout( () => {
                        window.location.href = "<?php echo esc_url( $attributes['confirmationRedirect'] ); ?>";
                    }, 3000 );
                </script>
                <?php
            } else {
                ?>
                <div class="ive-alert ive-alert-form ive-alert-form-success">
                  <div class="ive-alert-content">
                    <p>
                      <?php echo wp_kses_post( $attributes['confirmationMessage'] ); ?>
                    </p>
                  </div>
                </div>
                <?php
            }
            $new_content = ob_get_clean();
        }

        if ( $new_content ) {
            ob_start();
            ?>
            <div class="<?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $form_id ); ?>">
                <?php echo $new_content; // phpcs:ignore ?>
            </div>
            <?php
            return ob_get_clean();
        }

        return $block_content;
    }















    /**
     * Verify Google reCaptcha.
     *
     * @return bool
     */
    private function verify_recaptcha() {
        $recaptcha_secret_key = get_option( 'ive_google_recaptcha_api_secret_key' );
        $recaptcha_site_key   = get_option( 'ive_google_recaptcha_api_site_key' );

        // no captcha enabled.
        if ( ! $recaptcha_secret_key || ! $recaptcha_site_key ) {
            return true;
        }

        // phpcs:disable
        if ( ! isset( $this->form_post_data['ive_form_google_recaptcha'] ) ) {
            return false;
        }

        $token = sanitize_text_field( wp_unslash( $this->form_post_data['ive_form_google_recaptcha'] ) );
        // phpcs:enable

        // empty token.
        if ( ! $token ) {
            return false;
        }

        $verify_token_request = wp_remote_post(
            'https://www.google.com/recaptcha/api/siteverify',
            array(
                'timeout' => 30,
                'body'    => array(
                    'secret'   => $recaptcha_secret_key,
                    'response' => $token,
                ),
            )
        );

        if ( is_wp_error( $verify_token_request ) ) {
            return false;
        }

        $response = wp_remote_retrieve_body( $verify_token_request );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $response = json_decode( $response, true );

        if ( ! isset( $response['success'] ) ) {
            return false;
        }

        return $response['success'];
    }

    /**
     * Template string with POST data.
     *
     * @param String $string - string for template.
     *
     * @return string
     */
    public function template( $string ) {
        $all_fields = '';

        // phpcs:ignore
        foreach ( (array) $this->form_post_data as $name => $val ) {
            if ( is_array( $val ) && isset( $val['value'] ) ) {
                $sanitized_label = isset( $val['label'] ) ? sanitize_text_field( wp_unslash( $val['label'] ) ) : '';
                $sanitized_val   = '';

                if ( is_array( $val['value'] ) ) {
                    foreach ( $val['value'] as $val ) {
                        $sanitized_val .= '<li>' . sanitize_textarea_field( wp_unslash( $val ) ) . '</li>';
                    }
                    $sanitized_val = '<ul>' . $sanitized_val . '</ul>';
                } else {
                    $sanitized_val = sanitize_textarea_field( wp_unslash( $val['value'] ) );

                    // Name field support.
                    if ( isset( $val['middle'] ) ) {
                        $sanitized_val .= ' ' . sanitize_textarea_field( wp_unslash( $val['middle'] ) );
                    }
                    if ( isset( $val['last'] ) ) {
                        $sanitized_val .= ' ' . sanitize_textarea_field( wp_unslash( $val['last'] ) );
                    }
                }

                $string = str_replace( "{{$name}}", $sanitized_val, $string );

                $all_fields .= '
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="field-row"><tbody>
                        <tr><td class="field-row-label">' . ( $sanitized_label ? ( '<strong>' . $sanitized_label . '</strong>' ) : '' ) . '</td></tr>
                        <tr><td class="field-row-value">' . wpautop( $sanitized_val ) . '</td></tr>
                    </tbody></table>
                ';
            }
        }

        $string = str_replace( '{all_fields}', $all_fields, $string );

        return $string;
    }

    /**
     * Process email using wp_mail function.
     *
     * @param array $attributes - Form block attributes.
     *
     * @return boolean
     */
    public function process_mail( $attributes ) {
        // Template all attributes.
        foreach ( $attributes as $k => $attr ) {
            if ( is_string( $attr ) ) {
                $attributes[ $k ] = $this->template( $attr );
            }
        }

        // Prepare headers.
        $headers = "Content-Type: text/html; charset=utf-8\n";
        $headers = "From: {$attributes['mailFrom']}\n";
        $headers = "Return-Path: {$attributes['mailTo']}\n";
        $headers = "Reply-To: {$attributes['mailReplyTo']}\n";

        // Prepare message.
        $message = $this->get_mail_html( $attributes );

        return wp_mail( $attributes['mailTo'], $attributes['mailSubject'], $message, $headers );
    }

    /**
     * Get mail HTML template.
     *
     * @param array $attributes - From block attributes.
     *
     * @return string
     */
    public function get_mail_html( $attributes ) {
        ob_start();

        include IVE_DIR . 'src/blocks/form/mail-template/index.php';

        return ob_get_clean();
    }

    /**
     * Mail before send.
     */
    public function mail_before_send() {
        add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
    }

    /**
     * Mail after send.
     */
    public function mail_after_send() {
        remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
    }

    /**
     * Change wp_mail content type to HTML.
     *
     * @return string
     */
    public function get_content_type() {
        return 'text/html';
    }
}
new IVE_Form_Block();
