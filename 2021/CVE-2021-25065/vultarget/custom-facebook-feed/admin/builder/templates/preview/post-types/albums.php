<section id="cff-post-list-section" class="cff-preview-posts-list-ctn cff-fb-fs cff-preview-section" :data-dimmed="!isSectionHighLighted('postList')" :class="customizerFeedData.settings.feedlayout == 'masonry' ? 'cff-preview-posts-masonry' : (customizerFeedData.settings.feedlayout == 'grid' ? 'cff-preview-posts-grid' : '')" v-if="customizerFeedData.settings.feedtype == 'albums'" :data-generate-masonry="customizerFeedData.settings.feedlayout == 'masonry' || customizerFeedData.settings.feedlayout == 'grid' ? generateMasonryGridHeight() : false" :data-feed-layout="customizerFeedData.settings.feedlayout" :data-feed-columns="getFeedColumns()">
	<div class="cff-albums-item-ctn cff-singlemedia-item cff-post-item-ctn cff-fb-fs" v-for="(singlePost, postIndex) in customizerFeedData.posts" :data-lightbox="customizerFeedData.settings.disablelightbox != 'off' ? 'false' : 'true'">
		<div class="cff-post-item-content cff-fb-fs">
			<div class="cff-albums-item-cover cff-fb-fs">
				<div v-if="customizerFeedData.settings.feedlayout == 'grid'" :style="'background:url('+(hasOwnNestedProperty(singlePost, 'cover_photo.source') ? singlePost.cover_photo.source : '')+')'" class="cff-post-grid-image"></div>
				<img v-if="customizerFeedData.settings.feedlayout != 'grid'" :src="hasOwnNestedProperty(singlePost, 'cover_photo.source') ? singlePost.cover_photo.source : ''" class="cff-fb-fs">
			</div>
			<div class="cff-albums-item-info cff-singlemedia-item-info cff-fb-fs">
				<h4 class="cff-fb-fs">
					<a :href="'https://www.facebook.com/'+singlePost.id" target="_blank" v-html="singlePost.name"></a>
				</h4>
				<p class="cff-fb-fs">
					{{singlePost.count}} <span v-html="(singlePost.count > 1) ? genericText.photos : genericText.photo"></span>
				</p>
			</div>
		</div>
	</div>
</section>


<section id="cff-post-list-section" class="cff-preview-posts-list-ctn cff-preview-media-list-ctn cff-fb-fs" :data-dimmed="!isSectionHighLighted('postList')" :class="customizerFeedData.settings.feedlayout == 'masonry' ? 'cff-preview-posts-masonry' : (customizerFeedData.settings.feedlayout == 'grid' ? 'cff-preview-posts-grid' : '')" v-if="customizerFeedData.settings.feedtype == 'singlealbum'" :data-generate-masonry="customizerFeedData.settings.feedlayout == 'masonry' || customizerFeedData.settings.feedlayout == 'grid' ? generateMasonryGridHeight() : false" :data-feed-layout="customizerFeedData.settings.feedlayout"  :data-feed-type="customizerFeedData.settings.feedtype" :data-feed-columns="getFeedColumns()">
	<div class="cff-photos-item-ctn cff-post-item-ctn cff-fb-fs cff-media-item-ctn" v-for="(singlePost, postIndex) in customizerFeedData.posts" :style="customizerFeedData.settings.feedlayout == 'carousel' ? 'background-image:url(' + processPhotoSource(singlePost) + ')!important;' : ''">
		<img :src="processPhotoSource(singlePost)" v-if="customizerFeedData.settings.feedlayout == 'masonry'" class="cff-post-item-content cff-media-item-image-poster cff-fb-fs">
		<div v-if="customizerFeedData.settings.feedlayout == 'grid'" :style="'background:url('+processPhotoSource(singlePost)+')'" class="cff-post-grid-image"></div>
		<img :src="processPhotoSource(singlePost)" v-if="customizerFeedData.settings.feedlayout != 'carousel'" class="cff-media-item-image-real cff-fb-fs">
	</div>
</section>