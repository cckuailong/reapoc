<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_metabox_wcps_layout{
	
	public function __construct(){

		//meta box action for "wcps"
		add_action('add_meta_boxes', array($this, 'metabox_wcps_layout'));
		add_action('save_post', array($this, 'metabox_wcps_layout_save'));



		}


	public function metabox_wcps_layout($post_type){

            add_meta_box('metabox-wcps-layout',__('Layout data', 'woocommerce-products-slider'), array($this, 'meta_box_wcps_layout_data'), 'wcps_layout', 'normal', 'high');

		}






	public function meta_box_wcps_layout_data($post) {
 
        // Add an nonce field so we can check for it later.
        wp_nonce_field('wcps_nonce_check', 'wcps_nonce_check_value');
 
        // Use get_post_meta to retrieve an existing value from the database.
       // $wcps_data = get_post_meta($post -> ID, 'wcps_data', true);

        $post_id = $post->ID;


        $settings_tabs_field = new settings_tabs_field();

        $wcps_settings_tab = array();

        $wcps_settings_tab[] = array(
            'id' => 'layout_builder',
            'title' => sprintf(__('%s Layout builder','woocommerce-products-slider'),'<i class="fas fa-qrcode"></i>'),
            'priority' => 4,
            'active' => true,
        );


        $wcps_settings_tab[] = array(
            'id' => 'custom_scripts',
            'title' => sprintf(__('%s Custom scripts','woocommerce-products-slider'),'<i class="far fa-building"></i>'),
            'priority' => 5,
            'active' => false,
        );



        $wcps_settings_tab = apply_filters('wcps_layout_metabox_navs', $wcps_settings_tab);

        $tabs_sorted = array();
        foreach ($wcps_settings_tab as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
        array_multisort($tabs_sorted, SORT_ASC, $wcps_settings_tab);



        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');


        wp_enqueue_style( 'jquery-ui');
        wp_enqueue_style( 'font-awesome-5' );
        wp_enqueue_style( 'settings-tabs' );
        wp_enqueue_script( 'settings-tabs' );


		?>


        <div class="settings-tabs vertical">
            <ul class="tab-navs">
                <?php
                foreach ($wcps_settings_tab as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];
                    $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                    $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                    ?>
                    <li <?php if(!empty($data_visible)):  ?> data_visible="<?php echo $data_visible; ?>" <?php endif; ?> class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo $id; ?>"><?php echo $title; ?></li>
                    <?php
                }
                ?>
            </ul>
            <?php
            foreach ($wcps_settings_tab as $tab){
                $id = $tab['id'];
                $title = $tab['title'];
                $active = $tab['active'];
                ?>

                <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo $id; ?>">
                    <?php
                    do_action('wcps_layout_metabox_content_'.$id, $post_id);
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="clear clearfix"></div>

        <?php






        //do_action('wcps_metabox_wcps_data', $post);


   		}




	public function metabox_wcps_layout_save($post_id){

        /*
         * We need to verify this came from the our screen and with
         * proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (!isset($_POST['wcps_nonce_check_value']))
            return $post_id;

        $nonce = sanitize_text_field($_POST['wcps_nonce_check_value']);

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'wcps_nonce_check'))
            return $post_id;

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id))
                return $post_id;

        } else {

            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }

        /* OK, its safe for us to save the data now. */

        // Update the meta field.
        do_action('wcps_layout_metabox_save', $post_id);


					
		}
	
	}


new class_metabox_wcps_layout();