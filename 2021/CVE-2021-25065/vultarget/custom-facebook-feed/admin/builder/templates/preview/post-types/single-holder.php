<div class="cff-single-holder-ctn cff-fb-fs" v-if="(customizerFeedData.settings.feedtype == 'singlealbum' || customizerFeedData.settings.feedtype == 'featuredpost') && customizerFeedData.posts.length == 0">
	<div class="cff-single-holder-img" v-html="singleHolderData.icon"></div>
	<div class="cff-single-holder-content cff-fb-fs">
		<strong class="cff-fb-fs" v-html="singleHolderData.heading"></strong>
		<p class="cff-fb-fs" v-html="singleHolderData.text"></p>
	</div>
</div>