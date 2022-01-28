<?php
/**
 * The Template for displaying all single posts.
 *
 * @package adamos
 * @since adamos 1.0
 */
wp_enqueue_script('jquery');
wp_enqueue_script('jquery.ajax','//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js',array(), '1.0');
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
/* footer js */

$post_meta = get_post_meta(get_the_ID());

$post_id = get_the_ID();
// echo '<pre>';
// print_r($post_meta);
// die;
?>
<script src="//cdn.jsdelivr.net/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="http://formvalidation.io/vendor/formvalidation/js/formValidation.min.js"></script>
<script src="http://formvalidation.io/vendor/formvalidation/js/framework/bootstrap.min.js"></script>



<div class="container  width-container ">
    <div class="col-md-12">
        <div class="col-md-8">
            <?php
            $car_data = get_post_meta(get_the_ID());
            if (!empty($car_data['car_carsellers_images'][0])) {
                ?>
                <div data-ride="carousel" class="carousel slide" id="myCarousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <?php
                        $car_images = unserialize($car_data['car_carsellers_images'][0]);
                        $count = 0;
                        foreach ($car_images as $key => $value) {
                            if ($count == 0) {
                                echo ' <li data-target="#myCarousel" data-slide-to="' . $count . '" class="active"></li>';
                            } else {
                                echo ' <li data-target="#myCarousel" data-slide-to="' . $count . '" ></li>';
                            }

                            $count++;
                        }
                        ?>             
                    </ol>

                    <!-- Wrapper for slides -->
                    <div role="listbox" class="carousel-inner">

    <?php
    if (!empty($car_data['car_carsellers_images'][0])) {
        $car_images = unserialize($car_data['car_carsellers_images'][0]);
        $count = 0;
        foreach ($car_images as $key => $value) {
            
            if ($count == 0) {
                $thumb = wp_get_attachment_image_src( $value['car_carsellers_image']['id'], 'big-carseller');
                $image_url = $thumb['0'];
                if(empty($image_url))
                {
                    $thumb = wp_get_attachment_image_src( $value['car_carsellers_image']['id'], 'large');
                $image_url = $thumb['0'];
                }        
                echo '<div class="item active">'
                .'<a  data-gallery="remoteload" data-toggle="lightbox" data-parent="" href="' . $value['car_carsellers_image']['url'] . '">'
                . '<img src="' . $image_url . '" />'
                . '</a>'
                . '</div>';
            } else {
                $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($value['car_carsellers_image']['id']), 'big-carseller' );
                $image_url = $thumb['0'];
                if(empty($image_url))
                {
                    $thumb = wp_get_attachment_image_src( $value['car_carsellers_image']['id'], 'large');
                $image_url = $thumb['0'];
                } 
                echo ' <div class="item">'
                         .'<a   data-gallery="remoteload" data-toggle="lightbox" data-parent="" href="' . $value['car_carsellers_image']['url'] . '">'
                                     .' <img src="' . $image_url. '" />'
                . '</a></div>';
            }

            $count++;
        }
    }
    ?>



                    </div>

                    <!-- Left and right controls -->
                    <a data-slide="prev" role="button" href="#myCarousel" class="left carousel-control">
                        <span aria-hidden="true" class="glyphicon glyphicon-chevron-left"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a data-slide="next" role="button" href="#myCarousel" class="right carousel-control">
                        <span aria-hidden="true" class="glyphicon glyphicon-chevron-right"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
    <?php
} else {
    echo '<img src="' . plugins_url('cars-seller-auto-classifieds-script') . '/images/no-image.png" title="';
    the_title();
    echo '" class="img-responsive " width="100%">';
}
?>
        </div>
        <div class="col-md-4">
            <?php
            if (get_the_title(get_the_ID())) {
                echo '<div class="carseller-title  col-md-10 panel-heading">';
                echo '<h1 id="page-heading">' . get_the_title(get_the_ID()) . '</h1>';
                echo '</div>';
            }
            $car_data = get_post_meta(get_the_ID());
            ?>
            <div class="car-sidebar  col-md-10">
                <div id="price-sidebar" class="sidebar-item widget widget_text">
                    <div id="sidebar-price">
            <?php
            if (!empty($post_meta['car_our_price'][0])) {
                ?>
                            <h3 class="pcd-pricing">
                                <span class="pcd-price"><b>Price:</b> <?php
                $currency_symbol = get_currency_symbol();
                echo $currency_symbol['symbol'] . '&nbsp;' . number_format($post_meta['car_our_price'][0],
                        0)
                ?> </span><br>
                            </h3>
    <?php
}
?>
                    </div>


                    <div class="chk-availability sidebar-button-price">

                        <button class="btn btn-info active btn btn-success btn-medium" onclick="check_availability(<?php echo $post_id; ?>);" type="button"> <i class="glyphicon glyphicon-send"></i> &nbsp; Request <strong>Information</strong></button>
                    </div>
                                <?php
                                if (!empty($post_meta['car_address'][0])) {
                                    ?>
                        <div class="chk-availability-location ">
                            
                            <address style="color:transparent">
    <?php echo $post_meta['car_address'][0] ?>
                            </address>    
                            <script type="text/javascript">
                              $(document).ready(function () {
                                  $("address").each(function () {
                                      var embed = "<iframe width='100%' height='310' frameborder='0' scrolling='no'  marginheight='0' marginwidth='0'   src='https://maps.google.com/maps?&amp;q=" + encodeURIComponent($(this).text()) + "&amp;output=embed'></iframe>";
                                      $(this).html(embed);

                                  });
                              });

                            </script>

                        </div>
    <?php
}
?>

                </div>
            </div>
        </div>
    </div>


