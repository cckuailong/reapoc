<?php
/**
 * The template that displays the welcome message when the plugin is first activated.
 */

$main     = Tribe__Main::instance();
$has_plus = class_exists( 'Tribe__Tickets_Plus__Main' );
$has_tec  = class_exists( 'Tribe__Events__Main' );

$desktop_graphic = 'images/header/welcome-desktop-et.jpg';
$logo_image      = 'images/logo/event-tickets.svg';
$mobile_graphic  = 'images/header/welcome-mobile-et.jpg';

if ( $has_plus ) {
	$desktop_graphic = 'images/header/welcome-desktop-etplus.jpg';
	$logo_image      = 'images/logo/event-tickets-plus.svg';
	$mobile_graphic  = 'images/header/welcome-mobile-etplus.jpg';
}
?>

<?php if ( $has_plus ) : ?>
	<div class="tribe-events-admin-notice">
		<div class="tribe-events-admin-content-wrapper">
			<img
				class="tribe-events-admin-notice__logo"
				src="<?php echo esc_url( tribe_resource_url( 'images/icons/horns-white.svg', false, null, $main ) ); ?>"
				alt="<?php esc_attr_e( 'The Events Calendar product suite logo', 'event-tickets' ); ?>"
			/>
			<p><strong><?php echo esc_html_x( 'WOOHOO!', 'short expression of excitement', 'event-tickets' ); ?></strong> <?php esc_html_e( 'You\'re the proud owner of Event Tickets Plus! Let\'s get started…', 'event-tickets' ); ?></p>
		</div>
	</div>
<?php endif; ?>

