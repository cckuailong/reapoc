<?php

/**
 * WooCommerce post form template
 */
class WPUF_Post_Form_Template_WooCommerce extends WPUF_Post_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'WooCommerce' );
        $this->title       = __( 'WooCommerce Product', 'wp-user-frontend' );
        $this->description = __( 'Create a simple product form for WooCommerce.', 'wp-user-frontend' );
        $this->image       = WPUF_ASSET_URI . '/images/templates/woocommerce.png';
        $this->form_fields = [
            [
                'input_type'  => 'text',
                'template'    => 'post_title',
                'required'    => 'yes',
                'label'       => 'Product Name',
                'name'        => 'post_title',
                'is_meta'     => 'no',
                'help'        => '',
                'css'         => '',
                'placeholder' => 'Please enter your product name',
                'default'     => '',
                'size'        => '40',
                'wpuf_cond'   => $this->conditionals,
            ],
            [
                'input_type'       => 'textarea',
                'template'         => 'post_content',
                'required'         => 'yes',
                'label'            => 'Product description',
                'name'             => 'post_content',
                'is_meta'          => 'no',
                'help'             => 'Write the full description of your product',
                'css'              => '',
                'rows'             => '5',
                'cols'             => '25',
                'placeholder'      => '',
                'default'          => '',
                'rich'             => 'yes',
                'insert_image'     => 'yes',
                'word_restriction' => '',
                'wpuf_cond'        => $this->conditionals,
            ],
            [
                'input_type'  => 'textarea',
                'template'    => 'post_excerpt',
                'required'    => 'no',
                'label'       => 'Product Short Description',
                'name'        => 'post_excerpt',
                'is_meta'     => 'no',
                'help'        => 'Provide a short description of your product',
                'css'         => '',
                'rows'        => '5',
                'cols'        => '25',
                'placeholder' => '',
                'default'     => '',
                'rich'        => 'no',
                'wpuf_cond'   => $this->conditionals,
            ],
            [
                'input_type'      => 'text',
                'template'        => 'text_field',
                'required'        => 'yes',
                'label'           => 'Regular Price',
                'name'            => '_regular_price',
                'is_meta'         => 'yes',
                'help'            => '',
                'css'             => '',
                'placeholder'     => 'regular price of your product',
                'default'         => '',
                'size'            => '40',
                'step_text_field' => '0.01',
                'min_value_field' => '0',
                'max_value_field' => '',
                'wpuf_cond'       => $this->conditionals,
            ],
            [
                'input_type'      => 'text',
                'template'        => 'text_field',
                'required'        => 'no',
                'label'           => 'Sale Price',
                'name'            => '_sale_price',
                'is_meta'         => 'yes',
                'help'            => '',
                'css'             => '',
                'placeholder'     => 'sale price of your product',
                'default'         => '',
                'size'            => '40',
                'step_text_field' => '0.01',
                'min_value_field' => '0',
                'max_value_field' => '',
                'wpuf_cond'       => $this->conditionals,
            ],
            [
                'input_type'   => 'image_upload',
                'template'     => 'featured_image',
                'count'        => '1',
                'required'     => 'yes',
                'label'        => 'Product Image',
                'button_label' => 'Product Image',
                'name'         => 'featured_image',
                'is_meta'      => 'no',
                'help'         => 'Upload the main image of your product',
                'css'          => '',
                'max_size'     => '1024',
                'wpuf_cond'    => $this->conditionals,
            ],
            [
                'input_type'   => 'image_upload',
                'template'     => 'image_upload',
                'required'     => 'no',
                'label'        => 'Product Image Gallery',
                'button_label' => 'Product Image Gallery',
                'name'         => '_product_image',
                'is_meta'      => 'yes',
                'help'         => 'Upload additional pictures of your product and will be shown as image gallery',
                'css'          => '',
                'max_size'     => '1024',
                'count'        => '5',
                'wpuf_cond'    => $this->conditionals,
            ],
            [
                'input_type' => 'select',
                'template'   => 'dropdown_field',
                'required'   => 'yes',
                'label'      => 'Catalog visibility',
                'name'       => '_visibility',
                'is_meta'    => 'yes',
                'help'       => 'Choose where this product should be displayed in your catalog. The product will always be accessible directly.',
                'css'        => '',
                'first'      => ' - select -',
                'options'    => [
                    'visible'    => 'Catalog/search',
                    'catalog'    => 'Catalog',
                    'search'     => 'Search',
                    'hidden'     => 'Hidden',
                ],
                'wpuf_cond'  => $this->conditionals,
            ],
            [
                'input_type'       => 'textarea',
                'template'         => 'textarea_field',
                'required'         => 'no',
                'label'            => 'Purchase note',
                'name'             => '_purchase_note',
                'is_meta'          => 'yes',
                'help'             => 'Enter an optional note to send to the customer after purchase',
                'css'              => '',
                'rows'             => '5',
                'cols'             => '25',
                'placeholder'      => '',
                'default'          => '',
                'rich'             => 'no',
                'word_restriction' => '',
                'wpuf_cond'        => $this->conditionals,
            ],
            [
                'input_type'      => 'checkbox',
                'template'        => 'checkbox_field',
                'required'        => 'no',
                'label'           => 'Product Reviews',
                'name'            => 'product_reviews',
                'is_meta'         => 'yes',
                'help'            => '',
                'css'             => '',
                'options'         => [
                    '_enable_reviews' => 'Enable reviews',
                ],
                'wpuf_cond'       => $this->conditionals,
            ],
        ];

        $this->form_settings = [
            'post_type'                  => 'product',
            'post_status'                => 'publish',
            'default_cat'                => '-1',
            'guest_post'                 => 'false',
            'message_restrict'           => 'This page is restricted. Please %login% / %register% to view this page.',
            'redirect_to'                => 'post',
            'comment_status'             => 'open',
            'submit_text'                => 'Create Product',
            'edit_post_status'           => 'publish',
            'edit_redirect_to'           => 'same',
            'update_message'             => 'Product has been updated successfully. <a target="_blank" href="%link%">View Product</a>',
            'edit_url'                   => '',
            'update_text'                => 'Update Product',
            'form_template'              => __CLASS__,
            'notification'               => [
                'new'                        => 'on',
                'new_to'                     => get_option( 'admin_email' ),
                'new_subject'                => 'New product has been created',
                'new_body'                   => 'Hi,
A new product has been created in your site %sitename% (%siteurl%).

Here is the details:
Product Title: %post_title%
Description: %post_content%
Short Description: %post_excerpt%
Author: %author%
Post URL: %permalink%
Edit URL: %editlink%',
                'edit'                       => 'off',
                'edit_to'                    => get_option( 'admin_email' ),
                'edit_subject'               => 'Product has been edited',
                'edit_body'                  => 'Hi,
The product "%post_title%" has been updated.

Here is the details:
Product Title: %post_title%
Description: %post_content%
Short Description: %post_excerpt%
Author: %author%
Post URL: %permalink%
Edit URL: %editlink%',
                ],
            ];
    }

    /**
     * Run necessary processing after new post insert
     *
     * @param int   $post_id
     * @param int   $form_id
     * @param array $form_settings
     *
     * @return void
     */
    public function after_insert( $post_id, $form_id, $form_settings ) {
        $this->handle_form_updates( $post_id, $form_id, $form_settings );
    }

    /**
     * Run necessary processing after editing a post
     *
     * @param int   $post_id
     * @param int   $form_id
     * @param array $form_settings
     *
     * @return void
     */
    public function after_update( $post_id, $form_id, $form_settings ) {
        $this->handle_form_updates( $post_id, $form_id, $form_settings );
    }

    /**
     * Run the functions on update/insert
     *
     * @param int   $post_id
     * @param int   $form_id
     * @param array $form_settings
     *
     * @return void
     */
    public function handle_form_updates( $post_id, $form_id, $form_settings ) {
        $this->update_reviews( $post_id );
        $this->update_price( $post_id );
        $this->update_gallery_images( $post_id );
        $this->update_meta( $post_id );
    }

    /**
     * Update the product reviews
     *
     * @param int $post_id
     *
     * @return void
     */
    public function update_reviews( $post_id ) {
        global $wpdb;

        $reviews = get_post_meta( $post_id, 'product_reviews', true );
        $status  = !empty( $reviews ) ? 'open' : 'closed';

        // wp_update_post( array( 'ID' => $post_id, 'comment_status' => $status ) );

        $comment_sql = "UPDATE {$wpdb->prefix}posts SET comment_status='{$status}' WHERE ID={$post_id} AND post_status='publish' AND post_type='product'";
        $wpdb->get_results( $comment_sql );
    }

    /**
     * Update the proper price
     *
     * @param int $post_id
     *
     * @return void
     */
    public function update_price( $post_id ) {
        $regular_price = (float) get_post_meta( $post_id, '_regular_price', true );
        $sale_price    = (float) get_post_meta( $post_id, '_sale_price', true );

        if ( $sale_price && $regular_price > $sale_price ) {
            update_post_meta( $post_id, '_price', $sale_price );
        } else {
            update_post_meta( $post_id, '_price', $regular_price );
        }
    }

    /**
     * Update image gallery
     *
     * @param int $post_id
     *
     * @return void
     */
    public function update_gallery_images( $post_id ) {
        $images = get_post_meta( $post_id, '_product_image' );

        if ( !empty( $images ) ) {
            if ( is_array( $images[0] ) ) {
                $images = $images[0];
            }

            if ( is_serialized( $images[0] ) ) {
                $images = maybe_unserialize( $images[0] );
            }
            update_post_meta( $post_id, '_product_image_gallery', implode( ',', $images ) );
        }
    }

    /**
     *  Fix for visibily not updating from frontend post
     *
     * @param int $post_id
     *
     * @return void
     */
    public function update_meta( $post_id ) {

        //keep backwards compatible
        if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
            return;
        }

        $visibility = get_post_meta( $post_id, '_visibility', true );

        $product = wc_get_product( $post_id );

        if ( !empty( $visibility ) ) {
            $product->set_catalog_visibility( $visibility );
        }

        $product->save();
    }
}
