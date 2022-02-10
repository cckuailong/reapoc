<?php if ( ! defined( 'ABSPATH' ) ) exit; 

  $psAutoplay = $this_widget_postsSlider['psAutoplay'];
  $psSlideDelay = $this_widget_postsSlider['psSlideDelay'];
  $psSlideLoop = $this_widget_postsSlider['psSlideLoop'];
  $psSlideTransition = $this_widget_postsSlider['psSlideTransition'];
  $psPostsNumber = $this_widget_postsSlider['psPostsNumber'];
  $psDots = $this_widget_postsSlider['psDots'];
  $psArrows = $this_widget_postsSlider['psArrows'];
  $psFtrImage = $this_widget_postsSlider['psFtrImage'];
  $psFtrImageSize = $this_widget_postsSlider['psFtrImageSize'];
  $psExcerpt = $this_widget_postsSlider['psExcerpt'];
  $psReadMore = $this_widget_postsSlider['psReadMore'];
  $psMoreLinkText = $this_widget_postsSlider['psMoreLinkText'];
  $psHeadingSize = $this_widget_postsSlider['psHeadingSize'];
  $psTextAlignment = $this_widget_postsSlider['psTextAlignment'];
  $psBgColor = $this_widget_postsSlider['psBgColor'];
  $psTxtColor = $this_widget_postsSlider['psTxtColor'];
  $psHeadingTxtColor = $this_widget_postsSlider['psHeadingTxtColor'];
  $psPostType = $this_widget_postsSlider['psPostType'];
  $psPostsOrderBy = $this_widget_postsSlider['psPostsOrderBy'];
  $psPostsOrder = $this_widget_postsSlider['psPostsOrder'];
  $psPostsFilterBy = $this_widget_postsSlider['psPostsFilterBy'];
  $psFilterValue = $this_widget_postsSlider['psFilterValue'];

   

	$args = array (
        'post_type'              => $psPostType,
        $psPostsFilterBy         => $psFilterValue, 
        'posts_per_page'         => $psPostsNumber,
        'order'                  => $psPostsOrder,
        'orderby'                => $psPostsOrderBy,
    );

    $the_query = new WP_Query( $args );
  $PSrandID = (rand(500,1000)*2)*rand(10,500);
  $DotColor = '#333';

  $psScripts = ' ';

  ob_start();

  echo $psScripts.'<div id="PbPostSlider-'.$PSrandID.'" class="pbOwl-carousel" style="background:'.$psBgColor.'; text-align:'.$psTextAlignment.'; width:95%; margin: 0 auto; padding:0.1% 0 2% 0;">';

  
  
  while ($the_query -> have_posts()) : $the_query -> the_post();
  
  $post_id = get_the_ID();
  
  if ( has_post_thumbnail() && $psFtrImage === 'initial' ) {
    $thumbUrl = get_the_post_thumbnail($post_id,$psFtrImageSize,array( 'class' => 'align'.$psTextAlignment ));
    }
    else {
    	$thumbUrl = '';
    }

  $PSliderHeading = '<h3 style="color:'.$psHeadingTxtColor.'; font-size:'.$psHeadingSize.'px; text-align:'.$psTextAlignment.'; ">' . get_the_title() . '</h3>';
  $PSliderReadMore = '<a href="'.get_permalink( $post_id ).'" style="display:'.$psReadMore.';"> '.$psMoreLinkText.' </a>';
  $PSliderExcerpt = '<p style="display:'.$psExcerpt.'; text-align:'.$psTextAlignment.';color:'.$psTxtColor.'; font-size:'.(($psHeadingSize/2)+1).'px;">'. get_the_excerpt() .'  '.$PSliderReadMore.'</p>';
  $PSliderFtrImage = '<br style="display:'.$psFtrImage.';">'.$thumbUrl;


  echo '<div class="PS-Single-slide" style="background:'.''.'; text-align:'.$psTextAlignment.'; width:95%; margin: 0 auto; padding:0.1% 0 2% 0;">'.$PSliderFtrImage.$PSliderHeading.$PSliderExcerpt.'</div>';

 endwhile;

 echo '</div>';

 echo "<style> #PbPostSlider-$PSrandID .dashicons { color:$DotColor !important; margin-right:5px !important; font-size:40px !important; } 
	.owl-theme .owl-controls .owl-page span { background: $DotColor !important; }
  #PbPostSlider-$PSrandID .read-more {display:none;}
 </style>";
 $PSlider =  ob_get_contents();
 ob_end_clean();

  $psInitJS = "

    jQuery(document).ready(function() {
            
   
    jQuery('#PbPostSlider-$PSrandID').owlCarousel({
      items:1,
      singleItem: true,
      autoPlay : $psAutoplay,
      stopOnHover : true,
      navigation: $psArrows ,
      paginationSpeed : ".$psSlideDelay."00,
      goToFirstSpeed : ".$psSlideDelay."00,
      singleItem : false,
      autoHeight : true,
      slideSpeed : ".$psSlideDelay."000,
      transitionStyle: '$psSlideTransition',
      pagination : $psDots,
      paginationNumbers: false,
      navigationText : ['<span class=\"dashicons dashicons-arrow-left-alt2\" > </span>', '<span class=\"dashicons dashicons-arrow-right-alt2\" > </span>'],
      theme: 'pbOwl-theme',
      baseClass: 'pbOwl-carousel'

    });
   });

 ";

$widgetJQueryLoadScripts = true;

?>