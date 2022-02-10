<section id="cff-post-list-section" class="cff-preview-posts-list-ctn cff-preview-media-list-ctn cff-fb-fs" :class="customizerFeedData.settings.feedlayout == 'masonry' ? 'cff-preview-posts-masonry' : (customizerFeedData.settings.feedlayout == 'grid' ? 'cff-preview-posts-grid' : '')" v-if="customizerFeedData.settings.feedtype == 'photos'" :data-generate-masonry="customizerFeedData.settings.feedlayout == 'masonry' || customizerFeedData.settings.feedlayout == 'grid' ? generateMasonryGridHeight() : false" :data-feed-layout="customizerFeedData.settings.feedlayout" :data-feed-columns="getFeedColumns()">
	<div class="cff-photos-item-ctn cff-post-item-ctn cff-fb-fs cff-media-item-ctn" v-for="(singlePost, postIndex) in customizerFeedData.posts">
		<img :src="processPhotoSource(singlePost)" v-if="customizerFeedData.settings.feedlayout == 'masonry'" class="cff-post-item-content cff-media-item-image-poster cff-fb-fs">
		<div v-if="customizerFeedData.settings.feedlayout == 'grid'" :style="'background:url('+processPhotoSource(singlePost)+')'" class="cff-post-grid-image"></div>
		<img :src="processPhotoSource(singlePost)" class="cff-media-item-image-real cff-fb-fs">
	</div>
</section>