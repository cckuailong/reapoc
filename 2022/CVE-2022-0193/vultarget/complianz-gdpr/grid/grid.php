<?php
defined( 'ABSPATH' ) or die( "you do not have access to this page!" );

add_action( 'admin_enqueue_scripts', 'cmplz_enqueue_grid_assets' );
function cmplz_enqueue_grid_assets( $hook ) {
	if (strpos($hook, "toplevel_page_complianz")===false ) return;

	wp_register_style( ' cmplz-muuri', trailingslashit( cmplz_url ) . "grid/css/muuri.css", "", cmplz_version );
	wp_enqueue_style( ' cmplz-muuri' );
	wp_register_script( ' cmplz-muuri', trailingslashit( cmplz_url ) . 'grid/js/muuri.min.js', array( "jquery" ), cmplz_version );
	wp_enqueue_script( ' cmplz-muuri' );
	wp_register_script( ' cmplz-grid', trailingslashit( cmplz_url ) . 'grid/js/grid.js', array( "jquery", " cmplz-muuri" ), cmplz_version );
	wp_enqueue_script( ' cmplz-grid' );
}

function cmplz_grid_container($content){
	$file = trailingslashit(cmplz_path) . 'grid/templates/grid-container.php';

	if (strpos($file, '.php') !== false) {
		ob_start();
		require $file;
		$contents = ob_get_clean();
	} else {
		$contents = file_get_contents($file);
	}

	return str_replace('{content}', $content, $contents);
}

/**
 * Get menu for a settings page
 *
 * @param string $title
 * @param array $args
 *
 * @return string
 */
function cmplz_grid_settings_menu( $title, $args ) {
	$path = trailingslashit(cmplz_path) . 'grid/templates/';
	$menu_items = '';
	foreach( $args as $key => $grid_item ) {
		$menu_items .=  cmplz_get_template( 'grid-settings-menu-item.php', $grid_item, $path );
	}

	$args = array(
		'title' => $title,
		'content' => $menu_items,
	);
	return cmplz_get_template( 'grid-settings-menu.php', $args , $path );
}

function cmplz_grid_container_settings($title, $grid_items){
	$menu = cmplz_grid_settings_menu($title, $grid_items );
	$grid_elements = '';
	foreach ( $grid_items as $title => $grid_item )
	{
		$template = $grid_item['page'] . '/' . $grid_item['name'] . '.php';
		$html = cmplz_get_template($template);
		if (empty($html)){
			ob_start();
			do_action("cmplz_settings_tab_content_$title" );
			COMPLIANZ::$field->get_fields( $grid_item['page'], $title );
			$html = ob_get_clean();
		}
		$grid_item['body'] = $html;
		$grid_elements .= cmplz_settings_element( $grid_item );;
	}

	$file = trailingslashit(cmplz_path) . 'grid/templates/grid-container-settings.php';
	ob_start();
	require $file;
	$contents = ob_get_clean();

	$content = $menu.str_replace('{content}', $grid_elements, $contents);
	$args = array(
		'page'    => 'settings',
		'content' => $content,
	);
	return cmplz_get_template('admin_wrap.php', $args );
}

/**
 * @param $grid_item
 *
 * @return false|string|string[]
 */
function cmplz_settings_element($grid_item){
	$defaults = array(
		'controls' => '',
		'body' => '',
		'footer' => '',
		'conditions' => '',
		'class' => '',
		'index' => '',
	);
	$grid_item = wp_parse_args($grid_item, $defaults);

	$file = trailingslashit(cmplz_path) . 'grid/templates/settings-element.php';
	ob_start();
	require $file;
	$contents = ob_get_clean();

	// Controls
	if ( ! $grid_item['controls'] ) {
		$controls = apply_filters('cmplz_controls_'.$grid_item['body'], $grid_item['controls']);
	} else {
		$controls = $grid_item['controls'];
	}

	// Body
	if ( ! $grid_item['body'] ) {
		$body = $grid_item['page'] . '/' . $grid_item['name'] . '.php';
		$body = cmplz_get_template($body);
	} else {
		$body = $grid_item['body'];
	}

	// Footer
	$template_part_footer = $grid_item['page'].'/'.$grid_item['name'].'-footer.php';
	$template_part_footer = cmplz_get_template($template_part_footer);
	if ($template_part_footer) {
		$footer = $template_part_footer;
	} else {
		$template_part_footer = $grid_item['page'].'/footer.php';
		$footer = cmplz_get_template($template_part_footer, array('footer' => '') );
	}

	$contents = str_replace( array(
		'{page}',
		'{name}',
		'{class}',
		'{header}',
		'{controls}',
		'{conditions}',
		'{body}',
		'{index}',
		'{footer}',
	), array(
		$grid_item['page'],
		$grid_item['name'],
		$grid_item['class']. ' cmplz-' . $grid_item['name'],
		$grid_item['header'],
		$controls,
		$grid_item['conditions'],
		$body,
		$grid_item['index'],
		$footer,
	), $contents );

	return $contents;
}

/**
 * Get grid block element
 * @param array $grid_item
 *
 * @return string
 */
function cmplz_grid_element($grid_item){
    $defaults = array(
        'controls' => '',
        'body' => '',
        'footer' => '',
        'conditions' => '',
        'class' => '',
    );
    $grid_item = wp_parse_args($grid_item, $defaults);

    $file = trailingslashit(cmplz_path) . 'grid/templates/grid-element.php';
    ob_start();
    require $file;
    $contents = ob_get_clean();
    $controls = apply_filters('cmplz_controls_'.$grid_item['name'], $grid_item['controls']);

    // Body
    if ( ! $grid_item['body'] ) {
        $body = $grid_item['page'] . '/' . $grid_item['name'] . '.php';
        $body = cmplz_get_template($body);
    } else {
        $body = $grid_item['body'];
    }

    // Footer
    $template_part_footer = $grid_item['page'].'/'.$grid_item['name'].'-footer.php';
    $template_part_footer = cmplz_get_template($template_part_footer);
    if ($template_part_footer) {
        $footer = $template_part_footer;
    } else {
        $template_part_footer = $grid_item['page'].'/footer.php';
        $footer = cmplz_get_template($template_part_footer, array('footer' => '') );
    }

	$contents = str_replace( array(
	    '{name}',
		'{class}',
		'{header}',
		'{controls}',
		'{conditions}',
		'{body}',
		'{index}',
        '{footer}',
	), array(
	    $grid_item['name'],
		$grid_item['class']. ' cmplz-' . $grid_item['name'],
		$grid_item['header'],
		$controls,
		$grid_item['conditions'],
        $body,
		$grid_item['index'],
        $footer,
	), $contents );

	return $contents;
}


