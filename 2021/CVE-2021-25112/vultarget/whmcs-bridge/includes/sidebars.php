<?php
function cc_whmcs_sidebar_init() {
	add_action('widgets_init', function() { return register_widget("cc_whmcs_sidebar_main"); });
	add_action('widgets_init', function() { return register_widget("cc_whmcs_sidebarAcInf_main"); });
	add_action('widgets_init', function() { return register_widget("cc_whmcs_sidebarAcSta_main"); });
	add_action('widgets_init', function() { return register_widget("cc_whmcs_topNav_main"); });
	add_action('widgets_init', function() { return register_widget("cc_whmcs_welcomebox_main"); });
	add_action('widgets_init', function() { return register_widget("cc_whmcs_sidebarNav_main"); });
	add_action('widgets_init', function() { return register_widget("cc_whmcs_sidebarNav_Acc"); });
}

class cc_whmcs_sidebar_main extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct(false, $name = 'WHMCS Main');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		extract( $args );
		if (!$cc_whmcs_bridge_content) $cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$title ) $title='WHMCS main';
		echo $before_title . $title . $after_title;
		if (isset($cc_whmcs_bridge_content['sidebarNav'])) echo $cc_whmcs_bridge_content['sidebarNav'];
		if (isset($cc_whmcs_bridge_content['sidebarAcInf'])) echo $cc_whmcs_bridge_content['sidebarAcInf'];
		if (isset($cc_whmcs_bridge_content['sidebarAcSta'])) echo $cc_whmcs_bridge_content['sidebarAcSta'];
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}

class cc_whmcs_sidebarAcInf_main extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct(false, $name = 'WHMCS Account Info');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		if (!$cc_whmcs_bridge_content) $cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
		if (!isset($cc_whmcs_bridge_content['sidebarAcInf'])) return;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$title ) $title=$cc_whmcs_bridge_content['mode'][1];
		echo $before_title . $title . $after_title;
		echo $cc_whmcs_bridge_content['sidebarAcInf'];
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}

class cc_whmcs_sidebarAcSta_main extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct(false, $name = 'WHMCS Account Statistics');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		if (!$cc_whmcs_bridge_content) $cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
		if (!isset($cc_whmcs_bridge_content['sidebarAcSta'])) return;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$title ) $title=$cc_whmcs_bridge_content['mode'][2];
		echo $before_title . $title . $after_title;
		echo '<!--start-->';
		echo $cc_whmcs_bridge_content['sidebarAcSta'];
		echo '<!--end-->';
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}

class cc_whmcs_welcomebox_main extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct(false, $name = 'WHMCS Welcome Box');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		if (!$cc_whmcs_bridge_content) $cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
		if (!isset($cc_whmcs_bridge_content['welcomebox'])) return;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		echo $cc_whmcs_bridge_content['welcomebox'];
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}

class cc_whmcs_sidebarNav_main extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct(false, $name = 'WHMCS Quick Navigation');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		if (!$cc_whmcs_bridge_content) $cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
		if (!isset($cc_whmcs_bridge_content['sidebarNav'])) return;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$title ) $title=$cc_whmcs_bridge_content['mode'][0];
		echo $before_title . $title . $after_title;
		echo $cc_whmcs_bridge_content['sidebarNav'];
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}

class cc_whmcs_sidebarNav_acc extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct(false, $name = 'WHMCS Client Navigation');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		if (!$cc_whmcs_bridge_content) $cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
		if (!$cc_whmcs_bridge_content['topNav']) return; 
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$title ) $title=$cc_whmcs_bridge_content['mode'][0];
		echo $before_title . $title . $after_title;
		echo $cc_whmcs_bridge_content['topNav'];
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}


class cc_whmcs_topNav_main extends WP_Widget {
    /** constructor */
    function __construct() {
        parent::__construct(false, $name = 'WHMCS Client Navigation (Top)');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        global $cc_whmcs_bridge_content;
        if (!$cc_whmcs_bridge_content) $cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_title . $title . $after_title;
        echo '<div id="top_menu">'.$cc_whmcs_bridge_content['topNav'].'</div>';
        echo '<div class="clear"></div>';
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        echo '<p>';
        echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
        echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
        echo '</p>';
    }
}

// contribution northgatewebhosting.co.uk
class cc_whmcs_carttotal_main extends WP_Widget {
    /** constructor */
    function __construct() {
        parent::__construct(false, $name = 'WHMCS Cart Total');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        global $cc_whmcs_bridge_content;
        if (!$cc_whmcs_bridge_content) $cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
        if (!isset($cc_whmcs_bridge_content['carttotal'])) return;
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if ( $title ) echo $before_title . $title . $after_title;
        echo $cc_whmcs_bridge_content['carttotal'];
        echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        echo '<p>';
        echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
        echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
        echo '</p>';
    }
}
// contribution northgatewebhosting.co.uk
