<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Add new Element
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


include_once( 'settings/database.php' );

$url_form = admin_url() . 'admin.php?page=' . $this->plugin['slug'];

$test_mode = array(
	'label'   => esc_attr__( 'Test Mode', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[test_mode]',
		'id'    => 'test_mode',
		'value' => isset( $param['test_mode'] ) ? $param['test_mode'] : '',
	],
	'tooltip'    => esc_attr__( 'If test mode is enabled, the popup will show for admin only', 'modal-window' ),
);

$status_mode = array(
	'label'   => esc_attr__( 'Activated', 'modal-window' ),
	'attr'    => [
		'name'  => 'status',
		'id'    => 'status',
		'value' => $status,
	],
	'tooltip'    => esc_attr__( 'If check - the popup will show on the frontend. If uncheck - popup not displayed on the frontend.', 'modal-window' ),
);

?>

<form action="<?php echo $url_form; ?>" method="post" name="post" class="wow-plugin" id="wow-plugin">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-1">
            <div id="post-body-content" style="position: relative;">
                <div id="titlediv" class="is-b-margin">
                    <div id="titlewrap">
                        <label class="screen-reader-text" id="title-prompt-text" for="title">
							<?php esc_attr_e( 'Enter title here', 'modal-window' ); ?>
                        </label>
                        <div class="field has-addons">
                            <div class="control is-expanded">
                                <input class="input is-radiusless is-info" type="text" placeholder="<?php esc_attr_e( 'Register an item name', 'modal-window' ); ?>" value="<?php echo $title; ?>" name="title" id="item-title">
                            </div>
                            <div class="control">
                                <button class="button button-primary button-large is-size-6 is-radiusless" id="submit">
                                    <span><?php echo $btn; ?></span>
                                    <span class="icon is-small has-text-white">
                                        &#10004;
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column">
	                    <div class="is-inline-block"><?php $this->checkbox($status_mode);?></div>
	                    <div class="is-inline-block"><?php $this->checkbox($test_mode);?></div>
                    </div>
                </div>

	            <?php include_once 'settings/preview.php'; ?>
            </div>

            <div id="postbox-container-2" class="postbox-container">
                <div id="postoptions" class="postbox ">
						<?php include_once 'settings/main.php'; ?>
                </div>
            </div>
        </div>
    </div>
    <!--  main param for adding in database-->
    <input type="hidden" name="tool_id" value="<?php echo $tool_id; ?>" id="tool_id"/>
    <input type="hidden" name="param[time]" value="<?php echo time(); ?>"/>
    <input type="hidden" name="add" id="add_action" value="<?php echo $add_action; ?>"/>
    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
    <input type="hidden" name="data" value="<?php echo $data; ?>"/>
    <input type="hidden" name="page" value="<?php echo $this->plugin['slug']; ?>"/>
    <input type="hidden" name="prefix" value="<?php echo $this->plugin['prefix']; ?>" id="prefix"/>
	<?php wp_nonce_field( $this->plugin['slug'] . '_action', $this->plugin['slug'] . '_nonce' ); ?>
</form>
