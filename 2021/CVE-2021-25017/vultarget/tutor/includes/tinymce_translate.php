<?php
/**
 * Add translation support for external tinyMCE button
 * 
 * Containing all translate able strings
 * 
 * @since 1.9.7
 */
if ( ! defined( 'ABSPATH' ) )
exit;

if ( ! class_exists( '_WP_Editors' ) )
require( ABSPATH . WPINC . '/class-wp-editor.php' );

function tutor_tinymce_plugin_translation() {
    $strings = array(
        'tutor_shortcode'               => __( 'Tutor ShortCode', 'tutor' ),
        'student_registration_form'     => __( 'Student Registration Form', 'tutor' ),
        'instructor_registration_form'  => __( 'Instructor Registration Form', 'tutor' ),
        'courses'                       => _x( 'Courses', 'tinyMCE button courses', 'tutor' ),
        'courses_shortcode'             => __( 'Courses Shortcode', 'tutor' ),
        'courses_separate_by'           => __( 'Course id, separate by (,) comma', 'tutor' ),
        'exclude_course_ids'            => __( 'Exclude Course IDS', 'tutor' ),
        'category_ids'                  => __( 'Category IDS', 'tutor' ),
        'order_by'                      => _x( 'Order By :', 'tinyMCE button order by', 'tutor' ),
        'order'                         => __( 'Order :', 'tinyMCE button order', 'tutor' ),
        'count'                         => __( 'Count', 'tutor' ),
    );

    $locale = _WP_Editors::$mce_locale;
    $translated = 'tinyMCE.addI18n("' . $locale . '.tutor_button", ' . json_encode( $strings ) . ");\n";

    return $translated;
}

$strings = tutor_tinymce_plugin_translation();