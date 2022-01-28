<?php
/**
 * The template for displaying Archive pages for carsellers.
 *
 *
 */


wp_enqueue_script('jquery');
wp_register_style('bootstrap-css',       plugins_url('css/bootstrap.min.css', __FILE__),        array(), '1.0');
wp_enqueue_style('bootstrap-css');
wp_enqueue_script('loadingoverlay', plugins_url('js/loadingoverlay.min.js', __FILE__),array(),'1.0');
wp_enqueue_script('bootstrap.min', plugins_url('js/bootstrap.min.js', __FILE__),array(),'1.0');
wp_enqueue_script('formValidation.min', plugins_url('js/formValidation.min.js', __FILE__),array(),'1.0');
wp_enqueue_script('bootstrap.min.cust', plugins_url('bootstrap.min.js', __FILE__),array(),'1.0');

get_header();

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
<div class=" carsellers-category-list width-container  container ">
    <div class=" col-md-12" role="main">

        <?php
        $vichleTitle = '';
        if (isset($_GET['vt']) && !empty($_GET['vt'])) {
            $vichleTitle = $_GET['vt'];
        }

        $vichleModel = '';
        if (isset($_GET['md']) && !empty($_GET['md'])) {
            $vichleModel = $_GET['md'];
        }

        $category = '';
        if (isset($_GET['ca']) && !empty($_GET['ca'])) {
            $category = $_GET['ca'];
        }
        $search = false;
        if (isset($_GET['se']) && !empty($_GET['se']) && $_GET['se']==1) {
            $search=true;
        }

        $price = '10250,54500';
        if (isset($_GET['pr']) && !empty($_GET['pr'])) {
            $price = $_GET['pr'];
        }
        
        ?>
        
        <h1 class="luxury-heading-categaory carseller_heading_title " >
                    <?php
                    $title=single_cat_title('', false);
                    if(empty($title))
                        echo 'Vehicles ';
                    else
                        echo $title
                    ?>
                    
            </h1>
        
        <form id="wp-advanced-search" name="wp-advanced-search" class="wp-advanced-search col-md-3 "  action="<?php echo car_sellers_archive_url() ?>" method="GET">
            <div class=" col-md-12 ">
                <h4  class="car_seller_search_title" >Filters</h4>
                
            </div>
			<hr>
            <div class="" style="padding-bottom:15px;">
                <div class=" ">
                    <div class="form-group  ">

                        <div class="col-md-12">
                            <label  class=" strong"> <b> Title</b> </label>
                            <input id="vichleTitle" name="vt" type="text" placeholder="Please enter vehicle title" class="form-control input-md" value="<?php echo $vichleTitle ?>">
                        </div>

                    </div>
                </div>
                <div class=" " style="height:50px;">
                    <div class="form-group">

                        <div class="col-md-12">
                            <label class="" ><b>Price</b></label>
                            <div class=" ">
                                <?php
                                $currency_symbol = get_currency_symbol();
                                // print_r($currency_symbol);
                                echo '<span style="font-size:12px">' . $currency_symbol['symbol'] . '0</span>';
                                ?>


                                <input id="ex2" type="text" class="span2 col-md-9" value="" name="pr" data-slider-min="0" data-slider-max="100000" data-slider-step="1" data-slider-value="[<?php echo $price ?>]"/>
<?php
echo '<span style="font-size:12px">' . $currency_symbol['symbol'] . '100000</span>';
?>

                            </div>
                            <br>
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
                                'selected' => $category,
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
        <div id="luxury-carsellers" class="row col-md-9">
			<br>		