<div class="row col-md-12" >



   
    <article id="post-383" class="post-383  vehicle type-vehicle status-publish has-post-thumbnail hentry col-md-12">

 <hr >

        <div class="content-container-boxed container-fluid row ">

            

            <ul class="nav nav-tabs nav-justified nopadding" id="vehicle_discription">
                <li class="active">
                    
                    <a data-target="#progression-description" data-toggle="tab"><i class="glyphicon glyphicon-home"></i> &nbsp; Description</a>
                </li>
                <li>
                    <a data-target="#progression-specs" data-toggle="tab"> <i class="glyphicon glyphicon-list-alt"></i> &nbsp; Specifications</a>
                </li>
                <li><a  data-target="#Request_Information" data-toggle="tab" onclick="check_availability(<?php echo $post_id; ?>);"> <i class="glyphicon glyphicon-send"></i> &nbsp; Request Information</a></li>
            </ul>

            <div class="tab-content">
                <div id="progression-description"  class="tab-pane col-md-12 active jumbotron">
                    <h3 class=" col-md-12">Vehicle Description</h3>
                    <p  class=" col-md-12"><?php echo $car_data['car_Vehicle_Description'][0] ?></p>
                </div>
                <div id="progression-specs"  class="tab-pane  col-md-12 jumbotron" >
                    <h3 class=" col-md-12">Vehicle Specifications</h3>
                    <ul id="pro-vehicle-specifications"  class="list-group   col-md-12">

<?php
if (!empty($car_data['car_model'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Model:</span> <span class="spec-value">' . $car_data['car_model'][0] . '</span>
                            </li>';
}

if (!empty($car_data['car_registration_date'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Registration Date:</span> <span class="spec-value">' . $car_data['car_registration_date'][0] . '</span>
                            </li>';
}

if (!empty($car_data['car_running_status'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Running Status:</span> <span class="spec-value">' . $car_data['car_running_status'][0] . '</span>
                            </li>';
}

if (!empty($car_data['car_ownership'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Ownership:</span> <span class="spec-value">' . $car_data['car_ownership'][0] . '</span>
                            </li>';
}

if (!empty($car_data['car_insurance_status'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Insurance Status:</span> <span class="spec-value">' . $car_data['car_insurance_status'][0] . '</span>
                            </li>';
}

if (!empty($car_data['car_fuel_type'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Fuel Type:</span> <span class="spec-value">' . $car_data['car_fuel_type'][0] . '</span>
                            </li>';
}

if (!empty($car_data['car_mileage'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Mileage:</span> <span class="spec-value">' . $car_data['car_mileage'][0] . '</span>
                            </li>';
}

if (!empty($car_data['car_condition'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Condition:</span> <span class="spec-value">' . $car_data['car_condition'][0] . '</span>
                            </li>';
}

if (!empty($car_data['car_exterior_color'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Exterior Color:</span> <span class="spec-value">' . $car_data['car_exterior_color'][0] . '</span>
                            </li>';
}

if (!empty($car_data['car_interior_color'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Interior Color:</span> <span class="spec-value">' . $car_data['car_interior_color'][0] . '</span>
                            </li>';
}
if (!empty($car_data['car_transmission'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Transmission:</span> <span class="spec-value">' . $car_data['car_transmission'][0] . '</span>
                            </li>';
}

if (!empty($car_data['car_engine'][0])) {
    echo '<li class="list-group-item"> 
                              <span class="spec-label">Engine:</span> <span class="spec-value">' . $car_data['car_engine'][0] . '</span>
                            </li>';
}
?>

                    </ul>
                </div>
                <div class="tab-pane" id="Request_Information"></div>
            </div>


        </div>
    </article>
</div>

</div>
<div id="element"  ></div>
                        <?php //get_sidebar(); ?>
                        <?php get_footer(); ?>
<script type='text/javascript' src="//seiyria.github.io/bootstrap-slider/javascripts/bootstrap-slider.js"></script>
<link href="//seiyria.github.io/bootstrap-slider/stylesheets/bootstrap-slider.css" rel="stylesheet">
<script type="text/javascript">
    
    jQuery.LoadingOverlaySetup({
            color           : "rgba(0, 0, 0, 0.5)",
             image           : "<?php echo plugins_url('images/loading-1.gif', __FILE__)?>",
            maxSize         : "100px",
            minSize         : "100px",
            resizeInterval  : 0,
            size            : "50%"
       });      
       
       
</script> 



