<?php
/**
 * The Events Calendar Integration Template
 *
 * @since 2.9
 */
class WPUF_Post_Form_Template_Events_Calendar extends WPUF_Post_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'Tribe__Events__Main' );
        $this->title       = __( 'The Events Calendar', 'wp-user-frontend' );
        $this->description = __( 'Form for creating events. The Events Calendar plugin is required.', 'wp-user-frontend' );
        $this->image       = WPUF_ASSET_URI . '/images/templates/post.png';
        $this->form_fields = [
            [
                'input_type'  => 'text',
                'template'    => 'post_title',
                'required'    => 'yes',
                'label'       => __( 'Event Title', 'wp-user-frontend' ),
                'name'        => 'post_title',
                'is_meta'     => 'no',
                'help'        => '',
                'css'         => '',
                'placeholder' => __( 'Please enter your event title', 'wp-user-frontend' ),
                'default'     => '',
                'size'        => '40',
                'wpuf_cond'   => $this->conditionals,
            ],
            [
                'input_type'       => 'textarea',
                'template'         => 'post_content',
                'required'         => 'yes',
                'label'            => __( 'Event details', 'wp-user-frontend' ),
                'name'             => 'post_content',
                'is_meta'          => 'no',
                'help'             => __( 'Write the full description of your event', 'wp-user-frontend' ),
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
                'input_type' => 'date',
                'template'   => 'date_field',
                'required'   => 'no',
                'label'      => __( 'Event Start', 'wp-user-frontend' ),
                'name'       => '_EventStartDate',
                'is_meta'    => 'yes',
                'width'      => 'large',
                'format'     => 'yy-mm-dd',
                'time'       => 'yes',
                'css'        => 'wpuf_hidden_field',
                'wpuf_cond'  => $this->conditionals,
            ],
            [
                'input_type' => 'date',
                'template'   => 'date_field',
                'required'   => 'no',
                'label'      => __( 'Event End', 'wp-user-frontend' ),
                'name'       => '_EventEndDate',
                'is_meta'    => 'yes',
                'width'      => 'large',
                'format'     => 'yy-mm-dd',
                'time'       => 'yes',
                'css'        => 'wpuf_hidden_field',
                'wpuf_cond'  => $this->conditionals,
            ],
            [
                'input_type' => 'radio',
                'template'   => 'radio_field',
                'required'   => 'no',
                'label'      => __( 'All Day Event', 'wp-user-frontend' ),
                'name'       => '_EventAllDay',
                'is_meta'    => 'yes',
                'selected'   => [],
                'inline'     => 'yes',
                'options'    => [
                    'yes' => 'Yes',
                    'no'  => 'No',
                ],
                'wpuf_cond' => $this->conditionals,
            ],
            [
                'input_type' => 'url',
                'template'   => 'website_url',
                'required'   => 'no',
                'label'      => __( 'Event Website', 'wp-user-frontend' ),
                'name'       => '_EventURL',
                'is_meta'    => 'yes',
                'width'      => 'large',
                'size'       => 40,
                'wpuf_cond'  => $this->conditionals,
            ],
            [
                'input_type' => 'text',
                'template'   => 'text_field',
                'required'   => 'no',
                'label'      => __( 'Currency Symbol', 'wp-user-frontend' ),
                'name'       => '_EventCurrencySymbol',
                'is_meta'    => 'yes',
                'size'       => 40,
                'wpuf_cond'  => $this->conditionals,
            ],
            [
                'input_type' => 'text',
                'template'   => 'text_field',
                'required'   => 'no',
                'label'      => __( 'Cost', 'wp-user-frontend' ),
                'name'       => '_EventCost',
                'is_meta'    => 'yes',
                'wpuf_cond'  => $this->conditionals,
            ],
            [
                'input_type'   => 'image_upload',
                'template'     => 'featured_image',
                'count'        => '1',
                'required'     => 'no',
                'label'        => __( 'Featured Image', 'wp-user-frontend' ),
                'button_label' => __( 'Featured Image', 'wp-user-frontend' ),
                'name'         => 'featured_image',
                'is_meta'      => 'no',
                'help'         => __( 'Upload the main image of your event', 'wp-user-frontend' ),
                'css'          => '',
                'max_size'     => '1024',
                'wpuf_cond'    => $this->conditionals,
            ],
            [
                'input_type'  => 'textarea',
                'template'    => 'post_excerpt',
                'required'    => 'no',
                'label'       => __( 'Excerpt', 'wp-user-frontend' ),
                'name'        => 'post_excerpt',
                'is_meta'     => 'no',
                'help'        => __( 'Provide a short description of this event (optional)', 'wp-user-frontend' ),
                'css'         => '',
                'rows'        => '5',
                'cols'        => '25',
                'placeholder' => '',
                'default'     => '',
                'rich'        => 'no',
                'wpuf_cond'   => $this->conditionals,
            ],
            [
                'input_type'  => 'text',
                'template'    => 'post_tags',
                'required'    => 'no',
                'label'       => __( 'Event Tags', 'wp-user-frontend' ),
                'name'        => 'tags',
                'is_meta'     => 'no',
                'help'        => __( 'Separate tags with commas.', 'wp-user-frontend' ),
                'css'         => '',
                'placeholder' => '',
                'default'     => '',
                'size'        => '40',
                'wpuf_cond'   => $this->conditionals,
            ],
        ];

        $this->form_settings = [
            'post_type'                  => 'tribe_events',
            'post_status'                => 'publish',
            'default_cat'                => '-1',
            'guest_post'                 => 'false',
            'message_restrict'           => __( 'This page is restricted. Please Log in / Register to view this page.', 'wp-user-frontend' ),
            'redirect_to'                => 'post',
            'comment_status'             => 'open',
            'submit_text'                => __( 'Create Event', 'wp-user-frontend' ),
            'edit_post_status'           => 'publish',
            'edit_redirect_to'           => 'same',
            'update_message'             => __( 'Event has been updated successfully. <a target="_blank" href="%link%">View event</a>', 'wp-user-frontend' ),
            'edit_url'                   => '',
            'update_text'                => __( 'Update Event', 'wp-user-frontend' ),
            'form_template'              => __CLASS__,
            'notification'               => [
                'new'                        => 'on',
                'new_to'                     => get_option( 'admin_email' ),
                'new_subject'                => 'New event has been created',
                'new_body'                   => 'Hi,
A new event has been created in your site %sitename% (%siteurl%).

Here is the details:
Event Title: %post_title%
Description: %post_content%
Short Description: %post_excerpt%
Author: %author%
Post URL: %permalink%
Edit URL: %editlink%',
                'edit'                       => 'off',
                'edit_to'                    => get_option( 'admin_email' ),
                'edit_subject'               => 'Post has been edited',
                'edit_body'                  => 'Hi,
The event "%post_title%" has been updated.

Here is the details:
Event Title: %post_title%
Description: %post_content%
Short Description: %post_excerpt%
Author: %author%
Post URL: %permalink%
Edit URL: %editlink%',
                ],
            ];
    }
}
