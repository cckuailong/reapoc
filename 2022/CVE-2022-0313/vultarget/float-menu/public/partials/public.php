<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $param['menu_1']['item_icon'] ) ):

	$count_i = count( $param['menu_1']['item_icon'] );

	if ( $count_i > 0 ) {
		$menu = '<div class="floating-menu float-menu-' . absint( $id ) . '">';
		$menu .= '<ul class="fm-bar">';
		for ( $i = 0; $i < $count_i; $i ++ ) {

			$icon = '<i class="' . esc_attr( $param['menu_1']['item_icon'][ $i ] ) . '"></i>';

			$button_class = $param['menu_1']['button_class'][ $i ];
			$class_add    = ! empty( $button_class ) ? ' class="' . esc_attr( $button_class ) . '"' : '';
			$button_id    = $param['menu_1']['button_id'][ $i ];
			$id_add       = ! empty( $button_id ) ? ' id="' . esc_attr( $button_id ) . '"' : '';
			$link_rel     = ! empty( $param['menu_1']['link_rel'][ $i ] ) ? ' rel="' . esc_attr( $param['menu_1']['link_rel'][ $i ] ) . '"' : '';

			$link_param = $id_add . $class_add . $link_rel;
			$menu       .= '<li class="fm-item-' . absint( $id ) . '-' . absint( $i ) . '">';
			$tooltip    = $param['menu_1']['item_tooltip'][ $i ];
			$name       = '<div class="fm-icon">' . $icon . '</div><div class="fm-label">' . $tooltip . '</div>';


			$target = ! empty( $param['menu_1']['new_tab'][ $i ] ) ? '_blank' : '_self';
			$link   = $param['menu_1']['item_link'][ $i ];
			$menu   .= '<a href="' . $link . '" target="' . $target . '" ' . $link_param . '>' . $name . '</a>';


			$menu .= '</li>';

		}
		$menu .= '</ul>';

		$menu .= '</div>';
		echo wp_kses_post( $menu );
	}
endif;