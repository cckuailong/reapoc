<?php
/*
|| --------------------------------------------------------------------------------------------
|| Default Metaboxes Fields
|| --------------------------------------------------------------------------------------------
||
|| @package		Dilaz Metabox
|| @subpackage	Default Options
|| @since		Dilaz Metabox 1.0
|| @author		WebDilaz Team, http://webdilaz.com
|| @copyright	Copyright (C) 2017, WebDilaz LTD
|| @link		http://webdilaz.com/metaboxes
|| @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
|| 
*/

defined('ABSPATH') || exit;


# BOX - Options Set 1
# =============================================================================================
$dilaz_meta_boxes[] = array(
	'id'	   => $prefix .'box-simple-fields',
	'title'	   => __('Default Options', 'dilaz-metabox'),
	'pages'    => array('post', 'page'),
	'context'  => 'normal',
	'priority' => 'high',
	'type'     => 'metabox_set'
);
	
	# TAB - Simple Options Set
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'simple_options',
		'title' => __('Simple Options', 'dilaz-metabox'),
		'icon'  => 'fa-cog',
		'type'  => 'metabox_tab'
	);
		
		# FIELDS - Simple Fields
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'range',
			'name' => __('Range:', 'dilaz-metabox'),
			'desc' => __('Set range between two minimum and maximum values.', 'dilaz-metabox'),
			'type' => 'range',
			'args' => array(
				'min'           => array( 8,   __('Min', 'dilaz-metabox') ), 
				'max'           => array( 100, __('Max', 'dilaz-metabox') ), 
				'step'          => '2', 
				'prefix'        => '',
				'suffix'        => '%',
				'post_format'   => array('standard', 'gallery', 'quote'),
				'page_template' => array('custom', 'custom_2')
			),
			'std' => array('min_std' => 20, 'max_std' => 45),
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'switch_enable',
			'name'    => __('Switch Enable/Disable', 'dilaz-metabox'),
			'desc'    => __('Enable/disable switch option.', 'dilaz-metabox'),
			'type'    => 'switch',
			'options' => array(
				'enable'  => __('Enable', 'dilaz-metabox'), 
				'disable' => __('Disable', 'dilaz-metabox'),
			),
			'std'  => 'disable',
			'args' => array(
				'post_format'   => array('standard', 'video', 'chat'),
				'page_template' => array('custom_2')
			),
			'class' => ''
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'switch',
			'name'    => __('Switch', 'dilaz-metabox'),
			'desc'    => __('On/Off switch option.', 'dilaz-metabox'),
			'type'    => 'switch',
			'options' => array(
				1 => __('On', 'dilaz-metabox'), 
				0 => __('Off', 'dilaz-metabox'),
			),
			'std'   => 0,
			'class' => ''
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'buttonset',
			'name'    => __('Button Set', 'dilaz-metabox'),
			'desc'    => __('Set multiple options using buttonset.', 'dilaz-metabox'),
			'type'    => 'buttonset',
			'options' => array(
				'yes'   => __('Yes', 'dilaz-metabox'), 
				'no'    => __('No', 'dilaz-metabox'),
				'maybe' => __('Maybe', 'dilaz-metabox')
			),
			'std'   => 'no',
			'class' => ''
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'slider',
			'name' => __('Slider:', 'dilaz-metabox'),
			'desc' => __('Select value from range slider.', 'dilaz-metabox'),
			'type' => 'slider',
			'args' => array(
				'min'    => '8', 
				'max'    => '100', 
				'step'   => '2', 
				'suffix' => '%'
			),
			'std'   => '40',
			'class' => ''
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'textarea',
			'name'   => __('Textarea:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'textarea',
			'std'    => 'Sample content...',
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'textarea_custom',
			'name'   => __('Textarea Custom:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'textarea',
			'std'    => 'Sample content...',
			'args'   => array('cols' => '30', 'rows' => '5'),
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'stepper',
			'name'   => __('Numeric Stepper:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'stepper',
			'std'    => '2',
			'args'   => array('wheel_step' => '1', 'arrow_step' => '1', 'step_limit' => '1,10'),
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'stepper_custom',
			'name'   => __('Numeric Stepper Custom:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'stepper',
			'std'    => '200000',
			'args'   => array('wheel_step' => '5000', 'arrow_step' => '5000', 'step_limit' => '1000,900000', 'size' => '10'),
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'number',
			'name'   => __('Number:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'number',
			'std'    => '',
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'number_custom',
			'name'   => __('Number Custom Size:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'number',
			'std'    => '',
			'args'   => array('size' => '20'),
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'url',
			'name'   => __('URL:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'url',
			'std'    => '',
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'url_custom',
			'name'   => __('URL Custom Size:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'url',
			'std'    => '',
			'args'   => array('size' => '50'),
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'password',
			'name'   => __('Password:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'password',
		);
		$dilaz_meta_boxes[] = array(
			'id'    => $prefix .'paragraph',
			'name'  => __('Paragraph Field', 'dilaz-metabox'),
			'desc'  => __('Description 1.', 'dilaz-metabox'),
			'desc2' => __('Description 2.', 'dilaz-metabox'),
			'type'  => 'paragraph',
			'value' => 'Your privacy is important to us. We at ThemeDilaz, have created this privacy policy to demonstrate our firm commitment to protecting your personal information and informing you about how we handle it. 
			
			This privacy policy only applies to transactions and activities in which you engage, and data gathered, on the ThemeDilaz Website. Please review this privacy policy periodically as we may modify it from time to time. 
			
			Each time you visit the Site or provide us with information, by doing so you are accepting the practices described in this privacy policy at that time.',
		);
		$dilaz_meta_boxes[] = array(
			'id'    => $prefix .'codeoutput',
			'name'  => __('Code Output', 'dilaz-metabox'),
			'desc'  => __('Description 1.', 'dilaz-metabox'),
			'desc2' => __('Description 2.', 'dilaz-metabox'),
			'type'  => 'codeoutput',
			'value' => '<?php if (function_exists("json_encode")) {
				json_encode($result);
			} ?>',
		);
		$dilaz_meta_boxes[] = array(
			'id'    => $prefix .'hidden',
			'type'  => 'hidden',
			'value' => 'hidden',
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'text',
			'name'   => __('Text:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'text',
			'std'    => 'placeholder',
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'email',
			'name'   => __('Email:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'email',
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'text_custom',
			'name'   => __('Text Custom Size:', 'dilaz-metabox'),
			'desc'   => __('Description 1.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'text',
			'std'    => 'placeholder',
			'args'   => array('size' => '40'),
		);
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'multi_text_inline',
			'name'	  => __('Multiple Text (Inline 3 Columns):', 'dilaz-metabox'),
			'desc'	  => __('Set any number of multiple text field options.', 'dilaz-metabox'),
			'desc2'	  => __('Description 2.', 'dilaz-metabox'),
			'type'	  => 'multitext',
			'options' => array(
				'text1' => array('name' => __('Text 1', 'dilaz-metabox'), 'default' => 'Sample One'),
				'text2' => array('name' => __('Text 2', 'dilaz-metabox'), 'default' => 'Sample Two'),
				'text3' => array('name' => __('Text 3', 'dilaz-metabox'), 'default' => 'Sample Three'),
				'text4' => array('name' => __('Text 4', 'dilaz-metabox'), 'default' => 'Sample Four'),
				'text5' => array('name' => __('Text 5', 'dilaz-metabox'), 'default' => 'Sample Five'),
				'text6' => array('name' => __('Text 6', 'dilaz-metabox'), 'default' => 'Sample Six'),
			),
			'args' => array('inline' => true, 'cols' => 3),
		);
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'multi_text',
			'name'	  => __('Multiple Text (Single Column):', 'dilaz-metabox'),
			'desc'	  => __('Set any number of multiple text field options.', 'dilaz-metabox'),
			'desc2'	  => __('Description 2.', 'dilaz-metabox'),
			'type'	  => 'multitext',
			'options' => array(
				'text1' => array('name' => __('Text 1', 'dilaz-metabox'), 'default' => 'Sample One'),
				'text2' => array('name' => __('Text 2', 'dilaz-metabox'), 'default' => 'Sample Two'),
				'text3' => array('name' => __('Text 3', 'dilaz-metabox'), 'default' => 'Sample Three')
			),
			'args' => array(),
		);
	
	# TAB - Repeatable Options Set
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'repeatable_fields',
		'title' => __('Repeatable Options', 'dilaz-metabox'),
		'icon'  => 'fa-align-justify',
		'type'  => 'metabox_tab'
	);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'repeatable_one',
			'name' => __('Repeatable (Single Field per Row):', 'dilaz-metabox'),
			'desc' => __('Single row items repeatable field. ', 'dilaz-metabox'),
			'type' => 'repeatable',
			'args' => array(
				'sortable' => true,
				'removable' => true,
				'add_more' => true,
				'add_text' => __('Add More', 'dilaz-metabox'),
			),
			'options' => array(
				array(
					array('label' => '', 'value' => 'Row 1 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 2 - Value 2'), 
				)
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'repeatable_two',
			'name' => __('Repeatable (Multiple Fields per Row):', 'dilaz-metabox'),
			'desc' => __('Multiple row items repeatable field. You can add any number item fields per row.', 'dilaz-metabox'),
			'type' => 'repeatable',
			'args' => array(
				'sortable' => true,
				'removable' => true,
				'add_more' => true,
				'add_text' => __('Add More Custom', 'dilaz-metabox'),
			),
			'options' => array(
				array(
					array('label' => 'Label One', 'value' => 'Row 1 - Value 1', 'size' => 15), 
					array('label' => 'Label Two', 'value' => 'Row 1 - Value 2', 'size' => 15), 
					array('label' => 'Label Three', 'value' => 'Row 1 - Value 3', 'size' => 15), 
					array('label' => 'Label Four', 'value' => 'Row 1 - Value 4', 'size' => 15), 
				),
				array(
					array('label' => 'Label One', 'value' => 'Row 2 - Value 1', 'size' => 15), 
					array('label' => 'Label Two', 'value' => 'Row 2 - Value 2', 'size' => 15), 
					array('label' => 'Label Three', 'value' => 'Row 2 - Value 3', 'size' => 15), 
					array('label' => 'Label Four', 'value' => 'Row 2 - Value 4', 'size' => 15), 
				)
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'repeatable_three',
			'name' => __('Repeatable (Add More Field Disabled):', 'dilaz-metabox'),
			'desc' => __('Add more button is disabled.', 'dilaz-metabox'),
			'type' => 'repeatable',
			'args' => array(
				'sortable' => true,
				'removable' => true,
				'add_more' => false,
			),
			'options' => array(
				array(
					array('label' => '', 'value' => 'Row 1 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 2 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 3 - Value 1'), 
				)
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'repeatable_four',
			'name' => __('Repeatable (Sorting Disabled):', 'dilaz-metabox'),
			'desc' => __('Field sorting is disabled.', 'dilaz-metabox'),
			'type' => 'repeatable',
			'args' => array(
				'sortable' => false,
				'removable' => true,
				'add_more' => true,
			),
			'options' => array(
				array(
					array('label' => '', 'value' => 'Row 1 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 2 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 3 - Value 1'), 
				)
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'repeatable_five',
			'name' => __('Repeatable (Sorting Disabled for First 2 Fields):', 'dilaz-metabox'),
			'desc' => __('Field sorting is disabled for the first 2 items.', 'dilaz-metabox'),
			'type' => 'repeatable',
			'args' => array(
				'sortable' => true,
				'not_sortable' => 2,
				'add_more' => true,
			),
			'options' => array(
				array(
					array('label' => '', 'value' => 'Row 1 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 2 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 3 - Value 1'), 
				)
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'repeatable_six',
			'name' => __('Repeatable (Removal Disabled):', 'dilaz-metabox'),
			'desc' => __('Field removal is disabled.', 'dilaz-metabox'),
			'type' => 'repeatable',
			'args' => array(
				'removable' => false,
				'add_more' => true,
			),
			'options' => array(
				array(
					array('label' => '', 'value' => 'Row 1 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 2 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 3 - Value 1'), 
				)
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'repeatable_seven',
			'name' => __('Repeatable (Removal Disabled for the 1st and 2nd Fields):', 'dilaz-metabox'),
			'desc' => __('Field removal is disabled for the 1st and 2nd items.', 'dilaz-metabox'),
			'type' => 'repeatable',
			'args' => array(
				'not_removable' => 2,
				'add_more' => true,
			),
			'options' => array(
				array(
					array('label' => '', 'value' => 'Row 1 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 2 - Value 1'), 
				),
				array(
					array('label' => '', 'value' => 'Row 3 - Value 1'), 
				)
			),
		);
	
	# TAB - Media Options Set
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'media_options',
		'title' => __('Media Options', 'dilaz-metabox'),
		'icon'  => 'fa-tv',
		'type'  => 'metabox_tab'
	);
		
		# FIELDS - Media Fields
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'image_file_multiple',
			'name' => __('Image File (Multiple):', 'dilaz-metabox'),
			'desc' => __('Select/Upload multiple image files from media library.', 'dilaz-metabox'),
			'type' => 'upload',
			'std'  => '',
			'args' => array(
				'file_type' => 'image', 
				'multiple'  => true,
				// 'file_specific' => true, 
			),
		);
		$dilaz_meta_boxes[] = array( 
			'id'   => $prefix .'image_file',
			'name' => __('Image File:', 'dilaz-metabox'),
			'desc' => __('Select/Upload single image file from media library.', 'dilaz-metabox'),
			'type' => 'upload',
			'std'  => '',
			'args' => array(
				'file_type' => 'image', 
				// 'file_specific' => true
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'audio_file_multiple',
			'name' => __('Audio File (Multiple):', 'dilaz-metabox'),
			'desc' => __('Select/Upload multiple audio files from media library.', 'dilaz-metabox'),
			'type' => 'upload',
			'std'  => '',
			'args' => array(
				'file_type' => 'audio',  
				'multiple'  => true,
				// 'file_specific' => true,
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'audio_file',
			'name' => __('Audio File:', 'dilaz-metabox'),
			'desc' => __('Select/Upload single audio file from media library.', 'dilaz-metabox'),
			'type' => 'upload',
			'std'  => '',
			'args' => array(
				'file_type' => 'audio', 
				// 'file_specific' => true
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'video_file_multiple',
			'name' => __('Video File (Multiple):', 'dilaz-metabox'),
			'desc' => __('Select/Upload multiple video files from media library.', 'dilaz-metabox'),
			'type' => 'upload',
			'std'  => '',
			'args' => array(
				'file_type' => 'video',
				'multiple'  => true, 
				// 'file_specific' => true
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'video_file',
			'name' => __('Video File:', 'dilaz-metabox'),
			'desc' => __('Select/Upload single video file from media library.', 'dilaz-metabox'),
			'type' => 'upload',
			'std'  => '',
			'args' => array(
				'file_type' => 'video', 
				// 'file_specific' => true
			),
		);
	
	# TAB - Date Options Set
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'date_options',
		'title' => __('Date Options', 'dilaz-metabox'),
		'icon'  => 'fa-calendar',
		'type'  => 'metabox_tab'
	);
		
		# FIELDS - Date Fields
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'date_time_from_to',
			'name' => __('Date Time (From - To):', 'dilaz-metabox'),
			'desc' => __('Combined date and time difference between time sets.', 'dilaz-metabox'),
			'type' => 'date_time_from_to',
			'std'  => '',
			'args' => array(
				'size'      => '50',
				'from_args' => array(
					'format'    => 'l, d F Y h:i:s A',
					'date_time' => '',
					'prefix'    => __('From:', 'dilaz-metabox'),
				),
				'to_args' => array(
					'format'    => 'l, d F Y h:i:s A',
					'date_time' => '',
					'prefix'    => __('To:', 'dilaz-metabox'),
				),					
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'date_time',
			'name' => __('Date Time:', 'dilaz-metabox'),
			'desc' => __('Combined date and time option.', 'dilaz-metabox'),
			'type' => 'date_time',
			'std'  => '',
			'args' => array(
				'format' => 'l, d F Y h:i:s A', 
				'size'   => '50'
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'time_from_to',
			'name' => __('Time (From - To):', 'dilaz-metabox'),
			'desc' => __('Time difference between time sets.', 'dilaz-metabox'),
			'type' => 'time_from_to',
			'std'  => '',
			'args' => array(
				'size'      => '20',
				'from_args' => array(
					'format' => 'h:i:s A',
					'time'   => '',
					'prefix' => __('From:', 'dilaz-metabox'),
				),
				'to_args' => array(
					'format' => 'h:i:s A',
					'time'   => '',
					'prefix' => __('To:', 'dilaz-metabox'),
				),					
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'time',
			'name' => __('Time:', 'dilaz-metabox'),
			'desc' => __('Time option.', 'dilaz-metabox'),
			'type' => 'time',
			'std'  => '07:00',
			'args' => array(
				'format' => 'h:i:s A', 
				'size'   => '20'
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'month_from_to',
			'name' => __('Month (From - To):', 'dilaz-metabox'),
			'desc' => __('Month difference between time sets.', 'dilaz-metabox'),
			'type' => 'month_from_to',
			'std'  => '',
			'args' => array(
				'size'      => '20',
				'from_args' => array(
					'format' => 'F, Y',
					'month'  => '',
					'prefix' => __('From:', 'dilaz-metabox'),
				),
				'to_args' => array(
					'format' => 'F, Y',
					'month'  => '',
					'prefix' => __('To:', 'dilaz-metabox'),
				),					
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'month',
			'name' => __('Month:', 'dilaz-metabox'),
			'desc' => __('Month option.', 'dilaz-metabox'),
			'type' => 'month',
			'std'  => '',
			'args' => array(
				'format' => 'F, Y', 
				'size'   => '20'
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'date_from_to',
			'name' => __('Date (From - To):', 'dilaz-metabox'),
			'desc' => __('Date difference between time sets.', 'dilaz-metabox'),
			'type' => 'date_from_to',
			'std'  => '',
			'args' => array(
				'size'      => '30',
				'from_args' => array(
					'format' => 'l, d F, Y',
					'date'   => '',
					'prefix' => __('From:', 'dilaz-metabox'),
				),
				'to_args' => array(
					'format' => 'l, d F, Y',
					'date'   => '',
					'prefix' => __('To:', 'dilaz-metabox'),
				),					
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'date',
			'name' => __('Date:', 'dilaz-metabox'),
			'desc' => __('Date option.', 'dilaz-metabox'),
			'type' => 'date',
			'std'  => '',
			'args' => array(
				'format' => 'l, d F, Y', 
				'size'   => '20'
			),
		);
	
	# TAB - Color Options Set
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'color_options',
		'title' => __('Color Options', 'dilaz-metabox'),
		'icon'  => 'fa-paint-brush',
		'type'  => 'metabox_tab'
	);
		
		# FIELDS - Color Fields
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'multi_color',
			'name'	  => __('Multiple Colors:', 'dilaz-metabox'),
			'desc'	  => __('Set any number of multiple color properties.', 'dilaz-metabox'),
			'desc2'	  => __('Description 2.', 'dilaz-metabox'),
			'type'	  => 'multicolor',
			'options' => array(
				'color1' => array('color' => '#111111', 'name' => __('Color 1', 'dilaz-metabox')),
				'color2' => array('color' => '#333333', 'name' => __('Color 2', 'dilaz-metabox')),
				'color3' => array('color' => '#555555', 'name' => __('Color 3', 'dilaz-metabox')),
				'color4' => array('color' => '#777777', 'name' => __('Color 4', 'dilaz-metabox')),
				'color5' => array('color' => '#999999', 'name' => __('Color 5', 'dilaz-metabox')),
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'	=> $prefix .'color',
			'name'	=> __('Color:', 'dilaz-metabox'),
			'desc'	=> __('Set single color property.', 'dilaz-metabox'),
			'desc2'	=> __('Description 2.', 'dilaz-metabox'),
			'type'	=> 'color',
			'std'   => '#222222',
		);
	
	# TAB - Choice Options Set
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'choice_options',
		'title' => __('Choice Options', 'dilaz-metabox'),
		'icon'  => 'fa-sliders',
		'type'  => 'metabox_tab'
	);
	
		# FIELDS - Choice Fields
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'radioimage',
			'name'	  => __('Radio Image:', 'dilaz-metabox'),
			'desc'	  => __('Images used as radio option fields.', 'dilaz-metabox'),
			'type'	  => 'radioimage',
			'options' => array(
				'default' => $parameters['dir_url'] .'assets/images/layout-default.png',
				'left'    => $parameters['dir_url'] .'assets/images/layout-left.png',
				'right'   => $parameters['dir_url'] .'assets/images/layout-right.png',
				'full'    => $parameters['dir_url'] .'assets/images/layout-full.png',
			),
			'std' => 'project_default'
		);
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'term_select',
			'name'	  => __('Term Select:', 'dilaz-metabox'),
			'desc'	  => __('Select single or multiple terms from any taxonomy.', 'dilaz-metabox'),
			'type'	  => 'queryselect',
			'options' => '',
			'std'     => '',
			'args'    => array(
				'select2'      => 'select2multiple',
				'query_type'   => 'term',
				'placeholder'  => __('Select category', 'dilaz-metabox'),
				'select2width' => '50%',
				'min_input'    => 1,
				'max_input'    => 100,
				'max_options'  => 10,
				'query_args'   => array(
					'taxonomy'   => 'category',
					'hide_empty' => false,
					'orderby'    => 'term_id',
					'order'      => 'ASC',
				),
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'user_select',
			'name'	  => __('User Select:', 'dilaz-metabox'),
			'desc'	  => __('Select single or multiple users from all registered members.', 'dilaz-metabox'),
			'type'	  => 'queryselect',
			'options' => '',
			'std'     => '',
			'args'    => array(
				'select2'      => 'select2multiple',
				'query_type'   => 'user',
				'placeholder'  => __('Select user', 'dilaz-metabox'),
				'select2width' => '50%',
				'min_input'    => 1,
				'max_input'    => 100,
				'max_options'  => 10,
				'query_args'   => array(
					'orderby' => 'ID',
					'order'   => 'ASC',
				),
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'post_select',
			'name'	  => __('Post Select:', 'dilaz-metabox'),
			'desc'	  => __('Select single or multiple posts.', 'dilaz-metabox'),
			'type'	  => 'queryselect',
			'options' => '',
			'std'     => '',
			'args'    => array(
				'select2'      => 'select2multiple',
				'query_type'   => 'post',
				'placeholder'  => __('Type to select a post', 'dilaz-metabox'),
				'select2width' => '50%',
				'min_input'    => 1,
				'max_input'    => 100,
				'max_options'  => 10,
				'query_args'   => array(
					'posts_per_page' => -1,
					'post_status'    => array('publish'),
					'post_type'      => array('post'),
					'order'          => 'ASC',
					'orderby'        => 'ID',
				),
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'timezone',
			'name'	  => __('Timezone:', 'dilaz-metabox'),
			'desc'	  => __('Select preferred time zone.', 'dilaz-metabox'),
			'type'	  => 'timezone',
			'options' => DilazMetaboxFunction::time_zones(),
			'std'     => ''
		);
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'timezone_select2',
			'name'	  => __('Timezone Select2:', 'dilaz-metabox'),
			'desc'	  => __('Select preferred time zone - with select2 search capability.', 'dilaz-metabox'),
			'type'	  => 'timezone',
			'options' => DilazMetaboxFunction::time_zones(),
			'std'     => '',
			'args'    => array(
				'select2'      => 'select2single',
				'select2width' => '365px',
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'multiselect_select2',
			'name'    => __('Multiselect Single - Select2 Plugin:', 'dilaz-metabox'),
			'desc'    => __('Select multiple options using select2 plugin.', 'dilaz-metabox'),
			'type'    => 'multiselect',
			'options' => array(
				''    => '',
				'mon' => __('Monday', 'dilaz-metabox'),
				'tue' => __('Tuesday', 'dilaz-metabox'),
				'wed' => __('Wednesday', 'dilaz-metabox'),
				'thu' => __('Thursday', 'dilaz-metabox'),
				'fri' => __('Friday', 'dilaz-metabox'),
				'sat' => __('Saturday', 'dilaz-metabox'),
				'sun' => __('Sunday', 'dilaz-metabox')
			),
			'std'  => array('thu'),
			'args' => array(
				'select2'      => 'select2single',
				'select2width' => '50%',
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'multiselect',
			'name'    => __('Multiselect Single Default:', 'dilaz-metabox'),
			'desc'    => __('Select multiple options by pressing Ctrl(PC) or Cmd(Mac).', 'dilaz-metabox'),
			'type'    => 'multiselect',
			'options' => array(
				''    => '',
				'mon' => __('Monday', 'dilaz-metabox'),
				'tue' => __('Tuesday', 'dilaz-metabox'),
				'wed' => __('Wednesday', 'dilaz-metabox'),
				'thu' => __('Thursday', 'dilaz-metabox'),
				'fri' => __('Friday', 'dilaz-metabox'),
				'sat' => __('Saturday', 'dilaz-metabox'),
				'sun' => __('Sunday', 'dilaz-metabox')
			),
			'std' => array('thu'),
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'select_select2',
			'name'    => __('Select Single - Select2 Plugin:', 'dilaz-metabox'),
			'desc'    => __('Select single option using select2 plugin - has search capability.', 'dilaz-metabox'),
			'type'    => 'select',
			'options' => array(
				''    => '',
				'mon' => __('Monday', 'dilaz-metabox'),
				'tue' => __('Tuesday', 'dilaz-metabox'),
				'wed' => __('Wednesday', 'dilaz-metabox'),
				'thu' => __('Thursday', 'dilaz-metabox'),
				'fri' => __('Friday', 'dilaz-metabox'),
				'sat' => __('Saturday', 'dilaz-metabox'),
				'sun' => __('Sunday', 'dilaz-metabox')
			),
			'std'  => array('thu'),
			'args' => array(
				'select2'      => 'select2single',
				'select2width' => '50%',
			),
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'select',
			'name'    => __('Select Single Default:', 'dilaz-metabox'),
			'desc'    => __('Default single option selection.', 'dilaz-metabox'),
			'type'    => 'select',
			'options' => array(
				''    => '',
				'mon' => __('Monday', 'dilaz-metabox'),
				'tue' => __('Tuesday', 'dilaz-metabox'),
				'wed' => __('Wednesday', 'dilaz-metabox'),
				'thu' => __('Thursday', 'dilaz-metabox'),
				'fri' => __('Friday', 'dilaz-metabox'),
				'sat' => __('Saturday', 'dilaz-metabox'),
				'sun' => __('Sunday', 'dilaz-metabox')
			),
			'std' => array('thu'),
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'multicheck',
			'name'    => __('Day of the Week:', 'dilaz-metabox'),
			'desc'    => __('Tiled multiple checkbox options selection.', 'dilaz-metabox'),
			'type'    => 'multicheck',
			'options' => array(
				'mon' => __('Monday', 'dilaz-metabox'),
				'tue' => __('Tuesday', 'dilaz-metabox'),
				'wed' => __('Wednesday', 'dilaz-metabox'),
				'thu' => __('Thursday', 'dilaz-metabox'),
				'fri' => __('Friday', 'dilaz-metabox'),
				'sat' => __('Saturday', 'dilaz-metabox'),
				'sun' => __('Sunday', 'dilaz-metabox')
			),
			'std' => array('thu'),
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'multicheck_inline',
			'name'    => __('Multicheck Inline:', 'dilaz-metabox'),
			'desc'    => __('Inline multiple checkbox options selection.', 'dilaz-metabox'),
			'type'    => 'multicheck',
			'options' => array(
				'mon' => __('Monday', 'dilaz-metabox'),
				'tue' => __('Tuesday', 'dilaz-metabox'),
				'wed' => __('Wednesday', 'dilaz-metabox'),
				'thu' => __('Thursday', 'dilaz-metabox'),
				'fri' => __('Friday', 'dilaz-metabox'),
				'sat' => __('Saturday', 'dilaz-metabox'),
				'sun' => __('Sunday', 'dilaz-metabox')
			),
			'std'  => array('thu'),
			'args' => array('inline' => true, 'cols' => 4),
		);
		$dilaz_meta_boxes[] = array(
			'id'     => $prefix .'checkbox',
			'name'   => __('Checkbox:', 'dilaz-metabox'),
			'desc'   => __('Single checkbox selection.', 'dilaz-metabox'),
			'desc2'  => __('Description 2.', 'dilaz-metabox'),
			'prefix' => __('Prefix', 'dilaz-metabox'),
			'suffix' => __('Suffix', 'dilaz-metabox'),
			'type'   => 'checkbox',
			'std'    => 0,
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'radio',
			'name'    => __('Radio:', 'dilaz-metabox'),
			'desc'    => __('Tiled radio options selection.', 'dilaz-metabox'),
			'type'    => 'radio',
			'options' => array(
				'one'   => __('One', 'dilaz-metabox'),
				'two'   => __('Two', 'dilaz-metabox'),
				'three' => __('Three', 'dilaz-metabox'),
			),
			'std' => 'two',
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'radio_inline',
			'name'    => __('Radio Inline:', 'dilaz-metabox'),
			'desc'    => __('Inline radio options selection.', 'dilaz-metabox'),
			'type'    => 'radio',
			'options' => array(
				'one'   => __('One', 'dilaz-metabox'),
				'two'   => __('Two', 'dilaz-metabox'),
				'three' => __('Three', 'dilaz-metabox'),
			),
			'std'  => 'two',
			'args' => array('inline' => true),
		);
	
	# TAB - Editor Options Set
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'editor_options',
		'title' => __('Editor Options', 'dilaz-metabox'),
		'icon'  => 'fa-bold',
		'type'  => 'metabox_tab'
	);
		
		# FIELDS - Editor Fields
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'editor',
			'name' => __('Editor:', 'dilaz-metabox'),
			'desc' => __('Default WordPress text editor.', 'dilaz-metabox'),
			'type' => 'editor',
		);
		$dilaz_meta_boxes[] = array(
			'id'   => $prefix .'editor_custom',
			'name' => __('Editor Custom:', 'dilaz-metabox'),
			'desc' => __('Default WordPress text editor with more rows.', 'dilaz-metabox'),
			'type' => 'editor',
			'args' => array('rows' => '55'),
		);
		
		
	# TAB - Conditionals Options Set
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'conditionals',
		'title' => __('Conditionals', 'dilaz-metabox'),
		'icon'  => 'fa-toggle-on',
		'type'  => 'metabox_tab'
	);
		
		# FIELDS - Conditional Fields
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'continent',
			'name'	  => __('Continent:', 'dilaz-metabox'),
			'desc'	  => '',
			'type'	  => 'select',
			'options' => array(
				''   => __('Select Continent', 'dilaz-metabox'),
				'eu' => __('Europe', 'dilaz-metabox'),
				'na' => __('North America', 'dilaz-metabox'),
			),
			'std'  => 'default',
			'args' => array('inline' => true),
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'eu_country',
			'name'    => __('Europe Country:', 'dilaz-metabox'),
			'desc'    => '',
			'type'    => 'radio',
			'options' => array(
				'de' => __('Germany', 'dilaz-metabox'),
				'gb' => __('United Kingdom', 'dilaz-metabox'),
			),
			'std'      => 'default',
			'args'     => array('inline' => true),
			'req_args' => array(
				$prefix .'continent' => 'eu'
			),
			'req_action' => 'show',
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'na_country',
			'name'    => __('North America Country:', 'dilaz-metabox'),
			'desc'    => '',
			'type'    => 'radio',
			'options' => array(
				'us' => __('United States', 'dilaz-metabox'),
				'ca' => __('Canada', 'dilaz-metabox'),
			),
			'std'      => 'default',
			'args'     => array('inline' => true),
			'req_args' => array(
				$prefix .'continent' => 'na'
			),
			'req_cond'   => 'AND',
			'req_action' => 'show',
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'de_division',
			'name'    => __('Germany Division:', 'dilaz-metabox'),
			'desc'    => '',
			'type'    => 'multicheck',
			'options' => array(
				'hh' => __('Hamburg', 'dilaz-metabox'),
				'be' => __('Berlin', 'dilaz-metabox'),
				'sh' => __('Schleswig-Holstein', 'dilaz-metabox'),
			),
			'std'      => 'default',
			'args'     => array('inline' => true),
			'req_args' => array(
				$prefix .'continent'  => 'eu',
				$prefix .'eu_country' => 'de'
			),
			'req_cond'   => 'AND',
			'req_action' => 'show',
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'gb_division',
			'name'    => __('United Kingdom Division:', 'dilaz-metabox'),
			'desc'    => '',
			'type'    => 'multicheck',
			'options' => array(
				'abd' => __('Aberdeen City', 'dilaz-metabox'),
				'bir' => __('Birmingham', 'dilaz-metabox'),
				'lce' => __('Leicester', 'dilaz-metabox'),
				'man' => __('Manchester', 'dilaz-metabox'),
			),
			'std'      => 'default',
			'args'     => array('inline' => true),
			'req_args' => array(
				$prefix .'continent'  => 'eu',
				$prefix .'eu_country' => 'gb'
			),
			'req_cond'   => 'AND',
			'req_action' => 'show',
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'us_division',
			'name'    => __('US State:', 'dilaz-metabox'),
			'desc'    => '',
			'type'    => 'multicheck',
			'options' => array(
				'wa' => __('Washington', 'dilaz-metabox'),
				'oh' => __('Ohio', 'dilaz-metabox'),
				'mt' => __('Montana', 'dilaz-metabox'),
				'ga' => __('Georgia', 'dilaz-metabox'),
			),
			'std'      => 'default',
			'args'     => array('inline' => true),
			'req_args' => array(
				$prefix .'continent'  => 'na',
				$prefix .'na_country' => 'us'
			),
			'req_cond'   => 'AND',
			'req_action' => 'show',
		);
		$dilaz_meta_boxes[] = array(
			'id'      => $prefix .'us_division',
			'name'    => __('Canada Division:', 'dilaz-metabox'),
			'desc'    => '',
			'type'    => 'multicheck',
			'options' => array(
				'on' => __('Ontario', 'dilaz-metabox'),
				'sk' => __('Saskatchewan', 'dilaz-metabox'),
				'qc' => __('Quebec', 'dilaz-metabox'),
			),
			'std'      => 'default',
			'args'     => array('inline' => true),
			'req_args' => array(
				$prefix .'continent'  => 'na',
				$prefix .'na_country' => 'ca'
			),
			'req_cond'   => 'AND',
			'req_action' => 'show',
		);
		
		
# BOX - Test Beta
# =============================================================================================
$dilaz_meta_boxes[] = array(
	'id'	   => 'dilaz-meta-box-test-beta',
	'title'	   => __('Test Beta', 'dilaz-metabox'),
	'pages'    => array('post', 'page'),
	'context'  => 'normal',
	'priority' => 'low',
	'type'     => 'metabox_set'
);

	# TAB - Beta Tab 1
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'beta_tab_1',
		'title' => __('Beta Tab 1', 'dilaz-metabox'),
		'icon'  => 'fa-anchor',
		'type'  => 'metabox_tab'
	);
		
		# FIELDS - Beta Tab 1
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'beta_select_option',
			'name'	  => __('Select Something:', 'dilaz-metabox'),
			'desc'	  => __('Select an option below.', 'dilaz-metabox'),
			'type'	  => 'select',
			'options' => array(
				'0' => __('Default', 'dilaz-metabox'),
				'1' => __('One', 'dilaz-metabox'),
				'2' => __('Two', 'dilaz-metabox'),
				'3' => __('Three', 'dilaz-metabox'),
				'4' => __('Four', 'dilaz-metabox'),
				'5' => __('Five', 'dilaz-metabox')
			),
			'std' => 'default'
		);
		
		
		
		
		
return $dilaz_meta_boxes;