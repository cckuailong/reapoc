<div v-if="viewsActive.pageScreen == 'selectFeed'" class="cff-fb-fs">
	<div class="cff-fb-create-ctn cff-fb-wrapper">
		<div class="cff-fb-heading">
			<h1>{{selectFeedTypeScreen.mainHeading}}</h1>
			<div class="cff-fb-btn cff-fb-slctf-nxt cff-fb-btn-ac cff-btn-orange" :data-active="creationProcessCheckAction()" @click.prevent.default="creationProcessNext()">
				<span>{{genericText.next}}</span>
				<svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M1.3332 0.00683594L0.158203 1.18184L3.97487 5.00684L0.158203 8.83184L1.3332 10.0068L6.3332 5.00684L1.3332 0.00683594Z" fill="white"/>
				</svg>
			</div>
		</div>
		<?php
		include_once CFF_BUILDER_DIR . 'templates/sections/feeds-type.php';
		include_once CFF_BUILDER_DIR . 'templates/sections/select-source.php';
		include_once CFF_BUILDER_DIR . 'templates/sections/create-feed/single-album.php';
		include_once CFF_BUILDER_DIR . 'templates/sections/create-feed/featured-post.php';
		include_once CFF_BUILDER_DIR . 'templates/sections/create-feed/videos.php';
		?>
	</div>
	<div class="cff-fb-ft-action cff-fb-slctfd-action cff-fb-fs">
		<div class="cff-fb-wrapper">
			<div class="cff-fb-slctf-back cff-fb-hd-btn cff-btn-grey" @click.prevent.default="creationProcessBack()"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M6.3415 1.18184L5.1665 0.00683594L0.166504 5.00684L5.1665 10.0068L6.3415 8.83184L2.52484 5.00684L6.3415 1.18184Z" fill="#141B38"/>
			</svg>
			<span>{{genericText.back}}</span>
		</div>
		<div class="cff-fb-btn cff-fb-slctf-nxt cff-fb-btn-ac cff-btn-orange" :data-active="creationProcessCheckAction()" @click.prevent.default="creationProcessNext()">
			<span>{{genericText.next}}</span>
			<svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M1.3332 0.00683594L0.158203 1.18184L3.97487 5.00684L0.158203 8.83184L1.3332 10.0068L6.3332 5.00684L1.3332 0.00683594Z" fill="white"/>
			</svg>
		</div>
	</div>
</div>
</div>