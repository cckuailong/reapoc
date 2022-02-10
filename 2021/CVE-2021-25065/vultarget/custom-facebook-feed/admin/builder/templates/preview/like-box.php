<div id="cff-like-box-section" class="cff-preview-likebox-ctn cff-fb-fs cff-preview-section" :data-dimmed="!isSectionHighLighted('likeBox')"  v-if="valueIsEnabled(customizerFeedData.settings.showlikebox)">
	<iframe :src="displayLikeBoxIframe()"></iframe>
</div>