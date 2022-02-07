<?php	
if ( ! defined('ABSPATH')) exit;  // if direct access

$wcps_plugin_info = get_option('wcps_plugin_info');
$wcps_settings_upgrade = isset($wcps_plugin_info['settings_upgrade']) ? $wcps_plugin_info['settings_upgrade'] : '';
$wcps_upgrade = isset($wcps_plugin_info['wcps_upgrade']) ? $wcps_plugin_info['wcps_upgrade'] : '';

//echo '<pre>'.var_export($wcps_upgrade, true).'</pre>';


$url = admin_url().'edit.php?post_type=wcps&page=upgrade_status';

?>
<?php

?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div><h2><?php echo sprintf(__('%s Settings - Update', 'woocommerce-products-slider'), wcps_plugin_name)?></h2>
    <p>WCPS settings and WCPS options data should automatic upgrade. please wait until all update completed. each loop will take 1 minute to completed, based on your wcps it will take take few minutes to completed.</p>
    <p>If you have any issue please <a href="https://www.pickplugins.com/forum/">create support ticket</a> on our forum</p>
    <p>Don't panic while updating, your old data still saved on database and you can downgrade plugin any time, please <a href="https://wordpress.org/plugins/woocommerce-products-slider/advanced/#plugin-download-history-stats">download from here</a> old version and reinstall.</p>


    <script>
        setTimeout(function(){
            window.location.href = '<?php echo $url; ?>';
        }, 1000*80);

    </script>

    <h3>WCPS settings upgrade status</h3>

    <?php

    if(!empty($wcps_settings_upgrade)){
        ?>
        <p>Completed</p>
        <?php
    }else{
        ?>
        <p>Pending</p>
        <?php
    }

    ?>




    <h3>WCPS post data upgrade status</h3>
    <?php

    $meta_query = array();

    $meta_query[] = array(
        'key' => 'wcps_upgrade_status',
        'value' => 'done',
        'compare' => '='
    );

    $args = array(
        'post_type'=>'wcps',
        'post_status'=>'any',
        'posts_per_page'=> -1,
        'meta_query'=> $meta_query,

    );

    $wp_query = new WP_Query($args);

    if ( $wp_query->have_posts() ) :
        ?>
        <ul>
        <?php
        while ( $wp_query->have_posts() ) : $wp_query->the_post();

            $wcps_id = get_the_id();
            $wcps_title = get_the_title();
            ?>
            <li><?php echo $wcps_title; ?> - Done</li>
            <?php

        endwhile;
        ?>
        </ul>
        <?php

    else:
        ?>
        <p>Pending</p>
        <?php
    endif;


    if($wcps_upgrade == 'done'){
        wp_safe_redirect(admin_url().'edit.php?post_type=wcps');
    }


    ?>



    <p><a class="button" href="<?php echo admin_url().'edit.php?post_type=wcps&page=upgrade_status'; ?>">Refresh</a> to check Migration stats.</p>












</div>
