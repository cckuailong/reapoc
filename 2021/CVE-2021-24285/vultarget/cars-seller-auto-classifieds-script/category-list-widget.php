<?php

class category_list_widget_carseller extends WP_Widget {

	// constructor
	function category_list_widget_carseller() {
//		parent::WP_Widget(false, $name = __('carseller Category list', 'category_list_widget_carseller') );
                parent::__construct(false, $name = __('Carseller Category list', 'category_list_widget_carseller') );
	}

	// widget form creation
	// widget form creation
function form($instance) {

// Check values
if( $instance) {
     $title = esc_attr($instance['title']);
     
     $selected_categories=$instance['category_list'];
     
} else {
     $title = '';
    
     $selected_categories='';
}
?>

<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'category_list_widget_carseller'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>


<p>
<label for="<?php echo $this->get_field_id('category_list'); ?>"><?php _e('Select Categories to show on front:', 'category_list_widget_carseller'); ?></label><br>
<span style="font-size: 11px;font-weight: bold;">Please use ctrl button to select multiple categories.</span>

<?php 

	$taxonomy = 'carsellers_category';
$terms = get_terms($taxonomy);
	// $terms = get_object_taxonomies( 'carsellers_category' );
// echo '<pre>';
// 	print_r($terms);
	
        if( $terms )
        {
            printf(
                '<select multiple="multiple" name="%s[]" id="%s" class="widefat" size="15">',
                $this->get_field_name('category_list'),
                $this->get_field_id('category_list')
            );
            foreach ($terms as $post)
            {
                printf(
                    '<option value="%s" class="hot-topic" %s style="margin-bottom:3px;">%s</option>',
                    $post->term_id,
                    in_array( $post->term_id, $selected_categories) ? 'selected="selected"' : '',
                    $post->name
                );
            }
            echo '</select>';
        }
        else
            echo 'No Category found.';
        ?>

</p>
<?php
}

	// widget update
	function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
      
      $instance['category_list'] = esc_sql($new_instance['category_list']);
   
     return $instance;
}

	// widget display
	function widget($args, $instance) {
   extract( $args );
   // these are the widget options
   $title = apply_filters('widget_title', $instance['title']);
   $selected_categories=$instance['category_list'];
   echo $before_widget;
   // Display the widget
   echo '<div class="widget-text category_list_widget_carseller_box">';

   // Check if title is set
   if ( $title ) {
      echo $before_title . $title . $after_title;
   }

   // Check if text is set
   if( $text ) {
      echo '<p class="category_list_widget_carseller_text">'.$text.'</p>';
   }
   // Check if textarea is set
   if( $selected_categories ) {
   		// print_r( $selected_categories);
   		echo '<div class="carseller_category_list">
   				<ul>';
   		$taxonomy = 'carsellers_category';
		$terms = get_terms($taxonomy);
		if( $terms )
        {
        	foreach ($terms as $term)
        	{
        		if(in_array( $term->term_id, $selected_categories))
        		{
        		$term = sanitize_term( $term, $taxonomy );

    			$term_link = get_term_link( $term, $taxonomy );
    			if ( is_wp_error( $term_link ) ) {
			        continue;
			    }

			    
			    echo '<li><a href="' . esc_url( $term_link ) . '">' . $term->name . '</a></li>';
				}
               
               
        	}
        }


   		// foreach ($selected_categories as $value)
     //    {
     //    	echo $value;
     //    	$term = get_terms( $value, 'carsellers_category' );
     //    	echo '<pre>';
     //    	print_r($term);
     //    	die;
     //    	echo  '<a href="">test</a>';
     //    }
        echo '</ul></div>';    
   		$term = get_term( $term_id, $taxonomy );

     // echo '<p class="category_list_widget_carseller_textarea">'.$textarea.'</p>';
   }
   echo '</div>';
   echo $after_widget;
}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("category_list_widget_carseller");'));




class carseller_search_form extends WP_Widget {

	// constructor
	function carseller_search_form() {
//		parent::WP_Widget(false, $name = __('CarSeller Search Form', 'carseller_search_form') );
                 parent::__construct(false, $name = __('CarSeller Search Form', 'carseller_search_form') );
	}

	// widget form creation
	// widget form creation
function form($instance) {

// Check values
if( $instance) {
     $title = esc_attr($instance['title']);
     
     
     
} else {
     $title = '';
    
     
}
?>

<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'category_list_widget_carseller'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<?php
}

