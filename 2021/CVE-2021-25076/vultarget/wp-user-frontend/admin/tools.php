<?php

if ( !class_exists( 'WPUF_Admin_Tools' ) ) {
    require_once WPUF_ROOT . '/admin/class-tools.php';
}

$tab   = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'tools';
$tools = new WPUF_Admin_Tools();
?>

<div class="wrap">
    <h2 class="nav-tab-wrapper">
        <a class="nav-tab <?php echo ( $tab == 'tools' ) ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( [ 'page'   => 'wpuf_tools', 'tab' => 'tools' ], admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Tools', 'wp-user-frontend' ); ?></a>

        <a class="nav-tab <?php echo ( $tab == 'import' ) ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( [ 'page'   => 'wpuf_tools', 'tab' => 'import' ], admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Import', 'wp-user-frontend' ); ?></a>

        <a class="nav-tab <?php echo ( $tab == 'export' ) ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( [ 'page'   => 'wpuf_tools', 'tab' => 'export' ], admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Export', 'wp-user-frontend' ); ?></a>
    </h2>

    <?php

    switch ( $tab ) {
        case 'export':
            $tools->list_forms();
            $tools->list_regis_forms();
            break;

        case 'tools':
            $tools->tool_page();
            break;

        case 'import':
            $tools->import_data();
            break;

        default:
            $tools->tool_page();
            break;
    }
    ?>
</div>

<style>
    select.formlist{
        display: block;
        width: 300px;
    }

</style>

<script>
    (function($){

        $('.formlist').hide();
        $('input.export_type').on('change',function(){
            $(this).closest('form').find('.formlist').slideUp(200);

            if( $(this).attr('value') == 'selected' ) {
                $(this).closest('form').find('.formlist').slideDown(200);
            }
        });


    })(jQuery);

</script>

