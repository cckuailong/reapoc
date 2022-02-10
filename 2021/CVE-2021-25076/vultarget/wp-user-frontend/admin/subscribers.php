<?php
$option = 'per_page';
$args   = [
    'label'   => __( 'Number of subscribers per page:', 'wp-user-frontend' ),
    'default' => 20,
    'option'  => 'subscribers_per_page',
];

add_screen_option( $option, $args );

if ( !class_exists( 'WPUF_List_Table_Subscribers' ) ) {
    require_once WPUF_ROOT . '/includes/class-list-table-subscribers.php';
}

$this->subscribers_list_table_obj = new WPUF_List_Table_Subscribers();
?>
<div class="wrap">
    <h2><?php esc_html_e( 'Subscribers', 'wp-user-frontend' ); ?></h2>

    <form method="post">
        <input type="hidden" name="page" value="subscribers">
        <?php
            $this->subscribers_list_table_obj->prepare_items();
            $this->subscribers_list_table_obj->get_views();
            $this->subscribers_list_table_obj->display();
        ?>
    </form>
</div>

<script type="text/javascript">
    jQuery(function($) {
        $('.toplevel_page_wp-user-frontend').each(function(index, el) {
            $(el).removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu wp-menu-open');
        });
    });
</script>