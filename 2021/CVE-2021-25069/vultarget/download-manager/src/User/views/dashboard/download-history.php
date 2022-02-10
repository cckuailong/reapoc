<?php if(!defined('ABSPATH')) die(); ?>
<div class="card card-default dashboard-card">
    <div class="card-header"><?php echo __( "Download History", "download-manager" ); ?></div>
    <table class="table">
        <thead>
        <tr>
            <th><?php _e( "Package Name" , "download-manager" ); ?></th>
            <th><?php _e( "Download Time" , "download-manager" ); ?></th>
            <th><?php _e( "IP" , "download-manager" ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        global $wp_rewrite, $wp_query;
        $items_per_page = 30;
        $start = isset($_GET['pgd'])?((int)$_GET['pgd']-1)*$items_per_page:0;
        $res = $wpdb->get_results("select p.post_title,s.* from {$wpdb->prefix}posts p, {$wpdb->prefix}ahm_download_stats s where s.uid = '{$current_user->ID}' and s.pid = p.ID order by `timestamp` desc limit $start, $items_per_page");
        foreach($res as $stat){
            ?>
            <tr>
                <td><a href="<?php echo get_permalink($stat->pid); ?>"><?php echo $stat->post_title; ?></a></td>
                <td><?php echo date_i18n(get_option('date_format')." H:i",$stat->timestamp); ?></td>
                <td><?php echo $stat->ip; ?></td>
            </tr>
            <?php
        }
        ?>

        </tbody>
    </table>
    <div class="card-footer">
        <?php

            isset($_GET['pgd']) && $_GET['pgd'] > 1 ? $current = (int)$_GET['pgd'] : $current = 1;
            $pagination = array(
                'base' => @add_query_arg('pgd','%#%'),
                'format' => '',
                'total' => ceil($wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_download_stats where uid = '{$current_user->ID}'")/$items_per_page),
                'current' => $current,
                'show_all' => false,
                'type' => 'list',
                'prev_next'    => True,
                'prev_text' => '<i class="icon icon-angle-left"></i> '.__( "Previous", "download-manager" ),
                'next_text' => __( "Next", "download-manager" ).' <i class="icon icon-angle-right"></i>',
            );

            //if( $wp_rewrite->using_permalinks() && !is_search())
            //    $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg('s',get_pagenum_link(1) ) ) . 'paged=%#%', 'paged');

            if( !empty($wp_query->query_vars['s']) )
                $pagination['add_args'] = array('s'=>get_query_var('s'));

            echo '<div class="text-center">' . str_replace('<ul class=\'page-numbers\'>','<ul class="pagination pagination-centered page-numbers">', paginate_links($pagination)) . '</div>';
        ?>
    </div>
</div>
