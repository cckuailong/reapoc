<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

//RTEC_ADMIN_URL
$rtec = RTEC();
$form = $rtec->form->instance();
$db = $rtec->db_frontend->instance();

$admin_registrations = new RTEC_Admin_Registrations();
$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'registrations';

$view_type = isset( $_GET['v'] ) ? sanitize_text_field( $_GET['v'] ) : 'grid';
$query_type = isset( $_GET['qtype'] ) ? sanitize_text_field( $_GET['qtype'] ) : 'upcoming';
$start_date = isset( $_GET['start'] ) ? date( 'Y-m-d H:i:s', strtotime( $_GET['start'] ) ) : date( 'Y-m-d H:i:s' );
$reg_status = isset( $_GET['with'] ) ? sanitize_text_field( $_GET['with'] ) : 'with';
$query_offset = isset( $_GET['off'] ) ? max( (int)$_GET['off'], 0 ) : 0;
$event_id = isset( $_GET['id'] ) ? (int)$_GET['id'] : 0;
$settings = array(
    'v' => $view_type,
    'qtype' => $query_type,
    'with' => $reg_status,
    'off' => $query_offset,
    'start' => $start_date,
    'id' => $event_id
);

$admin_registrations->build_admin_registrations( $tab, $settings );

$admin_registrations->the_registrations_detailed_view();

$form->build_form( $event_id );
$fields_atts = $form->get_field_attributes();
$event_meta = $form->get_event_meta();
$event_obj = new RTEC_Admin_Event();
$event_obj->build_admin_event( $event_id, 'single', '', $form );
$admin_registrations->add_event_id_on_page( $event_id );

$custom_column_keys = $event_obj->form_obj->get_custom_column_keys();
$custom_fields_label_name_pairs = $event_obj->form_obj->get_custom_fields_label_name_pairs();
$custom_fields_name_label_pairs = array_flip ( $custom_fields_label_name_pairs );

?>
<h1><?php _e( 'Single Event Details', 'registrations-for-the-events-calendar' ); ?></h1>
<a id="rtec-back-overview" href="<?php $admin_registrations->the_toolbar_href( 'tab', 'registrations' ) ?>"><?php _e( 'Back to Overview', 'registrations-for-the-events-calendar' ); ?></a>

