<?php
/*
|| --------------------------------------------------------------------------------------------
|| Theme/Plugin Metaboxes Fields
|| --------------------------------------------------------------------------------------------
||
|| @package		Dilaz Metaboxes
|| @subpackage	Main Options
|| @since		Dilaz Metaboxes 1.1
|| @author		WebDilaz Team, http://webdilaz.com
|| @copyright	Copyright (C) 2017, WebDilaz LTD
|| @link		http://webdilaz.com/metaboxes
|| @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
|| 
|| NOTE 1: Rename this file from "options-sample.php" to "options.php". If you
||         don't rename it, all your options and settings will be overwritten
||         when updating Dilaz Metaboxes.
|| 
|| NOTE 2: Add all your theme/plugin metabox options in this file
|| 
*/

defined('ABSPATH') || exit;

/**
 * Define the metaboxes and field configurations
 *
 * @param	array $dilaz_meta_boxes
 * @return	array
 */
//add_filter( 'dilaz_meta_boxes_filter', 'dilaz_meta_boxes' );
//function dilaz_meta_boxes( array $dilaz_meta_boxes ) {
	
	# BOX - Sample Set 1
	# =============================================================================================
	$dilaz_meta_boxes[] = array(
		'id'	   => $prefix .'layout_set',
		'title'	   => __('Futurio Extra', 'futurio-extra'),
		'pages'    => array('post', 'page'),
		'context'  => 'normal',
		'priority' => 'high',
		'type'     => 'metabox_set'
	);
		
		# TAB - Tab 1
		# *****************************************************************************************
		$dilaz_meta_boxes[] = array(
			'id'    => $prefix .'layout_tab',
			'title' => __('Layout', 'futurio-extra'),
			'icon'  => 'fa-cogs',
			'type'  => 'metabox_tab'
		);
			
			# FIELDS - Tab 1
			# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'content_layout',
				'name'    => __('Content layout', 'futurio-extra'),
				'type'    => 'select',
				'options' => array(
					'default' => esc_attr__( 'Default', 'futurio_extra' ),
          'fullwidth' => esc_attr__( 'Full Width', 'futurio_extra' ),
          'fullwidth_builders' => esc_attr__( 'Full Width & no gaps - Page Builders', 'futurio_extra' ),
				),
				'std'  => array('default'),
				'args' => array(
					'select2'      => 'select2single',
					'select2width' => '50%',
				),
			);
      $dilaz_meta_boxes[] = array(
  			'id'      => $prefix .'note',
  			'name'    => esc_attr__('&larr; To disable title & featured image, hit the &quot;Display&quot; tab', 'futurio-extra'),
        'type'  => 'paragraph',
        'value' => '',
        'req_args' => array(
  				$prefix .'content_layout' => 'fullwidth_builders'
  			),
        'req_action' => 'show',
  		);
      
			$dilaz_meta_boxes[] = array(
				'id'	  => $prefix .'sidebar',
				'name'	  => __('Sidebar', 'futurio-extra'),
				'type'	  => 'radioimage',
				'options' => array(
					'inherit' => FUTURIO_EXTRA_PLUGIN_URL .'lib/metabox/assets/images/layout-default.png',
					'right'    => FUTURIO_EXTRA_PLUGIN_URL .'lib/metabox/assets/images/layout-left.png',
					'left'   => FUTURIO_EXTRA_PLUGIN_URL .'lib/metabox/assets/images/layout-right.png',
					'no_sidebar'    => FUTURIO_EXTRA_PLUGIN_URL .'lib/metabox/assets/images/layout-full.png',
				),
				'std' => 'inherit'
			);
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'content_spacing_option',
				'name'    => __('Custom content spacing', 'futurio-extra'),
				'type'    => 'switch',
				'options' => array(
					'enable'  => __('Enable', 'futurio-extra'), 
					'disable' => __('Disable', 'futurio-extra'),
				),
				'std'  => 'disable',
				'class' => ''
			);
      $dilaz_meta_boxes[] = array(
  				'id'   => $prefix .'content_spacing',
  				'name' => __('Content spacing', 'futurio-extra'),
  				'type' => 'slider',
  				'args' => array(
  					'min'    => '0', 
  					'max'    => '50', 
  					'step'   => '1', 
  					'suffix' => '%'
  				),
  				'std'   => '0',
  				'class' => '',
          'req_args' => array(
  					$prefix .'content_spacing_option'  => 'enable',
  				),
  				'req_action' => 'show',
  			);
			
			
		# TAB - Tab 2
		# *****************************************************************************************
		$dilaz_meta_boxes[] = array(
			'id'    => $prefix .'samp_1_tab_2',
			'title' => __('Display', 'futurio-extra'),
			'icon'  => 'fa-eye',
			'type'  => 'metabox_tab'
		);
			
			# FIELDS - Tab 2
			# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'disable_title',
				'name'    => __('Title', 'futurio-extra'),
				'type'    => 'switch',
				'options' => array(
					'enable'  => __('Enable', 'futurio-extra'), 
					'disable' => __('Disable', 'futurio-extra'),
				),
				'std'  => 'enable',
				'class' => ''
			);
      if ( get_theme_mod( 'single_title_position', 'full' ) == 'full' ) {		
  			
  			 $dilaz_meta_boxes[] = array(
  				'id'      => $prefix .'subtitle',
  				'name'    => __('Subtitle', 'futurio-extra'),
  				'type'    => 'textarea',
  				'std'    => '',
  				'args'   => array('cols' => '90', 'rows' => '5'),
          'req_args' => array(
  					$prefix .'disable_title' => 'enable'
  				),
          'req_action' => 'show',
  			);
  	
    	}
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'disable_featured_image',
				'name'    => __('Featured image', 'futurio-extra'),
				'type'    => 'switch',
				'options' => array(
					'enable'  => __('Enable', 'futurio-extra'), 
					'disable' => __('Disable', 'futurio-extra'),
				),
				'std'  => 'enable',
				'class' => ''
			);
      if ( get_theme_mod( 'single_title_position', 'full' ) == 'full' || get_theme_mod( 'single_featured_image', 'full' ) == 'full' ) {	
        $dilaz_meta_boxes[] = array(
  				'id'   => $prefix .'featured_image_height',
  				'name' => __('Featured image/Title height', 'futurio-extra'),
  				'type' => 'slider',
  				'args' => array(
  					'min'    => '0', 
  					'max'    => '600', 
  					'step'   => '1', 
  					'suffix' => 'px'
  				),
  				'std'   => '',
  				'class' => '',
          'req_args' => array(
  					$prefix .'disable_title'  => 'enable',
  					$prefix .'disable_featured_image' => 'enable'
  				),
  				'req_cond'   => 'OR', 
  				'req_action' => 'show',
  			);
      }
      if ( in_array( 'wordpress-seo/wp-seo.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $dilaz_meta_boxes[] = array(
  				'id'      => $prefix .'disable_breadcrumbs',
  				'name'    => __('Breadcrumbs', 'futurio-extra'),
  				'type'    => 'switch',
  				'options' => array(
  					'enable'  => __('Enable', 'futurio-extra'), 
  					'disable' => __('Disable', 'futurio-extra'),
  				),
  				'std'  => 'enable',
  				'class' => ''
  			);
      }

        $dilaz_meta_boxes[] = array(
  				'id'      => $prefix .'disable_meta',
  				'name'    => __('Meta', 'futurio-extra'),
  				'type'    => 'switch',
  				'options' => array(
  					'enable'  => __('Enable', 'futurio-extra'), 
  					'disable' => __('Disable', 'futurio-extra'),
  				),
  				'std'  => 'enable',
  				'class' => ''
  			);
        $dilaz_meta_boxes[] = array(
  				'id'      => $prefix .'disable_cats_tags',
  				'name'    => __('Categories & tags', 'futurio-extra'),
  				'type'    => 'switch',
  				'options' => array(
  					'enable'  => __('Enable', 'futurio-extra'), 
  					'disable' => __('Disable', 'futurio-extra'),
  				),
  				'std'  => 'enable',
  				'class' => ''
  			);
        $dilaz_meta_boxes[] = array(
  				'id'      => $prefix .'disable_navigation',
  				'name'    => __('Post navigation', 'futurio-extra'),
  				'type'    => 'switch',
  				'options' => array(
  					'enable'  => __('Enable', 'futurio-extra'), 
  					'disable' => __('Disable', 'futurio-extra'),
  				),
  				'std'  => 'enable',
  				'class' => ''
  			);
        $dilaz_meta_boxes[] = array(
  				'id'      => $prefix .'disable_author_box',
  				'name'    => __('Author box', 'futurio-extra'),
  				'type'    => 'switch',
  				'options' => array(
  					'enable'  => __('Enable', 'futurio-extra'), 
  					'disable' => __('Disable', 'futurio-extra'),
  				),
  				'std'  => 'enable',
  				'class' => ''
  			);
        $dilaz_meta_boxes[] = array(
  				'id'      => $prefix .'disable_comments',
  				'name'    => __('Comments', 'futurio-extra'),
  				'type'    => 'switch',
  				'options' => array(
  					'enable'  => __('Enable', 'futurio-extra'), 
  					'disable' => __('Disable', 'futurio-extra'),
  				),
  				'std'  => 'enable',
  				'class' => ''
  			);
    

		
    # TAB - Tab 3
		# *****************************************************************************************
		$dilaz_meta_boxes[] = array(
			'id'    => $prefix .'extra_tab',
			'title' => __('Elements', 'futurio-extra'),
			'icon'  => 'fa-globe',
			'type'  => 'metabox_tab'
		);
      $top_bar = get_theme_mod( 'top_bar_sort', array() );
      if ( !empty( $top_bar ) ) {
        $dilaz_meta_boxes[] = array(
  				'id'      => $prefix .'disable_top_bar',
  				'name'    => __('Top Bar', 'futurio-extra'),
  				'type'    => 'switch',
  				'options' => array(
  					'enable'  => __('Enable', 'futurio-extra'), 
  					'disable' => __('Disable', 'futurio-extra'),
  				),
  				'std'  => 'enable',
  				'class' => ''
  			);
      }
      if ( get_theme_mod( 'custom_header', '' ) == ''  ) {
        if ( get_theme_mod( 'title_heading', 'boxed' ) == 'full' ) {
          $dilaz_meta_boxes[] = array(
    				'id'      => $prefix .'disable_header',
    				'name'    => __('Header', 'futurio-extra'),
    				'type'    => 'switch',
    				'options' => array(
    					'enable'  => __('Enable', 'futurio-extra'), 
    					'disable' => __('Disable', 'futurio-extra'),
    				),
    				'std'  => 'enable',
    				'class' => ''
    			);
        }
        $dilaz_meta_boxes[] = array(
  				'id'      => $prefix .'disable_main_menu',
  				'name'    => __('Main Menu', 'futurio-extra'),
  				'type'    => 'switch',
  				'options' => array(
  					'enable'  => __('Enable', 'futurio-extra'), 
  					'disable' => __('Disable', 'futurio-extra'),
  				),
  				'std'  => 'enable',
  				'class' => ''
  			);
      } else {
        $dilaz_meta_boxes[] = array(
  				'id'      => $prefix .'disable_elementor_header',
  				'name'    => __('Header', 'futurio-extra'),
  				'type'    => 'switch',
  				'options' => array(
  					'enable'  => __('Enable', 'futurio-extra'), 
  					'disable' => __('Disable', 'futurio-extra'),
  				),
  				'std'  => 'enable',
  				'class' => ''
  			);
      }
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'disable_footer_widgets',
				'name'    => __('Footer Widgets', 'futurio-extra'),
				'type'    => 'switch',
				'options' => array(
					'enable'  => __('Enable', 'futurio-extra'), 
					'disable' => __('Disable', 'futurio-extra'),
				),
				'std'  => 'enable',
				'class' => ''
			);
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'disable_footer',
				'name'    => __('Footer Credits', 'futurio-extra'),
				'type'    => 'switch',
				'options' => array(
					'enable'  => __('Enable', 'futurio-extra'), 
					'disable' => __('Disable', 'futurio-extra'),
				),
				'std'  => 'enable',
				'class' => ''
			);
      
      # TAB - Tab 4
  		# *****************************************************************************************
  		$dilaz_meta_boxes[] = array(
  			'id'    => $prefix .'bg_tab',
  			'title' => __('Background', 'futurio-extra'),
  			'icon'  => 'fa-file-o',
  			'type'  => 'metabox_tab'
  		);
      $dilaz_meta_boxes[] = array( 
				'id'   => $prefix .'image_bg',
				'name' => __('Background Image', 'futurio-extra'),
				'type' => 'upload',
				'std'  => '',
				'args' => array(
					'file_type' => 'image', 
					// 'file_specific' => true
				),
			);
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'image_bg_position',
				'name'    => __('Background Position', 'futurio-extra'),
				'type'    => 'select',
				'options' => array(
          ''    => '',
					'left_top'    => __('left top', 'futurio-extra'),
					'left_center' => __('left center', 'futurio-extra'),
					'left_bottom' => __('left bottom', 'futurio-extra'),
					'right_top' => __('right top', 'futurio-extra'),
					'right_center' => __('right center', 'futurio-extra'),
					'right_bottom' => __('right bottom', 'futurio-extra'),
					'center_top' => __('center top', 'futurio-extra'),
					'center_center' => __('center center', 'futurio-extra'),
          'center_bottom' => __('center bottom', 'futurio-extra'),
				),
				'std'  => array('center_center'),
				'args' => array(
					'select2'      => 'select2single',
					'select2width' => '50%',
				),
        
			);
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'image_bg_attachment',
				'name'    => __('Background Attachment', 'futurio-extra'),
				'type'    => 'select',
				'options' => array(
          ''    => '',
					'scroll'    => __('scroll', 'futurio-extra'),
					'fixed' => __('fixed', 'futurio-extra'),
				),
				'std'  => array(''),
				'args' => array(
					'select2'      => 'select2single',
					'select2width' => '50%',
				),
			);
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'image_bg_repeat',
				'name'    => __('Background Repeat', 'futurio-extra'),
				'type'    => 'select',
				'options' => array(
          ''    => '',
					'repeat'    => __('repeat', 'futurio-extra'),
					'repeat-x' => __('repeat-x', 'futurio-extra'),
          'repeat-y' => __('repeat-y', 'futurio-extra'),
          'no-repeat' => __('no-repeat', 'futurio-extra'),
				),
				'std'  => '',
				'args' => array(
					'select2'      => 'select2single',
					'select2width' => '50%',
				),
			);
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'image_bg_size',
				'name'    => __('Background Size', 'futurio-extra'),
				'type'    => 'select',
				'options' => array(
          ''    => '',
					'auto'    => __('auto', 'futurio-extra'),
					'cover' => __('cover', 'futurio-extra'),
          'contain' => __('contain', 'futurio-extra'),
				),
				'std'  => '',
				'args' => array(
					'select2'      => 'select2single',
					'select2width' => '50%',
				),
			);
    if ( futurio_extra_check_for_futurio_pro() ) {
        # TAB - Tab 1
		# *****************************************************************************************
		$dilaz_meta_boxes[] = array(
			'id'    => $prefix .'menu_tab',
			'title' => __('Main Menu', 'futurio-extra'),
			'icon'  => 'fa-bars',
			'type'  => 'metabox_tab'
		);
			
			# FIELDS - Tab 1
			# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
      $dilaz_meta_boxes[] = array(
				'id'      => $prefix .'transparent_menu_option',
				'name'    => __('Transparent menu', 'futurio-extra'),
				'type'    => 'switch',
				'options' => array(
					'enable'  => __('Enable', 'futurio-extra'), 
					'disable' => __('Disable', 'futurio-extra'),
				),
				'std'  => 'disable',
				'class' => ''
			);
      $dilaz_meta_boxes[] = array(
				'id'	=> $prefix .'transparent_bg_color',
				'name'	=> __('Menu background color', 'futurio-extra'),
				'type'	=> 'color',
				'std'   => '',
        'class' => '',
        'req_args' => array(
  				$prefix .'transparent_menu_option'  => 'enable',
  			),
  			'req_action' => 'show',
			);
      $dilaz_meta_boxes[] = array(
  				'id'   => $prefix .'transparent_bg_color_opacity',
  				'name' => __('Background color opacity', 'futurio-extra'),
  				'type' => 'slider',
  				'args' => array(
  					'min'    => '0', 
  					'max'    => '100', 
  					'step'   => '1', 
  				),
  				'std'   => '0',
  				'class' => '',
          'req_args' => array(
    				$prefix .'transparent_menu_option'  => 'enable',
    			),
    			'req_action' => 'show',
  			); 
      $dilaz_meta_boxes[] = array(
				'id'	=> $prefix .'transparent_menu_color',
				'name'	=> __('Menu color', 'futurio-extra'),
				'type'	=> 'color',
				'std'   => '',
        'class' => '',
        'req_args' => array(
  				$prefix .'transparent_menu_option'  => 'enable',
  			),
  			'req_action' => 'show',
			);
      $dilaz_meta_boxes[] = array(
				'id'	=> $prefix .'transparent_title_color',
				'name'	=> __('Site title color', 'futurio-extra'),
				'type'	=> 'color',
				'std'   => '',
        'class' => '',
        'req_args' => array(
  				$prefix .'transparent_menu_option'  => 'enable',
  			),
  			'req_action' => 'show',
			);
      $dilaz_meta_boxes[] = array(
				'id'	=> $prefix .'transparent_tagline_color',
				'name'	=> __('Tagline color', 'futurio-extra'),
				'type'	=> 'color',
				'std'   => '',
        'class' => '',
        'req_args' => array(
  				$prefix .'transparent_menu_option'  => 'enable',
  			),
  			'req_action' => 'show',
			);
      if ( has_custom_logo() && get_theme_mod( 'title_heading', 'boxed' ) == 'boxed' ) {
        $dilaz_meta_boxes[] = array( 
  				'id'   => $prefix .'transparent_menu_logo',
  				'name' => __('Custom logo', 'futurio-extra'),
  				'type' => 'upload',
  				'std'  => '',
  				'args' => array(
  					'file_type' => 'image', 
  					// 'file_specific' => true
  				),
          'req_args' => array(
    				$prefix .'transparent_menu_option'  => 'enable',
    			),
    			'req_action' => 'show',
  			);
      }
      
    }
	return $dilaz_meta_boxes;
//}