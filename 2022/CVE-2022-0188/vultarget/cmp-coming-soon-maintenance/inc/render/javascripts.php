
<?php

do_action('cmp-before-footer-scripts');

//include theme defaults, to check if new counter is available

if ( file_exists( $this->cmp_theme_dir($themeslug).$themeslug.'/'.$themeslug.'-defaults.php' ) ) {
	include ( $this->cmp_theme_dir($themeslug).$themeslug.'/'.$themeslug.'-defaults.php' );
}

$counter_script = false;

if ( isset($_GET['background']) && is_numeric($_GET['background']) ) {
    $background = esc_attr($_GET['background']);
}

if ( $background !== false ) { ?>
    <!-- Fade in background image after load -->
    <script>
        window.addEventListener("load",function(event) {
            init();
        });

        function init(){

            var image = document.getElementById('background-image');
            var body = document.getElementById('body');
            
            if ( image === null ) {
                image = document.getElementById('body');
            } 

            if ( image != null ) {
                if ( image.className !== 'image-unsplash') {
                    image.classList.add('loaded');
                    body.classList.add('loaded');
                }
            }

            <?php
            // theme specific function after init
            switch ( $themeslug ) {
                case 'fifty': ?>
                    var contentWrapper = document.getElementsByClassName('content-wrapper')[0];
                    setTimeout(function(){ contentWrapper.className += " overflow"; }, 1500);
                    
                    <?php 
                break;

                case 'hardwork_premium': ?>
                    var contentWrapper = document.getElementsByClassName('section-body')[0];
                    setTimeout(function(){ contentWrapper.className += " overflow"; }, 1500);
                    <?php 
                break;
                
                default:
                    break;
            } ?>
        }
    </script>

    <?php

    // include background scripts
    switch ( $background ) {
        // VIDIM script for background video 
        case '5': 
            $video_autoloop	= get_option('niteoCS_video_autoloop', '1');
            $size = $this->isMobile() ? 'large' : 'full'; ?>
            <script src='<?php echo plugins_url('cmp-coming-soon-maintenance/js/external/vidim.min.js?v=1.0.2"');?>'></script>
            <script>
                <?php 
                $video_poster   = wp_get_attachment_image_src( get_option('niteoCS_video_thumb'), $size );

                if ( !empty( $video_poster ) ) {
                    $video_poster = $video_poster[0];       
                }
                // video
                $source = get_option('niteoCS_banner_video');
                
                // sanitize source
                switch ( $source ) {
                    case 'youtube':
                        $source = 'YouTube';
                        break;
                    case 'local':
                        $source = 'video/mp4';
                        break;
                    default:
                        break;
                }

                switch ( $source ) {
                    case 'YouTube': ?>
                        var src = '<?php echo esc_url(get_option('niteoCS_youtube_url')); ?>'.split('?t=');
                        var myBackground = new vidim( '#player', {
                            src: src[0],
                            type: 'YouTube',
                            poster: '<?php echo esc_url( $video_poster ); ?>',
                            quality: 'hd1080',
                            muted: true,
                            loop: <?php echo $video_autoloop ? 'true' : 'false' ; ?>,
                            startAt: src.length > 1 ? src[1] : '0',
                            showPosterBeforePlay: true
                        });

                    <?php 
                        break;

                    case 'vimeo': ?>
                        var src = <?php esc_url(get_option('niteoCS_vimeo_url')); ?>
                        var myBackground = new vidim( '#player', {
                            src: src,
                            type: 'vimeo',
                            poster: '<?php echo esc_url( $video_poster ); ?>',
                            loop: <?php echo $video_autoloop ? 'true' : 'false' ; ?>
                        });
                        <?php
                        break;

                    case 'video/mp4':
                        $banner_url = get_option('niteoCS_video_file_url');
                        $banner_url = wp_get_attachment_url( $banner_url ); ?>
                        var myBackground = new vidim( '#player', {
                            src: [
                                {
                                type: 'video/mp4',
                                src: '<?php echo esc_url( $banner_url ); ?>',
                                },
                            ],
                            poster: '<?php echo esc_url( $video_poster ); ?>',
                            showPosterBeforePlay: true,
                            loop: <?php echo $video_autoloop ? 'true' : 'false' ; ?>
                        });
                        <?php 
                        break;
                    default:
                        break;
                } ?>
            </script>
            <?php 
            break;

        // SLIDER SCRIPTS FOR UNSPLASH or Custom IMAGES
        case '0': 
        case '1':
            $slider = get_option('niteoCS_slider', '0');

            if (  $slider == '1' ) {
                $slider_effect      = get_option('niteoCS_slider_effect', 'true');
                $slider_autoplay    = get_option('niteoCS_slider_auto', '1');

                switch ( $slider_effect ) {
                    // slice effect scripts
                    case 'slice':
                        ?>
                        <script src='<?php echo plugins_url('cmp-coming-soon-maintenance/js/external/imagesloaded.pkgd.min.js' );?>'></script>
                        <script src='<?php echo plugins_url('cmp-coming-soon-maintenance/js/external/anime.min.js' );?>'></script>
                        <script src='<?php echo plugins_url('cmp-coming-soon-maintenance/js/external/uncover.js' );?>'></script>
                        <script src='<?php echo $this->cmp_themeURL($themeslug).$themeslug.'/js/slice.js';?>'></script>
                        <?php 
                        break;

                    // mask transition effect scripts
                    case 'mask-transition':
                        if ( !$this->jquery ) {
                            echo '<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" Crossorigin="anonymous"></script>';
                            $this->jquery = TRUE;
                        } ?>
                        <script src='<?php echo $this->cmp_themeURL($themeslug).$themeslug.'/js/modernizr-custom.js';?>'></script>
                        <script src='<?php echo $this->cmp_themeURL($themeslug).$themeslug.'/js/imagesloaded.pkgd.min.js';?>'></script>
                        <script src='<?php echo $this->cmp_themeURL($themeslug).$themeslug.'/js/mask-transition.js';?>'></script>

                        <?php 
                        break;

                    case 'train':
                        break;
                        
                    // default (true, false) means slick carousel scripts
                    default:
                        if ( !$this->jquery ) {
                            echo '<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" Crossorigin="anonymous"></script>';
                            $this->jquery = TRUE;
                        } ?>
                        <!-- slick carousel script -->
                        <script src='https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js'></script>
                        <script>
                            $('#slider').slick({
                                slide: '.slide',
                                slidesToShow: 1,
                                arrows: false,
                                fade: <?php echo esc_attr( $slider_effect );?>,
                                speed: 1000,
                                autoplay: <?php echo esc_attr( $slider_autoplay );?>,
                                autoplaySpeed: 7000,
                            });

                            $('.prev').click(function() {
                                $('#slider').slick('slickPrev');
                            });

                            $('.next').click(function() {
                                $('#slider').slick('slickNext');
                            });

                            // custom dots navigation
                            $('.slide-number').click(function() {
                                $('#slider').slick('slickGoTo', parseInt($(this).data('slide')) );
                                
                            });

                            // update custom dots on change
                            $('#slider').on('beforeChange', function(event, slick, currentSlide, nextSlide){
                            $('.slide-number').removeClass('active');
                            $('[data-slide="' + nextSlide + '"]').addClass('active');
                            });

                        </script>
                        <?php
                        break;
                }

            }

            break;
        
        default:
            break;
    }
}

