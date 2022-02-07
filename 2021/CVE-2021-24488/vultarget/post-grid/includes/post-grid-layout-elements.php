<?php
if ( ! defined('ABSPATH')) exit;  // if direct access



add_action('post_grid_layout_element_option_wrapper_start','post_grid_layout_element_option_wrapper_start');


function post_grid_layout_element_option_wrapper_start($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $wrapper_id = isset($element_data['wrapper_id']) ? $element_data['wrapper_id'] : '';
    $wrapper_class = isset($element_data['wrapper_class']) ? $element_data['wrapper_class'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';


    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';


    ?>
    <div class="item wrapper_start">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Wrapper start','post-grid'); ?></span>

            <span class="handle-start"><i class="fas fa-level-up-alt"></i></span>

        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'wrapper_id',
                'parent' => $input_name.'[wrapper_start]',
                'title'		=> __('Wrapper id','post-grid'),
                'details'	=> __('Write wrapper id, ex: my-unique-id.','post-grid'),
                'type'		=> 'text',
                'value'		=> $wrapper_id,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'wrapper_class',
                'parent' => $input_name.'[wrapper_start]',
                'title'		=> __('Wrapper class','post-grid'),
                'details'	=> __('Write wrapper class, ex: layer-thumbnail','post-grid'),
                'type'		=> 'text',
                'value'		=> $wrapper_class,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[wrapper_start]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[wrapper_start]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[wrapper_start]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}




add_action('post_grid_layout_element_wrapper_start', 'post_grid_layout_element_wrapper_start', 10);
function post_grid_layout_element_wrapper_start($args){

    $index = isset($args['index']) ? $args['index'] : '';
    $element_class = !empty($index) ? 'element_'.$index : '';

    //echo '<pre>'.var_export($args, true).'</pre>';
    $element = isset($args['element']) ? $args['element'] : array();
    $wrapper_class = isset($element['wrapper_class']) ? $element['wrapper_class'] : '';
    $wrapper_id = isset($element['wrapper_id']) ? $element['wrapper_id'] : '';



    ?>
    <div class="<?php echo $wrapper_class; ?> <?php echo $element_class; ?>" id="<?php echo $wrapper_id; ?>">
    <?php

}


add_action('post_grid_layout_element_css_wrapper_start', 'post_grid_layout_element_css_wrapper_start', 10);
function post_grid_layout_element_css_wrapper_start($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';

    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';

    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
</style>
    <?php
}





add_action('post_grid_layout_element_option_wrapper_end','post_grid_layout_element_option_wrapper_end');


function post_grid_layout_element_option_wrapper_end($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();

    $wrapper_id = isset($element_data['wrapper_id']) ? $element_data['wrapper_id'] : '';

    ?>
    <div class="item wrapper_end">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Wrapper end','post-grid'); ?></span>
            <span class="handle-end"><i class="fas fa-level-down-alt"></i></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'wrapper_id',
                'wraper_class'		=> 'hidden',

                'parent' => $input_name.'[wrapper_end]',
                'title'		=> __('Wrapper id','post-grid'),
                'details'	=> __('Write wrapper id, ex: div, p, span.','post-grid'),
                'type'		=> 'hidden',
                'value'		=> $wrapper_id,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);





            ?>

        </div>
    </div>
    <?php

}



add_action('post_grid_layout_element_wrapper_end', 'post_grid_layout_element_wrapper_end', 10);
function post_grid_layout_element_wrapper_end($args){


    ?>
    </div>
    <?php

}