	// widget update
	function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
     
   
     return $instance;
}

	// widget display
	function widget($args, $instance) {
   extract( $args );
   // these are the widget options
   $title = apply_filters('widget_title', $instance['title']);
   $selected_categories=$instance['category_list'];
   echo $before_widget;
   // Display the widget
   echo '<div class="widget-text category_list_widget_carseller_box">';

   // Check if title is set
   if ( $title ) {
      echo $before_title . $title . $after_title;
   }

   // Check if text is set
   if( $text ) {
      echo '<p class="category_list_widget_carseller_text">'.$text.'</p>';
   }
   
   
   
   wp_register_style('bootstrap-css',       plugins_url('css/bootstrap.min.css', __FILE__),        array(), '1.0');
wp_enqueue_style('bootstrap-css');
wp_enqueue_script('loadingoverlay', plugins_url('js/loadingoverlay.min.js', __FILE__),array(),'1.0');
wp_enqueue_script('bootstrap.min', plugins_url('js/bootstrap.min.js', __FILE__),array(),'1.0');
wp_enqueue_script('formValidation.min', plugins_url('js/formValidation.min.js', __FILE__),array(),'1.0');
wp_enqueue_script('bootstrap.min.cust', plugins_url('bootstrap.min.js', __FILE__),array(),'1.0');


wp_register_style('carsellers',  plugins_url('css/style.css', __FILE__),array(),'1.0');

wp_enqueue_style('carsellers');
wp_register_style('check_availability', plugins_url('css/check_availability.css', __FILE__),        array(), '1.0');
wp_register_style('bootstrap-slider', plugins_url('css/bootstrap-slider.css', __FILE__),        array(), '1.0');
wp_enqueue_style('check_availability');
wp_enqueue_style('bootstrap-slider');
wp_enqueue_script('carsellers', plugins_url('js/carsellers.js', __FILE__));
wp_enqueue_script('blockUI', plugins_url('js/blockUI.js', __FILE__));
wp_enqueue_script('bootbox', plugins_url('js/bootbox.js', __FILE__), array(), '1.0');
wp_enqueue_script('bootstrap-slider-js', plugins_url('js/bootstrap-slider.js', __FILE__), array(), '1.0');



   ?>


	<form id="wp-advanced-search-widget" name="wp-advanced-search" class="wp-advanced-search-widget "  action="<?php echo car_sellers_archive_url() ?>" method="GET">
            <div class=" col-md-12 ">
				
				<?php
				if( $text ) {
					echo '<h4  class="car_seller_search_title" >'.$text.'</h4>';
				 }
				?>
                
            </div>
            <div class="" style="padding-bottom:15px;">
                <div class=" ">
                    <div class="form-group  ">

                        <div class="col-md-12">
                            <label  class=" strong"> <b> Title</b> </label>
                            <input id="vichleTitle" name="vt" type="text" placeholder="Please enter vehicle title" class="form-control input-md" value="">
                        </div>

                    </div>
                </div>
                
            </div>
            <div class="" style="">
                <div class="">
                    <div class="form-group">

                        <div class="col-md-12" style="">
                            <label  class=" strong"> <b>Category</b> </label>
                            <?php
                            $args = array(
                                'show_option_all' => '',
                                'show_option_none' => 'Please select category',
                                'option_none_value' => '',
                                'orderby' => 'ID',
                                'order' => 'ASC',
                                'show_count' => 0,
                                'hide_empty' => 0,
                                'child_of' => 0,
                                'exclude' => '',
                                'echo' => 1,
                                'selected' => '',
                                'hierarchical' => 0,
                                'name' => 'ca',
                                'id' => '',
                                'required' => true,
                                'class' => 'form-control',
                                'depth' => 0,
                                'tab_index' => 0,
                                'taxonomy' => 'carsellers_category',
                                'hide_if_empty' => false,
                                'value_field' => 'term_id',
                            );
                            wp_dropdown_categories($args);
                            ?>



                        </div>
                    </div>
                </div>
                <div class="  ">
                    <div class="form-group  ">

                        <div class="col-md-12 ">
                            <label  class="strong"> <b> Model</b> </label>
                            <input id="vichleModel" name="md" type="text" placeholder="Please enter vehicle model" class="form-control input-md" value="<?php echo $vichleModel ?>">
                            <input type="hidden" name="se" value="1">
                        </div>

                    </div>
                </div>



            </div>	
            <div class=" col-md-12">
                <div class="form-group   nopadding">

                    <div class="col-md-12 ">
                        <button id="singlebutton" class="btn btn-primary col-md-12">Filter</button>
                    </div>
                    
                </div>
            </div>
            <hr class=" col-md-12">

        </form>



<script type="text/javascript">
        var slider = new Slider('#ex234', {});
        jQuery.LoadingOverlaySetup({
            color           : "rgba(0, 0, 0, 0.5)",
             image           : "<?php echo plugins_url('images/loading-1.gif', __FILE__)?>",
            maxSize         : "100px",
            minSize         : "100px",
            resizeInterval  : 0,
            size            : "50%"
       });
//       jQuery("#element").show();                      
//                            jQuery("#element").LoadingOverlay('show');                         
//        
         
         
         
         

         
</script>
<?php
   echo '</div>';
   echo $after_widget;
}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("carseller_search_form");'));










