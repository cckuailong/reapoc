<div class="cff-feeds-list cff-fb-fs" v-if="(feedsList != null && feedsList.length > 0 ) || (legacyFeedsList != null && legacyFeedsList.length > 0)">
	<?php
		include_once CFF_BUILDER_DIR . 'templates/sections/feeds/legacy-feeds.php';
		include_once CFF_BUILDER_DIR . 'templates/sections/feeds/feeds.php';
	?>
</div>
<?php
include_once CFF_BUILDER_DIR . 'templates/sections/feeds/instances.php';