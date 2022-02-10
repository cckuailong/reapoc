<script type="text/x-template" id="cff-post-videosposts-component">
	<section id="cff-post-list-section" class="cff-preview-posts-list-ctn cff-fb-fs cff-preview-section" :data-dimmed="!$parent.isSectionHighLighted('postList')" :class="customizerFeedData.settings.feedlayout == 'masonry' ? 'cff-preview-posts-masonry' : (customizerFeedData.settings.feedlayout == 'grid' ? 'cff-preview-posts-grid' : '')" v-if="customizerFeedData.settings.feedtype == 'videos'"  :data-generate-masonry="customizerFeedData.settings.feedlayout == 'masonry' ? $parent.generateMasonryGridHeight() : false" :data-feed-layout="customizerFeedData.settings.feedlayout" :data-feed-columns="$parent.getFeedColumns()">
		<div class="cff-videos-item-ctn cff-singlemedia-item cff-fb-fs cff-post-item-ctn" v-for="(singlePost, postIndex) in customizerFeedData.posts" :data-lightbox="customizerFeedData.settings.disablelightbox != 'off' ? 'false' : 'true'">
			<div class="cff-post-item-content cff-fb-fs">
				<div class="cff-post-overlay-parent cff-fb-fs" :style="(customizerFeedData.settings.feedlayout == 'grid' ? 'background-image:url(' + $parent.processVideosFeedImage(singlePost) + ')!important;' : '' )">
					<div class="cff-fb-fs" v-if="(customizerFeedData.settings.videoaction == 'post' && customizerFeedData.settings.disablelightbox == 'off') || customizerFeedData.settings.videoaction == 'facebook'">
						<img  class="cff-fb-fs" v-if="customizerFeedData.settings.feedlayout != 'grid'" :src="$parent.processVideosFeedImage(singlePost)">
						<div class="cff-play-video-icon"></div>
						<a class="cff-fullsize-link" :href="'https://www.facebook.com/'+singlePost.id" target="_blank" v-if="customizerFeedData.settings.videoaction == 'facebook'"></a>
						<cff-post-overlay-component v-if="customizerFeedData.settings.videoaction == 'post'" :single-post="singlePost" :customizer-feed-data="customizerFeedData" :parent="$parent"></cff-post-overlay-component>
					</div>
					<div class="cff-videos-item-source cff-fb-fs" v-html="singlePost.embed_html" v-if="customizerFeedData.settings.videoaction == 'post' && customizerFeedData.settings.disablelightbox != 'off'"></div>
				</div>
				<div class="cff-videos-item-info cff-singlemedia-item-info cff-fb-fs">
					<h4>
						<a :href="'https://www.facebook.com/'+singlePost.id" target="_blank" v-html="singlePost.title"></a>
					</h4>
					<p v-html="(singlePost.description != null && singlePost.description != undefined ? (singlePost.description.substring(0, 50) + (singlePost.description.length > 50 ? '...' : '') ) : '')"></p>
				</div>
			</div>
		</div>
	</section>
</script>
<cff-post-videosposts-component :customizer-feed-data="customizerFeedData"></cff-post-videosposts-component>