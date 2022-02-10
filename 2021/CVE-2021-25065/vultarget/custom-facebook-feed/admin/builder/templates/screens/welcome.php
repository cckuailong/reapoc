<div class="cff-fb-full-wrapper cff-fb-fs" v-if="viewsActive.pageScreen == 'welcome' && !iscustomizerScreen">
    <?php
        /**
         * CFF Admin Notices
         *
         * @since 4.0
         */
        do_action('cff_admin_notices');
    ?>

	<div class="cff-fb-wlcm-header cff-fb-fs">
		<h2>{{welcomeScreen.mainHeading}}</h2>
        <div class="sb-positioning-wrap" v-bind:class="{ 'sb-onboarding-highlight' : viewsActive.onboardingStep === 1 }">
            <div class="cff-fb-btn cff-fb-btn-new cff-btn-orange" @click.prevent.default="! viewsActive.onboardingPopup ? switchScreen('pageScreen', 'selectFeed') : switchScreen('welcome')">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.66537 5.66659H5.66536V9.66659H4.33203V5.66659H0.332031V4.33325H4.33203V0.333252H5.66536V4.33325H9.66537V5.66659Z" fill="white"/>
                </svg>
                <span>{{genericText.addNew}}</span>
            </div>
        </div>
	</div>
	<?php
		include_once CFF_BUILDER_DIR . 'templates/sections/empty-state.php';
		include_once CFF_BUILDER_DIR . 'templates/sections/feeds-list.php';
	?>

	<div v-if="licenseType == 'free'" class="cff-fb-fs">
		<?php
			CustomFacebookFeed\CFF_View::render( 'sections.builder_footer_cta' );
		?>
	</div>
</div>