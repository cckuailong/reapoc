<?php
/**
 * Badge
 *
 * @package     Wow_Plugin
 * @subpackage  Settings/
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$enable_badge_help = array(
	'text' => esc_attr__( 'Enable the Notification badge for button.', $this->plugin['text'] ),
);

?>

	<div class="container">
		<div class="element">
			<input type="checkbox" disabled id="enable_badge"><label
				for="enable_badge"><?php esc_html_e( 'Enable Notification badge',
				$this->plugin['text'] ); ?></label><?php echo self::tooltip( $enable_badge_help ); ?>
				<?php echo self::pro(); ?>
		</div>



	</div>


<?php
