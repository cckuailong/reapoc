<?php
/**
 * Render Style
 *
 * @package     WP Responsive Menu
 * @author      MagniGenie
 * @copyright   Copyright (c) 2019, WP Responsive Menu
 * @link        https://magnigenie.com/
 * @since       WP Responsive Menu 3.1.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders Style For WP Responsive Menu
 */
if ( ! class_exists('WPRMenu_Styles') ) {

  class WPRMenu_Styles {
  
    protected $options = '';

    /**
     * Constructor
     */
    public function __construct() {
      $this->wprmenu_options = get_option( 'wprmenu_options' );
    }

    public function wprmenu_option( $option ) {
      
     if ( isset($_COOKIE['wprmenu_live_preview']) 
        && $_COOKIE['wprmenu_live_preview'] == 'yes' ) {
        $check_transient = get_transient('wpr_live_settings');

        if ( $check_transient ) {
          if ( isset( $check_transient[$option] ) 
            && $check_transient[$option] != '' ) {
              return $check_transient[$option];
          }
        }
      }
      else {
        if ( isset( $this->wprmenu_options[$option] ) && $this->wprmenu_options[$option] != '' )
          return $this->wprmenu_options[$option];
      }
    }

    public function generate_style() {

      $inlinecss = '';

      if ( $this->wprmenu_option( 'enabled' ) ) :
        
        //Setup Variables
        $from_width = $this->wprmenu_option( 'from_width' ) !== '' ? $this->wprmenu_option( 'from_width' ) : '768';
        $how_wide = $this->wprmenu_option( 'how_wide' ) !== '' ? $this->wprmenu_option( 'how_wide' ) : '40';
        $menu_max_width = $this->wprmenu_option( 'menu_max_width' );
        $border_top_color = $this->hex2rgba( $this->wprmenu_option( 'menu_border_top' ), $this->wprmenu_option( 'menu_border_top_opacity' ) );
        $border_bottom_color = $this->hex2rgba( $this->wprmenu_option( 'menu_border_bottom' ), $this->wprmenu_option( 'menu_border_bottom_opacity' ) );
        $menu_title_font = $this->wprmenu_option( 'menu_title_size' ) !== '' ? $this->wprmenu_option('menu_title_size') : '20';
        $overlay_bg_color = $this->hex2rgba( $this->wprmenu_option( 'menu_bg_overlay_color' ), $this->wprmenu_option( 'menu_background_overlay_opacity' ) );
        $menu_padding = $this->wprmenu_option( 'header_menu_height' );
        $menu_padding = intval( $menu_padding );
        
        
        $inlinecss .= '@media only screen and ( max-width: '.$from_width.'px ) {';
        $inlinecss .= 'html body div.wprm-wrapper {
          overflow: scroll;
        }';

        //menu background image
        if ( $this->wprmenu_option('menu_bg') != '' ) :
          $inlinecss .= '#mg-wprm-wrap {
            background-image: url( '. $this->wprmenu_option( 'menu_bg' ) .');
            background-size: '. $this->wprmenu_option( 'menu_bg_size' ) .';
            background-repeat: '. $this->wprmenu_option( 'menu_bg_rep' ) .';
        }';
        endif;

        if ( $this->wprmenu_option( 'enable_overlay' ) == '1' ) :
          $inlinecss .= 'html body div.wprm-overlay{ background: ' .$overlay_bg_color .' }';
        endif;

        if ( $this->wprmenu_option( 'menu_border_bottom_show' ) == 'yes' ):
          $inlinecss .= '
          #mg-wprm-wrap ul li {
            border-top: solid 1px '.$border_top_color.';
            border-bottom: solid 1px '.$border_bottom_color.';
          }
          ';
        endif;

        if ( $this->wprmenu_option( 'menu_bar_bg' ) !== '' ) :
          $inlinecss .= '
          #wprmenu_bar {
            background-image: url( '.$this->wprmenu_option( 'menu_bar_bg' ).' );
            background-size: '.$this->wprmenu_option( 'menu_bar_bg_size' ).' ;
            background-repeat: '.$this->wprmenu_option( 'menu_bar_bg_rep' ).';
          }';
        endif;

        if ( $menu_title_font > 26 ) :
          $inlinecss .= '#wprmenu_bar .menu_title a{ top: 0; }';
        endif;
        
        $inlinecss .= '
        #wprmenu_bar {
          background-color: '.$this->wprmenu_option( 'bar_bgd' ).';
        }
        html body div#mg-wprm-wrap .wpr_submit .icon.icon-search {
          color: '.$this->wprmenu_option( 'search_icon_color' ).';
        }
        #wprmenu_bar .menu_title, #wprmenu_bar .wprmenu_icon_menu, #wprmenu_bar .menu_title a {
          color: '.$this->wprmenu_option( 'bar_color' ).';
        }
        #wprmenu_bar .menu_title {
          font-size: '.$menu_title_font.'px;
          font-weight: '.$this->wprmenu_option( 'menu_title_weight' ).';
        }
        #mg-wprm-wrap li.menu-item a {
          font-size: '.$this->wprmenu_option( 'menu_font_size' ).'px;
          text-transform: '.$this->wprmenu_option( 'menu_font_text_type' ).';
          font-weight: '.$this->wprmenu_option( 'menu_font_weight' ).';
        }
        #mg-wprm-wrap li.menu-item-has-children ul.sub-menu a {
          font-size: '.$this->wprmenu_option( 'sub_menu_font_size' ).'px;
          text-transform: '.$this->wprmenu_option( 'sub_menu_font_text_type' ).';
          font-weight: '.$this->wprmenu_option( 'sub_menu_font_weight' ).';
        }
        #mg-wprm-wrap li.current-menu-item > a {
          background: '.$this->wprmenu_option( 'active_menu_bg_color' ).';
        }
        #mg-wprm-wrap li.current-menu-item > a,
          #mg-wprm-wrap li.current-menu-item span.wprmenu_icon{
          color: '.$this->wprmenu_option( 'active_menu_color' ).' !important;
        }
        #mg-wprm-wrap {
          background-color: '.$this->wprmenu_option( 'menu_bgd' ).';
        }
        .cbp-spmenu-push-toright, .cbp-spmenu-push-toright .mm-slideout {
          left: '.$how_wide.'% ;
        }
        .cbp-spmenu-push-toleft {
          left: -'.$how_wide.'% ;
        }
        #mg-wprm-wrap.cbp-spmenu-right,
        #mg-wprm-wrap.cbp-spmenu-left,
        #mg-wprm-wrap.cbp-spmenu-right.custom,
        #mg-wprm-wrap.cbp-spmenu-left.custom,
        .cbp-spmenu-vertical {
          width: '.$how_wide.'%;
          max-width: '.$menu_max_width.'px;
        }
        #mg-wprm-wrap ul#wprmenu_menu_ul li.menu-item a,
        div#mg-wprm-wrap ul li span.wprmenu_icon {
          color: '.$this->wprmenu_option( 'menu_color' ).';
        }
        #mg-wprm-wrap ul#wprmenu_menu_ul li.menu-item:valid ~ a{
        color: '.$this->wprmenu_option( 'active_menu_color' ).';
        }
        #mg-wprm-wrap ul#wprmenu_menu_ul li.menu-item a:hover {
          background: '.$this->wprmenu_option( 'menu_textovrbgd' ).';
          color: '.$this->wprmenu_option( 'menu_color_hover' ).' !important;
        }
        div#mg-wprm-wrap ul>li:hover>span.wprmenu_icon {
          color: '.$this->wprmenu_option( 'menu_color_hover' ).' !important;
        }
        .wprmenu_bar .hamburger-inner, .wprmenu_bar .hamburger-inner::before, .wprmenu_bar .hamburger-inner::after {
          background: '.$this->wprmenu_option( 'menu_icon_color' ).';
        }
        .wprmenu_bar .hamburger:hover .hamburger-inner, .wprmenu_bar .hamburger:hover .hamburger-inner::before,
       .wprmenu_bar .hamburger:hover .hamburger-inner::after {
          background: '.$this->wprmenu_option( 'menu_icon_hover_color' ).';
        }
        ';

        if ( $this->wprmenu_option( 'menu_symbol_pos' ) == 'left' ) :
          $inlinecss .= 'div.wprmenu_bar div.hamburger{padding-right: 6px !important;}';
        endif;
        
        if ( $this->wprmenu_option( 'menu_border_bottom_show' ) == 'no' ):
          $inlinecss .= '
          #wprmenu_menu, #wprmenu_menu ul, #wprmenu_menu li, .wprmenu_no_border_bottom {
            border-bottom:none;
          }
          #wprmenu_menu.wprmenu_levels ul li ul {
            border-top:none;
          }
        ';
        endif;

        $inlinecss .= '
        #wprmenu_menu.left {
          width:'.$how_wide.'%;
          left: -'.$how_wide.'%;
          right: auto;
        }
        #wprmenu_menu.right {
          width:'.$how_wide.'%;
          right: -'.$how_wide.'%;
          left: auto;
        }';

        if ( $this->wprmenu_option( 'menu_symbol_pos' ) == 'right' ) :
          $inlinecss .= '
          .wprmenu_bar .hamburger {
            float: '.$this->wprmenu_option( 'menu_symbol_pos' ).';
          }
          .wprmenu_bar #custom_menu_icon.hamburger {
            top: '.$this->wprmenu_option( 'custom_menu_top' ).'px;
            right: '.$this->wprmenu_option( 'custom_menu_left' ).'px;
            float: right;
            background-color: '.$this->wprmenu_option( 'custom_menu_bg_color' ).';
          }';
        endif;

        if ( $this->wprmenu_option( 'menu_icon_type' ) == 'default' ) :

          if ( $menu_padding > 50 ) {
            $menu_padding = $menu_padding - 27;
            $menu_padding = $menu_padding / 2;
            $top_position = $menu_padding + 30;

            $inlinecss .= 'html body div#wprmenu_bar {
              padding-top: '.$menu_padding.'px;
              padding-bottom: '.$menu_padding.'px;
            }';

            if ( $this->wprmenu_option( 'menu_type' ) == 'default' ) {
              $inlinecss .= '.wprmenu_bar div.wpr_search form {
                top: '.$top_position.'px;
              }';
            }
          }
          
          $inlinecss .= 'html body div#wprmenu_bar {
            height : '.$this->wprmenu_option( 'header_menu_height' ).'px;
          }';
        endif;

        if ( $this->wprmenu_option( 'menu_type' ) == 'default'  ) :
          $inlinecss .= '#mg-wprm-wrap.cbp-spmenu-left, #mg-wprm-wrap.cbp-spmenu-right, #mg-widgetmenu-wrap.cbp-spmenu-widget-left, #mg-widgetmenu-wrap.cbp-spmenu-widget-right {
            top: '.$this->wprmenu_option( 'header_menu_height' ).'px !important;
          }';
        endif;

        if ( $this->wprmenu_option( 'menu_symbol_pos' ) == 'left' ) :
          $inlinecss .= '
          .wprmenu_bar .hamburger {
            float: '.$this->wprmenu_option( 'menu_symbol_pos' ).';
          }
          .wprmenu_bar #custom_menu_icon.hamburger {
            top: '.$this->wprmenu_option( 'custom_menu_top' ).'px;
            left: '.$this->wprmenu_option( 'custom_menu_left' ).'px;
            float: left !important;
            background-color: '.$this->wprmenu_option( 'custom_menu_bg_color' ).';
          }';
        endif;

        if ( $this->wprmenu_option( 'hide' ) != '' ):
          $inlinecss .= $this->wprmenu_option( 'hide' ).'{ display: none !important; }';
        endif;

        $inlinecss .= '.wpr_custom_menu #custom_menu_icon {
          display: block;
        }';

        if ( $this->wprmenu_option( 'menu_type' ) !== 'custom' ) : 
          $inlinecss .= 'html { padding-top: 42px !important; }';
        endif;

        $inlinecss .= '#wprmenu_bar, #mg-wprm-wrap { display: block; }
        div#wpadminbar { position: fixed; }';

        $inlinecss .= '}';
      endif;
    return $inlinecss;
    }

    /**
     * Convert hex2rgba color
     *
     * @since 3.1.4
     * @param string $color
     * @param string $opacity
     * @return string
     */
    function hex2rgba( $color, $opacity = false ) {
      $default = 'rgb(0,0,0)';
    
      //Return default if no color provided
      if ( empty($color) )
        return $default; 
 
      //Sanitize $color if "#" is provided 
      if ( $color[0] == '#' ) {
        $color = substr( $color, 1 );
      }
 
      //Check if color has 6 or 3 characters and get values
      if ( strlen($color) == 6 ) {
        $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
      } elseif ( strlen( $color ) == 3 ) {
        $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
      } else {
        return $default;
      }
 
      //Convert hexadec to rgb
      $rgb =  array_map( 'hexdec', $hex );

      //Check if opacity is set(rgba or rgb)
      if ( $opacity ) {
        if ( abs( $opacity ) > 1 )
          $opacity = 1.0;
        $output = 'rgba('.implode( ",",$rgb ).','.$opacity.')';
      } else {
        $output = 'rgb( '.implode(",",$rgb).' )';
      }

      //Return rgb(a) color string
      return $output;
    }

    /**
     * Trim CSS
     *
     * @since 3.1.4
     * @param string $css CSS content to trim.
     * @return string
     */
    public static function trim_css( $css = '' ) {

      // Trim white space for faster page loading.
      if ( ! empty( $css ) ) {
        $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
        $css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
        $css = str_replace( ', ', ',', $css );
      }

      return $css;
    }

  }

  new WPRMenu_Styles();
}