class carseller_latest_post extends WP_Widget {

	// constructor
	function carseller_latest_post() {
//		parent::WP_Widget( false, $name = __( 'CarSeller Latest Post', 'carseller_latest_post' ) );
                parent::__construct(false, $name = __('CarSeller Latest Post', 'carseller_latest_post') );
	}

	// widget form creation
	// widget form creation
	function form( $instance ) {

// Check values
		if ( $instance ) {
			$title = esc_attr( $instance['title'] );
			$no_of_post = esc_attr( $instance['no_of_post'] );
		} else {
			$title = '';
			$no_of_post = '';
		}
		?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title', 'category_list_widget_carseller' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'no_of_post' ); ?>"><?php _e( 'Number of post', 'category_list_widget_carseller' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'no_of_post' ); ?>" name="<?php echo $this->get_field_name( 'no_of_post' ); ?>" type="text" value="<?php echo $no_of_post; ?>" />
		</p>

		<?php
	}

	// widget update
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		// Fields
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['no_of_post'] = strip_tags( $new_instance['no_of_post'] );


		return $instance;
	}

	// widget display
	function widget( $args, $instance ) {
		extract( $args );
		// these are the widget options
		$title = apply_filters( 'widget_title', $instance['title'] );
		$no_of_post = apply_filters( 'widget_title', $instance['no_of_post'] );
		echo $before_widget;
		// Display the widget
		

		// Check if title is set
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		echo '<div class="widget-text category_latest_post col-md-12 nopadding">';
		if(empty($no_of_post))
		{
			echo '<div class="alert alert-danger fade in"><strong>Error!</strong> Number of post is not defined.
</div>';
		}
		
		
		global $post;
		$listings = new WP_Query();
		$listings->query('post_type=carsellers&posts_per_page=' . $no_of_post );
		if($listings->found_posts > 0) {
			echo '<ul class="realty_widget col-md-12">';
			$currency_symbol = get_currency_symbol();
				while ($listings->have_posts()) {
					$listings->the_post();
					$permalink = get_permalink($post_id);
					$image = (has_post_thumbnail($post->ID)) ? get_the_post_thumbnail($post->ID, 'thumb-carseller',array('class' => 'img-responsive img-thumbnail')) : '<div class="noThumb"><img src="' . plugins_url('cars-seller-auto-classifieds-script') . '/images/no-image.png" class="img-responsive img-thumbnail"></div>';
					$listItem = '<li class="car-latest-section pull-left col-md-12 nopadding"><div class="col-md-3 pull-left nopadding ">' . $image;
					$listItem .= '</div>'
							. '<div class="col-md-9 pull-right ">'
							. '<a href="' . get_permalink() . '">';
					$listItem .= get_the_title() . '</a>';
					$listItem .= '<div >' .$currency_symbol['symbol'] . '&nbsp;' .get_post_meta(get_the_ID(),'car_our_price',true). '</div>'
							. '</div>'
							. '<div class="col-md-12 pull-right view_details">'
							
							. '</div>'
							. '</li>';
					echo $listItem;
				}
			echo '</ul>';
			wp_reset_postdata();
		}else{
			echo '<p style="padding:25px;">No listing found</p>';
		}
		
		
		
		
		echo '</div>';
		echo $after_widget;
	}

}

// register widget
add_action( 'widgets_init', create_function( '', 'return register_widget("carseller_latest_post");' ) );