add_action('post_grid_layout_element_option_custom_text','post_grid_layout_element_option_custom_text');
function post_grid_layout_element_option_custom_text($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $text = isset($element_data['text']) ? $element_data['text'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Custom text','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[custom_text]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text',
                'css_id'		=> $element_index.'_text',
                'parent' => $input_name.'[custom_text]',
                'title'		=> __('Custom text','post-grid'),
                'details'	=> __('Write custom text.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $text,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_custom_text',
                'parent' => $input_name.'[custom_text]',
                'title'		=> __('Color','post-grid'),
                'details'	=> __('Title text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[custom_text]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Set font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[custom_text]',
                'title'		=> __('Font family','post-grid'),
                'details'	=> __('Set font family.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[custom_text]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[custom_text]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[custom_text]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[custom_text]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('post_grid_layout_element_custom_text', 'post_grid_layout_element_custom_text');
function post_grid_layout_element_custom_text($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $title = get_the_title($post_id);

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $text = isset($element['text']) ?  $element['text'] : '';

    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> custom_text ">
        <?php echo esc_html($text); ?>
    </div>
    <?php
}



add_action('post_grid_layout_element_css_custom_text', 'post_grid_layout_element_css_custom_text', 10);
function post_grid_layout_element_css_custom_text($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';

    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';

    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
</style>
    <?php
}



add_action('post_grid_layout_element_option_title','post_grid_layout_element_option_title');
function post_grid_layout_element_option_title($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $link_to = isset($element_data['link_to']) ? $element_data['link_to'] : '';
    $link_target = isset($element_data['link_target']) ? $element_data['link_target'] : '';
    $char_limit = isset($element_data['char_limit']) ? $element_data['char_limit'] : 0;

    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post title','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[title]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'link_to',
                'css_id'		=> $element_index.'_link_to',
                'parent' => $input_name.'[title]',
                'title'		=> __('Link to','post-grid'),
                'details'	=> __('Choose option to link title.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_to,
                'default'		=> 'post_link',
                'args'		=> apply_filters('post_grid_link_to_args',
					array(
						'post_link'=> __('Post link', 'post-grid'),
						'none'=> __('None', 'post-grid'),
					)
                ),
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'link_target',
                'css_id'		=> $element_index.'_link_target',
                'parent' => $input_name.'[title]',
                'title'		=> __('Link target','post-grid'),
                'details'	=> __('Choose option link target.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_target,
                'default'		=> 'post_link',
                'args'		=> array(
                    '_blank'=> __('_blank', 'post-grid'),
                    '_parent'=> __('_parent', 'post-grid'),
                    '_self'=> __('_self', 'post-grid'),
                    '_top'=> __('_top', 'post-grid'),

                ),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'char_limit',
                'css_id'		=> $element_index.'_char_limit',
                'parent' => $input_name.'[title]',
                'title'		=> __('Character limit','post-grid'),
                'details'	=> __('Set character limit.','post-grid'),
                'type'		=> 'text',
                'value'		=> $char_limit,
                'default'		=> '20',
                'placeholder'		=> '5',
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_title_color',
                'parent' => $input_name.'[title]',
                'title'		=> __('Color','post-grid'),
                'details'	=> __('Title text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[title]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Set font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '20px',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[title]',
                'title'		=> __('Font family','post-grid'),
                'details'	=> __('Set font family.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[title]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '10px 0',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[title]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);




            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[title]',
                'title'		=> __('CSS','post-grid'),
                'details'	=> __('Set css.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[title]',
                'title'		=> __('CSS hover','post-grid'),
                'details'	=> __('Set hover css.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('post_grid_layout_element_title', 'post_grid_layout_element_title');

function post_grid_layout_element_title($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';

    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $layout_id  = isset($args['layout_id']) ? $args['layout_id'] : '';

    $post_link = get_permalink($post_id);

    $post = get_post( $post_id );
    $title = isset( $post->post_title ) ? $post->post_title : '';


    //$title = get_the_title($post_id);

    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $char_limit = isset($element['char_limit']) ? (int) $element['char_limit'] : 0;
    $char_end = isset($element['char_end']) ? $element['char_end'] : '...';
    $link_to = isset($element['link_to']) ? $element['link_to'] : 'post_link';


    if($char_limit > 0){
        $title = wp_trim_words($title, $char_limit, $char_end);
    }


    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> title ">
        <?php if($link_to == 'post_link'): ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_link); ?>"><?php echo esc_html($title); ?></a>

		<?php elseif($link_to == 'custom_link'):

            $post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);
			$thumb_custom_url = !empty($post_grid_post_settings['thumb_custom_url']) ? $post_grid_post_settings['thumb_custom_url'] : $post_link;

			//var_dump($thumb_custom_url);

            ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($thumb_custom_url); ?>"><?php echo esc_html($title); ?></a>

		<?php else: ?>
            <?php echo esc_html($title); ?>
        <?php endif; ?>


    </div>
    <?php
}


add_action('post_grid_layout_element_css_title', 'post_grid_layout_element_css_title', 10);
function post_grid_layout_element_css_title($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';
    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';

    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
}
</style>
    <?php
}





add_action('post_grid_layout_element_option_title_link','post_grid_layout_element_option_title_link');


function post_grid_layout_element_option_title_link($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $link_to = isset($element_data['link_to']) ? $element_data['link_to'] : '';
    $link_target = isset($element_data['link_target']) ? $element_data['link_target'] : '';
    $char_limit = isset($element_data['char_limit']) ? $element_data['char_limit'] : 0;

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post title with link','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'link_to',
                'css_id'		=> $element_index.'_link_to',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('Link to','post-grid'),
                'details'	=> __('Choose option to link title.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_to,
                'default'		=> 'none',
                'args'		=> apply_filters('post_grid_link_to_args',
					array(
						'post_link'=> __('Post link', 'post-grid'),
						'none'=> __('None', 'post-grid'),
					)
				),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'link_target',
                'css_id'		=> $element_index.'_link_target',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('Link target','post-grid'),
                'details'	=> __('Choose option link target.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_target,
                'default'		=> 'post_link',
                'args'		=> array(
                    '_blank'=> __('_blank', 'post-grid'),
                    '_parent'=> __('_parent', 'post-grid'),
                    '_self'=> __('_self', 'post-grid'),
                    '_top'=> __('_top', 'post-grid'),

                ),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'char_limit',
                'css_id'		=> $element_index.'_char_limit',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('Character limit','post-grid'),
                'details'	=> __('Set character limit.','post-grid'),
                'type'		=> 'text',
                'value'		=> $char_limit,
                'default'		=> '',
                'placeholder'		=> '5',
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('Color','post-grid'),
                'details'	=> __('Title text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Set font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('Font family','post-grid'),
                'details'	=> __('Set font family.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('CSS','post-grid'),
                'details'	=> __('Set css.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[title_link]',
                'title'		=> __('CSS hover','post-grid'),
                'details'	=> __('Set hover css.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);




            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('post_grid_layout_element_title_link', 'post_grid_layout_element_title_link');

function post_grid_layout_element_title_link($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';

    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $layout_id  = isset($args['layout_id']) ? $args['layout_id'] : '';


    $title = get_the_title($post_id);
    $post_link = get_permalink($post_id);

    $link_to = isset($element['link_to']) ? $element['link_to'] : 'post_link';
    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $char_limit = isset($element['char_limit']) ? (int) $element['char_limit'] : 0;
    $char_end = isset($element['char_end']) ? $element['char_end'] : '...';


    if($char_limit > 0){
        $title = wp_trim_words($title, $char_limit, $char_end);
    }


    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> title_link ">
        <?php if($link_to == 'post_link'): ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_link); ?>"><?php echo esc_html($title); ?></a>
		<?php elseif($link_to == 'custom_link'):

			$post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);
			$thumb_custom_url = !empty($post_grid_post_settings['thumb_custom_url']) ? $post_grid_post_settings['thumb_custom_url'] : $post_link;

			//var_dump($thumb_custom_url);

			?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($thumb_custom_url); ?>"><?php echo esc_html($title); ?></a>
        <?php else: ?>
            <?php echo esc_html($title); ?>
        <?php endif; ?>
    </div>
    <?php
}



add_action('post_grid_layout_element_css_title_link', 'post_grid_layout_element_css_title_link', 10);
function post_grid_layout_element_css_title_link($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';

    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';

    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
}
</style>
    <?php
}



add_action('post_grid_layout_element_option_content','post_grid_layout_element_option_content');


function post_grid_layout_element_option_content($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Content','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[content]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[content]',
                'title'		=> __('Font family','post-grid'),
                'details'	=> __('Set font family.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[content]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[content]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[content]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[content]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);


            ?>

        </div>
    </div>
    <?php

}




add_action('post_grid_layout_element_content', 'post_grid_layout_element_content');

function post_grid_layout_element_content($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';

    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $layout_id  = isset($args['layout_id']) ? $args['layout_id'] : '';

    $post = get_post( $post_id );
    $post_content = isset( $post->post_content ) ? $post->post_content : '';


    //$post_content = get_the_content($post_id);


    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';

    $post_content = wpautop($post_content);
    $post_content = do_shortcode($post_content);


    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> content ">
        <?php echo ($post_content); ?>
    </div>
    <?php
}


add_action('post_grid_layout_element_option_excerpt','post_grid_layout_element_option_excerpt');
function post_grid_layout_element_option_excerpt($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $excerpt_source = isset($element_data['excerpt_source']) ? $element_data['excerpt_source'] : '';
//    $remove_html = isset($element_data['remove_html']) ? $element_data['remove_html'] : '';
//    $remove_shortcodes = isset($element_data['remove_shortcodes']) ? $element_data['remove_shortcodes'] : '';

    $link_target = isset($element_data['link_target']) ? $element_data['link_target'] : '';
    $char_limit = isset($element_data['char_limit']) ? $element_data['char_limit'] : 0;
    $read_more_text = isset($element_data['read_more_text']) ? $element_data['read_more_text'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post excerpt','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'excerpt_source',
                'css_id'		=> $element_index.'_excerpt_source',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Excerpt source','post-grid'),
                'details'	=> __('Choose excerpt source.','post-grid'),
                'type'		=> 'select',
                'value'		=> $excerpt_source,
                'default'		=> 'post_link',
                'args'		=> array(
                    'excerpt_field'=> __('Excerpt field', 'post-grid'),
                    'content'=> __('Content', 'post-grid'),
                    'excerpt_content'=> __('Excerpt first then Content', 'post-grid'),


                ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'char_limit',
                'css_id'		=> $element_index.'_char_limit',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Word limit','post-grid'),
                'details'	=> __('Set word limit.','post-grid'),
                'type'		=> 'text',
                'value'		=> $char_limit,
                'default'		=> '',
                'placeholder'		=> '20',
            );

            $settings_tabs_field->generate_field($args);

//
//            $args = array(
//                'id'		=> 'remove_html',
//                'css_id'		=> $element_index.'_remove_html',
//                'parent' => $input_name.'[excerpt]',
//                'title'		=> __('Remove HTML','post-grid'),
//                'details'	=> __('Choose option to remove html on excerpt.','post-grid'),
//                'type'		=> 'select',
//                'value'		=> $remove_html,
//                'default'		=> 'yes',
//                'args'		=> array(
//                    'yes'=> __('Yes', 'post-grid'),
//                    'no'=> __('No', 'post-grid'),
//
//
//                ),
//            );
//
//            $settings_tabs_field->generate_field($args);
//
//
//            $args = array(
//                'id'		=> 'remove_shortcodes',
//                'css_id'		=> $element_index.'_remove_shortcodes',
//                'parent' => $input_name.'[excerpt]',
//                'title'		=> __('Remove shortcodes','post-grid'),
//                'details'	=> __('Choose option to remove shortcodes on excerpt.','post-grid'),
//                'type'		=> 'select',
//                'value'		=> $remove_shortcodes,
//                'default'		=> 'yes',
//                'args'		=> array(
//                    'yes'=> __('Yes', 'post-grid'),
//                    'no'=> __('No', 'post-grid'),
//                ),
//            );
//
//            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'read_more_text',
                'css_id'		=> $element_index.'_read_more_text',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Read more text','post-grid'),
                'details'	=> __('Custom read more text.','post-grid'),
                'type'		=> 'text',
                'value'		=> $read_more_text,
                'default'		=> '',
                'placeholder'		=> 'Read more',
            );

            $settings_tabs_field->generate_field($args);
            $args = array(
                'id'		=> 'link_target',
                'css_id'		=> $element_index.'_link_target',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Link target','post-grid'),
                'details'	=> __('Choose option link target.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_target,
                'default'		=> 'post_link',
                'args'		=> array(
                    '_blank'=> __('_blank', 'post-grid'),
                    '_parent'=> __('_parent', 'post-grid'),
                    '_self'=> __('_self', 'post-grid'),
                    '_top'=> __('_top', 'post-grid'),

                ),
            );

            $settings_tabs_field->generate_field($args);






            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '10px 0',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Text Color','post-grid'),
                'details'	=> __('Choose text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Set font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '15px',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[excerpt]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('post_grid_layout_element_excerpt', 'post_grid_layout_element_excerpt');

function post_grid_layout_element_excerpt($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';

    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $layout_id  = isset($args['layout_id']) ? $args['layout_id'] : '';




    //$post_excerpt = get_the_excerpt($post_id);


    $post_link = get_permalink($post_id);
    $excerpt_source = !empty($element['excerpt_source']) ? $element['excerpt_source'] : 'excerpt_content';

    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $char_limit = !empty($element['char_limit']) ? (int) $element['char_limit'] : 50;
    $read_more_text = isset($element['read_more_text']) ? $element['read_more_text'] : '';

    //var_dump($excerpt_source);

    if($excerpt_source == 'excerpt_field'){

        $the_post = get_post($post_id);
        $post_excerpt = isset($the_post->post_excerpt) ? $the_post->post_excerpt : '';
        //$post_content = strip_shortcodes( $post_content );
        //$post_excerpt = excerpt_remove_blocks( $post_content );



    }elseif($excerpt_source == 'content'){

        $the_post = get_post($post_id);
        $post_content = isset($the_post->post_content) ? $the_post->post_content : '';
        $post_content = strip_shortcodes( $post_content );

        if(function_exists('excerpt_remove_blocks')){
            $post_excerpt = excerpt_remove_blocks( $post_content );
        }



    }elseif($excerpt_source == 'excerpt_content'){

        $the_post = get_post($post_id);
        $post_excerpt = isset($the_post->post_excerpt) ? $the_post->post_excerpt : '';
        $post_content = isset($the_post->post_content) ? $the_post->post_content : '';

        $post_excerpt = !empty($post_excerpt) ? $post_excerpt : $post_content;
        $post_excerpt = strip_shortcodes( $post_excerpt );
        if(function_exists('excerpt_remove_blocks')){
            $post_excerpt = excerpt_remove_blocks( $post_excerpt );
        }


    }




    if($char_limit > 0){
        $post_excerpt = wp_trim_words($post_excerpt, $char_limit, '');
    }

    //var_dump($post_excerpt);


    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> excerpt ">
        <?php echo ($post_excerpt); ?>
        <?php
        if(!empty($read_more_text)):
            ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_link); ?>"><?php echo esc_html($read_more_text); ?></a>
        <?php
        endif;
        ?>
    </div>
    <?php
}


add_action('post_grid_layout_element_css_excerpt', 'post_grid_layout_element_css_excerpt', 10);
function post_grid_layout_element_css_excerpt($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';

    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';
    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
}
</style>
    <?php
}



add_action('post_grid_layout_element_option_excerpt_read_more','post_grid_layout_element_option_excerpt_read_more');
function post_grid_layout_element_option_excerpt_read_more($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $excerpt_source = isset($element_data['excerpt_source']) ? $element_data['excerpt_source'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $link_target = isset($element_data['link_target']) ? $element_data['link_target'] : '';
    $char_limit = isset($element_data['char_limit']) ? $element_data['char_limit'] : 0;
    $read_more_text = isset($element_data['read_more_text']) ? $element_data['read_more_text'] : __('Read more', 'post-grid');

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $color = isset($element_data['color']) ? $element_data['color'] : '';

    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Excerpt read more','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'excerpt_source',
                'css_id'		=> $element_index.'_excerpt_source',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Excerpt source','post-grid'),
                'details'	=> __('Choose excerpt source.','post-grid'),
                'type'		=> 'select',
                'value'		=> $excerpt_source,
                'default'		=> 'post_link',
                'args'		=> array(
                    'excerpt_field'=> __('Excerpt field', 'post-grid'),
                    'content'=> __('Content', 'post-grid'),
                    'excerpt_content'=> __('Excerpt first then Content', 'post-grid'),


                ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'char_limit',
                'css_id'		=> $element_index.'_char_limit',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Word limit','post-grid'),
                'details'	=> __('Set word limit.','post-grid'),
                'type'		=> 'text',
                'value'		=> $char_limit,
                'default'		=> '',
                'placeholder'		=> '20',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'read_more_text',
                'css_id'		=> $element_index.'_read_more_text',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Read more text','post-grid'),
                'details'	=> __('Custom read more text.','post-grid'),
                'type'		=> 'text',
                'value'		=> $read_more_text,
                'default'		=> '',
                'placeholder'		=> 'Read more',
            );

            $settings_tabs_field->generate_field($args);
            $args = array(
                'id'		=> 'link_target',
                'css_id'		=> $element_index.'_link_target',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Link target','post-grid'),
                'details'	=> __('Choose option link target.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_target,
                'default'		=> 'post_link',
                'args'		=> array(
                    '_blank'=> __('_blank', 'post-grid'),
                    '_parent'=> __('_parent', 'post-grid'),
                    '_self'=> __('_self', 'post-grid'),
                    '_top'=> __('_top', 'post-grid'),

                ),
            );

            $settings_tabs_field->generate_field($args);






            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Text Color','post-grid'),
                'details'	=> __('Choose text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Set font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '16px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[excerpt_read_more]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('post_grid_layout_element_excerpt_read_more', 'post_grid_layout_element_excerpt_read_more');

function post_grid_layout_element_excerpt_read_more($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';

    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $layout_id  = isset($args['layout_id']) ? $args['layout_id'] : '';



    $post_link = get_permalink($post_id);

    $excerpt_source = !empty($element['excerpt_source']) ? $element['excerpt_source'] : 'excerpt_content';

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $char_limit = isset($element['char_limit']) ? (int) $element['char_limit'] : 0;
    $read_more_text = isset($element['read_more_text']) ? $element['read_more_text'] : '';
    $link_target = isset($element['link_target']) ? $element['link_target'] : '';


    if($excerpt_source == 'excerpt_field'){

        $the_post = get_post($post_id);
        $post_excerpt = isset($the_post->post_excerpt) ? $the_post->post_excerpt : '';
        //$post_content = strip_shortcodes( $post_content );
        //$post_excerpt = excerpt_remove_blocks( $post_content );


    }elseif($excerpt_source == 'content'){

        $the_post = get_post($post_id);
        $post_content = isset($the_post->post_content) ? $the_post->post_content : '';
        $post_content = strip_shortcodes( $post_content );
        if(function_exists('excerpt_remove_blocks')){
            $post_excerpt = excerpt_remove_blocks( $post_content );
        }


    }elseif($excerpt_source == 'excerpt_content'){

        $the_post = get_post($post_id);
        $post_excerpt = isset($the_post->post_excerpt) ? $the_post->post_excerpt : '';
        $post_content = isset($the_post->post_content) ? $the_post->post_content : '';

        $post_excerpt = !empty($post_excerpt) ? $post_excerpt : $post_content;
        $post_excerpt = strip_shortcodes( $post_excerpt );
        if(function_exists('excerpt_remove_blocks')){
            $post_excerpt = excerpt_remove_blocks( $post_excerpt );
        }


    }

    if($char_limit > 0){
        $post_excerpt = wp_trim_words($post_excerpt, $char_limit, '');
    }


    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> excerpt_read_more ">
        <?php echo ($post_excerpt); ?>
        <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_link); ?>"><?php echo esc_html($read_more_text); ?></a>

    </div>
    <?php
}





add_action('post_grid_layout_element_css_excerpt_read_more', 'post_grid_layout_element_css_excerpt_read_more', 10);
function post_grid_layout_element_css_excerpt_read_more($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';

    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';
    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
}
</style>
    <?php
}




add_action('post_grid_layout_element_option_read_more','post_grid_layout_element_option_read_more');
function post_grid_layout_element_option_read_more($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $link_to = isset($element_data['link_to']) ? $element_data['link_to'] : '';
    $link_target = isset($element_data['link_target']) ? $element_data['link_target'] : '';
    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $read_more_text = isset($element_data['read_more_text']) ? $element_data['read_more_text'] : __('Read more', 'post-grid');

    $color = isset($element_data['color']) ? $element_data['color'] : '';

    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Read more','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[read_more]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'read_more_text',
                'css_id'		=> $element_index.'_read_more_text',
                'parent' => $input_name.'[read_more]',
                'title'		=> __('Read more text','post-grid'),
                'details'	=> __('Custom read more text.','post-grid'),
                'type'		=> 'text',
                'value'		=> $read_more_text,
                'default'		=> '',
                'placeholder'		=> 'Read more',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'link_to',
                'css_id'		=> $element_index.'_link_to',
                'parent' => $input_name.'[read_more]',
                'title'		=> __('Link to','post-grid'),
                'details'	=> __('Choose option to link title.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_to,
                'default'		=> 'none',
                'args'		=> apply_filters('post_grid_link_to_args',
					array(
						'post_link'=> __('Post link', 'post-grid'),
						'none'=> __('None', 'post-grid'),
					)
				),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'link_target',
                'css_id'		=> $element_index.'_link_target',
                'parent' => $input_name.'[read_more]',
                'title'		=> __('Link target','post-grid'),
                'details'	=> __('Choose option link target.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_target,
                'default'		=> 'post_link',
                'args'		=> array(
                    '_blank'=> __('_blank', 'post-grid'),
                    '_parent'=> __('_parent', 'post-grid'),
                    '_self'=> __('_self', 'post-grid'),
                    '_top'=> __('_top', 'post-grid'),

                ),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[read_more]',
                'title'		=> __('Text Color','post-grid'),
                'details'	=> __('Choose text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[read_more]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[read_more]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Set font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '16px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[read_more]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[read_more]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[read_more]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('post_grid_layout_element_read_more', 'post_grid_layout_element_read_more');

function post_grid_layout_element_read_more($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $post_link = get_permalink($post_id);


    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $read_more_text = isset($element['read_more_text']) ? $element['read_more_text'] : '';
    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $link_to = isset($element['link_to']) ? $element['link_to'] : 'post_link';


    ?>
    <?php if($link_to == 'post_link'): ?>
        <a class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> read_more " target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_link); ?>"><?php echo esc_html($read_more_text); ?></a>
	<?php elseif($link_to == 'custom_link'):

		$post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);
		$thumb_custom_url = !empty($post_grid_post_settings['thumb_custom_url']) ? $post_grid_post_settings['thumb_custom_url'] : $post_link;

		?>
        <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($thumb_custom_url); ?>"><?php echo esc_html($title); ?></a>
    <?php else: ?>
        <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> read_more ">
            <?php echo esc_html($read_more_text); ?>
        </div>
    <?php endif; ?>

    <?php
}



add_action('post_grid_layout_element_css_read_more', 'post_grid_layout_element_css_read_more', 10);
function post_grid_layout_element_css_read_more($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';

    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';
    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?> !important;;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?> !important; ;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?> !important;;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?> !important;;
<?php endif; ?>
}
</style>
    <?php
}




add_action('post_grid_layout_element_option_media','post_grid_layout_element_option_media');


function post_grid_layout_element_option_media($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $media_source = isset($element_data['media_source']) ? $element_data['media_source'] : array();
    $padding = isset($element_data['padding']) ? $element_data['padding'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';


    $media_sources_list = apply_filters('post_grid_media_source_list',
        array(
            'featured_image'=>__('Featured Image','post-grid'),
            'first_image'=>__('First images from content','post-grid'),
            'empty_thumb'=>__('Empty thumbnail','post-grid'),
            'siteorigin_first_image'=>__('SiteOrigin first image','post-grid'),
        )
    );


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>
            <span class="expand"><?php echo __('Media','post-grid'); ?></span>
        </div>
        <div class="element-options options">
            <?php


            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[media]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'media-css-class',
            );

            $settings_tabs_field->generate_field($args);


            ob_start();

            if(!empty($media_sources_list)){
                $media_source_new = array();

                if(!empty($media_source))
                    foreach ($media_source as $elementIndex => $argData){
                        $enable = isset($argData['enable']) ? $argData['enable'] :'';

                        if($enable == 'yes')
                            $media_source_new[$elementIndex]  = array('enable'=> $enable);

                    }
                $media_sources_new = array_replace($media_source_new, $media_sources_list);
                $media_sources_new = (!empty($media_sources_new)) ? $media_sources_new : $media_sources_list;
                ?>
                <div class="expandable sortable">
                    <?php

                    if(!empty($media_sources_new))
                        foreach ($media_sources_new as $source_id => $source_name ) {

                            if(is_array($source_name)) continue;

                            $media_source_options = array();
                            $source_data = isset($media_source[$source_id]) ? $media_source[$source_id] : array();
                            $source_enable = isset($media_source[$source_id]['enable']) ? $media_source[$source_id]['enable'] : '';

                            $media_source_options['index'] = $element_index;
                            $media_source_options['input_name'] = $input_name;
                            $media_source_options['source_data'] = $source_data;

                            ?>
                            <div class="item">
                                <div class="element-title header ">
                                    <span class="sort"><i class="fas fa-sort"></i></span>
                                    <?php
                                    if($source_enable == 'yes'):
                                        ?><i class="fas fa-check"></i><?php
                                    else:
                                        ?><i class="fas fa-times"></i><?php
                                    endif;?>
                                    <span class="expand"><?php echo $source_name; ?></span>
                                </div>
                                <div class="element-options options">
                                    <?php
                                    do_action('media_source_options_'.$source_id, $media_source_options);
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                </div>
                <?php
            }

            $html = ob_get_clean();

            $args = array(
                'id' => 'media_source',
                'title' => __('Media source', 'post-grid'),
                'details' => __('Choose media sources.', 'post-grid'),
                'type' => 'custom_html',
                'html' => $html,
            );

            $settings_tabs_field->generate_field($args);



            $media_height = isset($element_data['media_height']) ? $element_data['media_height'] : '';

            $media_height_large = isset($media_height['large']) ? $media_height['large'] : '';
            $media_height_large_type = isset($media_height['large_type']) ? $media_height['large_type'] : '';

            $media_height_medium = isset($media_height['medium']) ? $media_height['medium'] : '';
            $media_height_medium_type = isset($media_height['medium_type']) ? $media_height['medium_type'] : '';

            $media_height_small = isset($media_height['small']) ? $media_height['small'] : '';
            $media_height_small_type = isset($media_height['small_type']) ? $media_height['small_type'] : '';


            $args = array(
                'id'		=> 'media_height',
                'title'		=> __('Media height','post-grid'),
                'details'	=> __('Set media height.','post-grid'),
                'type'		=> 'option_group',
                'options'		=> array(

                    array(
                        'id'		=> 'large_type',
                        'css_id'		=> $element_index.'_text_align',
                        'parent'		=> $input_name.'[media][media_height]',
                        'title'		=> __('In desktop','post-grid'),
                        'details'	=> __('min-width: 1200px, ex: 280px','post-grid'),
                        'type'		=> 'select',
                        'value'		=> $media_height_large_type,
                        'default'		=> 'left',
                        'args'		=> array('auto_height'=> __('Auto height', 'post-grid'),'fixed_height'=> __('Fixed height', 'post-grid'),'max_height'=> __('Max height', 'post-grid') ),
                    ),
                    array(
                        'id'		=> 'large',
                        'parent'		=> $input_name.'[media][media_height]',
                        'title'		=> __('Height value','post-grid'),
                        //'details'	=> __('','post-grid'),
                        'type'		=> 'text',
                        'value'		=> $media_height_large,
                        'default'		=> '',
                        'placeholder'   => '280px',
                    ),
                    array(
                        'id'		=> 'medium_type',
                        'css_id'		=> $element_index.'_text_align',
                        'parent'		=> $input_name.'[media][media_height]',
                        'title'		=> __('In tablet & small desktop','post-grid'),
                        'details'	=> __('min-width: 992px, ex: 280px','post-grid'),
                        'type'		=> 'select',
                        'value'		=> $media_height_medium_type,
                        'default'		=> 'left',
                        'args'		=> array('auto_height'=> __('Auto height', 'post-grid'),'fixed_height'=> __('Fixed height', 'post-grid'),'max_height'=> __('Max height', 'post-grid') ),
                    ),
                    array(
                        'id'		=> 'medium',
                        'parent'		=> $input_name.'[media][media_height]',
                        'title'		=> __('Height value','post-grid'),
                        //'details'	=> __('','post-grid'),
                        'type'		=> 'text',
                        'value'		=> $media_height_medium,
                        'default'		=> '',
                        'placeholder'   => '280px',
                    ),
                    array(
                        'id'		=> 'small_type',
                        'css_id'		=> $element_index.'_text_align',
                        'parent'		=> $input_name.'[media][media_height]',
                        'title'		=> __('In mobile','post-grid'),
                        'details'	=> __('max-width: 768px, ex: 280px','post-grid'),
                        'type'		=> 'select',
                        'value'		=> $media_height_small_type,
                        'default'		=> 'left',
                        'args'		=> array('auto_height'=> __('Auto height', 'post-grid'),'fixed_height'=> __('Fixed height', 'post-grid'),'max_height'=> __('Max height', 'post-grid') ),
                    ),
                    array(
                        'id'		=> 'small',
                        'parent'		=> $input_name.'[media][media_height]',
                        'title'		=> __('Height value','post-grid'),
                        //'details'	=> __('','post-grid'),
                        'type'		=> 'text',
                        'value'		=> $media_height_small,
                        'default'		=> '',
                        'placeholder'   => '280px',
                    ),
                ),

            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[media]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'padding',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[media]',
                'title'		=> __('Padding','post-grid'),
                'details'	=> __('Set padding.','post-grid'),
                'type'		=> 'text',
                'value'		=> $padding,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[media]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[media]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);


            ?>

        </div>
    </div>
    <?php

}



add_action('post_grid_layout_element_media', 'post_grid_layout_element_media');

function post_grid_layout_element_media($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $media_source = isset($element['media_source']) ? $element['media_source'] : '';

    $featured_img_size = !empty($element['featured_img_size']) ? $element['featured_img_size'] : 'full';
    $thumb_linked = !empty($element['thumb_linked']) ? $element['thumb_linked'] : 'yes';

    $post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);


    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> element-media ">
        <?php

        $html_media = '';

        $is_image = false;
        foreach($media_source as $source_id => $source_info){

            $args['source_id'] = $source_id;
            $args['source_args'] = $source_info;
            $args['post_settings'] = $post_grid_post_settings;


            //var_dump($source_id);
           // var_dump($source_info);

            $is_enable = isset($source_info['enable']) ? $source_info['enable'] : '';

            $media = post_grid_media($post_id, $args);

            if ( $is_image ) continue;

            if($is_enable == 'yes'){
                if(!empty($media)){

                    $html_media = post_grid_media($post_id, $args);
                    $is_image = true;
                }
                else{
                    $html_media = '';
                }
            }
        }

        echo $html_media;

        ?>


    </div>
    <?php
}


add_action('post_grid_layout_element_css_media', 'post_grid_layout_element_css_media', 10);
function post_grid_layout_element_css_media($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $media_height = isset($element['media_height']) ? $element['media_height'] : '';
    $thumb_height_large = isset($media_height['large']) ? $media_height['large'] : '';
    $thumb_height_medium = isset($media_height['medium']) ? $media_height['medium'] : '';
    $thumb_height_small = isset($media_height['small']) ? $media_height['small'] : '';

    $height_large_type = isset($media_height['large_type']) ? $media_height['large_type'] : '';
    $height_medium_type = isset($media_height['medium_type']) ? $media_height['medium_type'] : '';
    $height_small_type = isset($media_height['small_type']) ? $media_height['small_type'] : '';

    $padding = isset($element['padding']) ? $element['padding'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';

    //var_dump($css);

    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($padding)): ?>
    padding: <?php echo $padding; ?>;
<?php endif; ?>
    overflow: hidden;
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
@media only screen and (min-width: 1024px ){
    .layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    <?php if($height_large_type =='auto_height'):  ?>
            height: auto;
    <?php elseif ($height_large_type =='fixed_height'): ?>
        <?php if(!empty($thumb_height_large)): ?>
            height: <?php echo $thumb_height_large; ?>;
        <?php endif; ?>
    <?php elseif ($height_large_type =='max_height'): ?>
        <?php if(!empty($thumb_height_large)): ?>
            max-height: <?php echo $thumb_height_large; ?>;
        <?php endif; ?>
    <?php endif; ?>
    }
}
@media only screen and ( min-width: 768px ) and ( max-width: 1023px ) {
    .layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    <?php if($height_medium_type =='auto_height'):  ?>
        height: auto;
    <?php elseif ($height_medium_type =='fixed_height'): ?>
        <?php if(!empty($thumb_height_medium)): ?>
            height: <?php echo $thumb_height_medium; ?>;
        <?php endif; ?>
    <?php elseif ($height_medium_type =='max_height'): ?>
        <?php if(!empty($thumb_height_medium)): ?>
            max-height: <?php echo $thumb_height_medium; ?>;
        <?php endif; ?>
    <?php endif; ?>
    }
}
@media only screen and ( min-width: 0px ) and ( max-width: 767px ){
    .layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    <?php if($height_small_type =='auto_height'):  ?>
        height: auto;
    <?php elseif ($height_small_type =='fixed_height'): ?>
        <?php if(!empty($thumb_height_small)): ?>
            height: <?php echo $thumb_height_small; ?>;
        <?php endif; ?>
    <?php elseif ($height_small_type =='max_height'): ?>
        <?php if(!empty($thumb_height_small)): ?>
            max-height: <?php echo $thumb_height_small; ?>;
        <?php endif; ?>
    <?php endif; ?>
    }
}
</style>
    <?php
}




add_action('post_grid_layout_element_option_thumb','post_grid_layout_element_option_thumb');


function post_grid_layout_element_option_thumb($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $thumb_size = isset($element_data['thumb_size']) ? $element_data['thumb_size'] : '';
    $default_thumb_src = isset($element_data['default_thumb_src']) ? $element_data['default_thumb_src'] : '';

    $thumb_height = isset($element_data['thumb_height']) ? $element_data['thumb_height'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $link_to = isset($element_data['link_to']) ? $element_data['link_to'] : '';
    $link_target = isset($element_data['link_target']) ? $element_data['link_target'] : '';

    $thumb_height_large = isset($thumb_height['large']) ? $thumb_height['large'] : '';
    $thumb_height_medium = isset($thumb_height['medium']) ? $thumb_height['medium'] : '';
    $thumb_height_small = isset($thumb_height['small']) ? $thumb_height['small'] : '';


    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Thumbnail','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[thumb]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);


            $thumbnail_sizes = array();
            $thumbnail_sizes['full'] = __('Full', '');
            $get_intermediate_image_sizes =  get_intermediate_image_sizes();

            if(!empty($get_intermediate_image_sizes))
                foreach($get_intermediate_image_sizes as $size_key){
                    $size_name = str_replace('_', ' ',$size_key);
                    $size_name = str_replace('-', ' ',$size_name);

                    $thumbnail_sizes[$size_key] = ucfirst($size_name);
                }
            //echo '<pre>'.var_export($thumbnail_sizes, true).'</pre>';

            $args = array(
                'id'		=> 'thumb_size',
                'parent' => $input_name.'[thumb]',
                'title'		=> __('Thumbnail size','post-grid'),
                'details'	=> __('Choose thumbnail size.','post-grid'),
                'type'		=> 'select',
                'value'		=> $thumb_size,
                'default'		=> 'large',
                'args'		=> $thumbnail_sizes,
            );

            $settings_tabs_field->generate_field($args);




            $args = array(
                'id'		=> 'link_to',
                'css_id'		=> $element_index.'_link_to',
                'parent' => $input_name.'[thumb]',
                'title'		=> __('Link to','post-grid'),
                'details'	=> __('Choose option to link title.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_to,
                'default'		=> 'none',
                'args'		=> apply_filters('post_grid_link_to_args',
					array(
						'post_link'=> __('Post link', 'post-grid'),
						'none'=> __('None', 'post-grid'),
					)
				),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'link_target',
                'css_id'		=> $element_index.'_link_target',
                'parent' => $input_name.'[thumb]',
                'title'		=> __('Link target','post-grid'),
                'details'	=> __('Choose option link target.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_target,
                'default'		=> 'post_link',
                'args'		=> array(
                    '_blank'=> __('_blank', 'post-grid'),
                    '_parent'=> __('_parent', 'post-grid'),
                    '_self'=> __('_self', 'post-grid'),
                    '_top'=> __('_top', 'post-grid'),

                ),
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'thumb_height',
                'title'		=> __('Thumbnail height','post-grid'),
                'details'	=> __('Set thumbnail height.','post-grid'),
                'type'		=> 'option_group',
                'options'		=> array(
                    array(
                        'id'		=> 'large',
                        'parent'		=> $input_name.'[thumb][thumb_height]',
                        'title'		=> __('In desktop','post-grid'),
                        'details'	=> __('min-width: 1200px, ex: 280px','post-grid'),
                        'type'		=> 'text',
                        'value'		=> $thumb_height_large,
                        'default'		=> '',
                        'placeholder'   => '280px',
                    ),
                    array(
                        'id'		=> 'medium',
                        'parent'		=> $input_name.'[thumb][thumb_height]',
                        'title'		=> __('In tablet & small desktop','post-grid'),
                        'details'	=> __('min-width: 992px, ex: 280px','post-grid'),
                        'type'		=> 'text',
                        'value'		=> $thumb_height_medium,
                        'default'		=> '',
                        'placeholder'   => '280px',
                    ),
                    array(
                        'id'		=> 'small',
                        'parent'		=> $input_name.'[thumb][thumb_height]',
                        'title'		=> __('In mobile','post-grid'),
                        'details'	=> __('max-width: 768px, ex: 280px','post-grid'),
                        'type'		=> 'text',
                        'value'		=> $thumb_height_small,
                        'default'		=> '',
                        'placeholder'   => '280px',
                    ),
                ),

            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'default_thumb_src',
                'parent' => $input_name.'[thumb]',
                'title'		=> __('Default thumbnail','post-grid'),
                'details'	=> __('Choose default thumbnail.','post-grid'),
                'type'		=> 'media_url',
                'value'		=> $default_thumb_src,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[thumb]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[thumb]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[thumb]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);


            ?>

        </div>
    </div>
    <?php

}



add_action('post_grid_layout_element_thumb', 'post_grid_layout_element_thumb');

function post_grid_layout_element_thumb($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $default_thumb_src = isset($element['default_thumb_src']) ? $element['default_thumb_src'] : '';
    $thumb_size = isset($element['thumb_size']) ?  $element['thumb_size'] : 'large';

    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $link_to = isset($element['link_to']) ? $element['link_to'] : 'post_link';


    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $thumb_size );
    $thumb_url = !empty($thumb['0']) ? $thumb['0'] : $default_thumb_src;

    if(empty($thumb_url)) return;


    $post_link = get_permalink($post_id);



    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> thumb ">
        <?php
        if($link_to == 'post_link'):
            ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_link); ?>"><img src="<?php echo esc_url_raw($thumb_url); ?>"></a>
		<?php elseif($link_to == 'custom_link'):

			$post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);
			$thumb_custom_url = !empty($post_grid_post_settings['thumb_custom_url']) ? $post_grid_post_settings['thumb_custom_url'] : $post_link;

			//var_dump($thumb_custom_url);

			?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($thumb_custom_url); ?>"><img src="<?php echo esc_url_raw($thumb_url); ?>"></a>

        <?php
        else:
            ?>
            <img src="<?php echo esc_url_raw($thumb_url); ?>">
        <?php

        endif;
        ?>


    </div>
    <?php
}






add_action('post_grid_layout_element_css_thumb', 'post_grid_layout_element_css_thumb', 10);
function post_grid_layout_element_css_thumb($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $thumb_height = isset($element['thumb_height']) ? $element['thumb_height'] : '';
    $thumb_height_large = isset($thumb_height['large']) ? $thumb_height['large'] : '';
    $thumb_height_medium = isset($thumb_height['medium']) ? $thumb_height['medium'] : '';
    $thumb_height_small = isset($thumb_height['small']) ? $thumb_height['small'] : '';

    $margin = isset($element['margin']) ? $element['margin'] : '';
    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';

    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    overflow: hidden;
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
}
@media only screen and (min-width: 1024px ){
    .layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    <?php if(!empty($thumb_height_large)): ?>
        max-height: <?php echo $thumb_height_large; ?>;
    <?php endif; ?>
    }
}
@media only screen and ( min-width: 768px ) and ( max-width: 1023px ) {
    .layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    <?php if(!empty($thumb_height_medium)): ?>
        max-height: <?php echo $thumb_height_medium; ?>;
    <?php endif; ?>
    }
}
@media only screen and ( min-width: 0px ) and ( max-width: 767px ){
    .layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    <?php if(!empty($thumb_height_small)): ?>
        max-height: <?php echo $thumb_height_small; ?>;
    <?php endif; ?>
    }
}
</style>
    <?php
}


add_action('post_grid_layout_element_option_thumb_link','post_grid_layout_element_option_thumb_link');


function post_grid_layout_element_option_thumb_link($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $thumb_size = isset($element_data['thumb_size']) ? $element_data['thumb_size'] : '';
    $default_thumb_src = isset($element_data['default_thumb_src']) ? $element_data['default_thumb_src'] : '';
    $link_target = isset($element_data['link_target']) ? $element_data['link_target'] : '';

    $thumb_height = isset($element_data['thumb_height']) ? $element_data['thumb_height'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $link_to = isset($element_data['link_to']) ? $element_data['link_to'] : '';

    $thumb_height_large = isset($thumb_height['large']) ? $thumb_height['large'] : '';
    $thumb_height_medium = isset($thumb_height['medium']) ? $thumb_height['medium'] : '';
    $thumb_height_small = isset($thumb_height['small']) ? $thumb_height['small'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Thumbnail with link','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[thumb_link]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);

            $thumbnail_sizes = array();
            $thumbnail_sizes['full'] = __('Full', '');
            $get_intermediate_image_sizes =  get_intermediate_image_sizes();

            if(!empty($get_intermediate_image_sizes))
                foreach($get_intermediate_image_sizes as $size_key){
                    $size_name = str_replace('_', ' ',$size_key);
                    $size_name = str_replace('-', ' ',$size_name);

                    $thumbnail_sizes[$size_key] = ucfirst($size_name);
                }
            //echo '<pre>'.var_export($thumbnail_sizes, true).'</pre>';

            $args = array(
                'id'		=> 'thumb_size',
                'parent' => $input_name.'[thumb_link]',
                'title'		=> __('Thumbnail size','post-grid'),
                'details'	=> __('Choose thumbnail size.','post-grid'),
                'type'		=> 'select',
                'value'		=> $thumb_size,
                'default'		=> 'large',
                'args'		=> $thumbnail_sizes,
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'link_to',
                'css_id'		=> $element_index.'_link_to',
                'parent' => $input_name.'[thumb_link]',
                'title'		=> __('Link to','post-grid'),
                'details'	=> __('Choose option to link title.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_to,
                'default'		=> 'none',
                'args'		=> apply_filters('post_grid_link_to_args',
					array(
						'post_link'=> __('Post link', 'post-grid'),
						'none'=> __('None', 'post-grid'),
					)
				),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'link_target',
                'css_id'		=> $element_index.'_link_target',
                'parent' => $input_name.'[thumb_link]',
                'title'		=> __('Link target','post-grid'),
                'details'	=> __('Choose option link target.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_target,
                'default'		=> 'post_link',
                'args'		=> array(
                    '_blank'=> __('_blank', 'post-grid'),
                    '_parent'=> __('_parent', 'post-grid'),
                    '_self'=> __('_self', 'post-grid'),
                    '_top'=> __('_top', 'post-grid'),

                ),
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'thumb_height',
                'title'		=> __('Thumbnail height','post-grid'),
                'details'	=> __('Set thumbnail height.','post-grid'),
                'type'		=> 'option_group',
                'options'		=> array(
                    array(
                        'id'		=> 'large',
                        'parent'		=> $input_name.'[thumb_link][thumb_height]',
                        'title'		=> __('In desktop','post-grid'),
                        'details'	=> __('min-width: 1200px, ex: 280px','post-grid'),
                        'type'		=> 'text',
                        'value'		=> $thumb_height_large,
                        'default'		=> '',
                        'placeholder'   => '280px',
                    ),
                    array(
                        'id'		=> 'medium',
                        'parent'		=> $input_name.'[thumb_link][thumb_height]',
                        'title'		=> __('In tablet & small desktop','post-grid'),
                        'details'	=> __('min-width: 992px, ex: 280px','post-grid'),
                        'type'		=> 'text',
                        'value'		=> $thumb_height_medium,
                        'default'		=> '',
                        'placeholder'   => '280px',
                    ),
                    array(
                        'id'		=> 'small',
                        'parent'		=> $input_name.'[thumb_link][thumb_height]',
                        'title'		=> __('In mobile','post-grid'),
                        'details'	=> __('max-width: 768px, ex: 280px','post-grid'),
                        'type'		=> 'text',
                        'value'		=> $thumb_height_small,
                        'default'		=> '',
                        'placeholder'   => '280px',
                    ),
                ),

            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'default_thumb_src',
                'parent' => $input_name.'[thumb_link]',
                'title'		=> __('Default thumbnail','post-grid'),
                'details'	=> __('Choose default thumbnail.','post-grid'),
                'type'		=> 'media_url',
                'value'		=> $default_thumb_src,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[thumb_link]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[thumb_link]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[thumb_link]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);


            ?>

        </div>
    </div>
    <?php

}


add_action('post_grid_layout_element_thumb_link', 'post_grid_layout_element_thumb_link');

function post_grid_layout_element_thumb_link($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $default_thumb_src = isset($element['default_thumb_src']) ? $element['default_thumb_src'] : '';
    $thumb_size = isset($element['thumb_size']) ?  $element['thumb_size'] : 'large';

    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $link_to = isset($element['link_to']) ? $element['link_to'] : 'post_link';


    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $thumb_size );
    $thumb_url = !empty($thumb['0']) ? $thumb['0'] : $default_thumb_src;

    if(empty($thumb_url)) return;


    $post_link = get_permalink($post_id);



    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> thumb_link ">
        <?php
        if($link_to == 'post_link'):
            ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_link); ?>"><img src="<?php echo esc_url_raw($thumb_url); ?>"></a>

		<?php elseif($link_to == 'custom_link'):

			$post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);
			$thumb_custom_url = !empty($post_grid_post_settings['thumb_custom_url']) ? $post_grid_post_settings['thumb_custom_url'] : $post_link;

			//var_dump($thumb_custom_url);

			?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($thumb_custom_url); ?>"><img src="<?php echo esc_url_raw($thumb_url); ?>"></a>


        <?php
        else:
            ?>
            <img src="<?php echo esc_url_raw($thumb_url); ?>">
        <?php

        endif;
        ?>


    </div>
    <?php
}


add_action('post_grid_layout_element_css_thumb_link', 'post_grid_layout_element_css_thumb_link', 10);
function post_grid_layout_element_css_thumb_link($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $thumb_height = isset($element['thumb_height']) ? $element['thumb_height'] : '';
    $thumb_height_large = isset($thumb_height['large']) ? $thumb_height['large'] : '';
    $thumb_height_medium = isset($thumb_height['medium']) ? $thumb_height['medium'] : '';
    $thumb_height_small = isset($thumb_height['small']) ? $thumb_height['small'] : '';

    $margin = isset($element['margin']) ? $element['margin'] : '';
    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';

    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    overflow: hidden;
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
}
@media only screen and (min-width: 1024px ){
    .layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    <?php if(!empty($thumb_height_large)): ?>
        max-height: <?php echo $thumb_height_large; ?>;
    <?php endif; ?>
    }
}
@media only screen and ( min-width: 768px ) and ( max-width: 1023px ) {
    .layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    <?php if(!empty($thumb_height_medium)): ?>
        max-height: <?php echo $thumb_height_medium; ?>;
    <?php endif; ?>
    }
}
@media only screen and ( min-width: 0px ) and ( max-width: 767px ){
    .layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
    <?php if(!empty($thumb_height_small)): ?>
        max-height: <?php echo $thumb_height_small; ?>;
    <?php endif; ?>
    }
}
</style>
    <?php
}











add_action('post_grid_layout_element_option_post_date','post_grid_layout_element_option_post_date');
function post_grid_layout_element_option_post_date($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $date_format = isset($element_data['date_format']) ? $element_data['date_format'] : '';
    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $link_to = isset($element_data['link_to']) ? $element_data['link_to'] : '';

    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post date','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php


            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'date_format',
                'css_id'		=> $element_index.'_background_colorpost_date',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Date format','post-grid'),
                'details'	=> __('Choose date format.','post-grid'),
                'type'		=> 'text',
                'value'		=> $date_format,
                'default'		=> '',
                'placeholder'		=> 'd-m-Y',

            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Wrapper html','post-grid'),
                'details'	=> __('Write wrapper html, use <code>%s</code> to replace date output.','post-grid'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> 'Date: %s',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'link_to',
                'css_id'		=> $element_index.'_link_to',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Link to','post-grid'),
                'details'	=> __('Choose option to link title.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_to,
                'default'		=> 'none',
                'args'		=> apply_filters('post_grid_link_to_args',
					array(
						'post_link'=> __('Post link', 'post-grid'),
						'none'=> __('None', 'post-grid'),
					)
				),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_custom_text',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Color','post-grid'),
                'details'	=> __('Title text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Set font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Font family','post-grid'),
                'details'	=> __('Set font family.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php
}



add_action('post_grid_layout_element_post_date', 'post_grid_layout_element_post_date');

function post_grid_layout_element_post_date($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $link_to = isset($element['link_to']) ? $element['link_to'] : '';
    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $date_format = isset($element['date_format']) ? $element['date_format'] : 'd-m-Y';
    $wrapper_html  = !empty($element['wrapper_html']) ? $element['wrapper_html'] : '%s';


    $post_link = get_permalink($post_id);
    $post_date = get_the_date($date_format, $post_id);

    $post_date = sprintf($wrapper_html,$post_date);

    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> post_date ">
        <?php
        if($link_to == 'post_link'):
            ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_link); ?>"><?php echo esc_html($post_date); ?></a>

		<?php elseif($link_to == 'custom_link'):

			$post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);
			$thumb_custom_url = !empty($post_grid_post_settings['thumb_custom_url']) ? $post_grid_post_settings['thumb_custom_url'] : $post_link;

			//var_dump($thumb_custom_url);

			?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($thumb_custom_url); ?>"><?php echo esc_html($post_date); ?></a>


        <?php
        else:
            ?>
            <?php echo esc_html($post_date); ?>
            <?php
        endif;
        ?>

    </div>
    <?php
}


add_action('post_grid_layout_element_css_post_date', 'post_grid_layout_element_css_post_date', 10);
function post_grid_layout_element_css_post_date($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';
    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';
    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
}
</style>
    <?php
}






add_action('post_grid_layout_element_option_author','post_grid_layout_element_option_author');
function post_grid_layout_element_option_author($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $link_to = isset($element_data['link_to']) ? $element_data['link_to'] : '';

    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Author name','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[author]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'link_to',
                'css_id'		=> $element_index.'_link_to',
                'parent' => $input_name.'[author]',
                'title'		=> __('Link to','post-grid'),
                'details'	=> __('Choose option to link title.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_to,
                'default'		=> 'none',

				'args'		=> apply_filters('post_grid_author_link_to_args',
					array(
						'post_link'=> __('Post link', 'post-grid'),
						'author_posts_link'=> __('Author posts link', 'post-grid'),
						'none'=> __('None', 'post-grid'),
					)
				),

            );



            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[author]',
                'title'		=> __('Wrapper html','post-grid'),
                'details'	=> __('Write wrapper html.','post-grid'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> 'Author: %s',
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[author]',
                'title'		=> __('Text color','post-grid'),
                'details'	=> __('Choose text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[author]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Choose text font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '16px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_padding',
                'parent' => $input_name.'[author]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Choose padding.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 10px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[author]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[author]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[author]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('post_grid_layout_element_author', 'post_grid_layout_element_author');

function post_grid_layout_element_author($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $link_to = isset($element['link_to']) ? $element['link_to'] : '';
    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $wrapper_html = !empty($element['wrapper_html']) ? $element['wrapper_html'] : '%s';

    $post_link = get_permalink($post_id);

    $post = get_post($post_id);
    $post_author = isset($post->post_author) ? $post->post_author : '';
    $post_author_data = get_user_by('ID', $post_author);

    $post_author = isset($post_author_data->display_name) ? $post_author_data->display_name : '';

    $post_author = sprintf($wrapper_html, $post_author);

    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> author ">
        <?php
        if($link_to == 'post_link'):
            ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_link); ?>"><?php echo esc_html($post_author); ?></a>

		<?php elseif($link_to == 'custom_link'):

			$post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);
			$thumb_custom_url = !empty($post_grid_post_settings['thumb_custom_url']) ? $post_grid_post_settings['thumb_custom_url'] : $post_link;

			//var_dump($thumb_custom_url);

			?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($thumb_custom_url); ?>"><?php echo esc_html($post_author); ?></a>




		<?php
        elseif($link_to == 'author_posts_link'):
            $post_author_link = get_author_posts_url( get_the_author_meta( 'ID' ) ) ;

            ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_author_link); ?>"><?php echo esc_html($post_author); ?></a>
            <?php

        else:
            ?>
            <?php echo esc_html($post_author); ?>
        <?php
        endif;
        ?>

    </div>
    <?php
}



add_action('post_grid_layout_element_css_author', 'post_grid_layout_element_css_author', 10);
function post_grid_layout_element_css_author($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';
    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';
    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
}
</style>
    <?php
}




add_action('post_grid_layout_element_option_author_link','post_grid_layout_element_option_author_link');
function post_grid_layout_element_option_author_link($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $link_to = isset($element_data['link_to']) ? $element_data['link_to'] : '';

    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $background_color = isset($element_data['background_color']) ? $element_data['background_color'] : '';
    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Author name with link','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php


            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[author_link]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'link_to',
                'css_id'		=> $element_index.'_link_to',
                'parent' => $input_name.'[author_link]',
                'title'		=> __('Link to','post-grid'),
                'details'	=> __('Choose option to link title.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_to,
                'default'		=> 'none',
				'args'		=> apply_filters('post_grid_author_link_to_args',
					array(
						'post_link'=> __('Post link', 'post-grid'),
						'author_posts_link'=> __('Author posts link', 'post-grid'),
						'none'=> __('None', 'post-grid'),
					)
				),
			);

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[author_link]',
                'title'		=> __('Wrapper html','post-grid'),
                'details'	=> __('Write wrapper html, use <code>%s</code> to replace on-sale output.','post-grid'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> 'Author: %s',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[author_link]',
                'title'		=> __('Text color','post-grid'),
                'details'	=> __('Choose text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[author_link]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Choose text font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '16px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_padding',
                'parent' => $input_name.'[author_link]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Choose padding.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 10px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[author_link]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[author_link]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[author_link]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);
            ?>

        </div>
    </div>
    <?php

}




add_action('post_grid_layout_element_author_link', 'post_grid_layout_element_author_link');

function post_grid_layout_element_author_link($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $link_to = isset($element['link_to']) ? $element['link_to'] : 'post_link';
    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $wrapper_html = !empty($element['wrapper_html']) ? $element['wrapper_html'] : '%s';

    $post_link = get_permalink($post_id);
    $post_author = get_the_author();

    $post_author = sprintf($wrapper_html, $post_author);

    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> author_link ">
        <?php
        if($link_to == 'post_link'):
            ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_link); ?>"><?php echo esc_html($post_author); ?></a>


		<?php elseif($link_to == 'custom_link'):

			$post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);
			$thumb_custom_url = !empty($post_grid_post_settings['thumb_custom_url']) ? $post_grid_post_settings['thumb_custom_url'] : $post_link;

			//var_dump($thumb_custom_url);

			?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($thumb_custom_url); ?>"><?php echo esc_html($post_author); ?></a>



		<?php
        elseif($link_to == 'author_posts_link'):
            $post_author_link = get_author_posts_url( get_the_author_meta( 'ID' ) ) ;

            ?>
            <a target="<?php echo esc_attr($link_target); ?>" href="<?php echo esc_url_raw($post_author_link); ?>"><?php echo esc_html($post_author); ?></a>
        <?php

        else:
            ?>
            <?php echo esc_html($post_author); ?>
        <?php
        endif;
        ?>

    </div>
    <?php
}

add_action('post_grid_layout_element_css_author_link', 'post_grid_layout_element_css_author_link', 10);
function post_grid_layout_element_css_author_link($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : 'left';
    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';
    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
}
</style>
    <?php
}




add_action('post_grid_layout_element_option_categories','post_grid_layout_element_option_categories');
function post_grid_layout_element_option_categories($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $max_count = isset($element_data['max_count']) ? $element_data['max_count'] : '';
    $link_target = isset($element_data['link_target']) ? $element_data['link_target'] : '';
    $separator = isset($element_data['separator']) ? $element_data['separator'] : '';


    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $wrapper_margin = isset($element_data['wrapper_margin']) ? $element_data['wrapper_margin'] : '';
    $link_color = isset($element_data['link_color']) ? $element_data['link_color'] : '';
    $text_color = isset($element_data['text_color']) ? $element_data['text_color'] : '';

    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post categories','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php


            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'max_count',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Max count','post-grid'),
                'details'	=> __('Write max count','post-grid'),
                'type'		=> 'text',
                'value'		=> $max_count,
                'default'		=> 3,
                'placeholder'		=> '3',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'separator',
                'css_id'		=> $element_index.'_position_color',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Link separator','post-grid'),
                'details'	=> __('Choose link separator.','post-grid'),
                'type'		=> 'text',
                'value'		=> $separator,
                'default'		=> '',
                'placeholder'		=> ', ',

            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'link_target',
                'css_id'		=> $element_index.'_link_target',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Link target','post-grid'),
                'details'	=> __('Choose option link target.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_target,
                'default'		=> 'post_link',
                'args'		=> array(
                    '_blank'=> __('_blank', 'post-grid'),
                    '_parent'=> __('_parent', 'post-grid'),
                    '_self'=> __('_self', 'post-grid'),
                    '_top'=> __('_top', 'post-grid'),

                ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Wrapper html','post-grid'),
                'details'	=> __('Write wrapper html, use <code>%s</code> to replace category output.','post-grid'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> 'Categories: %s',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'wrapper_margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $wrapper_margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);





            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Choose font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '16px',

            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'link_color',
                'css_id'		=> $element_index.'_link_color',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Link color','post-grid'),
                'details'	=> __('Choose link color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $link_color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'text_color',
                'css_id'		=> $element_index.'_text_color',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Text color','post-grid'),
                'details'	=> __('Choose text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $text_color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[categories]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('post_grid_layout_element_categories', 'post_grid_layout_element_categories');

function post_grid_layout_element_categories($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $max_count = isset($element['max_count']) ? (int) $element['max_count'] : 3;
    $wrapper_html = !empty($element['wrapper_html']) ? $element['wrapper_html'] : '%s';
    $separator = isset($element['separator']) ? $element['separator'] : ', ';


    $term_list = wp_get_post_terms( $post_id, 'category', array( 'fields' => 'all' ) );

    $categories_html = '';
    $term_total_count = count($term_list);
    $max_term_limit = ($term_total_count < $max_count) ? $term_total_count : $max_count ;

    $i = 0;
    foreach ($term_list as $term){
        if($i >= $max_count) continue;

        $term_id = isset($term->term_id) ? $term->term_id : '';
        $term_name = isset($term->name) ? $term->name : '';
        $term_link = get_term_link($term_id);

        $categories_html .= '<a target="'.esc_attr($link_target).'" href="'.esc_url_raw($term_link).'">'.esc_html($term_name).'</a>';
        if( $i+1 < $max_term_limit){ $categories_html .= $separator;}

        $i++;
    }

    //var_dump($categories_html);

    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> categories ">
        <?php echo sprintf($wrapper_html, $categories_html); ?>
    </div>
    <?php
}


add_action('post_grid_layout_element_css_categories', 'post_grid_layout_element_css_categories', 10);
function post_grid_layout_element_css_categories($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $link_color = isset($element['link_color']) ? $element['link_color'] : '';
    $text_color = isset($element['text_color']) ? $element['text_color'] : '';

    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';
    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';
    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($text_color)): ?>
    color: <?php echo $text_color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($link_color)): ?>
    color: <?php echo $link_color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
</style>
    <?php
}





add_action('post_grid_layout_element_option_tags','post_grid_layout_element_option_tags');
function post_grid_layout_element_option_tags($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $max_count = isset($element_data['max_count']) ? $element_data['max_count'] : '';
    $link_target = isset($element_data['link_target']) ? $element_data['link_target'] : '';
    $separator = isset($element_data['separator']) ? $element_data['separator'] : '';

    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $wrapper_margin = isset($element_data['wrapper_margin']) ? $element_data['wrapper_margin'] : '';
    $link_color = isset($element_data['link_color']) ? $element_data['link_color'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post tag','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php
            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[tags]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'max_count',
                'parent' => $input_name.'[tags]',
                'title'		=> __('Max count','post-grid'),
                'details'	=> __('Write max count','post-grid'),
                'type'		=> 'text',
                'value'		=> $max_count,
                'default'		=> 3,
                'placeholder'		=> '3',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'separator',
                'css_id'		=> $element_index.'_position_color',
                'parent' => $input_name.'[tags]',
                'title'		=> __('Link separator','post-grid'),
                'details'	=> __('Choose link separator.','post-grid'),
                'type'		=> 'text',
                'value'		=> $separator,
                'default'		=> '',
                'placeholder'		=> ', ',

            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'link_target',
                'css_id'		=> $element_index.'_link_target',
                'parent' => $input_name.'[tags]',
                'title'		=> __('Link target','post-grid'),
                'details'	=> __('Choose option link target.','post-grid'),
                'type'		=> 'select',
                'value'		=> $link_target,
                'default'		=> 'post_link',
                'args'		=> array(
                    '_blank'=> __('_blank', 'post-grid'),
                    '_parent'=> __('_parent', 'post-grid'),
                    '_self'=> __('_self', 'post-grid'),
                    '_top'=> __('_top', 'post-grid'),

                ),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[tags]',
                'title'		=> __('Wrapper html','post-grid'),
                'details'	=> __('Write wrapper html, use <code>%s</code> to replace tags output.','post-grid'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> 'Tags: %s',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'wrapper_margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[tags]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $wrapper_margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'link_color',
                'css_id'		=> $element_index.'_link_color',
                'parent' => $input_name.'[tags]',
                'title'		=> __('Link color','post-grid'),
                'details'	=> __('Choose link color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $link_color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[tags]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[tags]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[tags]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}
add_action('post_grid_layout_element_tags', 'post_grid_layout_element_tags');

function post_grid_layout_element_tags($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $max_count = isset($element['max_count']) ? (int) $element['max_count'] : 3;
    $wrapper_html = !empty($element['wrapper_html']) ? $element['wrapper_html'] : '%s';
    $separator = isset($element['separator']) ? $element['separator'] : ', ';


    $term_list = wp_get_post_terms( $post_id, 'post_tag', array( 'fields' => 'all' ) );


    $categories_html = '';
    $term_total_count = count($term_list);
    $max_term_limit = ($term_total_count < $max_count) ? $term_total_count : $max_count ;

    $i = 0;
    foreach ($term_list as $term){
        if($i >= $max_count) continue;

        $term_id = isset($term->term_id) ? $term->term_id : '';
        $term_name = isset($term->name) ? $term->name : '';
        $term_link = get_term_link($term_id);

        $categories_html .= '<a target="'.esc_attr($link_target).'" href="'.esc_url_raw($term_link).'">'.esc_html($term_name).'</a>';
        if( $i+1 < $max_term_limit){ $categories_html .= $separator;}

        $i++;
    }

    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> tags ">
        <?php echo sprintf($wrapper_html, $categories_html); ?>
    </div>
    <?php
}


add_action('post_grid_layout_element_css_tags', 'post_grid_layout_element_css_tags', 10);
function post_grid_layout_element_css_tags($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $link_color = isset($element['link_color']) ? $element['link_color'] : '';
    $text_color = isset($element['text_color']) ? $element['text_color'] : '';

    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';

    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';
    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($text_color)): ?>
    color: <?php echo $text_color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($link_color)): ?>
    color: <?php echo $link_color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
</style>
    <?php
}




add_action('post_grid_layout_element_option_comments_count','post_grid_layout_element_option_comments_count');
function post_grid_layout_element_option_comments_count($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Comment count','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[comments_count]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[comments_count]',
                'title'		=> __('Wrapper html','post-grid'),
                'details'	=> __('Write wrapper html, use <code>%s</code> to replace comment count output.','post-grid'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> 'Total comments: %s',
            );

            $settings_tabs_field->generate_field($args);




            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[comments_count]',
                'title'		=> __('Color','post-grid'),
                'details'	=> __('Title text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[comments_count]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Set font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[comments_count]',
                'title'		=> __('Font family','post-grid'),
                'details'	=> __('Set font family.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[comments_count]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Set margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[comments_count]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[comments_count]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[comments_count]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);


            ?>

        </div>
    </div>
    <?php

}



add_action('post_grid_layout_element_comments_count', 'post_grid_layout_element_comments_count');

function post_grid_layout_element_comments_count($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $wrapper_html = isset($element['wrapper_html']) ? $element['wrapper_html'] : '%s';



    $comments_number = get_comments_number( $post_id );
    $comments_count_html = '';

    if(comments_open()){

        if ( $comments_number == 0 ) {
            $comments_count_html.= __('No Comments', 'post-grid');
        } elseif ( $comments_number > 1 ) {
            $comments_count_html.= sprintf(__('%s Comments', 'post-grid'), $comments_number);
        } else {
            $comments_count_html.= __('1 Comment', 'post-grid');
        }

        ?>
        <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> tags ">
            <?php echo sprintf($wrapper_html, $comments_count_html); ?>
        </div>
        <?php

    }


}


add_action('post_grid_layout_element_css_comments_count', 'post_grid_layout_element_css_comments_count', 10);
function post_grid_layout_element_css_comments_count($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($element['color']) ? $element['color'] : '';

    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_family = isset($element['font_family']) ? $element['font_family'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';

    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';
    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($font_family)): ?>
    font-family: <?php echo $font_family; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
</style>
    <?php
}










add_action('post_grid_layout_element_option_share_button','post_grid_layout_element_option_share_button');
function post_grid_layout_element_option_share_button($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $icon_color = isset($element_data['icon_color']) ? $element_data['icon_color'] : '';
    $icon_margin = isset($element_data['icon_margin']) ? $element_data['icon_margin'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Share button','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php


            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[share_button]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[share_button]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Choose text font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '16px',
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'icon_margin',
                'css_id'		=> $element_index.'_icon_margin',
                'parent' => $input_name.'[share_button]',
                'title'		=> __('Icon margin','post-grid'),
                'details'	=> __('Set icon margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $icon_margin,
                'default'		=> '',
                'placeholder'		=> '5px 10px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'icon_color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[share_button]',
                'title'		=> __('Icon color','post-grid'),
                'details'	=> __('Choose icon color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $icon_color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[share_button]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[share_button]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}
.element_<?php echo $element_index?> a{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);
            ?>

        </div>
    </div>
    <?php

}




add_action('post_grid_layout_element_share_button', 'post_grid_layout_element_share_button');

function post_grid_layout_element_share_button($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $wrapper_html = isset($element['wrapper_html']) ? $element['wrapper_html'] : '%s';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_color = isset($element['color']) ? $element['color'] : '';


    $post_title = get_the_title($post_id);
    $post_link = get_permalink($post_id);

    $share_button_html = '';

    $share_button_html.= '
		<span class="fb">
			<a target="'.$link_target.'" href="https://www.facebook.com/sharer/sharer.php?u='.$post_link.'"><i class="fab fa-facebook-square"></i></a>
		</span>
		<span class="twitter">
			<a target="'.$link_target.'" href="https://twitter.com/intent/tweet?url='.$post_link.'&text='.$post_title.'"><i class="fab fa-twitter-square"></i></a>
		</span>';

    $share_button_html = apply_filters('post_grid_share_buttons', $share_button_html);

    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> share_button ">
        <?php echo sprintf($wrapper_html, $share_button_html); ?>
    </div>
    <?php
}


add_action('post_grid_layout_element_css_share_button', 'post_grid_layout_element_css_share_button', 10);
function post_grid_layout_element_css_share_button($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $font_size = isset($element['font_size']) ? $element['font_size'] : '';

    $wrapper_margin = isset($element['wrapper_margin']) ? $element['wrapper_margin'] : '';

    $text_align = isset($element['text_align']) ? $element['text_align'] : '';
    $icon_margin = isset($element['icon_margin']) ? $element['icon_margin'] : '';
    $icon_color = isset($element['icon_color']) ? $element['icon_color'] : '';
    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';

    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($icon_color)): ?>
    color: <?php echo $icon_color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($wrapper_margin)): ?>
    margin: <?php echo $wrapper_margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?> a{
<?php if(!empty($icon_color)): ?>
    color: <?php echo $icon_color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($icon_margin)): ?>
    margin: <?php echo $icon_margin; ?>;
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
</style>
    <?php
}




add_action('post_grid_layout_element_option_hr','post_grid_layout_element_option_hr');
function post_grid_layout_element_option_hr($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';
    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $background_color = isset($element_data['background_color']) ? $element_data['background_color'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $height = isset($element_data['height']) ? $element_data['height'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Horizontal line','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[hr]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'background_color',
                'css_id'		=> $element_index.'_background_coloradd_to_cart',
                'parent' => $input_name.'[hr]',
                'title'		=> __('Background color','post-grid'),
                'details'	=> __('Choose background color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $background_color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'height',
                'css_id'		=> $element_index.'_height',
                'parent' => $input_name.'[hr]',
                'title'		=> __('Height','post-grid'),
                'details'	=> __('Choose height.','post-grid'),
                'type'		=> 'text',
                'value'		=> $height,
                'default'		=> '',
                'placeholder'		=> '5px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_padding',
                'parent' => $input_name.'[hr]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Choose margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 10px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[hr]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[hr]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);


            ?>

        </div>
    </div>
    <?php

}

add_action('post_grid_layout_element_hr', 'post_grid_layout_element_hr');

function post_grid_layout_element_hr($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $link_target = isset($element['link_target']) ? $element['link_target'] : '';
    $wrapper_html = isset($element['wrapper_html']) ? $element['wrapper_html'] : '%s';
    $height = isset($element['height']) ? $element['height'] : '';
    $background_color = isset($element['background_color']) ? $element['background_color'] : '';


    ?>
    <hr class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> hr "></hr>
    <?php
}


add_action('post_grid_layout_element_css_hr', 'post_grid_layout_element_css_hr', 10);
function post_grid_layout_element_css_hr($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $height = isset($element['height']) ? $element['height'] : '1px';

    $margin = isset($element['margin']) ? $element['margin'] : '';
    $background_color = isset($element['background_color']) ? $element['background_color'] : '';
    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';
    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($background_color)): ?>
    background-color: <?php echo $background_color; ?>;
<?php endif; ?>
<?php if(!empty($height)): ?>
    padding: <?php echo $height; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
</style>
    <?php
}



add_action('post_grid_layout_element_option_five_star','post_grid_layout_element_option_five_star');
function post_grid_layout_element_option_five_star($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $custom_class = isset($element_data['custom_class']) ? $element_data['custom_class'] : '';

    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $css = isset($element_data['css']) ? $element_data['css'] : '';
    $css_hover = isset($element_data['css_hover']) ? $element_data['css_hover'] : '';


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Five star','post-grid'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'custom_class',
                'css_id'		=> $element_index.'_custom_class',
                'parent' => $input_name.'[five_star]',
                'title'		=> __('Wrapper custom class','post-grid'),
                'details'	=> __('Set custom class.','post-grid'),
                'type'		=> 'text',
                'value'		=> $custom_class,
                'default'		=> '',
                'placeholder'		=> 'css-class',
            );

            $settings_tabs_field->generate_field($args);
            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[five_star]',
                'title'		=> __('Text color','post-grid'),
                'details'	=> __('Choose text color.','post-grid'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[five_star]',
                'title'		=> __('Font size','post-grid'),
                'details'	=> __('Choose text font size.','post-grid'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '16px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[five_star]',
                'title'		=> __('Margin','post-grid'),
                'details'	=> __('Choose margin.','post-grid'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 10px',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[five_star]',
                'title'		=> __('Text align','post-grid'),
                'details'	=> __('Choose text align.','post-grid'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'post-grid'),'right'=> __('Right', 'post-grid'),'center'=> __('Center', 'post-grid') ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css',
                'css_id'		=> $element_index.'_css',
                'parent' => $input_name.'[five_star]',
                'title'		=> __('Custom CSS','post-grid'),
                'details'	=> __('Set csutom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'css_hover',
                'css_id'		=> $element_index.'_css_hover',
                'parent' => $input_name.'[five_star]',
                'title'		=> __('Hover CSS','post-grid'),
                'details'	=> __('Set hover custom CSS.','post-grid'),
                'type'		=> 'textarea',
                'value'		=> $css_hover,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element_<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','post-grid'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','post-grid'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('post_grid_layout_element_five_star', 'post_grid_layout_element_five_star');

function post_grid_layout_element_five_star($args){

    $element  = isset($args['element']) ? $args['element'] : array();
    $elementIndex  = isset($args['index']) ? $args['index'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

    if(empty($post_id)) return;

    $custom_class = isset($element['custom_class']) ? $element['custom_class'] : '';
    $star_count = isset($element['star_count']) ? $element['star_count'] : 5;
    $wrapper_html = isset($element['wrapper_html']) ? $element['wrapper_html'] : '%s';
    $star_icon = isset($element['star_html']) ? $element['star_html'] : '';

    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $font_color = isset($element['color']) ? $element['color'] : '';


    if(empty($star_count)) return;

    $load_fontawesome = !empty($post_grid_meta_options['load_fontawesome']) ? $post_grid_meta_options['load_fontawesome'] : 'no';
    $post_grid_settings = get_option('post_grid_settings');
    $font_aw_version = isset($post_grid_settings['font_aw_version']) ? $post_grid_settings['font_aw_version'] : '';

    if(empty($star_icon)){
        if($font_aw_version == 'v_5'){
            $star_icon =  '<i class="fas fa-star"></i>';
        }elseif ($font_aw_version =='v_4'){
            $star_icon =  '<i class="fa fa-star"></i>';
        }
    }



    $five_star_html = '';

    for($i=1; $i<=$star_count; $i++){

        $five_star_html.= $star_icon;
    }

    ?>
    <div class="element element_<?php echo esc_attr($elementIndex); ?> <?php echo esc_attr($custom_class); ?> five_star ">
        <?php echo sprintf($wrapper_html, $five_star_html); ?>
    </div>
    <?php
}



add_action('post_grid_layout_element_css_five_star', 'post_grid_layout_element_css_five_star', 10);
function post_grid_layout_element_css_five_star($args){


    $index = isset($args['index']) ? $args['index'] : '';
    $element = isset($args['element']) ? $args['element'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';


    $font_size = isset($element['font_size']) ? $element['font_size'] : '';
    $color = isset($element['color']) ? $element['color'] : '';
    $margin = isset($element['margin']) ? $element['margin'] : '';
    $text_align = isset($element['text_align']) ? $element['text_align'] : '';

    $css = isset($element['css']) ? $element['css'] : '';
    $css_hover = isset($element['css_hover']) ? $element['css_hover'] : '';

    ?>
<style type="text/css">
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>{
<?php if(!empty($color)): ?>
    color: <?php echo $color; ?>;
<?php endif; ?>
<?php if(!empty($font_size)): ?>
    font-size: <?php echo $font_size; ?>;
<?php endif; ?>
<?php if(!empty($margin)): ?>
    margin: <?php echo $margin; ?>;
<?php endif; ?>
<?php if(!empty($text_align)): ?>
    text-align: <?php echo $text_align; ?>;
<?php endif; ?>
<?php if(!empty($css)): ?>
<?php echo $css; ?>
<?php endif; ?>
}
<?php if(!empty($css_hover)): ?>
.layout-<?php echo $layout_id; ?> .element_<?php echo $index; ?>:hover{
<?php echo $css_hover; ?>
}
<?php endif; ?>
</style>
    <?php
}







