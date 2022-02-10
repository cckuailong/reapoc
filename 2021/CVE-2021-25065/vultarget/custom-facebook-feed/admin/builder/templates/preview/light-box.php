<script type="text/x-template" id="cff-post-lightbox-component">
	<div class="cff-lightbox-ctn" v-if="singlePost != null" :data-visibility="lightBox.visibility" :data-type="lightBox.type" id="cff-lightbox-ctn" :data-comments="$parent.valueIsEnabled(customizerFeedData.settings.lightboxcomments) ? 'true' : 'false'">
		<div class="cff-lightbox-closer" @click.prevent.default="$parent.hideLightBox()"></div>
		<div class="cff-lightbox-content">
			<div class="cff-lightbox-content-insider">
				<div class="cff-lightbox-element">
					<div class="cff-lightbox-image" v-if="lightBox.type == 'albums' || lightBox.type == 'photos' || lightBox.type == 'events'">
						<img v-if="$parent.checkNotEmpty(lightBox.activeImage)" :src="lightBox.activeImage">
					</div>
					<div class="cff-lightbox-video" v-if="lightBox.type == 'videos' && lightBox.videoSource != null">
						<iframe :src="lightBox.videoSource"></iframe>
					</div>
					<div class="cff-lightbox-video" v-if="lightBox.type == 'embed_videos' && lightBox.videoSource != null" v-html="lightBox.videoSource">
					</div>
				</div>

				<div class="cff-lightbox-sidebar" v-if="$parent.valueIsEnabled(customizerFeedData.settings.lightboxcomments)">
					<div class="cff-lightbox-cls" @click.prevent.default="$parent.hideLightBox()"></div>
					<cff-post-author-component v-if="lightBox.post" :single-post="lightBox.post" :customizer-feed-data="customizerFeedData"></cff-post-author-component>
					<div class="cff-post-item-text cff-fb-fs">
						<cff-post-event-detail-component :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-event-detail-component>
						<span v-html="$parent.printPostText( singlePost, true )"></span>
					</div>
					<cff-post-meta-component v-if="lightBox.post && $parent.valueIsEnabled(customizerFeedData.settings.lightboxcomments)" :single-post="lightBox.post" :customizer-feed-data="customizerFeedData" :translated-text="translatedText"></cff-post-meta-component>
				</div>
				<div class="cff-lightbox-prev cff-lightbox-nav" @click.prevent.default="$parent.navigateLightboxAlbumImage('prev',singlePost.attachments.data[0].subattachments.data)"><div class="cff-lightbox-nav-icon" v-if="lightBox.type == 'albums' && singlePost.attachments.data[0].subattachments.data.length > 1"></div></div>
				<div class="cff-lightbox-next cff-lightbox-nav" @click.prevent.default="$parent.navigateLightboxAlbumImage('next',singlePost.attachments.data[0].subattachments.data)"><div class="cff-lightbox-nav-icon" v-if="lightBox.type == 'albums' && singlePost.attachments.data[0].subattachments.data.length > 1"></div></div>
			</div>

			<div class="cff-lightbox-thumbs cff-fb-fs" v-if="lightBox.type == 'albums' && singlePost.attachments.data[0].subattachments.data.length > 1">
				<span class="cff-lightbox-thumb-item" v-for="(subAttachment, subAttachmentIndex) in singlePost.attachments.data[0].subattachments.data" @click.prevent.default="$parent.switchLightboxAlbumImage(subAttachment.media.image.src, subAttachmentIndex)" :data-active="lightBox.albumIndex === subAttachmentIndex ? 'true' : 'false'" :style="'background-image:url('+subAttachment.media.image.src+');'"></span>
			</div>

			<div class="cff-lightbox-caption" v-if="!$parent.valueIsEnabled(customizerFeedData.settings.lightboxcomments)">
				<div class="cff-lightbox-cls" @click.prevent.default="$parent.hideLightBox()"></div>
				<div class="cff-post-item-text cff-fb-fs">
					<cff-post-event-detail-component :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-event-detail-component>
					<span v-html="$parent.printPostText( singlePost, true )"></span>
				</div>
			</div>


		</div>

	</div>
</script>

<script type="text/x-template" id="cff-post-dummy-lightbox-component">
<div class="cff-lightbox-ctn cff-dummy-lightbox-ctn" :data-visibility="dummyLightBoxData.visibility" data-type="photos">
	<div class="cff-lightbox-closer" @click.prevent.default="$parent.hideLightBox()"></div>
	<div class="cff-lightbox-content">
		<div class="cff-lightbox-content-insider">
			<div class="cff-lightbox-element">
				<div class="cff-lightbox-image">
					<img :src="dummyLightBoxData.image">
				</div>
			</div>
			<div class="cff-lightbox-sidebar">
				<div class="cff-lightbox-cls" @click.prevent.default="$parent.hideLightBox()"></div>
				<cff-post-author-component v-if="dummyLightBoxData.post" :single-post="dummyLightBoxData.post" :customizer-feed-data="customizerFeedData"></cff-post-author-component>
				<div class="cff-post-item-text cff-fb-fs">
					<cff-post-event-detail-component :single-post="dummyLightBoxData.post" :customizer-feed-data="customizerFeedData"></cff-post-event-detail-component>
					<span v-html="$parent.printPostText( dummyLightBoxData.post, true )"></span>
				</div>
				<cff-post-meta-component v-if="dummyLightBoxData.post && $parent.valueIsEnabled(customizerFeedData.settings.lightboxcomments)" :single-post="dummyLightBoxData.post" :customizer-feed-data="customizerFeedData" :translated-text="translatedText"></cff-post-meta-component>
			</div>
			<div class="cff-lightbox-prev cff-lightbox-nav"><div class="cff-lightbox-nav-icon"></div></div>
			<div class="cff-lightbox-next cff-lightbox-nav"><div class="cff-lightbox-nav-icon"></div></div>
		</div>
	</div>
</script>
