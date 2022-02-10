<?php 
// Form Border - Settings
if( $settings->form_show_border == 'yes' ) {
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'form_border_group',
		'selector' 		=> ".fl-node-$id .pp-cf7-content",
	) );
}
// Button Border - Settings
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'button_border_group',
	'selector' 		=> ".fl-node-$id .pp-cf7-content input.wpcf7-submit",
) );

// Validation Error border - Settings
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'form_error_field_border_group',
	'selector' 		=> ".fl-node-$id .pp-cf7-content .wpcf7-response-output",
) );


// Title Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'title_typography',
	'selector' 		=> ".fl-node-$id .pp-cf7-form-title",
) );

// Description Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'description_typography',
	'selector' 		=> ".fl-node-$id .pp-cf7-form-description",
) );

// Label Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'label_typography',
	'selector' 		=> ".fl-node-$id .pp-cf7-content form p",
) );

// Input Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'input_typography',
	'selector' 		=> ".fl-node-$id .pp-cf7-content .wpcf7-text,
						.fl-node-$id .pp-cf7-content .wpcf7-email,
						.fl-node-$id .pp-cf7-content .wpcf7-textarea,
						.fl-node-$id .pp-cf7-content .wpcf7-quiz,
						.fl-node-$id .pp-cf7-content .wpcf7-number",
) );

// Button Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'button_typography',
	'selector' 		=> ".fl-node-$id .pp-cf7-content input.wpcf7-submit",
) );

