<?php

defined( 'ABSPATH' ) || exit;

class WPRMenu_Framework_Interface {

  /**
  * Generates the tabs that are used in the options menu
  */
  static function wpr_optionsframework_tabs() {
    $counter = 0;
    $options = & WPRMenu_Framework::_wpr_optionsframework_options();
    $menu = '';

    foreach ( $options as $value ) {
            
      // Heading for Navigation
      if ( $value['type'] == "heading" ) {
        $counter++;
        $class = '';
        $helper_class = 'helper';
        $helper = isset($value['helper']) ? esc_html( $value['helper'] ) : '';
        $class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
        $class = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower($class) ) . '-tab';
        $menu .= '<a id="options-group-'.  $counter . '-tab" class="nav-tab ' . $class .'" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#options-group-'.  $counter ) . '">' . esc_html( $value['name'] ) . '<span class="'.$helper_class.'">' . $helper . '</span></a>';
      }
    }

    return $menu;
  }

  /**
  * Generates the options fields that are used in the form.
  */
  static function wpr_optionsframework_fields() {
    global $allowedtags;
    $wpr_optionsframework_settings = get_option( 'wpr_optionsframework' );

    // Gets the unique option id
    if ( isset( $wpr_optionsframework_settings['id'] ) ) {
      $option_name = $wpr_optionsframework_settings['id'];
    }
    else {
      $option_name = 'wpr_optionsframework';
    };

    $settings = get_option($option_name);
    $options = & WPRMenu_Framework::_wpr_optionsframework_options();

    $counter = 0;
    $menu = '';

    foreach ( $options as $value ) {

      $val = '';
      $select_value = '';
      $output = '';

      // Wrap all options
      if ( ( $value['type'] != "heading" ) 
        && ( $value['type'] != "info" ) ) {

        // Keep all ids lowercase with no spaces
        $value['id'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['id']) );

        $id = 'section-' . $value['id'];

        $class = 'section';
                
        if ( isset( $value['type'] ) ) {
          $class .= ' section-' . $value['type'];
        }
                
        if ( isset( $value['class'] ) ) {
          $class .= ' ' . $value['class'];
        }

        // If there is a description save it for labels
        $explain_value = '';
                
        if ( isset( $value['desc'] ) ) {
          $explain_value = $value['desc'];
        }

        $output .= '<div id="' . esc_attr( $id ) .'" class="row ' . esc_attr( $class ) . '">'."\n";
                
        if ( isset( $value['name'] ) ) {
          $output .= '<div class="col-md-5 heading"><span class="label-wrapper pull-left"><label class="explain" for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['name'] ) . '</label></span>';
          
          if( $explain_value != '' )
            $output .= '<span class="dashicons dashicons-info pull-right" data-toggle="tooltip" data-placement="top" title="'. wp_kses( $explain_value, $allowedtags) .'"></span>';
                    $output .= '</div>' . "\n";
        }
                
        if ( $value['type'] != 'editor' ) {
          $output .= '<div class="col-md-7 option">' . "\n" . '<div class="controls">' . "\n";
        }
        
        else {
          $output .= '<div class="option">' . "\n" . '<div>' . "\n";
        }
      }

      // Set default value to $val
      if ( isset( $value['std'] ) ) {
        $val = $value['std'];
      }

      // If the option is already saved, override $val
      if ( ( $value['type'] != 'heading' ) && ( $value['type'] != 'info') ) {
        if ( isset( $settings[($value['id'])]) ) {
          $val = $settings[($value['id'])];
          // Striping slashes of non-array options
          if ( !is_array($val) ) {
            $val = stripslashes( $val );
          }
        }
      }

      // Set the placeholder if one exists
      $placeholder = '';
      if ( isset( $value['placeholder'] ) ) {
        $placeholder = ' placeholder="' . esc_attr( $value['placeholder'] ) . '"';
      }

      if ( has_filter( 'wpr_optionsframework_' . $value['type'] ) ) {
        $output .= apply_filters( 'wpr_optionsframework_' . $value['type'], $option_name, $value, $val );
      }


      switch ( $value['type'] ) {

      // Code
      case 'code':
        $output .= '<div id="' . esc_attr( $value['id'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '"></div>';
        $output .= '<textarea name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '_textarea" style="display: none">'.esc_attr( $val ).'</textarea>';
        break;

      case 'text' :
        $suffix = '';
        if( isset( $value['suffix'] ) && $value['suffix'] == 'px' ) {
          $suffix = '<span class="wprmenu-suffix">px</span>';
        }
        $output .= '<input id="' . esc_attr( $value['id'] ) . '" class="form-control enable-suffix" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '"' . $placeholder . ' />' . $suffix ;
        break;

      // Password input
      case 'password':
        $output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="password" value="' . esc_attr( $val ) . '" />';
        break;

      // Textarea
      case 'textarea':
        $rows = '8';

        if ( isset( $value['settings']['rows'] ) ) {
          $custom_rows = $value['settings']['rows'];
          
          if ( is_numeric( $custom_rows ) ) {
            $rows = $custom_rows;
          }
        }

        $val = stripslashes( $val );
        $output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '"' . $placeholder . '>' . esc_textarea( $val ) . '</textarea>';
        break;

      // Select Box
      case 'select':
        $output .= '<select class="form-control" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '">';

        foreach ($value['options'] as $key => $option ) {
          $output .= '<option'. selected( $val, $key, false ) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
        }
        $output .= '</select>';
        break;


      // Radio Box
      case "radio":
        $name = $option_name .'['. $value['id'] .']';
        $output .= '<div class="btn-group" data-toggle="buttons">';
        
        foreach ($value['options'] as $key => $option) {
          $id = $option_name . '-' . $value['id'] .'-'. $key;
          $active = '';
      
          if( $val == $key ) $active = 'active';
            $output .= '<label class="radio-btn btn btn-default '. $active .'" for="' . esc_attr( $id ) . '"><input class="of-input of-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .' autocomplete="off" />' . esc_html( $option ) . '</label>';
        }
        $output .= '</div>';
        break;

      // Image Selectors
      case "images":
        $name = $option_name .'['. $value['id'] .']';
                
        foreach ( $value['options'] as $key => $option ) {
          $selected = '';
          
          if ( $val != '' && ($val == $key) ) {
            $selected = ' of-radio-img-selected';
          }
          
          $output .= '<input type="radio" id="' . esc_attr( $value['id'] .'_'. $key) . '" class="of-radio-img-radio" value="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" '. checked( $val, $key, false ) .' />';
          $output .= '<div class="of-radio-img-label">' . esc_html( $key ) . '</div>';
          $output .= '<img src="' . esc_url( $option ) . '" alt="' . $option .'" class="of-radio-img-img' . $selected .'" onclick="document.getElementById(\''. esc_attr($value['id'] .'_'. $key) .'\').checked=true;" />';
        }
        break;

      case "hidemenupages":
        $cats = $val;
        
        $output .= '<select class="wprmenu-hide-menu-pages of-input" multiple="multiple" name="' . esc_attr( $option_name . '[' . $value['id'] . '][]' ) . '" id="' . esc_attr( $value['id'] ) . '">';
        
        foreach ($value['options'] as $key => $option ) {
          if( is_array( $cats) && !empty( $cats ) && isset($cats[$key] ) && $cats[$key] === $option ) {
            $output .= '<option value="' . esc_attr( $key ) . '" selected="selected">' . esc_html( $option ) . '</option>';
          }
          else {
            $output .= '<option  value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
          }
        }
        $output .= '</select>';
        break;

      // Checkbox
      case "checkbox":
        $output .= '<div id="' . $option_name . '_' . $value['id'] . '" class="wprmenu_checkbox_container">';
        $output .= '<div class="wprmenu_onoff_button"></div>';
        $output .=  '<input id="' . esc_attr( $value['id'] ) . '" class="checkbox of-input" style="display:none;" type="checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" '. checked( $val, 1, false) .' />';
        $output .= '</div>';
        break;

      // Multicheck
      case "multicheck":
      foreach ($value['options'] as $key => $option) {
        $checked = '';
        $label = $option;
        $option = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($key));

        $id = $option_name . '-' . $value['id'] . '-'. $option;
        $name = $option_name . '[' . $value['id'] . '][' . $option .']';

        if ( isset($val[$option]) ) {
          $checked = checked($val[$option], 1, false);
        }

        $output .= '<input id="' . esc_attr( $id ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $name ) . '" ' . $checked . ' /><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
      }
      
        break;

    // Color picker
    case "color":
      $default_color = '';

      $class = sprintf( ' %s-form-color', 'wprmenu' );
      
      if ( isset($value['std']) ) {
        if ( $val !=  $value['std'] )
          $default_color = ' data-default-color="' .$value['std'] . '" ';
      }
    
      $output .= '<div class="wprmenu-color-picker">';
      $output .= '<div class="input-group tinvwl-no-full">';
      $output .= '<input type="text" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '" value="' . esc_attr( $val ) . '"' . $default_color .' id="' . esc_attr( $value['id'] ) . '"  class="form-control form-color '.$class.' " style="">';
      $output .= '<div class="input-group-btn">';
      $output .= '<div class="eyedropper">';
      $output .= '<a href="javascript:void(0);"><i class="eyedropper-icon"></i></a>';
      $output .= '</div></div></div></div>';

      break;

    // Uploader
    case "upload":
      $output .= WPRMenu_Framework_Media_Uploader::wpr_optionsframework_uploader( $value['id'], $val, null );

      break;
            
    //Number field
    case "number":
      $output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="number" value="' . esc_attr( $val ) . '"' . $placeholder . ' />';
      break;

    //Icon field
    case "icon":
      $output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input wpr-icon-picker" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '"' . $placeholder . ' />';
      break;

    //Sort Field
    case "menusort":
      $order = empty($val) ? 'Menu,Search,Social' : $val;
      $output .= '<input type="hidden" id="' . esc_attr( $value['id'] ) . '" class="of-input" name="'.esc_attr( $option_name . '[order_menu_items]' ).'"  value="'.$val.'"><ul id="wpr-sortable" class="list-group">';
      
      foreach( explode(',', $order) as $item ) {
        if( $item == 'Social' )
          $class = 'facebook';
        else
          $class = $item;
        
        $output .= '<li class="list-group-item list-group-item-primary" id="'.$item.'"><i class="wpr-icon-'.strtolower($class).'"></i>'.$item.'</li>';
      }
      
      $output .= '</ul>';
      break;

    //Sort Header Menu Elements
    case "headermenusort" :
      $initial_menu = 'Logo,Menu,Search,Cart,Widget Menu';
      $initial_menu_arr = explode(',', $initial_menu);

      $order = empty($val) ? 'Logo,Menu,Search,Cart,Widget Menu' : $val;

      $output .= '<input type="hidden" id="'.esc_attr( $value['id']).'" class="of-input" name="'.esc_attr( $option_name . '[order_header_menu_items]').'" value="'.$val.'" >';
      $output .= '<ul id="wpr-header-sortable" class="wpr-header-menu-elements list-group">';

      foreach( explode(',', $order) as $key => $item ) {
        $order_names = strtolower($item);
        $order_names = str_replace(' ', '-', $order_names);
        $class = $item;
        $item_name = str_replace('-', ' ', $item);
        $item_name = ucwords($item_name);

        if( in_array($item_name, $initial_menu_arr) ) {
            $output .= '<li class="list-group-item list-group-item-primary" data-id="'.$order_names.'" id="'.$order_names.'">'.$item_name.'<i class="wpr-icon-eye"></i></li>';
        }  
      }
      $output .= '</ul>';
        break;

    //get menus for wpml
    case "wpmlmenu":
       
      $langs_data = array();     
      if (function_exists('icl_get_languages')) {
        //get list of used languages from WPML
        $get_langs = icl_get_languages('skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str');
        //Set current language for language based variables in theme.
        $i = 0;
        foreach( $get_langs as $key => $langs ) {
          $langs_data[$i]['code'] = $langs['code'];
          $langs_data[$i]['name'] = $langs['native_name'];
          $i++;
        }
      }

      $output .='<div class="wpr-new-field wpml-menu">';
      $output .= '<input type="button" class="btn btn-success wpml-new" value="Add New">';
      $output .= '</div>';
      $output .= '<div class="wpr-menu-fields">';

      $output .= '<div class="wpr-new-fields">';
      
      if( is_array($langs_data) && !empty($langs_data) ) {
        $output .= '<div class="menu-lang-wrap">';
        $output .= '<select name="' . esc_attr( $option_name . '[' . $value['id'] . '][lang][]' ) . '" class="wprmenu-langs-list of-input">';
        
        foreach( $langs_data as $key => $val ) {
          $output .= '<option value="'.$val['code'].'">'.$val['name'].'</option>';
        }
        $output .= '</select>';
        $output .= '</div>';
      }

      $output .= '<div class="menu-name-wrap">';
      
      $menus = get_terms( 'nav_menu',array( 'hide_empty'=>false ) );
      $menu = array();

      if( is_array($menus) && !empty($menus) ) {
        foreach( $menus as $m ) {
          $menu[$m->term_id] = $m->name;
        }
      }
      
      if( is_array($menu) && !empty($menu) ) {
        $output .= '<select name="' . esc_attr( $option_name . '[' . $value['id'] . '][menu][]' ) . '" class="wprmenu-langs-list of-input">';
        foreach( $menu as $menu_name ) {
          $output .= '<option>'.$menu_name.'</option>';
        }
        $output .= '</select>';
      }
      $output .= '</div>';
                        
                        
      $output .= '<input type="button" class="btn btn-danger pull-right" value="Remove" /></div>';

      $output .= '</div>';
      break;
            
    //Repeater field for social icons
    case "social" :
      $socials = json_decode( $val );
      $output .='<div class="wpr-new-field"><input type="button" class="wpr-add-new btn btn-success" value="Add New"></div><div class="wpr-social-fields">';
                
      if( is_array( $socials ) && !empty( $socials ) ) {
        foreach( $socials as $social ) {
          $output .= '<div class="wpr-new-fields"><input type="text" name="' . esc_attr( $option_name . '[' . $value['id'] . '][icon][]' ) . '" class="wpr-icon-picker"  value="'.$social->icon.'">';
                        
          $output .= '<input type="text" name="' . esc_attr( $option_name . '[' . $value['id'] . '][link][]' ) . '" class="social_link form-control" value="'.$social->link.'"><input type="button" class="btn btn-danger pull-right" value="Remove" /><div class="clear"></div></div>';
        }
      }
      else {
        $output .= '<div class="wpr-new-fields"><input type="text" name="' . esc_attr( $option_name . '[' . $value['id'] . '][icon][]' ) . '" class="wpr-icon-picker"  value="">';
                        
        $output .= '<input type="text" placeholder="Enter your url here" name="' . esc_attr( $option_name . '[' . $value['id'] . '][link][]' ) . '" class="' . esc_attr( $value['id'] . '_link form-control' ) . '"  value=""><input type="button" class="btn btn-danger pull-right" value="Remove" /><div class="clear"></div></div>';
      }
      $output .= '</div>';
        break;

      // Showcase and demo import
      case "showcase":
        $output .= '<div class="wprmenu-showcase-wrapper">';
        //$output .= WPRMenu_Framework_Interface::wprmenu_get_demodata();
        $output .= '</div>';
          break;

      // Background
      case 'background':

        $background = $val;

        // Background Color
        $default_color = '';
        
        if ( isset( $value['std']['color'] ) ) {
          if ( $val !=  $value['std']['color'] )
            $default_color = ' data-default-color="' .$value['std']['color'] . '" ';
          }
        
        $output .= '<input name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" class="of-color of-background-color"  type="text" value="' . esc_attr( $background['color'] ) . '"' . $default_color .' />';

        // Background Image
        if ( !isset($background['image']) ) {
          $background['image'] = '';
        }

        $output .= WPRMenu_Framework_Media_Uploader::wpr_optionsframework_uploader( $value['id'], $background['image'], null, esc_attr( $option_name . '[' . $value['id'] . '][image]' ) );

        $class = 'of-background-properties';
          
        if ( '' == $background['image'] ) {
          $class .= ' hide';
        }
        $output .= '<div class="' . esc_attr( $class ) . '">';

        // Background Repeat
        $output .= '<select class="of-background of-background-repeat" name="' . esc_attr( $option_name . '[' . $value['id'] . '][repeat]'  ) . '" id="' . esc_attr( $value['id'] . '_repeat' ) . '">';
        $repeats = wpr_of_recognized_background_repeat();

        foreach ($repeats as $key => $repeat) {
          $output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['repeat'], $key, false ) . '>'. esc_html( $repeat ) . '</option>';
        }
        $output .= '</select>';

        // Background Position
        $output .= '<select class="of-background of-background-position" name="' . esc_attr( $option_name . '[' . $value['id'] . '][position]' ) . '" id="' . esc_attr( $value['id'] . '_position' ) . '">';
        $positions = wpr_of_recognized_background_position();

        foreach ($positions as $key=>$position) {
          $output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['position'], $key, false ) . '>'. esc_html( $position ) . '</option>';
        }
        $output .= '</select>';

        // Background Attachment
        $output .= '<select class="of-background of-background-attachment" name="' . esc_attr( $option_name . '[' . $value['id'] . '][attachment]' ) . '" id="' . esc_attr( $value['id'] . '_attachment' ) . '">';
        $attachments = wpr_of_recognized_background_attachment();

        if( is_array($attachments) && !empty($attachments) ) {
          foreach ($attachments as $key => $attachment) {
            $output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['attachment'], $key, false ) . '>' . esc_html( $attachment ) . '</option>';
          }
        }
        
        $output .= '</select>';
        $output .= '</div>';

        break;

      // Editor
      case 'editor':

        echo $output;
        $textarea_name = esc_attr( $option_name . '[' . $value['id'] . ']' );
        $default_editor_settings = array(
          'textarea_name' => $textarea_name,
          'media_buttons' => false,
          'tinymce' => array( 'plugins' => 'wordpress' )
        );
        $editor_settings = array();
        if ( isset( $value['settings'] ) ) {
          $editor_settings = $value['settings'];
        }
        $editor_settings = array_merge( $default_editor_settings, $editor_settings );
        wp_editor( $val, $value['id'], $editor_settings );
        $output = '';
        break;

      // Info
      case "info":

        $id = '';
        $class = 'section';
        if ( isset( $value['id'] ) ) {
            $id = 'id="' . esc_attr( $value['id'] ) . '" ';
        }
        if ( isset( $value['type'] ) ) {
            $class .= ' section-' . $value['type'];
        }
        if ( isset( $value['class'] ) ) {
            $class .= ' ' . $value['class'];
        }

        $output .= '<div ' . $id . 'class="' . esc_attr( $class ) . '">' . "\n";
        if ( isset($value['name']) ) {
            $output .= '<h4 class="heading">' . esc_html( $value['name'] ) . '</h4>' . "\n";
        }
        if ( isset( $value['desc'] ) ) {
            $output .= apply_filters('wpr_of_sanitize_info', $value['desc'] ) . "\n";
        }
        $output .= '</div>' . "\n";
          break;

      // Heading for Navigation
      case "heading":
        
        $counter++;
        
        if ( $counter >= 2 ) {
          $output .= '</div>'."\n";
        }
        $class = '';
        $class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
        $class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
        $output .= '<div id="options-group-' . $counter . '" class="group ' . $class . '">';
        $output .= '<div class="wpr-options-heading"><h3>' . esc_html( $value['name'] ) . '</h3></div>' . "\n";
        break;
      }

      if ( ( $value['type'] != "heading" ) 
        && ( $value['type'] != "info" ) ) {
        $output .= '</div>';
                
        $output .= '</div></div>'."\n";
      }

      echo $output;
    }

    // Outputs closing div if there tabs
    if ( WPRMenu_Framework_Interface::wpr_optionsframework_tabs() != '' ) {
      echo '</div>';
    }
  }

  static function wprmenu_get_demodata($demo_type) {

    if( empty($demo_type) )
      return;

    $items = get_transient('wprm_api_demo_items_list');

    if( ! $items )
      $items = WPRMenu_Framework_Interface::wprm_fetch_demo_items();

    $data = '';
    $class = '';

    if( is_object($items) ) {


      if( isset($items->$demo_type) && !empty($items->$demo_type) ) {
        $data .= '<h3 class="wpr-demo-type">'.$demo_type.' '.__(' Version Demo', 'wprmenu').'</h3>';
        $data .= '<ul class="wprmenu-demo-list">';
        
        foreach( $items->$demo_type as $key => $val ) {
          $demo_type = $val->demo_type;

          $settings_path = $val->settings;
          $import_class = '';
          $import_demo_text = __('Import Demo', 'wprmenu');

          if( $demo_type == 'Pro' ) {
            $settings_path = '';
            $import_class = 'required-pro';
            $import_demo_text = __('Available In Pro', 'wprmenu');
          }

          $image_path = $val->image_path;
          $data .= '<li class="wprmenu-data-list">';
          $data .= '<div class="wprmenu-content image-overlay" >';
          $data .= '<div data-demo-type="'.$val->demo_type.'" data-demo-id="'.$val->demo_id.'"  data-settings="'.$settings_path.'" class="wprmenu-content-image" style="background-image: url('.$image_path.'); "></div>';
          $data .= '<span class="view-demo"><a target="_blank" href="'.$val->demo_url.'">'.__('View Demo', 'wprmenu').'</a></span>';
          $data .= '<span class="wprmenu-data import-demo '.$import_class.'" data-id="">'.$import_demo_text.'</span>';
          $data .= '</div>';
          $data .= '</li>';
          }
          $data .= '</ul>';
        }
      }
        
    return $data;
  }

  static function wpr_render_floating_buttons() {
    ob_start();
      ?>
    <div class="wpr-floating-menus save-settings">
      <div class="wpr-menu-quick-save wpr-floating-button">
        <i class="wpr-quicksave-icon"></i>
          <input type="submit" class="wpr-quick-save-btn" name="update" value="<?php esc_attr_e( 'SAVE', 'wprmenu' ); ?>" />
      </div><!-- / wpr-menu-quick-save -->
      <div class="clear"></div>
    </div> <!-- / wpr-floating-menus-->
    <?php
      $output = ob_get_contents();
      ob_get_clean();
    echo $output;
  }

  static function wpr_render_form_button() {
    ob_start(); ?>
    <div class="save-settings-wrap">
      <input type="submit" class="button-primary wpr-button save-settings" name="update" value="<?php esc_attr_e( 'Save Settings', 'wprmenu' ); ?>" />
    </div>
              
    <div class="reset-settings-wrap">
      <input type="submit" class="button-secondary reset-settings" name="update" value="<?php esc_attr_e( 'Restore Defaults', 'wprmenu' ); ?>"  />
    </div>
    <?php
    $output = ob_get_clean();
    echo $output;
  }

  static function wprm_fetch_demo_items() {
    $site_name = WPRMENU_DEMO_SITE_URL;
    $remoteLink = $site_name.'/wp-json/wprmenu-server/v1';

    $data = wp_remote_get($remoteLink);
      
    $items = array();

    if( is_array($data) && isset($data['body']) ) {
      $items = $data['body'];
      $items = json_decode($items);
      set_transient('wprm_api_demo_items_list', $items, 60 * 60 * 24);    
    }
    return $items;
  }

}