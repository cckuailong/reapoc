<?php
/**
* Functions file for Contact Form 7 module
*/

function cf7_module_form_titles() {

    $options = array( '' => __('None', 'bb-powerpack-lite') );

    if ( class_exists( 'WPCF7_ContactForm' ) ) {
        $args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
        if( $cf7Forms = get_posts( $args ) ){
            foreach ( $cf7Forms as $cf7Form )
            $options[$cf7Form->ID] = $cf7Form->post_title;
        }
    }

    return $options;
}
