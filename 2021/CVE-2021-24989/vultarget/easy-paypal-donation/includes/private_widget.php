<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class wpedon_button_widget extends WP_Widget {

// constructor
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'wpedon_widget',
			'description' => 'PayPal Donation Button',
		);
		parent::__construct( 'wpedon_widget', 'PayPal Donation Button', $widget_ops );
	}

	// public output
	function widget( $args, $instance ) {
		extract($args);
		
		if (!empty($instance['idvalue'])) {
			$idvalue = $instance['idvalue'];
			
			$code = "[wpedon id='$idvalue' widget='true']";
			
			echo do_shortcode($code);
		}
		
		echo $after_widget;
	}

	// private save
	function update( $new_instance, $old_instance ) {
		$instance = 			$old_instance;
		$instance['title'] = 	strip_tags($new_instance['title']);
		$instance['idvalue'] = 	strip_tags($new_instance['idvalue']);
		return $instance;
	}

	// private output
	function form( $instance ) {
	
		if (empty($instance['title'])) {
			$instance['title'] = "";
		}
		if (empty($instance['idvalue'])) {
			$instance['idvalue'] = "";
		}
		
		$title = 		esc_attr($instance['title']);
		$idvalue = 		esc_attr($instance['idvalue']);
		
		?>
		<p><label>Widget Name:</label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		
		Choose an existing button:
		<br />
			<select id="wpedon_button_id" name="<?php echo esc_attr($this->get_field_name('idvalue')); ?>">
				<?php
				$args = array('post_type' => 'wpplugin_don_button','posts_per_page' => -1);

				$posts = get_posts($args);

				$count = "0";
				
				if (isset($posts)) {
					
					foreach ($posts as $post) {

						$id = 			$posts[$count]->ID;
						$post_title = 	$posts[$count]->post_title;
						$price = 		get_post_meta($id,'wpedon_button_price',true);
						$sku = 			get_post_meta($id,'wpedon_button_id',true);

						echo "<option value='$id' "; if($idvalue == $id) { echo "SELECTED"; } echo ">";
						echo "Name: ";
						echo esc_html($post_title);
						echo " - Amount: ";
						echo esc_html($price);
						echo " - ID: ";
						echo esc_html($sku);
						echo "</option>";

						$count++;
					}
				}
				else {
					echo "<option>No buttons found.</option>";
				}
				
				?>
			</select>
			<br />
			Make a new button: <a target="_blank" href="admin.php?page=wpedon_buttons&action=new">here</a><br />
			Manage existing buttons: <a target="_blank" href="admin.php?page=wpedon_buttons">here</a>
		
		
		<br /><br />
<?php
	}
}



// Register and load the widget
function wpedon_button_widget_load() {
    register_widget( 'wpedon_button_widget' );
}
add_action( 'widgets_init', 'wpedon_button_widget_load' );