// render redirect script if CMP is in redirect mode
if ( $this->cmp_mode() == 3 ) {
    $url = get_option('niteoCS_URL_redirect');
    $time = get_option('niteoCS_redirect_time'); ?>

    <script>
        setTimeout(function() {
          window.location.href = "<?php echo esc_url( $url );?>";
        }, <?php echo esc_attr( $time * 1000 );?>);
    </script>

    <?php
}

// check for CMP Subscribe shortcode to include CF7 captcha
$subscribe = get_option('niteoCS_subscribe_type');
$sub_cf7 = false;
if ( $subscribe == '1' ) {
    $subscribe_code = get_option('niteoCS_subscribe_code');
    if ( strpos($subscribe_code, 'contact-form-7')  !== false ) {
        $sub_cf7 = true;
    }
}

// include jquery and CF7 scripts for CF7 themes or themes using CF7 for subscribe form
if ( (in_array( $themeslug, $this->cmp_cf7_themes() ) && get_option('niteoCS_contact_form_type') == 'cf7') || $sub_cf7 === true ) {

    $site_url = str_replace( '/', '\/', site_url() );

    if ( !$this->jquery ) {
        echo '<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" Crossorigin="anonymous"></script>';
        $this->jquery = TRUE;
    }

    if ( class_exists('WPCF7') ) {
        $sitekeys = WPCF7::get_option( 'recaptcha');
        if ( $sitekeys ) {
            $sitekeys = array_keys( $sitekeys ); ?>
            <!-- CF7 Recaptcha script -->
            <script type='text/javascript' src='https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr($sitekeys[0]);?>&#038;ver=3.0'></script>
            <script>!function(e,t){var a=function(){e.execute("<?php echo esc_attr($sitekeys[0]);?>",{action:"homepage"}).then(function(e){for(var t=document.getElementsByTagName("form"),a=0;a<t.length;a++)for(var n=t[a].getElementsByTagName("input"),r=0;r<n.length;r++){var c=n[r];if("g-recaptcha-response"===c.getAttribute("name")){c.setAttribute("value",e);break}}})};e.ready(a),document.addEventListener("wpcf7submit",a,!1)}(grecaptcha);</script>
            <?php 
        } ?>
        
        <script type='text/javascript'>
        /* <![CDATA[ */
        var wpcf7 = {"api":{"root":"<?php echo $site_url;?>\/index.php?rest_route=\/","namespace":"contact-form-7\/v1"}};
        /* ]]> */
        </script>
        <script src='<?php echo plugins_url('contact-form-7/includes/js/index.js?ver=5.5.2');?>'></script>
        <?php
    }
}