<div class="tribe-events-admin-content-wrapper tribe-events-admin-tickets <?php if ( $has_plus ) { echo 'tribe-events-admin-tickets-plus'; } ?>">
	<img
		class="tribe-events-admin-graphic tribe-events-admin-graphic--desktop-only"
		src="<?php echo esc_url( tribe_resource_url( $desktop_graphic, false, null, $main ) ); ?>"
		alt="<?php esc_attr_e( 'Shapes and lines for visual interest', 'event-tickets' ); ?>"
	/>

	<img
		class="tribe-events-admin-graphic tribe-events-admin-graphic--mobile-only"
		src="<?php echo esc_url( tribe_resource_url( $mobile_graphic, false, null, $main ) ); ?>"
		alt="<?php esc_attr_e( 'Shapes and lines for visual interest', 'event-tickets' ); ?>"
	/>

	<div class="tribe-events-admin-title">
		<img
			class="tribe-events-admin-title__logo"
			src="<?php echo esc_url( tribe_resource_url( $logo_image, false, null, $main ) ); ?>"
			alt="<?php esc_attr_e( 'Event Tickets logo', 'event-tickets' ); ?>"
		/>
		<h2 class="tribe-events-admin-title__heading">
			<?php
			if ( $has_plus ) :
				esc_html_e( 'Event Tickets Plus', 'event-tickets' );
			else :
				esc_html_e( 'Event Tickets', 'event-tickets' );
			endif;
			?>
		</h2>
		<p class="tribe-events-admin-title__description"><?php
			if ( $has_plus ) :
				esc_html_e( 'Thanks for installing Event Tickets Plus! Here are some handy resources for getting started with our plugins.', 'event-tickets' );
			else :
				esc_html_e( 'Thanks for installing Event Tickets! Here are some handy resources for getting started with our plugins.', 'event-tickets' );
			endif;
		?></p>
	</div>

	<div class="tribe-events-admin-quick-nav">
		<div class="tribe-events-admin-quick-nav__title"><?php esc_html_e( 'Quick Links:', 'event-tickets' ); ?></div>
		<ul class="tribe-events-admin-quick-nav__links">
			<?php if ( $has_plus && $has_tec ) : // ET+ with TEC. ?>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="edit.php?page=tribe-common&tab=event-tickets&post_type=tribe_events" target="_blank" rel="noopener noreferrer" class="tribe-events-admin-quick-nav__link"><?php esc_html_e( 'Configure Settings', 'event-tickets' ); ?></a>
				</li>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="plugin-install.php?tab=plugin-information&amp;plugin=woocommerce&amp;TB_iframe=true" class="tribe-events-admin-quick-nav__link thickbox open-plugin-details-modal"><?php esc_html_e( 'Install WooCommerce', 'event-tickets' ); ?></a>
				</li>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="post-new.php?post_type=tribe_events" class="tribe-events-admin-quick-nav__link"><?php esc_html_e( 'Create Ticket', 'event-tickets' ); ?></a>
				</li>
			<?php elseif ( $has_plus ) : // ET+ without TEC. ?>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="edit.php?page=tribe-common&tab=event-tickets" target="_blank" rel="noopener noreferrer" class="tribe-events-admin-quick-nav__link"><?php esc_html_e( 'Configure Settings', 'event-tickets' ); ?></a>
				</li>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="plugin-install.php?tab=plugin-information&amp;plugin=woocommerce&amp;TB_iframe=true" class="tribe-events-admin-quick-nav__link thickbox open-plugin-details-modal"><?php esc_html_e( 'Install WooCommerce', 'event-tickets' ); ?></a>
				</li>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="plugin-install.php?tab=plugin-information&amp;plugin=the-events-calendar&amp;TB_iframe=true" class="tribe-events-admin-quick-nav__link thickbox open-plugin-details-modal"><?php esc_html_e( 'Install The Events Calendar', 'event-tickets' ); ?></a>
				</li>
			<?php elseif ( $has_tec ) : // ET with TEC. ?>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="edit.php?page=tribe-common&tab=event-tickets&post_type=tribe_events" target="_blank" rel="noopener noreferrer" class="tribe-events-admin-quick-nav__link"><?php esc_html_e( 'Configure Settings', 'event-tickets' ); ?></a>
				</li>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="post-new.php?post_type=tribe_events" class="tribe-events-admin-quick-nav__link"><?php esc_html_e( 'Create RSVP', 'event-tickets' ); ?></a>
				</li>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="https://evnt.is/1axt" target="_blank" rel="noopener noreferrer" class="tribe-events-admin-quick-nav__link"><?php esc_html_e( 'Set Up Tickets Commerce', 'event-tickets' ); ?></a>
				</li>
			<?php else : // ET without TEC. ?>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="edit.php?page=tribe-common&tab=event-tickets" target="_blank" rel="noopener noreferrer" class="tribe-events-admin-quick-nav__link"><?php esc_html_e( 'Configure Settings', 'event-tickets' ); ?></a>
				</li>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="plugin-install.php?tab=plugin-information&amp;plugin=the-events-calendar&amp;TB_iframe=true" class="tribe-events-admin-quick-nav__link thickbox open-plugin-details-modal"><?php esc_html_e( 'Install The Events Calendar', 'event-tickets' ); ?></a>
				</li>
				<li class="tribe-events-admin-quick-nav__link-item">
					<a href="https://evnt.is/1axt" target="_blank" rel="noopener noreferrer" class="tribe-events-admin-quick-nav__link"><?php esc_html_e( 'Set Up Tickets Commerce', 'event-tickets' ); ?></a>
				</li>
			<?php endif; ?>
		</ul>
	</div>

	<h3 class="tribe-events-admin-section-header"><?php esc_html_e( 'Helpful Resources', 'event-tickets' ); ?></h3>

	<?php
	/* Video was not yet ready.
	<div class="tribe-events-admin-video">
		<iframe src="https://www.youtube.com/embed/5.0.3" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</div>
	*/
	?>

	<div class="tribe-events-admin-card-grid">
		<div class="tribe-events-admin-card tribe-events-admin-card--3up tribe-events-admin-card--first">
			<img
				class="tribe-events-admin-card__image"
				src="<?php echo esc_url( tribe_resource_url( 'images/welcome/guide-book-green.jpg', false, null, $main ) ); ?>"
				alt="<?php esc_attr_e( 'Illustration of a book with The Events Calendar logo', 'event-tickets' ); ?>"
			/>
			<div class="tribe-events-admin-card__title"><?php esc_html_e( 'Getting Started Guide', 'event-tickets' ); ?></div>
			<div class="tribe-events-admin-card__description"><?php esc_html_e( 'New to Event Tickets? Here\'s everything you need to get started.', 'event-tickets' ); ?></div>
			<a class="tribe-events-admin-card__link" href="https://evnt.is/1an9" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Check out the guide', 'event-tickets' ); ?></a>
		</div>
		<div class="tribe-events-admin-card tribe-events-admin-card--3up tribe-events-admin-card--middle">
			<img
				class="tribe-events-admin-card__image"
				src="<?php echo esc_url( tribe_resource_url( 'images/welcome/knowledgebase.jpg', false, null, $main ) ); ?>"
				alt="<?php esc_attr_e( 'Illustration of a thought lightbulb coming from a book', 'event-tickets' ); ?>"
			/>
			<div class="tribe-events-admin-card__title"><?php esc_html_e( 'Knowledgebase', 'event-tickets' ); ?></div>
			<div class="tribe-events-admin-card__description"><?php esc_html_e( 'Ready to dig deeper? Our Knowledgebase can help you get the most out of The Events Calendar suite.', 'event-tickets' ); ?></div>
			<a class="tribe-events-admin-card__link" href="https://evnt.is/1ane" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Dig deeper', 'event-tickets' ); ?></a>
		</div>
		<div class="tribe-events-admin-card tribe-events-admin-card--3up tribe-events-admin-card--last">
			<img
				class="tribe-events-admin-card__image"
				src="<?php echo esc_url( tribe_resource_url( 'images/welcome/translations.jpg', false, null, $main ) ); ?>"
				alt="<?php esc_attr_e( 'Illustration of characters being translated', 'event-tickets' ); ?>"
			/>
			<div class="tribe-events-admin-card__title"><?php esc_html_e( 'Translations', 'event-tickets' ); ?></div>
			<div class="tribe-events-admin-card__description"><?php esc_html_e( 'Need a language other than English? We\'ve got you covered here.', 'event-tickets' ); ?></div>
			<a class="tribe-events-admin-card__link" href="https://evnt.is/language" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Learn more', 'event-tickets' ); ?></a>
		</div>

		<div class="tribe-events-admin-card tribe-events-admin-card--1up">
			<img
				class="tribe-events-admin-card__image"
				src="<?php echo esc_url( tribe_resource_url( 'images/welcome/virtual-events.jpg', false, null, $main ) ); ?>"
				alt="<?php esc_attr_e( 'Illustration of a phone screen with a person\'s face', 'event-tickets' ); ?>"
			/>
			<div class="tribe-events-admin-card__title"><?php esc_html_e( 'Virtual Event Resources', 'event-tickets' ); ?></div>
			<div class="tribe-events-admin-card__description"><?php esc_html_e( 'Tips and tools to help with planning online events, webinars, and more on WordPress and beyond.', 'event-tickets' ); ?></div>
			<a class="tribe-events-admin-card__link" href="https://evnt.is/1ani" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Get started with online events', 'event-tickets' ); ?></a>
		</div>

		<?php if ( $has_plus && $has_tec ) : // ET+ with TEC. ?>
			<div class="tribe-events-admin-card tribe-events-admin-card--2up tribe-events-admin-card--first">
				<img
					class="tribe-events-admin-card__image"
					src="<?php echo esc_url( tribe_resource_url( 'images/welcome/extension-library.jpg', false, null, $main ) ); ?>"
					alt="<?php esc_attr_e( 'Illustration of a power plug and gears', 'event-tickets' ); ?>"
				/>
				<div class="tribe-events-admin-card__title"><?php esc_html_e( 'Browse our library of free extensions for Event Tickets.', 'event-tickets' ); ?></div>
				<a class="tribe-events-admin-card__link" href="https://evnt.is/1amf" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Learn more', 'event-tickets' ); ?></a>
			</div>
		<?php elseif ( $has_tec ) : // ET with TEC. ?>
			<div class="tribe-events-admin-card tribe-events-admin-card--2up tribe-events-admin-card--first">
				<img
					class="tribe-events-admin-card__image"
					src="<?php echo esc_url( tribe_resource_url( 'images/welcome/next-level.jpg', false, null, $main ) ); ?>"
					alt="<?php esc_attr_e( 'Illustration of a book with The Events Calendar logo', 'event-tickets' ); ?>"
				/>
				<div class="tribe-events-admin-card__title"><?php esc_html_e( 'Want to take your events to the next level?', 'event-tickets' ); ?></div>
				<a class="tribe-events-admin-card__link" href="admin.php?page=tribe-app-shop"><?php esc_html_e( 'Check out our suite of add-ons', 'event-tickets' ); ?></a>
			</div>
		<?php else : // ET or ET+ without TEC. ?>
			<div class="tribe-events-admin-card tribe-events-admin-card--2up tribe-events-admin-card--first">
				<img
					class="tribe-events-admin-card__image"
					src="<?php echo esc_url( tribe_resource_url( 'images/welcome/calendar.jpg', false, null, $main ) ); ?>"
					alt="<?php esc_attr_e( 'Illustration of a calendar', 'event-tickets' ); ?>"
				/>
				<div class="tribe-events-admin-card__title"><?php esc_html_e( 'Add The Events Calendar (it\'s free!) to enhance Event Tickets.', 'event-tickets' ); ?></div>
				<a class="tribe-events-admin-card__link" href="https://evnt.is/1anm" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Learn more', 'event-tickets' ); ?></a>
			</div>
		<?php endif; ?>

		<?php if ( $has_plus ) : // this is for ET+. ?>
			<div class="tribe-events-admin-card tribe-events-admin-card--2up tribe-events-admin-card--second">
				<img
					class="tribe-events-admin-card__image"
					src="<?php echo esc_url( tribe_resource_url( 'images/welcome/next-level.jpg', false, null, $main ) ); ?>"
					alt="<?php esc_attr_e( 'Illustration of a book with The Events Calendar logo', 'event-tickets' ); ?>"
				/>
				<div class="tribe-events-admin-card__title"><?php esc_html_e( 'Want to take your events to the next level?', 'event-tickets' ); ?></div>
				<a class="tribe-events-admin-card__link" href="admin.php?page=tribe-app-shop"><?php esc_html_e( 'Check out our suite of add-ons', 'event-tickets' ); ?></a>
			</div>
		<?php else : // this is for ET. ?>
			<div class="tribe-events-admin-card tribe-events-admin-card--2up tribe-events-admin-card--second">
				<img
					class="tribe-events-admin-card__image"
					src="<?php echo esc_url( tribe_resource_url( 'images/welcome/et-plus-upsell.jpg', false, null, $main ) ); ?>"
					alt="<?php esc_attr_e( 'Illustration of a hand holding a ticket and the WooCommerce logo', 'event-tickets' ); ?>"
				/>
				<div class="tribe-events-admin-card__title"><?php esc_html_e( 'Add WooCommerce integration, advanced registration, and more.', 'event-tickets' ); ?></div>
				<a class="tribe-events-admin-card__link" href="https://evnt.is/1ano" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Check out Event Tickets Plus', 'event-tickets' ); ?></a>
			</div>
		<?php endif; ?>

		<div class="tribe-events-admin-card tribe-events-admin-card--1up tribe-events-admin-card--promo-blue">
			<div class="tribe-events-admin-card__title"><?php esc_html_e( 'Want this emailed to you?', 'event-tickets' ); ?></div>
			<div class="tribe-events-admin-card__description"><?php esc_html_e( 'Keep this list of links on hand and stay subscribed to receive tips and tricks about The Events Calendar products.', 'event-tickets' ); ?></div>

			<form class="tribe-events-admin-card__form" action="https://support-api.tri.be/mailing-list/subscribe" method="post">
				<input class="tribe-events-admin-card__input" name="email" type="email" placeholder="<?php esc_attr_e( 'Your email', 'event-tickets' ); ?>" required />

				<button class="tribe-events-admin-card__button" type="submit"><?php esc_html_e( 'Sign Up', 'event-tickets' ); ?></button>

				<input type="hidden" name="list" value="tec-newsletter" />
				<input type="hidden" name="source" value="plugin:et" />
				<input type="hidden" name="consent" value="checked" />
			</form>
		</div>

	</div>

	<img
		class="tribe-events-admin-footer-logo"
		src="<?php echo esc_url( tribe_resource_url( 'images/logo/tec-brand.svg', false, null, $main ) ); ?>"
		alt="<?php esc_attr_e( 'The Events Calendar brand logo', 'event-tickets' ); ?>"
	/>

</div>
