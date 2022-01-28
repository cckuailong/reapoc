<?php
function latest_cars($atts) {
 
    	wp_enqueue_script('jquery');
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



	if(!empty($atts))
	{

		?>

		<div class="content-area carsellers-category-list width-container row col-md-12 ">
			<div class="" role="main">
				<div id="luxury-carsellers ">
		<div class="full-width-line"></div>
		<h1 class="luxury-heading">Latest <strong>Vehicles</strong></h1>

		<?php 
		$no_of_carsellers= $atts[0];

		$args = array( 'post_type' => 'carsellers', 'posts_per_page' => $no_of_carsellers );
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) : $loop->the_post();
			$post_id=get_the_ID();

			$car_meta = get_post_meta( get_the_ID() );	
			// echo '<pre>';
			// print_r($car_meta);
			// die;
			$permalink = get_permalink( $post_id );
			
		
			?>
			<div class="carseller_section col-md-4">
				<div class="carseller_image ">

				<?php 
				echo '<a href="'.$permalink.'">';
				if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
				  the_post_thumbnail(array(300,206), array('class' => 'img-responsive img-thumbnail')); 

				}
				else{
					echo '<img src="'.plugins_url('cars-seller-auto-classifieds-script').'/images/no-image.png" title="';
					the_title();
					echo '" class="img-responsive img-thumbnail"  style="height:165px">';
				} 
				echo '</a>';

				?>
					
				</div>
				<div class="carseller_title_location  panel-transparent">
					<h3 class="carseller_title hero-unit  col-sm-12"><?php echo '<a href="'.$permalink.'">';
					 the_title();
					 echo '</a>';?>
					<?php
					// echo '<pre>';
					// print_r($car_meta);
					if(!empty($car_meta['car_our_price'][0]))
					{
						
							$currency_symbol=get_currency_symbol();
							// print_r($currency_symbol);
							// echo $currency_symbol['symbol'];
							echo '<div ><span class="pcd-price" style="font-size:16px;"><b>Price:</b> '.$currency_symbol['symbol'].'&nbsp;'.number_format($car_meta['car_our_price'][0],0).' </span></div>
								';
						
					}
			
					?>
					</h3>
				</div>

				
				<div class="carseller-view-button ">

					<div class="btn-group col-sm-12">
						<a href="<?php echo  $permalink;?>" class="col-sm-6 btn btn-primary btn-warning view_details"> <span class="glyphicon glyphicon-tasks"></span> <strong>Details</strong></a>

						<a href="javascript:void(0);"  class="col-sm-6 btn btn-primary btn-success check_availability" onclick="check_availability(<?php echo $post_id;?>);">  <span class="glyphicon glyphicon-send"></span>  <strong>Request</strong></a>
					</div>
				</div>

			</div>

			<?php 
			
		endwhile;
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
	else
	{
		echo '<div class=" jumbotron" >Please select number of carsellers to show or check are you using right short code.</div>';
	}
    
}
add_shortcode( 'latest_cars', 'latest_cars' );