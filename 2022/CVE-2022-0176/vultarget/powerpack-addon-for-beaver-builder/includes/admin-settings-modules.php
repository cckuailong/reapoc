<?php
/*
Badges
0 = NULL
1 = Unique & Popular
2 = Unique
3 = Popular
4 = Coming Soon
*/
$modules = array(
	'content'   => array(
		'name'      => __('Content Modules', 'bb-powerpack-lite'),
		'modules'   => array(
			array('title' => __('Info Box', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/info-box/', 'installed' => true),
			array('title' => __('Info List', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/info-list/', 'installed' => true),
			array('title' => __('Dual Button', 'bb-powerpack-lite'), 'badge' => '3', 'demo' => 'https://wpbeaveraddons.com/demo/dual-button/', 'installed' => true),
			array('title' => __('Smart Heading', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/headings/', 'installed' => true),
			array('title' => __('Dual Color Headings', 'bb-powerpack-lite'), 'badge' => '3', 'demo' => 'https://wpbeaveraddons.com/demo/headings/', 'installed' => true),
			array('title' => __('Business Hours', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/business-hours/', 'installed' => true),
			array('title' => __('Icon / Number List', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/icon-number-list/', 'installed' => true),
			array('title' => __('Content Grid', 'bb-powerpack-lite'), 'badge' => '1', 'demo' => 'https://wpbeaveraddons.com/demo/content-grid/'),
			array('title' => __('Content Tiles', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/content-tiles/'),
			array('title' => __('Custom Grid', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/content-grid/'),
			array('title' => __('Team Member', 'bb-powerpack-lite'), 'badge' => '3', 'demo' => 'https://wpbeaveraddons.com/demo/team-member/'),
			array('title' => __('Smart Button', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/buttons/'),
			array('title' => __('Image', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/images/'),
			array('title' => __('Testimonials', 'bb-powerpack-lite'), 'badge' => '3', 'demo' => 'https://wpbeaveraddons.com/demo/testimonial-slider/'),
			array('title' => __('Advanced Tabs', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/advanced-tabs/'),
			array('title' => __('Advanced Accordion', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/advanced-accordions/'),
			array('title' => __('Image Panel', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/images/'),
			array('title' => __('Restaurant Menu', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/restaurant-menu/'),
			array('title' => __('Timeline', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/timeline/'),
			array('title' => __('Pricing Table', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/pricing-table/'),
			array('title' => __('Social Icons', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/#'),
		)
	),
	'creative'  => array(
		'name'      => __('Creative Modules', 'bb-powerpack-lite'),
		'modules'   => array(
			array('title' => __('Fancy Heading', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/fancy-heading/', 'installed' => true),
			array('title' => __('Spacer', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/#', 'installed' => true),
			array('title' => __('Advanced Menu', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/advanced-menu/'),
			array('title' => __('Logo Carousel', 'bb-powerpack-lite'), 'badge' => '1', 'demo' => 'https://wpbeaveraddons.com/demo/logo-grid-carousel/'),
			array('title' => __('Dot Navigation', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/dot-navigation/'),
			array('title' => __('Highlight Box', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/highlight-box/'),
			array('title' => __('Smart Banner', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/smart-banner/'),
			array('title' => __('Hover Cards', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/hover-cards/'),
			array('title' => __('Flip Box', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/flip-box/'),
			array('title' => __('Divider', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/line-separator/'),
			array('title' => __('3D Slider', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/3d-slider/'),
			array('title' => __('Gallery', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/#'),
			array('title' => __('Gallery Carousel', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/#'),
			array('title' => __('Filterable Gallery', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/filterable-gallery/'),
		)
	),
	'lead_gen'  => array(
		'name'      => __('Lead Generation Modules', 'bb-powerpack-lite'),
		'modules'   => array(
			array('title' => __('Modal Popup Box', 'bb-powerpack-lite'), 'badge' => '1', 'demo' => 'https://wpbeaveraddons.com/demo/modal-box/'),
			array('title' => __('Alert Box', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/alert-box/'),
			array('title' => __('Announcement Bar', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/images/'),
			array('title' => __('Smart Banner', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/smart-banner/'),
			array('title' => __('Subscribe Form', 'bb-powerpack-lite'), 'badge' => '3', 'demo' => 'https://wpbeaveraddons.com/demo/subscribe-form/'),
		)
	),
	'forms' => array(
		'name'      => __('Form Styler Modules', 'bb-powerpack-lite'),
		'modules'   => array(
			array('title' => __('Contact Form 7', 'bb-powerpack-lite'), 'badge' => '1', 'demo' => 'https://wpbeaveraddons.com/demo/forms/', 'installed' => true),
			array('title' => __('Contact Form', 'bb-powerpack-lite'), 'badge' => '1', 'demo' => 'https://wpbeaveraddons.com/demo/forms/'),
			array('title' => __('Gravity Forms', 'bb-powerpack-lite'), 'badge' => '1', 'demo' => 'https://wpbeaveraddons.com/demo/forms/'),
			array('title' => __('WPForms', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/forms/'),
			array('title' => __('Formidable Forms', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/forms/'),
			array('title' => __('Ninja Forms', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/forms/'),
			array('title' => __('Caldera Forms', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/forms/'),
		)
	),
	'extensions'    => array(
		'name'          => __('Extensions', 'bb-powerpack-lite'),
		'modules'       => array(
			array('title' => __('Row Separators', 'bb-powerpack-lite'), 'badge' => '3', 'demo' => 'https://wpbeaveraddons.com/demo/row-separators/', 'installed' => true),
			array('title' => __('Row Gradient', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/row-effects/', 'installed' => true),
			array('title' => __('Column Round Corners', 'bb-powerpack-lite'), 'badge' => '0', 'demo' => 'https://wpbeaveraddons.com/demo/column-extensions/', 'installed' => true),
			array('title' => __('Row Expander', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/row-effects/'),
			array('title' => __('Row Overlay Effects', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/row-effects/'),
			array('title' => __('Column Separators', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/column-separator/'),
			array('title' => __('Column Shadow', 'bb-powerpack-lite'), 'badge' => '1', 'demo' => 'https://wpbeaveraddons.com/demo/column-extensions/'),
			array('title' => __('Column Gradient', 'bb-powerpack-lite'), 'badge' => '2', 'demo' => 'https://wpbeaveraddons.com/demo/column-extensions/'),
		)
	)
);
?>

<div class="pp-wrap">
	<p class="pp-upgrade-msg"><strong><?php _e('Upgrade to PowerPack Pro to get these set of modules', 'bb-powerpack-lite'); ?></strong> &nbsp;<a href="<?php echo BB_POWERPACK_PRO; ?>" target="_blank" class="button button-primary"><?php _e('Upgrade Now', 'bb-powerpack-lite'); ?></a></p>
	<div class="pp-modules-list wp-clearfix">
		<?php foreach( $modules as $category => $list ) : ?>
			<div class="pp-column">
				<h2 class="pp-modules-category"><?php echo $list['name']; ?> <span class="pp-modules-count"><?php echo count($list['modules']); ?></span></h2>
				<ul class="pp-modules">
					<?php foreach ( $list['modules'] as $module ) : ?>
						<li class="pp-module pp-badge-<?php echo $module['badge']; ?>">
							<a href="<?php echo $module['demo']; ?>" target="_blank">
								<?php echo $module['title']; ?>
								<?php if ( isset( $module['installed'] ) && $module['installed'] ) { ?>
									<span class="pp-module-status"> - <?php _e('Installed!', 'bb-powerpack-lite'); ?></span>
								<?php } ?>
							</a>
						</li>
					<?php endforeach; ?>
					<li class="pp-module">
						<a href="https://wpbeaveraddons.com/" target="_blank"><?php echo esc_html__( 'More...', 'bb-powerpack-lite' ); ?></a>
					</li>
				</ul>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<style>
.pp-modules-list .pp-module.pp-badge-1 a:after {
	content: "<?php echo pp_modules_badges(1); ?>";
	background: #3fb57c;
}
.pp-modules-list .pp-module.pp-badge-2 a:after {
	content: "<?php echo pp_modules_badges(2); ?>";
	background: #3F51B5;
}
.pp-modules-list .pp-module.pp-badge-3 a:after {
	content: "<?php echo pp_modules_badges(3); ?>";
	background: #ff5722;
}
.pp-modules-list .pp-module.pp-badge-4 a:after {
	content: "<?php echo pp_modules_badges(4); ?>";
	background: #2196f3;
}
</style>

<script>
jQuery('.pp-modules-list .pp-modules-category').on('click', function() {
	jQuery(this).next().slideToggle('fast');
});
</script>
