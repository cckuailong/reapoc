<div class="sb-customizer-preview" :data-preview-device="customizerScreens.previewScreen">
	<?php
		/**
		 * CFF Admin Notices
		 *
		 * @since 4.0
		 */
		do_action('cff_admin_notices');
	?>
	<div class="sb-preview-ctn sb-tr-2">
		<div class="sb-preview-top-chooser cff-fb-fs">
			<strong>{{genericText.preview}}</strong>
			<div class="sb-preview-chooser">
				<button class="sb-preview-chooser-btn" v-for="device in previewScreens" v-bind:class="'sb-' + device" v-html="svgIcons[device]" @click.prevent.default="switchCustomizerPreviewDevice(device)" :data-active="customizerScreens.previewScreen == device"></button>
			</div>
		</div>

		<div class="cff-preview-ctn cff-fb-fs" :class="( !valueIsEnabled(customizerFeedData.settings.headeroutside) && !valueIsEnabled(customizerFeedData.settings.likeboxoutside) ) ? 'cff-feed-height' : ''" :data-preview-colorscheme="customizerFeedData.settings.colorpalette" :data-plugin="pluginType">
			<?php
			include_once CFF_BUILDER_DIR . 'templates/preview/error.php';
			include_once CFF_BUILDER_DIR . 'templates/preview/header.php';
			?>
			<div class="cff-fb-fs" :class="( valueIsEnabled(customizerFeedData.settings.headeroutside) && !valueIsEnabled(customizerFeedData.settings.likeboxoutside) ) ? 'cff-feed-height' : ''" >
				<div class="cff-fb-fs" :class="( valueIsEnabled(customizerFeedData.settings.headeroutside) && valueIsEnabled(customizerFeedData.settings.likeboxoutside) ) ? 'cff-feed-height' : ''" >
					<?php
						include_once CFF_BUILDER_DIR . 'templates/preview/timeline.php';

						include_once CFF_BUILDER_DIR . 'templates/preview/post-types/photos.php';
						include_once CFF_BUILDER_DIR . 'templates/preview/post-types/albums.php';
						include_once CFF_BUILDER_DIR . 'templates/preview/post-types/videos.php';
						include_once CFF_BUILDER_DIR . 'templates/preview/post-types/reviews.php';
						include_once CFF_BUILDER_DIR . 'templates/preview/post-types/events.php';
						include_once CFF_BUILDER_DIR . 'templates/preview/post-types/single-holder.php';

						//include_once CFF_BUILDER_DIR . 'templates/preview/load-more.php';
					?>
				</div>
				<?php
					include_once CFF_BUILDER_DIR . 'templates/preview/like-box.php';
				?>
			</div>

			<?php
				include_once CFF_BUILDER_DIR . 'templates/preview/preview-components.php';
				//include_once CFF_BUILDER_DIR . 'templates/preview/light-box.php';
			?>
		</div>
	</div>
	<cff-post-lightbox-component :single-post="lightBox.post" :light-box="lightBox" :customizer-feed-data="customizerFeedData"  :translated-text="translatedText"></cff-post-lightbox-component>
	<cff-post-dummy-lightbox-component :dummy-light-box-data="dummyLightBoxData" :light-box="lightBox" :customizer-feed-data="customizerFeedData"  :translated-text="translatedText"></cff-post-dummy-lightbox-component>
</div>