// special effects scripts for premium themes
if ( in_array( $themeslug, $this->cmp_premium_themes_installed() ) )  { 

    $effect = get_option('niteoCS_special_effect', 'disabled');

    // change effect for preview 
    if ( isset($_GET['effect']) && is_numeric($_GET['effect'])) {
        $effect = $_GET['effect'];
    }

    // case numbers for preview

    switch ( $effect ) {
        case 'constellation':
        case 'snow':
        case '1':
        case '3': ?>
            <!-- load external Particles script -->
            <script src="//cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
            <!-- INI particles -->
            <script>
                <?php 

                switch ($themeslug ) {
                    case 'libra': 
                    case 'oxygen': 
                    case 'elementor': ?>
                        var background= 'body';
                        <?php 
                        break;
                    case 'divi': ?>
                        var background= 'main-content';
                        <?php 
                        break;
                    default: ?>
                        var wrapper=document.getElementById("background-wrapper");
                        var background=(null===wrapper)?"slider-wrapper":"background-wrapper";
                        <?php 
                        break;
                }

                switch ( $effect ) {
                    case 'constellation':
                    case '1': ?>
                        particlesJS(background,{particles:{number:{value:100,density:{enable:!0,value_area:1e3}},color:{value:"<?php echo esc_attr( get_option('niteoCS_special_effect[constellation][color]', '#ffffff') );?>"},shape:{type:"circle",stroke:{width:0,color:"#fff"},polygon:{nb_sides:5}},opacity:{value:.6,random:!1,anim:{enable:!1,speed:1,opacity_min:.1,sync:!1}},size:{value:2,random:!0,anim:{enable:!1,speed:40,size_min:.1,sync:!1}},line_linked:{enable:!0,distance:120,color:"<?php echo esc_attr( get_option('niteoCS_special_effect[constellation][color]', '#ffffff') );?>",opacity:.4,width:1}},interactivity:{detect_on:"canvas",events:{onhover:{enable:!0,mode:"grab"},onclick:{enable:!1},resize:!0},modes:{grab:{distance:140,line_linked:{opacity:1}},bubble:{distance:400,size:40,duration:2,opacity:8,speed:3},repulse:{distance:200,duration:.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:!0});
                        <?php
                        break;
                    case 'snow':
                    case '3':  ?>
                        particlesJS(background,{particles:{number:{value:52,density:{enable:!0,value_area:631.3280775270874}},color:{value:"<?php echo esc_attr( get_option('niteoCS_special_effect[constellation][color]', '#ffffff') );?>"},shape:{type:"circle",stroke:{width:0,color:"#fff"},polygon:{nb_sides:5},image:{src:"img/github.svg",width:100,height:100}},opacity:{value:.5,random:!0,anim:{enable:!1,speed:1,opacity_min:.1,sync:!1}},size:{value:5,random:!0,anim:{enable:!1,speed:40,size_min:.1,sync:!1}},line_linked:{enable:!1,distance:500,color:"#ffffff",opacity:.4,width:2},move:{enable:!0,speed:1.5,direction:"bottom",random:!1,straight:!1,out_mode:"out",bounce:!1,attract:{enable:!1,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:!1,mode:"bubble"},onclick:{enable:!0,mode:"repulse"},resize:!0},modes:{grab:{distance:400,line_linked:{opacity:.5}},bubble:{distance:400,size:4,duration:.3,opacity:1,speed:3},repulse:{distance:200,duration:.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:!0});
                        <?php
                        break;
                    default:
                        break;
                } ?>
                
            </script>
            <?php 
            break;

        case 'bubbles':
        case '2':
            if ( !$this->jquery ) {
                echo '<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" Crossorigin="anonymous"></script>';
                $this->jquery = TRUE;
            } ?>
            <!-- INI bubbles -->
            <script>
                <?php 
                switch ($themeslug ) {
                    case 'libra': 
                    case 'oxygen': 
                    case 'elementor': ?>
                        var $wrapper = $('#body');
                        <?php 
                        break;
                    case 'divi':?>
                        var $wrapper = $('main-content');
                        <?php 
                        break;
                    default: ?>
                        var $wrapper=$("#background-wrapper").length?$("#background-wrapper"):$("#slider-wrapper");
                        <?php 
                        break;
                } ?>
                $wrapper.append("<canvas class='particles-js-canvas-el'></canvas><canvas class='particles-js-canvas-el'></canvas><canvas class='particles-js-canvas-el'></canvas>"),function(a){var e=$wrapper.children("canvas"),n=e[0],r=e[1],i=e[2],o={circle:{amount:18,layer:3,color:[<?php echo $this->hex2rgba( get_option('niteoCS_special_effect[constellation][color]', '#ffffff') );?>],alpha:.3},line:{amount:12,layer:3,color:[<?php echo $this->hex2rgba( get_option('niteoCS_special_effect[constellation][color]', '#ffffff') );?>],alpha:.3},speed:.5,angle:20};if(n.getContext){n.getContext("2d");var t,l,c,d=r.getContext("2d"),m=i.getContext("2d"),w=window.Math,s=o.angle/360*w.PI*2,p=[],u=[];requestAnimationFrame=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||window.msRequestAnimationFrame||window.oRequestAnimationFrame||function(a,e){setTimeout(a,1e3/60)},cancelAnimationFrame=window.cancelAnimationFrame||window.mozCancelAnimationFrame||window.webkitCancelAnimationFrame||window.msCancelAnimationFrame||window.oCancelAnimationFrame||clearTimeout;var h=function(){t=a(window).width(),l=a(window).height(),e.each(function(){this.width=t,this.height=l})},v=function(){var a,e,n,r,i,h,f,y,g,A,F,C,b,x,q=w.sin(s),R=w.cos(s);if(o.circle.amount>0&&o.circle.layer>0){d.clearRect(0,0,t,l);for(var k=0,S=p.length;k<S;k++){var $=(I=p[k]).x,P=I.y,T=I.radius,z=I.speed;$>t+T?$=-T:$<-T?$=t+T:$+=q*z,P>l+T?P=-T:P<-T?P=l+T:P-=R*z,I.x=$,I.y=P,a=$,e=P,n=T,r=I.color,i=I.alpha,h=void 0,(h=d.createRadialGradient(a,e,n,a,e,0)).addColorStop(0,"rgba("+r[0]+","+r[1]+","+r[2]+","+i+")"),h.addColorStop(1,"rgba("+r[0]+","+r[1]+","+r[2]+","+(i-.1)+")"),d.beginPath(),d.arc(a,e,n,0,2*w.PI,!0),d.fillStyle=h,d.fill()}}if(o.line.amount>0&&o.line.layer>0){m.clearRect(0,0,t,l);var G=0;for(S=u.length;G<S;G++){$=(I=u[G]).x,P=I.y;var I,j=I.width;z=I.speed;$>t+j*q?$=-j*q:$<-j*q?$=t+j*q:$+=q*z,P>l+j*R?P=-j*R:P<-j*R?P=l+j*R:P-=R*z,I.x=$,I.y=P,f=$,y=P,g=j,A=I.color,F=I.alpha,void 0,void 0,x=void 0,C=f+w.sin(s)*g,b=y-w.cos(s)*g,(x=m.createLinearGradient(f,y,C,b)).addColorStop(0,"rgba("+A[0]+","+A[1]+","+A[2]+","+F+")"),x.addColorStop(1,"rgba("+A[0]+","+A[1]+","+A[2]+","+(F-.1)+")"),m.beginPath(),m.moveTo(f,y),m.lineTo(C,b),m.lineWidth=3,m.lineCap="round",m.strokeStyle=x,m.stroke()}}c=requestAnimationFrame(v)},f=function(){if(p=[],u=[],o.circle.amount>0&&o.circle.layer>0)for(var a=0;a<o.circle.amount/o.circle.layer;a++)for(var e=0;e<o.circle.layer;e++)p.push({x:w.random()*t,y:w.random()*l,radius:w.random()*(20+5*e)+(20+5*e),color:o.circle.color,alpha:.2*w.random()+(o.circle.alpha-.1*e),speed:o.speed*(1+.5*e)});if(o.line.amount>0&&o.line.layer>0)for(var n=0;n<o.line.amount/o.line.layer;n++)for(var r=0;r<o.line.layer;r++)u.push({x:w.random()*t,y:w.random()*l,width:w.random()*(20+5*r)+(20+5*r),color:o.line.color,alpha:.2*w.random()+(o.line.alpha-.1*r),speed:o.speed*(1+.5*r)});cancelAnimationFrame(c),c=requestAnimationFrame(v)};a(document).ready(function(){h(),f()}),a(window).resize(function(){h(),f()})}}(jQuery);
            </script>
            <?php 
            break;
        
        default:
            break;
    }
} 

// check if content includes iframe, and if yes, include full-width video resize script
$content = $this->cmp_get_body();

if ( strpos( $content, 'iframe' ) !== false ) { ?>
    <!-- Script for full-width size of embedded UT and VIMEO Iframes -->
    <script>
        (function ( window, document, undefined ) {
          var iframes = document.getElementsByTagName( 'iframe' );
          for ( var i = 0; i < iframes.length; i++ ) {
            var iframe = iframes[i],
            players = /www.youtube.com|player.vimeo.com/;
            if ( iframe.src.search( players ) > 0 ) {
              var videoRatio        = ( iframe.height / iframe.width ) * 100;
              iframe.style.position = 'absolute';
              iframe.style.top      = '0';
              iframe.style.left     = '0';
              iframe.width          = '100%';
              iframe.height         = '100%';
              var wrap              = document.createElement( 'div' );
              wrap.className        = 'fluid-vids';
              wrap.style.width      = '100%';
              wrap.style.position   = 'relative';
              wrap.style.paddingTop = videoRatio + '%';
              var iframeParent      = iframe.parentNode;
              iframeParent.classList.add('video-paragraph');
              iframeParent.insertBefore( wrap, iframe );
              wrap.appendChild( iframe );
            }
          }
        })( window, document );
    </script>
    <?php 
}



if ( isset( $theme_supports['counter_script']) && $theme_supports['counter_script'] === TRUE ) {
    $counter_script = true;
}

if ( $counter_script && get_option('niteoCS_counter', '1') == '1') {
    $countdown_action   = get_option('niteoCS_countdown_action', 'no-action');
    $days_counter = array('delta', 'mercury', 'libra', 'thor', 'headliner', 'mosaic', 'saturn');
    // counter script for day only
    if ( in_array($themeslug, $days_counter)  ) { ?>
        <script>
            var countdown = document.getElementById('counter');
            var count = parseInt(document.getElementById('counter-day').textContent);
            var ms = 5;
            var day = countdown.dataset.date;
            var cmp_nonce = '<?php echo wp_create_nonce( 'cmp-coming-soon-maintenance-nonce' ); ?>';
            var cmp_status = '<?php echo esc_attr(get_option('niteoCS_countdown_action', 'no-action'));?>';
            var counter = setTimeout(timer, 2000);
            function timer() {
                if ( count >= day ) {
                    document.getElementById('counter-day').innerHTML = count;

                    if ( count - day > 50 ) {
                        count = count - 3;
                        var counter = setTimeout(timer, ms);
                    } else if ( count - day > 10 ) {
                        count = count - 1;
                        var counter = setTimeout(timer, 20);
                    } else if ( count - day > 1 ){
                        count = count - 1;
                        var counter = setTimeout(timer, 100);
                    } else {
                        count = count - 1;
                        var counter = setTimeout(timer, 150);
                    }
                }

                <?php 
                if ( $countdown_action === 'redirect' || $countdown_action === 'disable-cmp') { ?>
                    if ( count == 0 ) {
                        fetch( '<?php echo esc_url(admin_url( 'admin-ajax.php' ));?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
                            },
                            body: `action=cmp_disable_comingsoon_ajax&security=${cmp_nonce}&status=${cmp_status}`,
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then((data) => {
                            if ( data.message === 'success' ) {
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            }
                        })
                        .catch(function(error) { console.log(error.message); });

                    }
                    <?php
                } ?>
            } 
        </script>
        <?php 

    // full counter script
    } else { ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() { 
            var counter = document.getElementById("counter") ? document.getElementById("counter") : document.getElementById("cmp-counter");
            var cmp_nonce = '<?php echo wp_create_nonce( 'cmp-coming-soon-maintenance-nonce' ); ?>';
            var cmp_status = '<?php echo esc_attr(get_option('niteoCS_countdown_action', 'no-action'));?>';

            if ( !counter ) {
                return;
            }
            var unixtime = counter.getAttribute("data-date");
            var date = new Date(unixtime*1000);
            var countDownDate = new Date(date).getTime();
        
            var dayElement = document.getElementById("counter-day");
            var hourElement = document.getElementById("counter-hour");
            var minuteElement = document.getElementById("counter-minute");
            var secondElement = document.getElementById("counter-second");
            
            // Update the count down every 1 second
            var timerInt = setInterval(function() {
                // Get today date
                var now = new Date().getTime();
                
                // Find the distance between now an the count down date
                var distance = countDownDate - now;
                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                if (days < 10) {
                    days = "0" + days;
                }
                if (hours < 10) {
                    hours = "0" + hours;
                }
                if (minutes < 10) {
                    minutes = "0" + minutes;
                }
                if (seconds < 10) {
                    seconds = "0" + seconds;
                }
                if (distance >= 0) {
                    dayElement ? dayElement.innerHTML = days : null;
                        hourElement ? hourElement.innerHTML = hours : null;
                        minuteElement ? minuteElement.innerHTML = minutes : null;
                        secondElement ? secondElement.innerHTML = seconds : null;  
                }

                <?php 
                if ( $countdown_action === 'redirect' || $countdown_action === 'disable-cmp') { ?>

                    if ( distance < 0 ) {
                        fetch( '<?php echo esc_url(admin_url( 'admin-ajax.php' ));?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
                            },
                            body: `action=cmp_disable_comingsoon_ajax&security=${cmp_nonce}&status=${cmp_status}`,
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then((data) => {
                            if ( data.message === 'success' ) {
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            }
                        })
                        .catch(function(error) { console.log(error.message); });

                        clearInterval(timerInt);

                    }
                    <?php
                } 

                if ( $countdown_action === 'hide' ) { ?>

                    if ( distance < 0 ) {
                        counter.classList.add('expired');
                        clearInterval(timerInt);
                    } <?php
                } ?>
                
            }, 1000);

            
        });
    </script>
    <?php 
    }
} 

// Subscribe form script
if ( get_option('niteoCS_subscribe_type', '2') == '2' && ( get_option('niteoCS_inpage_subscribe', '1') || get_option('niteoCS_subscribe_popup', '0')) ) { 

    $site_key = get_option('niteoCS_recaptcha_site', '');
    if ( get_option( 'niteoCS_recaptcha_status', '1' ) === '1' && !empty($site_key)) {
        echo '<script src="https://www.google.com/recaptcha/api.js?render='.esc_attr($site_key).'" async defer></script>';
    }
    
    //  get translation
    $translation = $this->cmp_wpml_niteoCS_translation();
    $missing_gdpr = stripslashes( $translation[13]['translation'] );
    $empty_email = stripslashes( $translation[14]['translation'] );
    ?>

    <script>
        /* Subscribe form script */
        var ajaxWpUrl = '<?php echo admin_url( 'admin-ajax.php' );?>';
        var {pathname} = new URL(ajaxWpUrl);
        var ajaxurl = `${location.protocol}//${location.hostname}${pathname}`;
        var security = '<?php echo wp_create_nonce( 'cmp-subscribe-action' );?>';
        var msg = '';
        subForm = function( form, resultElement, emailInput, firstnameInput, lastnameInput, token = '' ) {
            if ( emailInput.value !== '' ) {
                const firstname = firstnameInput === null ? '' : firstnameInput.value;
                const lastname = lastnameInput === null ? '' : lastnameInput.value;

                fetch( ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
                        'Access-Control-Allow-Origin': '*',
                    },
                    body: `action=niteo_subscribe&ajax=true&form_honeypot=&email=${ encodeURIComponent(emailInput.value) }&firstname=${ encodeURIComponent(firstname) }&lastname=${ encodeURIComponent(lastname) }&security=${ security }&token=${ token }`,
                    credentials: 'same-origin'
                } )
                .then( (res) => {
                    return res.json();
                } )
                .then( (data) => {
                    resultElement.innerHTML = data.message; // Display the result inside result element.
                    form.classList.add('-subscribed');
                    if (data.status == 1) {
                        form.classList.remove('-subscribe-failed');
                        form.classList.add('-subscribe-successful');
                        emailInput.value = '';
                        firstnameInput ? firstnameInput.value = '' : null;
                        lastnameInput ? lastnameInput.value = '' : null;
                        <?php do_action('cmp-successfull-subscribe-action'); ?>

                    } else {
                        form.classList.add('-subscribe-failed');
                    }
                } )
                .catch(function(error) { console.log(error.message); });

            } else {
                resultElement.innerHTML = '<?php echo esc_attr($empty_email);?>';
            }
        }
    </script>
<?php
}

$this->cmp_custom_footer_scripts();

do_action('cmp-after-footer-scripts');

$this->cmp_wp_footer();

// render nt&cmp notes
echo $this->cmp_render_nt_info();