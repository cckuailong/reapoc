<?php
/**
 * Class WPCF7R_Actions
 * A helper class for managing form actions
 */

defined( 'ABSPATH' ) || exit;

class WPCF7R_Actions {
	public function __construct( $post_id, $wpcf7r_form ) {
		$this->post_type     = 'wpcf7r_action';
		$this->wpcf7_post_id = $post_id;
		$this->html          = new WPCF7R_Html( WPCF7R_Form::$mail_tags );
	}

	/**
	 * Get all actions that are relevant to this form
	 *
	 * @param $rule_id
	 * @param integer $count
	 * @param boolean $active
	 * @param array   $args
	 */
	public function get_actions( $rule_id, $count = -1, $active = false, $args = array() ) {
		$this->actions = array();
		$actions       = array();

		$actions_posts = $this->get_action_posts( $rule_id, $count, $active, $args );

		if ( $actions_posts && is_array( $actions_posts ) ) {
			$counter = 0;
			foreach ( $actions_posts as $action_post ) {
				$action = WPCF7R_Action::get_action( $action_post );

				if ( is_object( $action ) && ! is_wp_error( $action ) ) {
					$actions[ $action->priority . '_' . $counter ] = $action;
					$counter++;
				}
			}
		}

		ksort( $actions );
		$this->actions = $actions;

		return $this->actions;
	}

	/**
	 * Get and return the posts that are used as actions
	 *
	 * @param $rule_id
	 * @param integer $count
	 * @param boolean $active
	 * @param array   $extra_args
	 */
	public function get_action_posts( $rule_id, $count = -1, $active = false, $extra_args = array() ) {

		$post_type = $this->post_type;
		$post_id   = $this->wpcf7_post_id;

		$actions = wpcf7r_get_actions( $post_type, $count, $post_id, $rule_id, $extra_args, $active );

		return $actions;
	}

	/**
	 * Echo the templates used for the javascript process
	 */
	public function html_fregments() {
		if ( ! isset( $this->wpcf7_post_id ) ) {
			return;
		}

		$action = new WPCF7R_Action();

		$new_block = array(
			'block_title' => __( 'New Block', 'wpcf7-redirect' ),
			'groups'      => $action->get_groups(),
			'block_key'   => 'new_block',

		);

		$default_group               = $action->get_group_fields();
		$prefix                      = '[actions][action_id]';
		$fields                      = $this->get_plugin_default_fields_values();
		$row_template                = $this->html->get_conditional_row_template( $new_block['block_key'], 'new_group', 'new_row', reset( $default_group ), $prefix );
		$options['row_html']         = $row_template;
		$options['group_html']       = $this->html->group_display( 'new_block', 'new_group', reset( $new_block['groups'] ), $prefix );
		$options['block_html']       = $this->html->get_block_html( 'new_block', $new_block, false, false, $prefix );
		$options['block_title_html'] = $this->html->get_block_title( 'new_block', $new_block, false, false, $prefix );
		$options['mail_tags']        = WPCF7R_Form::$mail_tags;

		echo '<script>';
			echo 'var wpcfr_template = ' . json_encode( $options );
		echo '</script>';
	}

	/**
	 * Get form values
	 */
	public function get_plugin_default_fields_values() {

		$fields = WPCF7r_Form_Helper::get_plugin_default_fields();

		foreach ( $fields as $field ) {
			// get_post_meta( $this->wpcf7_post_id, '_wpcf7_redirect_' . $field['name'] , true );
			$values[ $field['name'] ] = '';
		}
		return $values;
	}
}
