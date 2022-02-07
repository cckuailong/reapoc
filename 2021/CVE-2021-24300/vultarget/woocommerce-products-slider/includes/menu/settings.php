<?php	
if ( ! defined('ABSPATH')) exit;  // if direct access


$current_tab = isset($_REQUEST['tab']) ? sanitize_text_field($_REQUEST['tab']) : 'general';

$wcps_settings_tab = array();

$wcps_settings_tab[] = array(
    'id' => 'general',
    'title' => sprintf(__('%s General','woocommerce-products-slider'),'<i class="fas fa-list-ul"></i>'),
    'priority' => 1,
    'active' => ($current_tab == 'general') ? true : false,
);



$wcps_settings_tab[] = array(
    'id' => 'help_support',
    'title' => sprintf(__('%s Help & support','woocommerce-products-slider'),'<i class="fas fa-hands-helping"></i>'),
    'priority' => 3,
    'active' => ($current_tab == 'help_support') ? true : false,
);

$wcps_settings_tab[] = array(
    'id' => 'buy_pro',
    'title' => sprintf(__('%s Buy Pro','woocommerce-products-slider'),'<i class="fas fa-store"></i>'),
    'priority' => 9,
    'active' => ($current_tab == 'buy_pro') ? true : false,
);

$wcps_settings_tab = apply_filters('wcps_settings_tabs', $wcps_settings_tab);

$tabs_sorted = array();

if(!empty($wcps_settings_tab))
foreach ($wcps_settings_tab as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
array_multisort($tabs_sorted, SORT_ASC, $wcps_settings_tab);


wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_script( 'jquery-ui-core' );
wp_enqueue_script('jquery-ui-accordion');
wp_enqueue_style( 'wp-color-picker' );
wp_enqueue_script('wp-color-picker');
wp_enqueue_style('font-awesome-5');
wp_enqueue_style('settings-tabs');
wp_enqueue_script('settings-tabs');

wp_enqueue_style('codemirror');
wp_enqueue_script('codemirror');

$wcps_settings = get_option('wcps_settings');

?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div><h2><?php echo sprintf(__('%s Settings', 'woocommerce-products-slider'), wcps_plugin_name)?></h2>
		<form  method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	        <input type="hidden" name="wcps_hidden" value="Y">
            <input type="hidden" name="tab" value="<?php echo $current_tab; ?>">
            <?php
            if(!empty($_POST['wcps_hidden'])){
                $nonce = sanitize_text_field($_POST['_wpnonce']);
                if(wp_verify_nonce( $nonce, 'wcps_nonce' ) && $_POST['wcps_hidden'] == 'Y') {
                    do_action('wcps_settings_save');
                    ?>
                    <div class="updated notice  is-dismissible"><p><strong><?php _e('Changes Saved.', 'woocommerce-products-slider' ); ?></strong></p></div>
                    <?php
                }
            }
            ?>
            <div class="settings-tabs-loading" style="">Loading...</div>
            <div class="settings-tabs vertical has-right-panel" style="display: none">
                <div class="settings-tabs-right-panel">
                    <?php
                    if(!empty($wcps_settings_tab))
                    foreach ($wcps_settings_tab as $tab) {
                        $id = $tab['id'];
                        $active = $tab['active'];
                        ?>
                        <div class="right-panel-content <?php if($active) echo 'active';?> right-panel-content-<?php echo $id; ?>">
                            <?php
                            do_action('wcps_settings_tabs_right_panel_'.$id);
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <ul class="tab-navs">
                    <?php
                    if(!empty($wcps_settings_tab))
                    foreach ($wcps_settings_tab as $tab){
                        $id = $tab['id'];
                        $title = $tab['title'];
                        $active = $tab['active'];
                        $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                        $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                        $is_pro = isset($tab['is_pro']) ? $tab['is_pro'] : false;
                        $pro_text = isset($tab['pro_text']) ? $tab['pro_text'] : '';
                        ?>
                        <li <?php if(!empty($data_visible)):  ?> data_visible="<?php echo $data_visible; ?>" <?php endif; ?> class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo $id; ?>">
                            <?php echo $title; ?>
                            <?php
                            if($is_pro):
                                ?><span class="pro-feature"><?php echo $pro_text; ?></span> <?php
                            endif;
                            ?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
                if(!empty($wcps_settings_tab))
                foreach ($wcps_settings_tab as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];
                    ?>
                    <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo $id; ?>">
                        <?php
                        do_action('wcps_settings_content_'.$id, $tab);
                        ?>
                    </div>
                    <?php
                }
                ?>
                <div class="clear clearfix"></div>
                <p class="submit">
                    <?php wp_nonce_field( 'wcps_nonce' ); ?>
                    <input class="button button-primary" type="submit" name="Submit" value="<?php _e('Save Changes','woocommerce-products-slider' ); ?>" />
                </p>
            </div>
		</form>
</div>