<?php if (have_posts()) : ?>
            



                
                
                <div class="row  col-md-12 center-block">
                    <?php
                    if (is_category()) {
                        // show an optional category description
                        $category_description = category_description();
                        if (!empty($category_description))
                                echo apply_filters('category_archive_meta',
                                    '<div class="taxonomy-description">' . $category_description . '</div>');
                    } elseif (is_tag()) {
                        // show an optional tag description
                        $tag_description = tag_description();
                        if (!empty($tag_description))
                                echo apply_filters('tag_archive_meta',
                                    '<div class="taxonomy-description">' . $tag_description . '</div>');
                    }
					
                    if($search){
                    $type = 'carsellers';
                    $args = array(
                        'post_type' => $type,
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        's' => $vichleTitle,
                        'caller_get_posts' => 1,
                        'orderby' => 'date',
                        'order' => 'DESC');

                    if (!empty($category)) {
                        $args['tax_query'] = array(
                            array(
                                'taxonomy' => 'carsellers_category',
                                'field' => 'term_id',
                                'terms' => $category,
                            ),
                        );
                    }
                    $priceFilter = explode(',', $price);
                    // print_r($priceFilter);
                    if (!empty($price) && $price != '10250,54500') {
                        $args['meta_query'] = array(
                            array(
                                'key' => 'car_our_price',
                                'value' => $priceFilter,
                                'compare' => 'BETWEEN',
                                'type' => 'numeric',
                            )
                        );
                    }

                    if (!empty($vichleModel)) {
                        $args['meta_query'] = array(
                            array(
                                'key' => 'car_model',
                                'value' => $vichleModel,
                            )
                        );
                    }


                    $my_query = null;
//                    $my_query = new WP_Query($args);
                    $my_query = new WP_Query($args);
//                     $my_query = new WP_Query('category__in=2');
                    
                    
                    if ($my_query->have_posts()) {
                        while ($my_query->have_posts()) : $my_query->the_post();

                            // }
                            // else{
                            // 	while ( have_posts() ) : the_post(); 
                            // }
                            $post_id = get_the_ID();

                            $car_meta = get_post_meta(get_the_ID());
                            // echo '<pre>';
                            // print_r($post_meta);
                            // die;
                            $permalink = get_permalink($post_id);
                            ?>
                            <div class="carseller_section col-md-4">
                                <div class="carseller_image  ">

                            <?php
                            echo '<a href="' . $permalink . '">';
                            if (has_post_thumbnail()) { // check if the post has a Post Thumbnail assigned to it.
                                the_post_thumbnail(array(300, 206),
                                        array('class' => 'img-responsive img-thumbnail'));
                            } else {
                                echo '<img src="' . plugins_url('cars-seller-auto-classifieds-script') . '/images/no-image.png" title="';
                                the_title();
                                echo '" class="img-responsive img-thumbnail"  height="274">';
                            }
                            echo '</a>';
                            ?>

                                </div>
                                <div class="carseller_title_location  panel-transparent">
                                    <h4 class="carseller_title hero-unit  col-sm-12"><?php
                                    echo '<a href="' . $permalink . '">';
                                    the_title();
                                    echo '</a>';
                                    ?>
                                    <?php
                                    if (!empty($car_meta['car_our_price'][0])) {

                                        $currency_symbol = get_currency_symbol();
                                        // print_r($currency_symbol);
                                        // echo $currency_symbol['symbol'];
                                        echo '<div ><span class="pcd-price" style=""><b>Price:</b> ' . $currency_symbol['symbol'] . '&nbsp;' . number_format($car_meta['car_our_price'][0],
                                                0) . ' </span></div>
								';
                                    }
                                    ?>
                                    </h4>
                                </div>

                                <div class="carseller-view-button ">

                                    <div class="btn-group col-sm-12">
                                        <a href="<?php echo $permalink; ?>" class="col-sm-6 btn btn-primary btn-warning view_details"> <span class="glyphicon glyphicon-tasks"></span>  <strong>Details</strong></a>

                                        <a href="javascript:void(0);"  class="col-sm-6 btn btn-primary btn-success check_availability" onclick="check_availability(<?php echo $post_id; ?>);"> <span class="glyphicon glyphicon-send"></span>  <strong>Request</strong></a>
                                    </div>
                                </div>

                            </div>
        <?php
        endwhile;
        }
		else {
					
					echo '<div class="row col-md-12 jumbotron ">No listing found!</div>';
		   }
		
    }
    else if (have_posts()) {
                        while (have_posts()) : the_post();

                            // }
                            // else{
                            // 	while ( have_posts() ) : the_post(); 
                            // }
                            $post_id = get_the_ID();

                            $car_meta = get_post_meta(get_the_ID());
                            // echo '<pre>';
                            // print_r($post_meta);
                            // die;
                            $permalink = get_permalink($post_id);
                            ?>
                            <div class="carseller_section col-md-4">
                                <div class="carseller_image ">

                            <?php
                            echo '<a href="' . $permalink . '">';
                            if (has_post_thumbnail()) { // check if the post has a Post Thumbnail assigned to it.
//                                the_post_thumbnail(array(300, 206),
//                                        array('class' => 'img-responsive img-thumbnail'));
                                the_post_thumbnail('thumb-carseller',
                                        array('class' => 'img-responsive img-thumbnail'));
                            } else {
                                echo '<img src="' . plugins_url('cars-seller-auto-classifieds-script') . '/images/no-image.png" title="';
                                the_title();
                                echo '" class="img-responsive img-thumbnail" height="274">';
                            }
                            echo '</a>';
                            ?>

                                </div>
                                <div class="carseller_title_location  panel-transparent">
                                    <h3 class="carseller_title hero-unit  col-sm-12"><?php
                                    echo '<a href="' . $permalink . '">';
                                    the_title();
                                    echo '</a>';
                                    ?>
                                    <?php
                                    if (!empty($car_meta['car_our_price'][0])) {

                                        $currency_symbol = get_currency_symbol();
                                        // print_r($currency_symbol);
                                        // echo $currency_symbol['symbol'];
                                        echo '<div ><span class="pcd-price" style=""><b>Price:</b> ' . $currency_symbol['symbol'] . '&nbsp;' . number_format($car_meta['car_our_price'][0],
                                                0) . ' </span></div>
								';
                                    }
                                    ?>
                                    </h3>
                                </div>

                                <div class="carseller-view-button ">

                                    <div class="btn-group col-sm-12">
                                        <a href="<?php echo $permalink; ?>" class="col-sm-6 btn btn-primary btn-warning view_details"> <span class="glyphicon glyphicon-tasks"></span> <strong>Details</strong></a>

                                        <a href="javascript:void(0);"  class="col-sm-6 btn btn-primary btn-success check_availability" onclick="check_availability(<?php echo $post_id; ?>);"> <span class="glyphicon glyphicon-send"></span> <strong>Availability</strong></a>
                                    </div>
                                </div>

                            </div>
        <?php
        endwhile;
        }
    else {
            echo '<div class="row col-md-12 jumbotron ">No listing found!</div>';
    }
    
    ?>
                </div>
            
    <?php adamos_content_nav('nav-below'); ?>

<?php else : ?>
			                
        <?php get_template_part('no-results', 'search'); ?>

<?php endif; ?>
</div>
    </div><!-- #content .site-content -->
</div><!-- #primary .content-area -->
<div id="element"  ></div>
<?php //get_sidebar(); ?>
<?php get_footer(); ?>

<!-- The form which is used to populate the item data -->



<script type="text/javascript">
        var slider = new Slider('#ex2', {});
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