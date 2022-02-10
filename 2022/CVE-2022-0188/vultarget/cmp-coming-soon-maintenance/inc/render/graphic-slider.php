<?php 

// change background if preview is set
if ( isset( $_GET['background'] ) && $_GET['background'] !== '1' ) {
    echo $this->cmp_background( $_GET['background'], $themeslug );
    return;
}

$niteoCS_banner     = get_option('niteoCS_banner', '1');
$slider_count       = get_option('niteoCS_slider_count', '3');
$slider_effect      = get_option('niteoCS_slider_effect', 'true');
$slider_autoplay    = get_option('niteoCS_slider_auto', '1');
$size               = $this->isMobile() ? 'large' : 'full';
$banner_ids         = $this->cmp_get_banner_ids();

// break slider if only one custom image uploaded
if ( $niteoCS_banner == '0' && isset( $banner_ids ) && count( $banner_ids ) < 2 ) {
    echo $this->cmp_background( '0', $themeslug ) ;
    return false;
} ?>

<div id="slider-wrapper">

        <div id="slider" class="slides effect-<?php echo esc_attr( $slider_effect );?>" data-autoplay="<?php echo esc_attr( $slider_autoplay );?>">
        <?php
        switch ( $niteoCS_banner ) {
            // custom media
            case '0':
                if ( isset( $banner_ids ) ) {
                    foreach ( $banner_ids as $id ) {
                        $slide_url = wp_get_attachment_image_src( $id, $size);
                        
                        if ( isset( $slide_url[0] ) ) {
                            $slide_url = $slide_url[0];
                        } ?>
                        <div class="slide">
                            <div class="slide-background" style="background-image:url('<?php echo esc_url( $slide_url ); ?>')"></div>
                        </div>
                        <?php 
                    }
                }
                break;

            // unsplash
            case '1':
                $unplash_feed   = get_option('niteoCS_unsplash_feed', '3');

                switch ( $unplash_feed ) {
                    // specific photo from id
                    case '0':
                        $params = array( 'feed' => '0', 'url' => get_option('niteoCS_unsplash_0', ''), 'count' => $slider_count );
                        $unsplash = $this->niteo_unsplash(  $params );
                        break;

                    // random from user
                    case '1':
                        $params = array( 'feed' => '1', 'custom_str' => get_option('niteoCS_unsplash_1', ''), 'count' => $slider_count  );
                        $unsplash = $this->niteo_unsplash(  $params );
                        break;

                    // random from collection
                    case '2':
                        $params = array( 'feed' => '2', 'url' => get_option('niteoCS_unsplash_2', ''), 'count' => $slider_count  );
                        $unsplash = $this->niteo_unsplash(  $params );
                        break;

                    // random photo
                    case '3':
                        $params = array( 'feed' => '3', 'url' => get_option('niteoCS_unsplash_3', ''), 'feat' => get_option('niteoCS_unsplash_feat', '0'), 'count' => $slider_count  );
                        $unsplash = $this->niteo_unsplash(  $params );
                        break;

                    default:
                        break;
                }

                // get raw url from response
                if ( isset( $unsplash['response'] ) && $unsplash['response'] == '200' ) {
                    $unsplash_body = json_decode($unsplash['body'], true);

                    $imgs = array();

                    if ( isset( $unsplash_body[0] ) ) {
                        foreach ( $unsplash_body as $item ) {
                            array_push( $imgs, $item['urls']['raw']);
                        }

                    } else {
                        $imgs[0] = $unsplash_body['urls']['raw'];
                    }

                    $imgs = json_encode( $imgs ); 

                    switch ( $themeslug ) {
                        case 'element':
                            $width = 1;
                            $height = 0.6;
                            break;
                        
                        default:
                            $width = 1;
                            $height = 1;
                            break;
                    }
                    ?>

                    <script>
                        var imgs = <?php echo $imgs;?>;

                        var width = document.getElementById('slider-wrapper').offsetWidth * <?php echo $width;?>;
                        var height = document.getElementById('slider-wrapper').offsetHeight * <?php echo $height;?>;
                        if ( height === 0 ) {
                            var body = document.body,
                            html = document.documentElement;

                            height = Math.max( body.scrollHeight, body.offsetHeight, 
                            html.clientHeight, html.scrollHeight, html.offsetHeight );
                        }
                        var dimension = 'w=' + width;
                        if ( width < height ) {
                            dimension = 'h=' + height;
                        }
                        var query  = '?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&fit=max&' + dimension;
                        var img = '';

                        for ( i=0; i < imgs.length; i++ ) {
                            var slide = document.createElement('div');

                            slide.className = 'slide';
                            img = imgs[i] + query;
                            var slide_background = '<div class="slide-background" style="background-image:url(\''+img+'\')"></div>'; 

                            slide.innerHTML = slide_background;
                            document.getElementById('slider').appendChild(slide);
                        }
                    </script>

                    <?php
                }

            default:
                break;
        } ?>
    </div>

    <?php 
    echo $this->background_overlay( $themeslug );

    ?>

</div>

<div class="slider-nav prev"></div>
<div class="slider-nav next"></div>

<?php

// render dot navigation for apollo theme
if ( $themeslug == 'apollo' ) { 

    if ( $niteoCS_banner == '0') {
        $slider_count = count( $banner_ids );
    } 
    
    echo '<div class="dot-nav">';

    for ( $i=0; $i < $slider_count; $i++ ) { 
        $slide_nm = $i + 1;

        if ( $i == 0 ) {
            echo '<div class="slide-number active" data-slide="0">0' . $slide_nm  . '</div>';
        } else {
            echo '<div class="slide-number" data-slide="' . $i . '">0' . $slide_nm . '</div>';
        }
        
    }

    echo '</div>';

}