?>
.fl-node-<?php echo $id; ?> .pp-cf7-content {
    <?php if( $settings->form_bg_type == 'color' ) { ?>
        <?php if( $settings->form_bg_color ) { ?>
			background-color: <?php echo pp_get_color_value( $settings->form_bg_color ); ?>;
        <?php } ?>
    <?php } ?>
    <?php if( $settings->form_bg_type == 'image' ) { ?>
        <?php if( $settings->form_bg_image ) { ?>background-image: url('<?php echo $settings->form_bg_image_src; ?>');<?php } ?>
        <?php if( $settings->form_bg_repeat ) { ?>background-repeat: <?php echo $settings->form_bg_repeat; ?>;<?php } ?>
        <?php if( $settings->form_bg_size ) { ?>background-size: <?php echo $settings->form_bg_size; ?>;<?php } ?>
    <?php } ?>
    <?php if( $settings->form_padding ) { ?>padding: <?php echo $settings->form_padding; ?>px;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-cf7-form-title {
    <?php if( $settings->title_color ) { ?>color: #<?php echo $settings->title_color; ?>;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-cf7-form-description {
    <?php if( $settings->description_color ) { ?>color: #<?php echo $settings->description_color; ?>;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-cf7-content form p {
    <?php if( $settings->form_label_color ) { ?>color: #<?php echo $settings->form_label_color; ?> !important;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-text,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-email,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-textarea,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-quiz,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-number {
    <?php if( $settings->input_field_text_color ) { ?>color: #<?php echo $settings->input_field_text_color; ?>;<?php } ?>
    <?php if( $settings->input_field_bg_color ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->input_field_bg_color ); ?>;
	<?php } else { ?> 
		background-color: transparent;
	<?php }?>
    <?php if( $settings->input_field_border_color ) { ?>border-color: #<?php echo $settings->input_field_border_color; ?>;<?php } ?>
        <?php if( $settings->input_field_border_radius >= 0 ) { ?>
            border-radius: <?php echo $settings->input_field_border_radius; ?>px;
            -moz-border-radius: <?php echo $settings->input_field_border_radius; ?>px;
            -webkit-border-radius: <?php echo $settings->input_field_border_radius; ?>px;
            -ms-border-radius: <?php echo $settings->input_field_border_radius; ?>px;
            -o-border-radius: <?php echo $settings->input_field_border_radius; ?>px;
        <?php } ?>
    <?php if( $settings->input_field_border_width >= 0 ) { ?>
        border-width: 0px;
        <?php echo $settings->input_field_border_position; ?>-width: <?php echo $settings->input_field_border_width; ?>px;
    <?php } ?>
    <?php if( $settings->input_height >= 0 ) { ?>height: <?php echo $settings->input_height; ?>px;<?php } ?>
    <?php if( $settings->input_width >= 0 ) { ?>width: <?php echo $settings->input_width; ?>%;<?php } ?>
    <?php if( $settings->input_field_padding >= 0 ) { ?>padding: <?php echo $settings->input_field_padding; ?>px;<?php } ?>
    <?php if ( $settings->input_field_border_position == 'border-bottom' ) { ?>
        padding-left: 0;
        padding-right: 0;
    <?php } ?>
    <?php if( $settings->input_field_margin_top >= 0 ) { ?>margin-top: <?php echo $settings->input_field_margin_top; ?>px;<?php } ?>
    <?php if( $settings->input_field_margin >= 0 ) { ?>margin-bottom: <?php echo $settings->input_field_margin; ?>px;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-text:focus,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-email:focus,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-textarea:focus,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-quiz:focus,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-number:focus {
    border-color: #<?php echo $settings->input_field_border_focus; ?>;
}

.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-textarea {
    <?php if( $settings->textarea_height >= 0  ) { ?>height: <?php echo $settings->textarea_height; ?>px;<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-file {
    <?php if( $settings->input_height >= 0 ) { ?>height: <?php echo $settings->input_height; ?>px;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-text,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-email,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-textarea,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-quiz,
.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-number {
    <?php if( $settings->input_field_box_shadow == 'yes' ) { ?>
        box-shadow: <?php echo ($settings->shadow_direction == 'inset') ? $settings->shadow_direction : ''; ?> 0 0 10px #<?php echo $settings->shadow_color; ?>;
        -moz-box-shadow: <?php echo ($settings->shadow_direction == 'inset') ? $settings->shadow_direction : ''; ?> 0 0 10px #<?php echo $settings->shadow_color; ?>;
        -webkit-box-shadow: <?php echo ($settings->shadow_direction == 'inset') ? $settings->shadow_direction : ''; ?> 0 0 10px #<?php echo $settings->shadow_color; ?>;
        -ms-box-shadow: <?php echo ($settings->shadow_direction == 'inset') ? $settings->shadow_direction : ''; ?> 0 0 10px #<?php echo $settings->shadow_color; ?>;
        -o-box-shadow: <?php echo ($settings->shadow_direction == 'inset') ? $settings->shadow_direction : ''; ?> 0 0 10px #<?php echo $settings->shadow_color; ?>;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content form p:before,
.fl-node-<?php echo $id; ?> .pp-cf7-content form p:after {
    content: "";
    display: table;
}
.fl-node-<?php echo $id; ?> .pp-cf7-content form p:after {
    clear: both;
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input.wpcf7-submit {
    <?php if ( ! empty( $settings->button_text_color ) ) { ?>
		color: #<?php echo $settings->button_text_color; ?>;
	<?php } ?>
    <?php if ( ! empty( $settings->button_bg_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->button_bg_color ); ?>;
	<?php } ?>
    <?php if( $settings->button_padding_top_bottom  >= 0 ) { ?>
        padding-top: <?php echo $settings->button_padding_top_bottom; ?>px;
        padding-bottom: <?php echo $settings->button_padding_top_bottom; ?>px;
    <?php } ?>
    <?php if( $settings->button_padding_left_right >= 0  ) { ?>
        padding-left: <?php echo $settings->button_padding_left_right; ?>px;
        padding-right: <?php echo $settings->button_padding_left_right; ?>px;
    <?php } ?>
    <?php if( $settings->button_alignment != 'center' || $settings->button_alignment != 'none' ) { ?>
		float: <?php echo $settings->button_alignment; ?>;
	<?php } ?>
    <?php if( $settings->button_alignment == 'center' || $settings->button_alignment == 'none' ) { ?>
        display: block;
        margin: 0 auto;
		float: none;
    <?php } ?>
    width: <?php echo ($settings->button_width == 'true') ? '100%' : $settings->button_width_size . 'px'; ?>;
}

.fl-node-<?php echo $id; ?> .pp-cf7-content input.wpcf7-submit:hover {
	<?php if ( ! empty( $settings->button_hover_text_color ) ) { ?>
		color: #<?php echo $settings->button_hover_text_color; ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->button_hover_bg_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->button_hover_bg_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->button_border_color_hover ) ) { ?>
		border-color: #<?php echo $settings->button_border_color_hover; ?>;
	<?php } ?>
}


.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-response-output {
    <?php if( $settings->form_error_field_background_color ) { ?>background-color: <?php echo pp_get_color_value( $settings->form_error_field_background_color ); ?>;<?php } ?>
    <?php if( $settings->validation_error_color ) { ?>color: #<?php echo $settings->validation_error_color; ?>;<?php } ?>
    <?php if($settings->validation_error == 'none') { ?>display: none !important;<?php } ?>
    <?php if( $settings->validation_error_font_size ) { ?>font-size: <?php echo $settings->validation_error_font_size; ?>px;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-cf7-content .wpcf7-not-valid-tip {
    <?php if( $settings->validation_message ) { ?>display: <?php echo $settings->validation_message; ?>;<?php } ?>
    <?php if( $settings->validation_message_color ) { ?>color: #<?php echo $settings->validation_message_color; ?>;<?php } ?>
}


.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=text]::-webkit-input-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=text]:-moz-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=text]::-moz-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=text]:-ms-input-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=tel]::-webkit-input-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=tel]:-moz-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=tel]::-moz-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
	color: transparent;
	opacity: 0;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=tel]:-ms-input-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
	<?php } else { ?>
	color: transparent;
	opacity: 0;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=email]::-webkit-input-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=email]:-moz-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
	<?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=email]::-moz-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content input[type=email]:-ms-input-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-cf7-content textarea::-webkit-input-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content textarea:-moz-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
	<?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content textarea::-moz-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
	<?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-cf7-content textarea:-ms-input-placeholder {
    <?php if( $settings->placeholder_color && $settings->show_placeholder == 'yes' ) { ?>
    color: #<?php echo $settings->placeholder_color; ?>;
	<?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