<input type="hidden" value="<?php echo esc_attr( $event_id ); ?>" name="event_id">
<div class="rtec-wrapper rtec-single<?php echo $event_obj->get_single_event_wrapper_classes(); ?>">
    <div class="rtec-single-event" data-rtec-event-id="<?php echo esc_attr( $event_id ); ?>" data-rtec-field-atts="<?php echo esc_attr( json_encode( $fields_atts ) ); ?>">
        <div class="rtec-event-meta">
            <?php do_action( 'rtec_registrations_tab_event_meta', $event_obj ); ?>
        </div>

        <table class="widefat wp-list-table fixed striped posts rtec-registrations-data"">
        <thead>
        <tr>
            <td scope="col" class="manage-column column-rtec check-column">
                <label class="screen-reader-text" for="rtec-select-all-1"><?php _e( 'Select All', 'registrations-for-the-events-calendar' ); ?></label>
                <input type="checkbox" id="rtec-select-all-1">
            </td>
            <?php foreach ( $event_obj->labels as $label ) : ?>
                <th><?php echo esc_html( stripslashes( $label ) ); ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <?php if ( ! empty( $event_obj->registrants_data ) ) : ?>
            <tbody>
            <?php foreach( $event_obj->registrants_data as $registration ) : ?>
                <?php
                $custom_data = isset( $registration['custom'] ) ? maybe_unserialize( $registration['custom'] ) : array();
                $is_user = isset( $registration['user_id'] ) && (int)$registration['user_id'] > 0 ? true : false;
                if ( isset( $registration['registration_date'] ) ) {
                	$time_format = rtec_get_time_format();
                    $registration['registration_date'] = date_i18n( 'F jS, ' . $time_format, strtotime( $registration['registration_date'] ) + rtec_get_time_zone_offset() );
                }
                ?>
                <tr class="rtec-reg-row<?php echo $admin_registrations->get_registrant_tr_classes( $registration['status'], $is_user ); ?>" data-rtec-id="<?php echo esc_attr( (int)$registration['id'] ); ?>">
                    <td scope="row" class="check-column rtec-checkbox">
                        <label class="screen-reader-text" for="rtec-select-<?php echo esc_attr( (int)$registration['id'] ); ?>">Select <?php echo esc_html( $registration['first_name'] ) . ' ' . esc_html( $registration['last_name'] ); ?></label>
                        <input type="checkbox" value="<?php echo esc_attr( (int)$registration['id'] ); ?>" id="rtec-select-<?php echo esc_attr( (int)$registration['id'] ) ?>" class="rtec-registration-select check-column" data-rtec-registration="<?php echo str_replace( '\:', '&#92;&#58;', esc_attr( json_encode( $registration ) ) ); ?>">
                        <div class="locked-indicator"></div>
                        <?php echo $admin_registrations->get_registrant_icons( $registration['status'], $is_user ); ?>
                    </td>
                    <?php foreach ( $event_obj->columns as $column ) : ?>
                        <?php if ( $column === 'registration_date' ) : ?>
                            <td class="rtec-data-cell rtec-reg-registration_date" data-rtec-value="<?php echo esc_attr( stripslashes( $registration['registration_date'] ) ); ?>"><?php echo esc_html( stripslashes( $registration['registration_date'] ) ); ?></td>
                        <?php elseif ( $column === 'venue' ) : ?>
                            <td class="rtec-data-cell rtec-reg-<?php echo $column; ?>" data-rtec-key="<?php echo $column; ?>" data-rtec-value="<?php if ( isset( $event_meta['mvt_fields'][ $registration[ $column ] ]['label'] ) ) { echo esc_html( $event_meta['mvt_fields'][ $registration[ $column ] ]['label'] ); } else { echo esc_html( stripslashes( $registration[ $column ] ) ); }?>"><?php if ( isset( $event_meta['mvt_fields'][ $registration[ $column ] ]['label'] ) ) { echo esc_html( $event_meta['mvt_fields'][ $registration[ $column ] ]['label'] ); } else { echo esc_html( stripslashes( $registration[ $column ] ) ); }?></td>
                        <?php elseif ( $column === 'phone' ) : ?>
                            <td class="rtec-data-cell rtec-reg-<?php echo $column; ?>" data-rtec-key="<?php echo $column; ?>" data-rtec-value="<?php echo esc_attr( rtec_format_phone_number( $registration[ $column ] ) ); ?>"><?php echo esc_html( rtec_format_phone_number( $registration[ $column ] ) ); ?></td>
                        <?php elseif ( in_array( $column,  $custom_column_keys, true ) ) :
                            // check what data structure is being used
                            $dep_data_structure = false;
                            if ( isset( $custom_fields_name_label_pairs[ $column ] ) && isset( $custom_data[ $custom_fields_name_label_pairs[ $column ] ] ) ) {
                                $dep_data_structure = true;
                            }
                            if ( $dep_data_structure === false ) :
                                $value = isset( $custom_data[ $column ]['value'] ) ? $custom_data[ $column ]['value'] : '';
                                echo '<td class="rtec-data-cell rtec-reg-custom" data-rtec-custom-key="'. esc_attr( $column ) . '" data-rtec-value="' . esc_attr( stripslashes( $value ) ) . '">' . esc_html( stripslashes( $value ) ) . '</td>';
                            elseif ( is_array( $custom_data ) && isset( $custom_data[ $column ] ) && is_array( $custom_data[ $column ] ) ) :
                                $value = isset( $custom_data[ $column ]['value'] ) ? $custom_data[ $column ]['value'] : '';
                                echo '<td class="rtec-data-cell rtec-reg-custom" data-rtec-custom-key="'. esc_attr( $column ) . '" data-rtec-value="' . esc_attr( stripslashes( $value ) ) . '">' . esc_html( stripslashes( $value ) ) . '</td>';
                            elseif ( isset( $custom_data[ $custom_fields_name_label_pairs[ $column ] ] ) ) :
                                $value = $custom_data[ $custom_fields_name_label_pairs[ $column ] ];
                                echo '<td class="rtec-data-cell rtec-reg-custom" data-rtec-custom-key="'. esc_attr( $column ) . '" data-rtec-value="' . esc_attr( stripslashes( $value ) ) . '">' . esc_html( stripslashes( $value ) ) . '</td>';
                            endif;
                        else :
                            $data = isset( $registration[ $column ] ) ? $registration[ $column ] : '';
                            ?>
                            <td class="rtec-data-cell rtec-reg-<?php echo $column; ?>" data-rtec-key="<?php echo $column; ?>" data-rtec-value="<?php echo esc_attr( stripslashes( $data ) ); ?>"><?php echo esc_html( stripslashes( $data ) ); ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </tr>
            <?php endforeach; ?>
            </tbody>
            <?php if ( count( $event_obj->registrants_data ) > 14 ) : ?>
                <tfoot>
                <tr>
                    <td scope="col" class="manage-column column-rtec check-column">
                        <label class="screen-reader-text" for="rtec-select-all-1"><?php _e( 'Select All', 'registrations-for-the-events-calendar' ); ?></label>
                        <input type="checkbox" id="rtec-select-all-1">
                    </td>
                    <?php foreach ( $event_obj->labels as $label ) : ?>
                        <?php if ( ! empty( $label ) ) : ?>
                            <th><?php echo esc_html( stripslashes( $label ) ); ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                </tfoot>
            <?php endif; ?>
        <?php else: ?>
            <tbody>
            <tr class="rtec-reg-row" data-rtec-id="" style="display: none;">
                <td scope="row" class="check-column rtec-checkbox">
                    <label class="screen-reader-text" for="rtec-select-">Select </label>
                    <input type="checkbox" value="" id="rtec-select-" class="rtec-registration-select check-column" data-rtec-registration="{}">
                    <div class="locked-indicator"></div>
                </td>
                <?php foreach ( $event_obj->columns as $column ) : ?>
                    <?php if ( $column === 'registration_date' ) : ?>
                        <td class="rtec-data-cell rtec-reg-registration_date"></td>
                    <?php elseif ( $column === 'venue' ) : ?>
                        <td class="rtec-data-cell rtec-reg-<?php echo $column; ?>" data-rtec-key="<?php echo $column; ?>"></td>
                    <?php elseif ( in_array( $column,  $custom_column_keys, true ) ) :
                        // check what data structure is being used
                        $dep_data_structure = false;
                        if ( isset( $custom_fields_name_label_pairs[ $column ] ) && isset( $custom_data[ $custom_fields_name_label_pairs[ $column ] ] ) ) {
                            $dep_data_structure = true;
                        }
                        if ( $dep_data_structure === false ) :
                            $value = isset( $custom_data[ $column ]['value'] ) ? $custom_data[ $column ]['value'] : '';
                            echo '<td class="rtec-data-cell rtec-reg-custom" data-rtec-custom-key="'. esc_attr( $column ) . '" data-rtec-value="' . esc_attr( stripslashes( $value ) ) . '">' . esc_html( stripslashes( $value ) ) . '</td>';
                        elseif ( is_array( $custom_data ) && is_array( $custom_data[ $column ] ) ) :
                            $value = isset( $custom_data[ $column ]['value'] ) ? $custom_data[ $column ]['value'] : '';
                            echo '<td class="rtec-data-cell rtec-reg-custom" data-rtec-custom-key="'. esc_attr( $column ) . '" data-rtec-value="' . esc_attr( stripslashes( $value ) ) . '">' . esc_html( stripslashes( $value ) ) . '</td>';
                        elseif ( isset( $custom_data[ $custom_fields_name_label_pairs[ $column ] ] ) ) :
                            $value = $custom_data[ $custom_fields_name_label_pairs[ $column ] ];
                            echo '<td class="rtec-data-cell rtec-reg-custom" data-rtec-custom-key="'. esc_attr( $column ) . '" data-rtec-value="' . esc_attr( stripslashes( $value ) ) . '">' . esc_html( stripslashes( $value ) ) . '</td>';
                        endif;
                    else : ?>
                        <td class="rtec-data-cell rtec-reg-<?php echo esc_attr( $column ); ?>" data-rtec-key="<?php echo esc_attr( $column ); ?>"></td>
                    <?php endif; ?>
                <?php endforeach; ?>

            </tr>
            <tr>
                <td colspan="6" align="center"><?php _e( 'No Registrations Yet', 'registrations-for-the-events-calendar' ); ?></td>
            </tr>
            </tbody>
        <?php endif; // registrations not empty?>
        </table>
        <?php
        $cap = apply_filters( 'rtec_registration_actions_capability', 'edit_posts' );
        if ( current_user_can( $cap ) ) :
        ?>
        <div class="rtec-event-actions rtec-clear">
            <div class="tablenav">
                <button class="button action rtec-action rtec-admin-secondary-button" data-rtec-action="delete"><i class="fa fa-minus" aria-hidden="true"></i> <?php _e( 'Delete Selected', 'registrations-for-the-events-calendar'  ); ?></button>
                <button class="button action rtec-action rtec-admin-secondary-button" data-rtec-action="edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php _e( 'Edit Selected', 'registrations-for-the-events-calendar'  ); ?></button>
                <button class="button action rtec-action rtec-admin-secondary-button" data-rtec-action="add"><i class="fa fa-plus" aria-hidden="true"></i> <?php _e( 'Add New', 'registrations-for-the-events-calendar'  ); ?></button>

                <form method="post" id="rtec_csv_export_form" action="">
                    <?php wp_nonce_field( 'rtec_csv_export', 'rtec_csv_export_nonce' ); ?>
                    <input type="hidden" name="rtec_id" value="<?php echo esc_attr( $event_id ); ?>" />
                    <input type="submit" name="rtec_event_csv" class="button action rtec-admin-secondary-button" value="<?php _e( 'Export (.csv)', 'registrations-for-the-events-calendar' ); ?>" />
                </form>
	            <?php do_action( 'rtec_registrations_tab_event_actions', $event_id ); ?>
            </div>
        </div>
	    <?php endif; ?>
    </div> <!-- rtec-single-event -->

</div> <!-- rtec-single-wrapper -->

<?php do_action( 'rtec_registrations_tab_after_single', $event_obj ); ?>

