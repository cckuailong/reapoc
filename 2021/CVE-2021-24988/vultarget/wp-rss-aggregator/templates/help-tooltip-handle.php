<?php
/**
 * Template for WP RSS Aggregator tooltip handle.
 * 
 * @package   WPRSSAggregator
 * @author    Jean Galea <info@wprssaggregator.com>
 * @copyright Copyright (c) 2012-2014, Jean Galea
 * @link      http://www.wprssaggregator.com/
 * @license   http://www.gnu.org/licenses/gpl.html
 */
?>
<?php /* @var $vars array Contains combined options for this template */ ?>
<a class="<?php echo $vars['tooltip_handle_class'] ?><?php if( !empty($vars['tooltip_handle_class_extra']) ): ?> <?php echo $vars['tooltip_handle_class_extra'] ?><?php endif ?>" href="#<?php echo $vars['tooltip_id_prefix'] . $vars['tooltip_id'] ?>"><?php echo $vars['tooltip_handle_text'] ?></a>

