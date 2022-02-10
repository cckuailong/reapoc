<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$form_id   = absint( ! empty( $_GET['form_id'] ) ? $_GET['form_id'] : 0 );
$form      = wpbs_get_form( $form_id );

if( is_null( $form ) )
    return;

$form_meta = wpbs_get_form_meta($form_id);
$form_data = $form->get('fields');

$tabs 		= $this->get_tabs();
$active_tab = ( ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'form-builder' );

$settings = get_option( 'wpbs_settings', array() );
$active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());
$languages = wpbs_get_languages();

$removable_query_args = wp_removable_query_args();

$available_field_types = wpbs_form_available_field_types();
$available_field_types_options = wpbs_form_available_field_types_options();

?>

<script>
    var wpbs_languages = <?php echo json_encode($languages); ?>;
    var wpbs_form_data = <?php echo json_encode($form_data); ?>;
    var wpbs_available_field_types = <?php echo json_encode($available_field_types);?>;
    var wpbs_available_field_types_options = <?php echo json_encode($available_field_types_options);?>;
</script>


<?php if(wpbs_form_notifications_check_unused_fields($form_id, $form_data, $form_meta) == false): ?>
	<!-- Unused fields notice -->
	<div class="notice notice-error"> 
		<p><?php echo __( 'Uh-oh! You are using email tags in your notifications that are not available in the form anymore.', 'wp-booking-system' ); ?></p>
	</div>
<?php endif ?>

<div class="wrap wpbs-wrap wpbs-wrap-edit-form">

    <form method="POST" action="" autocomplete="off">

        <!-- Page Heading -->
        <h1 class="wp-heading-inline"><?php echo __( 'Edit Form', 'wp-booking-system' ); ?><span class="wpbs-heading-tag"><?php printf( __( 'Form ID: %d', 'wp-booking-system' ), $form_id ); ?></span></h1>

        <!-- Page Heading Actions -->
        <div class="wpbs-heading-actions">

            <!-- Back Button -->
            <a href="<?php echo add_query_arg( array( 'page' => 'wpbs-forms'), admin_url( 'admin.php' ) ); ?>" class="button-secondary">Back to all forms</a>

            <!-- Save button -->
            <input type="submit" class="wpbs-save-form button-primary" value="<?php echo __( 'Save Form', 'wp-booking-system' ); ?>" />
            
        </div>

        <hr class="wp-header-end" />

        <div id="poststuff">

            <!-- Form Title -->
            <div id="titlediv">
                <div id="titlewrap">
                    <input type="text" name="form_name" size="30" value="<?php echo esc_attr( $form->get('name') ) ?>" id="title">
                </div>
            </div>

            
            <!-- Navigation Tabs -->
            <h2 class="wpbs-nav-tab-wrapper nav-tab-wrapper">
                <?php
                    if( ! empty( $tabs ) ) {
                        foreach( $tabs as $tab_slug => $tab_name ) {

                            echo '<a href="' . add_query_arg( array( 'page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'tab' => $tab_slug ), admin_url('admin.php') ) . '" data-tab="' . $tab_slug . '" class="nav-tab wpbs-nav-tab ' . ( $active_tab == $tab_slug ? 'nav-tab-active' : '' ) . '">' . $tab_name . '</a>';

                        }
                    }
                ?>
            </h2>

            <!-- Tabs Contents -->
            <div class="wpbs-tab-wrapper">

                <?php

                    if( ! empty( $tabs ) ) {

                        foreach( $tabs as $tab_slug => $tab_name ) {

                            echo '<div class="wpbs-tab wpbs-tab-' . $tab_slug . ' ' . ( $active_tab == $tab_slug ? 'wpbs-active' : '' ) . '" data-tab="' . $tab_slug . '">';

                            switch($tab_slug){
                                case 'form-builder':
                                    include 'view-edit-form-tab-form-builder.php';
                                    break;

                                case 'form-options':
                                    include 'view-edit-form-tab-form-options.php';
                                    break;

                                case 'admin-notification':
                                    include 'view-edit-form-tab-admin-notification.php';
                                    break;

                                case 'user-notification':
                                    include 'view-edit-form-tab-user-notification.php';
                                    break;

                                case 'form-confirmation':
                                    include 'view-edit-form-tab-form-confirmation.php';
                                    break;
                                
                                case 'form-strings':
                                    include 'view-edit-form-tab-form-strings.php';
                                    break;

                                default:
                                    /**
                                     * Action to dynamically add content for each tab
                                     *
                                     */
                                    do_action( 'wpbs_submenu_page_edit_form_tab_' . $tab_slug );

                            }

                            echo '</div>';

                        }

                    }

                ?>
            </div>


        </div><!-- / #poststuff -->

        <!-- Hidden fields -->
        <input type="hidden" name="form_id" value="<?php echo $form_id; ?>" />
        
        <!-- Nonce -->
        <?php wp_nonce_field( 'wpbs_edit_form', 'wpbs_token', false ); ?>
        <input type="hidden" name="wpbs_action" value="edit_form" />

        <!-- Save button -->
        <input type="submit" class="wpbs-save-form button-primary" value="<?php echo __( 'Save Form', 'wp-booking-system' ); ?>" />

        <!-- Save Button Spinner -->
        <div class="wpbs-save-form-spinner spinner"><!-- --></div>

    </form>

</div>