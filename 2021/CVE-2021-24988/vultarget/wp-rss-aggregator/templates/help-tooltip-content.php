<?php
/**
 * Template for WP RSS Aggregator tooltip content.
 * 
 * @package   WPRSSAggregator
 * @author    Jean Galea <info@wprssaggregator.com>
 * @copyright Copyright (c) 2012-2014, Jean Galea
 * @link      http://www.wprssaggregator.com/
 * @license   http://www.gnu.org/licenses/gpl.html
 */
?>
<div class="<?php echo $vars['tooltip_content_class'] ?>" id="<?php echo $vars['tooltip_id_prefix'] . $vars['tooltip_id'] ?>">
<?php echo isset( $vars['text_domain'] ) ? wpautop( $vars['tooltip_text'] ) : $vars['tooltip_text'] ?>
</div>