<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumSpoilers {
    private $asgarosforum = null;

    public function __construct($object) {
		$this->asgarosforum = $object;

        add_action('init', array($this, 'initialize'));
    }

    public function initialize() {
        if ($this->asgarosforum->options['enable_spoilers']) {
            add_shortcode('spoiler', array($this, 'render_spoiler'));
        }
    }

    public function render_spoiler($atts = false, $content = false) {
        // Set title-attribute.
        $atts = shortcode_atts(
            array(
                'title' => __('Spoiler', 'asgaros-forum')
            ),
            $atts,
            'spoiler'
        );

        $atts['title'] = (!empty($atts['title'])) ? $atts['title'] : __('Spoiler', 'asgaros-forum');

        // Generate output.
        $output = '';
        $output .= '<div class="spoiler">';
    	$output .= '<div class="spoiler-head closed"><span>'.$atts['title'].'</span></div>';
    	$output .= '<div class="spoiler-body">';

        // Hide spoiler if the current user is not logged-in (based on the settings).
        if ($this->asgarosforum->options['hide_spoilers_from_guests'] && !is_user_logged_in()) {
            $output .= __('Sorry, only logged-in users can see spoilers.', 'asgaros-forum');
        } else {
            $output .= $content;
        }

        $output .= '</div>';
    	$output .= '</div>';

        return $output;
    }
}
