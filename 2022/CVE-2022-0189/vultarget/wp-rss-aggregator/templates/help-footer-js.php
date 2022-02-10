<?php
/**
 * Template for WP RSS Aggregator tooltip JavaScript for the footer.
 * 
 * @package   WPRSSAggregator
 * @author    Jean Galea <info@wprssaggregator.com>
 * @copyright Copyright (c) 2012-2014, Jean Galea
 * @link      http://www.wprssaggregator.com/
 * @license   http://www.gnu.org/licenses/gpl.html
 */
?>
<script type="text/javascript" id="<?php echo WPRSS_Help::get_instance()->prefix('footer-js') ?>">
	(function($, document, window) {
		$(function() {
			var tooltipHandleClass = '<?php echo isset( $vars['tooltip_handle_class'] ) ? $vars['tooltip_handle_class'] : '' ?>';
			
			// If class defined
			tooltipHandleClass.length && (function() {
				$('.'+tooltipHandleClass).tooltip({
					items: '*',
					tooltipClass: '<?php echo $vars['tooltip_class'] ?>',
					hide: 100,
					show: 100,
					position:  {
						my: 'left+3 top+3',
						at: 'right bottom'
					},
					content: function(){
						var $this = $(this);
						return $($this.attr('href')).html();
					}
				});
				
				$('.'+tooltipHandleClass).on('click', function(e) {
					e.preventDefault();
				});
			})();
		});
	})(jQuery, document, top, undefined);
</script>