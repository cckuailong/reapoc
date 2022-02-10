<?php
// Heading Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'font_typography',
	'selector' 		=> ".fl-node-$id .pp-fancy-heading-title",
) );
?>
.fl-node-<?php echo $id; ?> {
    text-align: <?php echo $settings->font_typography['text_align']; ?>;
}
.fl-node-<?php echo $id; ?> .pp-fancy-heading-title {
	padding: 0 15px;
    <?php if ( $settings->heading_type == 'gradient' ) { ?>
        color: #<?php echo $settings->primary_color; ?>;
        background-image: -webkit-linear-gradient(92deg, #<?php echo $settings->primary_color; ?>, #<?php echo $settings->secondary_color; ?>);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        -webkit-animation: pp-hue <?php echo $settings->animation_speed; ?>s infinite linear;
        animation: pp-hue <?php echo $settings->animation_speed; ?>s infinite linear;
    <?php } ?>
    <?php if ( $settings->heading_type == 'solid' ) { ?>
        color: #<?php echo $settings->primary_color; ?>;
        -webkit-animation: pp-hue <?php echo $settings->animation_speed; ?>s infinite linear;
        animation: pp-hue <?php echo $settings->animation_speed; ?>s infinite linear;
    <?php } ?>
    <?php if ( $settings->heading_type == 'clip' ) { ?>
        background-image: url(<?php echo $settings->bg_image_src; ?>);
        background-repeat: <?php echo $settings->bg_repeat; ?>;
        background-position: <?php echo $settings->bg_position; ?>;
        background-attachment: <?php echo $settings->bg_attachment; ?>;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    <?php } ?>
    <?php if ( $settings->heading_type == 'fade' ) { ?>
        color: #<?php echo $settings->primary_color; ?>;
        -webkit-animation: pp-fade <?php echo $settings->animation_speed; ?>s infinite linear;
        animation: pp-fade <?php echo $settings->animation_speed; ?>s infinite linear;
    <?php } ?>
    <?php if ( $settings->heading_type == 'rotate' ) { ?>
        color: #<?php echo $settings->primary_color; ?>;
        -webkit-animation: pp-rotate <?php echo $settings->animation_speed; ?>s infinite linear;
        animation: pp-rotate <?php echo $settings->animation_speed; ?>s infinite linear;
    <?php } ?>
}
