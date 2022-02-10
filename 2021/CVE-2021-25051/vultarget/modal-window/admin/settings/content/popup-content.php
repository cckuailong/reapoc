<?php
/**
 * Popup content
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$content = array(
	'label' => esc_attr__( 'Popup Content', 'modal-window' ),
	'attr'  => [
		'name'  => 'param[content]',
		'id'    => 'popup_content',
		'value' => isset( $param['content'] ) ? $param['content'] : 'Welcome to Modal Window plugin',
	],
);

?>

<div class="columns is-multiline is-variable">
    <div class="column is-full editor">
		<?php $this->editor( $content ); ?>
    </div>

    <div class="column is-full content">
        <p>You can use any shortcodes in the modal window content. To create rows and columns you can use the following shortcode construct:<p/>
        <ul>
            <li><b>[w-row]</b> - create row</li>
            <li><b>[w-column]</b> - create a column and has the attributes:
                <ul>
                    <li>width - this value can be from 1 to 12. Value 12 = 100% width for column</li>
                    <li>align - this value can be: left, center, right</li>
                </ul>
            </li>
        </ul>
        <span class="has-text-info has-text-weight-semibold">Example</span>:<p/>
 <code>
[w-row]
[w-column width=6 align="center"] Content with 50% width and align center [/w-column]
[w-column width=6 align="right"] Content with 50% width and align right [/w-column]
[/w-row]
 </code>
    </div>

</div>
