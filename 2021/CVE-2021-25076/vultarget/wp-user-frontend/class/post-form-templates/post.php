<?php

/**
 * Normal post form
 */
class WPUF_Post_Form_Template_Post extends WPUF_Post_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Post Form', 'wp-user-frontend' );
        $this->description = __( 'Form for creating a blog post.', 'wp-user-frontend' );
        $this->image       = WPUF_ASSET_URI . '/images/templates/post.png';
        $this->form_fields = [
            [
                'input_type'       => 'text',
                'template'         => 'post_title',
                'required'         => 'yes',
                'label'            => __( 'Post Title', 'wp-user-frontend' ),
                'name'             => 'post_title',
                'is_meta'          => 'no',
                'help'             => '',
                'css'              => '',
                'placeholder'      => __( 'Please enter your post name', 'wp-user-frontend' ),
                'default'          => '',
                'size'             => '40',
                'wpuf_cond'        => $this->conditionals,
                'wpuf_visibility'  => $this->get_default_visibility_prop(),
                'restriction_type' => 'character'
            ],
            [
                'input_type'      => 'taxonomy',
                'template'        => 'taxonomy',
                'required'        => 'yes',
                'label'           => __( 'Category', 'wp-user-frontend' ),
                'name'            => 'category',
                'is_meta'         => 'no',
                'help'            => __( 'Select a category for your post', 'wp-user-frontend' ),
                'first'           => __( '- select -', 'wp-user-frontend' ),
                'css'             => '',
                'type'            => 'select',
                'orderby'         => 'name',
                'order'           => 'ASC',
                'exclude_type'    => 'exclude',
                'exclude'         => [],
                'woo_attr'        => 'no',
                'woo_attr_vis'    => 'no',
                'options'         => [],
                'wpuf_cond'       => $this->conditionals,
                'wpuf_visibility' => $this->get_default_visibility_prop(),
            ],
            [
                'input_type'       => 'textarea',
                'template'         => 'post_content',
                'required'         => 'yes',
                'label'            => __( 'Post description', 'wp-user-frontend' ),
                'name'             => 'post_content',
                'is_meta'          => 'no',
                'help'             => __( 'Write the full description of your Post', 'wp-user-frontend' ),
                'css'              => '',
                'rows'             => '5',
                'cols'             => '25',
                'placeholder'      => '',
                'default'          => '',
                'rich'             => 'yes',
                'insert_image'     => 'yes',
                'wpuf_cond'        => $this->conditionals,
                'wpuf_visibility'  => $this->get_default_visibility_prop(),
                'restriction_type' => 'character'
            ],
            [
                'input_type'      => 'image_upload',
                'template'        => 'featured_image',
                'count'           => '1',
                'required'        => 'yes',
                'label'           => __( 'Featured Image', 'wp-user-frontend' ),
                'button_label'    => __( 'Featured Image', 'wp-user-frontend' ),
                'name'            => 'featured_image',
                'is_meta'         => 'no',
                'help'            => __( 'Upload the main image of your post', 'wp-user-frontend' ),
                'css'             => '',
                'max_size'        => '1024',
                'wpuf_cond'       => $this->conditionals,
                'wpuf_visibility' => $this->get_default_visibility_prop(),
            ],
            [
                'input_type'       => 'textarea',
                'template'         => 'post_excerpt',
                'required'         => 'no',
                'label'            => __( 'Excerpt', 'wp-user-frontend' ),
                'name'             => 'post_excerpt',
                'is_meta'          => 'no',
                'help'             => __( 'Provide a short description of this post (optional)', 'wp-user-frontend' ),
                'css'              => '',
                'rows'             => '5',
                'cols'             => '25',
                'placeholder'      => '',
                'default'          => '',
                'rich'             => 'no',
                'wpuf_cond'        => $this->conditionals,
                'wpuf_visibility'  => $this->get_default_visibility_prop(),
                'restriction_type' => 'character'
            ],
            [
                'input_type'      => 'text',
                'template'        => 'post_tags',
                'required'        => 'no',
                'label'           => __( 'Tags', 'wp-user-frontend' ),
                'name'            => 'tags',
                'is_meta'         => 'no',
                'help'            => __( 'Separate tags with commas.', 'wp-user-frontend' ),
                'css'             => '',
                'placeholder'     => '',
                'default'         => '',
                'size'            => '40',
                'wpuf_cond'       => $this->conditionals,
                'wpuf_visibility' => $this->get_default_visibility_prop(),
            ],
        ];

        $this->form_settings = [
                'post_type'                  => 'post',
                'post_status'                => 'publish',
                'default_cat'                => '-1',
                'guest_post'                 => 'false',
                'message_restrict'           => __( 'This page is restricted. Please %login% / %register% to view this page.', 'wp-user-frontend' ),
                'redirect_to'                => 'post',
                'comment_status'             => 'open',
                'submit_text'                => __( 'Create Post', 'wp-user-frontend' ),
                'submit_button_cond'         => [
                    'condition_status' => 'no',
                    'cond_logic'       => 'any',
                    'conditions'       => [
                        [
                            'name'             => '',
                            'operator'         => '=',
                            'option'           => '',
                        ],
                    ],
                ],
                'edit_post_status'           => 'publish',
                'edit_redirect_to'           => 'same',
                'update_message'             => __( 'Post has been updated successfully. <a target="_blank" href="%link%">View post</a>', 'wp-user-frontend' ),
                'edit_url'                   => '',
                'update_text'                => __( 'Update Post', 'wp-user-frontend' ),
                'form_template'              => __CLASS__,
                'notification'               => [
                'new'                        => 'on',
                'new_to'                     => get_option( 'admin_email' ),
                'new_subject'                => 'New post has been created',
                'new_body'                   => 'Hi,
                A new post has been created in your site %sitename% (%siteurl%).

                Here is the details:
                Post Title: %post_title%
                Description: %post_content%
                Short Description: %post_excerpt%
                Author: %author%
                Post URL: %permalink%
                Edit URL: %editlink%',
                'edit'                       => 'off',
                'edit_to'                    => get_option( 'admin_email' ),
                'edit_subject'               => 'Post has been edited',
                'edit_body'                  => 'Hi,
                The post "%post_title%" has been updated.

                Here is the details:
                Post Title: %post_title%
                Description: %post_content%
                Short Description: %post_excerpt%
                Author: %author%
                Post URL: %permalink%
                Edit URL: %editlink%',
                ],
            ];
    }
}
