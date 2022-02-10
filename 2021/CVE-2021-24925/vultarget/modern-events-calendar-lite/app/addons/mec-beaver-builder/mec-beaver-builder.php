<?php
class mecBeaverBuilderShortcode extends FLBuilderModule {

    public function __construct()
    {
        parent::__construct(array(
            'name'            => __( 'Modern Events Calendar (MEC)', 'modern-events-calendar-lite' ),
            'description'     => __( 'MEC Shortcodes', 'modern-events-calendar-lite' ),
            'category'        => __( 'Basic', 'modern-events-calendar-lite' ),
            'dir'             => MEC_BEAVER_DIR . 'mec-beaver-builder/',
            'url'             => MEC_BEAVER_URL . 'mec-beaver-builder/',
            'icon'            => 'button.svg',
            'editor_export'   => true, // Defaults to true and can be omitted.
            'enabled'         => true, // Defaults to true and can be omitted.
            'partial_refresh' => false, // Defaults to false and can be omitted.
        ));
    }
}

$calendar_posts = get_posts(array('post_type'=>'mec_calendars', 'posts_per_page'=>'-1'));
$calendars = array();
foreach($calendar_posts as $calendar_post) $calendars[$calendar_post->ID] = $calendar_post->post_title;
FLBuilder::register_module( 'mecBeaverBuilderShortcode', array(
    'my-tab-1'      => array(
        'title'         => __( 'Content', 'modern-events-calendar-lite' ),
        'sections'      => array(
            'my-section-1'  => array(
                'title'         => __( 'Select Shortcode', 'modern-events-calendar-lite' ),
                'fields'        => array(
                    'mec_shortcode' => array(
						'type'    => 'select',
						'label'   => __( 'Select Shortcode', 'modern-events-calendar-lite' ),
						'options' => $calendars,
					),
                )
            )
        )
    )
) );