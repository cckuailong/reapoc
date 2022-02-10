<script type="text/x-template" id="cff-post-author-component">
	<div class="cff-post-item-info-ctn cff-fb-fs" v-if="customizerFeedData.settings.feedtype != 'events'">
		<div class="cff-post-item-avatar" v-if="customizerFeedData.settings.include.includes('author')">
			<a v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'from.picture.data.url')" href="" target="_blank">
				<img :src="singlePost.from.picture.data.url">
			</a>
		</div>
		<div class="cff-post-item-info" v-if="customizerFeedData.settings.include.includes('author')">
			<div class="cff-post-item-info-top">
				<a v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'from.name')" class="cff-post-item-author-name" href="" target="_blank" v-html="singlePost.from.name"></a>
				<span class="cff-post-item-story" v-html="$parent.$parent.printStory(singlePost)"></span>
				<span class="cff-rating" v-if="customizerFeedData.settings.feedtype == 'reviews' && singlePost.rating != undefined">
					<span class="cff-star" v-for="singleRating in singlePost.rating" :key="singleRating">★</span>
					<span class="cff-rating-num" v-html="singlePost.rating"></span>
				</span>
			</div>
			<div class="cff-post-item-info-bottom">
				<span class="cff-post-item-date" v-if="customizerFeedData.settings.include.includes('date')">
					<span class="cff-post-item-date-before" v-if="$parent.$parent.valueIsEnabled(customizerFeedData.settings.beforedateenabled)">{{customizerFeedData.settings.beforedate}}</span>
					<span v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'created_time')" v-html="$parent.$parent.printDate(singlePost.created_time)"></span>
					<span class="cff-post-item-date-after" v-if="$parent.$parent.valueIsEnabled(customizerFeedData.settings.afterdateenabled)">{{customizerFeedData.settings.afterdate}}</span>
				</span>
			</div>
		</div>
	</div>
</script>

<script type="text/x-template" id="cff-iframe-media-component">
	<div class="cff-post-item-media-ctn cff-fb-fs" v-if="postmedia">
		<div class="cff-post-item-iframe-ctn" :data-source="postmedia.site" v-if="checkIframePostDisplay(postmedia)">
			<div class="cff-post-item-iframe">
				<iframe :src="postmedia.url" height="200" frameborder="0" type="text/html" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div>
			<cff-post-overlay-component :parent="$parent.$parent" v-if="postmedia.site == 'video'" :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-overlay-component>
		</div>
		<div class="cff-post-item-video-ctn" v-if="postmedia.type == 'video' && customizerFeedData.settings.include.includes('media')">
			<iframe :src="'https://www.facebook.com/v2.3/plugins/video.php?href='+postmedia.args.unshimmedUrl" height="200" type="text/html"></iframe>
			<cff-post-overlay-component :parent="$parent.$parent" :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-overlay-component>
		</div>
		<div class="cff-post-item-link-ctn" v-if="postmedia.type == 'link' && customizerFeedData.settings.include.includes('sharedlinks')" :data-linkbox="customizerFeedData.settings.disablelinkbox">
			<a href="" v-if="customizerFeedData.settings.include.includes('media')">
				<img :src="postmedia.args.poster">
			</a>
			<div class="cff-post-item-link-info cff-fb-fs">
				<a class="cff-post-item-link-a" :href="postmedia.args.unshimmedUrl" target="_blank" v-html="postmedia.args.title"></a>
				<div class="cff-post-item-link-small" v-html="postmedia.args.domain"></div>
				<div class="cff-post-item-link-description" v-if="customizerFeedData.settings.include.includes('desc')" v-html="(postmedia.args.description != null) ? (postmedia.args.description.substring(0, customizerFeedData.settings.desclength) + (postmedia.args.description.length > customizerFeedData.settings.desclength ? '...' : '')) : ''"></div>
			</div>
		</div>
		<div class="cff-post-item-text" v-if="postmedia.type == 'link' && !customizerFeedData.settings.include.includes('sharedlinks')" v-html="postmedia.args.title">
		</div>
	</div>
</script>


<script type="text/x-template" id="cff-post-media-component">
	<div class="cff-post-item-media-wrap cff-fb-fs" v-if="$parent.$parent.checkProcessPostImage(singlePost)">
		<div class="cff-post-item-media-album">
			<cff-post-overlay-component :parent="$parent.$parent" :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-overlay-component>
			<a href="#">
				<div class="cff-post-item-album-poster">
					<img class="cff-post-item-full-img" :src="$parent.$parent.processPostImageSrc(singlePost)">
				</div>
				<div class="cff-post-item-album-thumbs" v-if="customizerFeedData.settings.feedtype != 'events' && singlePost.attachments.data[0].subattachments" :data-length="singlePost.attachments.data[0].subattachments.data.slice(1, 4).length">
					<span class="cff-post-item-album-thumb" v-for="(subAttachment, subAttachmentIndex) in singlePost.attachments.data[0].subattachments.data.slice(1, 4)" :style="'background-image:url('+subAttachment.media.image.src+');'">
						<span class="cff-post-item-album-thumb-overlay" v-if="singlePost.attachments.data[0].subattachments.data.length >= 4 && subAttachmentIndex == 2" v-html="$parent.$parent.printAlbumImageNumberOverlay(singlePost.attachments.data[0].subattachments.data)"></span>
					</span>
				</div>
			</a>
		</div>
	</div>
</script>


<script type="text/x-template" id="cff-post-text-component">

</script>

<script type="text/x-template" id="cff-post-overlay-component">
	<div class="cff-post-overlay" v-if="!parent.valueIsEnabled(customizerFeedData.settings.disablelightbox)" @click.prevent.default="parent.getPostElementOverlay(singlePost)">

	</div>
</script>




<script type="text/x-template" id="cff-post-meta-component">
	<div class="cff-post-item-meta-wrap cff-fb-fs" v-if="customizerFeedData.settings.feedtype != 'events'">
		<div class="cff-post-item-meta-top cff-fb-fs" v-if="customizerFeedData.settings.feedtype != 'reviews'">
			<div class="cff-post-item-meta cff-post-item-meta-bg" v-if="customizerFeedData.settings.include.includes('social') && pluginType == 'pro'" @click.prevent.default="$parent.$parent.toggleCommentSection(singlePost.id)"  :data-icon-theme="customizerFeedData.settings.iconstyle">
				<a class="cff-post-item-view-comment">
					<span class="cff-post-meta-item">
						<span class="cff-post-meta-item-icon cff-post-meta-item-icon-like">
							<svg viewBox="0 0 512 512"><path d="M496.656 285.683C506.583 272.809 512 256 512 235.468c-.001-37.674-32.073-72.571-72.727-72.571h-70.15c8.72-17.368 20.695-38.911 20.695-69.817C389.819 34.672 366.518 0 306.91 0c-29.995 0-41.126 37.918-46.829 67.228-3.407 17.511-6.626 34.052-16.525 43.951C219.986 134.75 184 192 162.382 203.625c-2.189.922-4.986 1.648-8.032 2.223C148.577 197.484 138.931 192 128 192H32c-17.673 0-32 14.327-32 32v256c0 17.673 14.327 32 32 32h96c17.673 0 32-14.327 32-32v-8.74c32.495 0 100.687 40.747 177.455 40.726 5.505.003 37.65.03 41.013 0 59.282.014 92.255-35.887 90.335-89.793 15.127-17.727 22.539-43.337 18.225-67.105 12.456-19.526 15.126-47.07 9.628-69.405zM32 480V224h96v256H32zm424.017-203.648C472 288 472 336 450.41 347.017c13.522 22.76 1.352 53.216-15.015 61.996 8.293 52.54-18.961 70.606-57.212 70.974-3.312.03-37.247 0-40.727 0-72.929 0-134.742-40.727-177.455-40.727V235.625c37.708 0 72.305-67.939 106.183-101.818 30.545-30.545 20.363-81.454 40.727-101.817 50.909 0 50.909 35.517 50.909 61.091 0 42.189-30.545 61.09-30.545 101.817h111.999c22.73 0 40.627 20.364 40.727 40.727.099 20.363-8.001 36.375-23.984 40.727zM104 432c0 13.255-10.745 24-24 24s-24-10.745-24-24 10.745-24 24-24 24 10.745 24 24z"></path></svg>
							<svg viewBox="0 0 512 512" class="cff-svg-bg"><path d="M104 224H24c-13.255 0-24 10.745-24 24v240c0 13.255 10.745 24 24 24h80c13.255 0 24-10.745 24-24V248c0-13.255-10.745-24-24-24zM64 472c-13.255 0-24-10.745-24-24s10.745-24 24-24 24 10.745 24 24-10.745 24-24 24zM384 81.452c0 42.416-25.97 66.208-33.277 94.548h101.723c33.397 0 59.397 27.746 59.553 58.098.084 17.938-7.546 37.249-19.439 49.197l-.11.11c9.836 23.337 8.237 56.037-9.308 79.469 8.681 25.895-.069 57.704-16.382 74.757 4.298 17.598 2.244 32.575-6.148 44.632C440.202 511.587 389.616 512 346.839 512l-2.845-.001c-48.287-.017-87.806-17.598-119.56-31.725-15.957-7.099-36.821-15.887-52.651-16.178-6.54-.12-11.783-5.457-11.783-11.998v-213.77c0-3.2 1.282-6.271 3.558-8.521 39.614-39.144 56.648-80.587 89.117-113.111 14.804-14.832 20.188-37.236 25.393-58.902C282.515 39.293 291.817 0 312 0c24 0 72 8 72 81.452z"></path></svg>
						</span>
						<span class="cff-post-meta-item-text cff-post-meta-txt" v-html="$parent.$parent.hasOwnNestedProperty(singlePost, 'likes.summary.total_count') ? singlePost.likes.summary.total_count : '0'"></span>
					</span>
					<span class="cff-post-meta-item">
						<span class="cff-post-meta-item-icon cff-post-meta-item-icon-share">
							<svg viewBox="0 0 576 512"><path d="M564.907 196.35L388.91 12.366C364.216-13.45 320 3.746 320 40.016v88.154C154.548 130.155 0 160.103 0 331.19c0 94.98 55.84 150.231 89.13 174.571 24.233 17.722 58.021-4.992 49.68-34.51C100.937 336.887 165.575 321.972 320 320.16V408c0 36.239 44.19 53.494 68.91 27.65l175.998-184c14.79-15.47 14.79-39.83-.001-55.3zm-23.127 33.18l-176 184c-4.933 5.16-13.78 1.73-13.78-5.53V288c-171.396 0-295.313 9.707-243.98 191.7C72 453.36 32 405.59 32 331.19 32 171.18 194.886 160 352 160V40c0-7.262 8.851-10.69 13.78-5.53l176 184a7.978 7.978 0 0 1 0 11.06z"></path></svg>
							<svg viewBox="0 0 512 512" class="cff-svg-bg"><path d="M503.691 189.836L327.687 37.851C312.281 24.546 288 35.347 288 56.015v80.053C127.371 137.907 0 170.1 0 322.326c0 61.441 39.581 122.309 83.333 154.132 13.653 9.931 33.111-2.533 28.077-18.631C66.066 312.814 132.917 274.316 288 272.085V360c0 20.7 24.3 31.453 39.687 18.164l176.004-152c11.071-9.562 11.086-26.753 0-36.328z"></path></svg>
						</span>
						<span class="cff-post-meta-item-text cff-post-meta-txt" v-html="$parent.$parent.hasOwnNestedProperty(singlePost, 'shares.count') ? singlePost.shares.count : '0'"></span>
					</span>
					<span class="cff-post-meta-item">
						<span class="cff-post-meta-item-icon cff-post-meta-item-icon-comment">
							<svg viewBox="0 0 512 512"><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm32 352c0 17.6-14.4 32-32 32H293.3l-8.5 6.4L192 460v-76H64c-17.6 0-32-14.4-32-32V64c0-17.6 14.4-32 32-32h384c17.6 0 32 14.4 32 32v288z"></path></svg>
							<svg viewBox="0 0 512 512" class="cff-svg-bg"><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"></path></svg>
						</span>
						<span class="cff-post-meta-item-text cff-post-meta-txt" v-html="$parent.$parent.hasOwnNestedProperty(singlePost, 'comments.summary.total_count') ? singlePost.comments.summary.total_count : '0'"></span>
					</span>
				</a>
			</div>
			<div class="cff-post-item-action-link" v-if="customizerFeedData.settings.include.includes('link')">
				<a class="cff-post-item-action-txt" v-if="$parent.$parent.valueIsEnabled(customizerFeedData.settings.showfacebooklink)" :href="'https://www.facebook.com/'+singlePost.id" target="_blank">{{customizerFeedData.settings.facebooklinktext}}</a>
				<span class="cff-post-item-action-txt cff-post-item-dot" v-if="$parent.$parent.valueIsEnabled(customizerFeedData.settings.showfacebooklink) && $parent.$parent.valueIsEnabled(customizerFeedData.settings.showsharelink)">&middot;</span>
				<span class="cff-post-item-share-link" v-if="$parent.$parent.valueIsEnabled(customizerFeedData.settings.showsharelink)">
					<div class="cff-post-item-share-tooltip" v-show="$parent.$parent.showedSocialShareTooltip == singlePost.id">
						<a v-for="(socialLink, socialName) in $parent.$parent.socialShareLink" :href="socialLink + 'https://www.facebook.com/'+singlePost.id" :class="'cff-bghv-'+socialName" v-html="$parent.$parent.svgIcons[socialName +'Share']" target="_blank"></a>
					</div>
					<span class="cff-post-item-action-txt" @click.prevent.default="$parent.$parent.toggleSocialShareTooltip(singlePost.id)">{{customizerFeedData.settings.sharelinktext}}</span>
				</span>
			</div>
		</div>
		<div class="cff-post-item-meta-comments cff-fb-fs" v-if="customizerFeedData.settings.feedtype != 'reviews'" v-show="($parent.$parent.showedCommentSection.includes(singlePost.id) && !$parent.$parent.valueIsEnabled(customizerFeedData.settings.expandcomments)) || (!$parent.$parent.showedCommentSection.includes(singlePost.id) && $parent.$parent.valueIsEnabled(customizerFeedData.settings.expandcomments))">
			<div class="cff-post-item-comments-top cff-fb-fs cff-post-item-meta-bg">
				<div class="cff-post-item-comments-icon">
					<svg viewBox="0 0 512 512"><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm32 352c0 17.6-14.4 32-32 32H293.3l-8.5 6.4L192 460v-76H64c-17.6 0-32-14.4-32-32V64c0-17.6 14.4-32 32-32h384c17.6 0 32 14.4 32 32v288z"></path></svg>
				</div>
				<a class="cff-post-meta-link" :href="'https://www.facebook.com/'+singlePost.id" v-html="translatedText.commentonFacebookText" target="_blank"></a>
			</div>
			<div class="cff-post-item-comments-list cff-fb-fs  cff-post-item-meta-bg" v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'comments.data') && singlePost.comments.data.length > 0">
				<div class="cff-post-comment-item cff-fb-fs" v-for="singleComment in singlePost.comments.data">
					<a class="cff-post-comment-item-avatar" v-if="$parent.$parent.hasOwnNestedProperty(singleComment, 'from.picture.data.url') && !$parent.$parent.valueIsEnabled(customizerFeedData.settings.hidecommentimages)" :href="$parent.$parent.hasOwnNestedProperty(singleComment, 'from.link') ? singleComment.from.link : '#'" target="_blank">
						<img :src="singleComment.from.picture.data.url" alt="">
					</a>
					<div class="cff-post-comment-item-content">
						<p>
							<a class="cff-post-comment-item-author cff-post-meta-link" :href="$parent.$parent.hasOwnNestedProperty(singleComment, 'from.link') ? singleComment.from.link : '#'" target="_blank" v-html="$parent.$parent.hasOwnNestedProperty(singleComment, 'from.name') ? singleComment.from.name : ''"></a>
							<span class="cff-post-comment-item-txt cff-post-meta-txt" v-html="singleComment.message"></span>
						</p>
						<span class="cff-post-comment-item-date cff-post-meta-txt" v-html="$parent.$parent.printDate(singleComment.created_time)"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="cff-fb-fs" v-if="customizerFeedData.settings.feedtype == 'reviews'">
			<a class="cff-post-item-action-txt" :href="$parent.$parent.hasOwnNestedProperty(customizerFeedData, 'header.id') ? 'https://www.facebook.com/'+customizerFeedData.header.id+'/reviews' : ''" target="_blank">{{customizerFeedData.settings.reviewslinktext}}</a>
		</div>
	</div>
</script>


<script type="text/x-template" id="cff-post-event-detail-component">
	<div class="cff-post-event-detail cff-fb-fs"  v-if="customizerFeedData.settings.feedtype == 'events' || (singlePost.status_type == 'created_event')">
		<p v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'attachments.data') && singlePost.attachments.data[0] && singlePost.attachments.data[0].title" v-html="singlePost.attachments.data[0].title">
		</p>
		<!--
		<p class="cff-post-event-title cff-fb-fs" v-if="customizerFeedData.settings.include.includes('eventtitle')">
			<a :href="'https://facebook.com/events/'+singlePost.id" target="_blank" v-html="singlePost.name"></a>
		</p>
		-->
		<p class="cff-post-event-date cff-fb-fs" v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'start_time') || $parent.$parent.hasOwnNestedProperty(singlePost, 'end_time')">
			<span class="cff-post-event-start-date" v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'start_time')" v-html="$parent.$parent.printDate(singlePost.start_time, true)"></span>
			<span class="cff-post-event-end-date" v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'end_time')" v-html="'-' + $parent.$parent.printDate(singlePost.end_time, true)"></span>
		</p>

		<p class="cff-post-event-location cff-fb-fs" v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'place') && customizerFeedData.settings.include.includes('eventdetails')">
			<a class="cff-post-event-place" v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'place.name') && $parent.$parent.hasOwnNestedProperty(singlePost, 'place.id')" :href="'https://facebook.com/'+singlePost.place.id" target="_blank"  v-html="$parent.$parent.htmlEntities(singlePost.place.name)"></a>
			<span class="cff-post-event-street" v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'place.location.street')" v-html="$parent.$parent.htmlEntities(singlePost.place.location.street)"></span>
			<span class="cff-post-event-city" v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'place.location.city')" v-html="$parent.$parent.htmlEntities(singlePost.place.location.city)"></span>
			<span class="cff-post-event-state" v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'place.location.state')" v-html="$parent.$parent.htmlEntities(singlePost.place.location.state)"></span>
			<span class="cff-post-event-zip" v-if="$parent.$parent.hasOwnNestedProperty(singlePost, 'place.location.zip')" v-html="$parent.$parent.htmlEntities(singlePost.place.location.zip)"></span>
			<a class="cff-post-event-maplink" v-if="$parent.$parent.getEventMapLink(singlePost.place) !== false" :href="$parent.$parent.getEventMapLink(singlePost.place)">Map</a>
		</p>


	</div>
</script>


<script type="text/x-template" id="cff-post-full-layout-component">
	<article class="cff-post-item-ctn" v-if="$parent.checkShowPost(singlePost)" :data-post-layout="customizerFeedData.settings.layout" :data-post-type="$parent.getPostTypeTimeline(singlePost)">
		<div class="cff-post-item-content cff-fb-fs">
			<cff-post-author-component :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-author-component>
			<cff-post-media-component v-if="pluginType == 'pro'" :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-media-component>
			<cff-iframe-media-component v-if="$parent.processIframeAndLinkAndVideo( singlePost ) !== false && pluginType == 'pro'" :single-post="singlePost" :postmedia="$parent.processIframeAndLinkAndVideo( singlePost )" :customizer-feed-data="customizerFeedData"></cff-iframe-media-component>
			<div class="cff-post-item-text cff-fb-fs" v-if="$parent.getPostTypeTimeline(singlePost) != 'links'">
				<cff-post-event-detail-component :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-event-detail-component>
				<span v-html="$parent.expandedPostText.includes(singlePost.id) ? $parent.printPostText( singlePost, true ) : $parent.printPostText( singlePost )" v-if="customizerFeedData.settings.include.includes('text')"></span>
				<span class="cff-post-item-text-expand" v-if="singlePost.message && singlePost.message.length > customizerFeedData.settings.textlength && customizerFeedData.settings.include.includes('text')">
					...<a @click.prevent.default="$parent.expandPostText( singlePost.id )" v-html="$parent.printExpandText(singlePost.id)"></a>
				</span>
			</div>
			<cff-free-linkbox-component v-if="$parent.processIframeAndLinkAndVideo( singlePost ) !== false && pluginType == 'free'" :single-post="singlePost" :postmedia="$parent.processVideoAndLink( singlePost )" :customizer-feed-data="customizerFeedData"></cff-free-linkbox-component>
			<span class="cff-post-item-date" v-if="!customizerFeedData.settings.include.includes('author') && customizerFeedData.settings.include.includes('date')">
				<span class="cff-post-item-date-before" v-if="$parent.valueIsEnabled(customizerFeedData.settings.beforedateenabled)">{{customizerFeedData.settings.beforedate}}</span>
				<span v-if="$parent.hasOwnNestedProperty(singlePost, 'created_time')" v-html="$parent.printDate(singlePost.created_time)"></span>
				<span class="cff-post-item-date-after" v-if="$parent.valueIsEnabled(customizerFeedData.settings.afterdateenabled)">{{customizerFeedData.settings.afterdate}}</span>
			</span>
			<cff-post-free-icon-component :single-post="singlePost" :customizer-feed-data="customizerFeedData" :translated-text="translatedText"></cff-post-free-icon-component>
			<cff-post-meta-component :single-post="singlePost" :customizer-feed-data="customizerFeedData" :translated-text="translatedText"></cff-post-meta-component>
		</div>
	</article>
</script>


<script type="text/x-template" id="cff-post-half-layout-component">
	<article class="cff-post-item-ctn" v-if="$parent.checkShowPost(singlePost)" :data-post-layout="customizerFeedData.settings.layout" :data-post-type="$parent.getPostTypeTimeline(singlePost)">
		<div class="cff-post-item-content cff-fb-fs">
			<div class="cff-post-item-sides cff-fb-fs">
				<div class="cff-post-item-side cff-post-item-left">
					<cff-post-media-component :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-media-component>
					<cff-iframe-media-component v-if="$parent.processIframeAndLinkAndVideo( singlePost ) !== false" :single-post="singlePost" :postmedia="$parent.processIframeAndLinkAndVideo( singlePost )" :customizer-feed-data="customizerFeedData"></cff-iframe-media-component>
				</div>

				<div class="cff-post-item-side cff-post-item-right">
					<cff-post-author-component :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-author-component>
					<div class="cff-post-item-text cff-fb-fs">
						<cff-post-event-detail-component :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-event-detail-component>
						<span v-html="$parent.expandedPostText.includes(singlePost.id) ? $parent.printPostText( singlePost, true ) : $parent.printPostText( singlePost )" v-if="customizerFeedData.settings.include.includes('text')"></span>
						<span class="cff-post-item-text-expand" v-if="singlePost.message && singlePost.message.length > customizerFeedData.settings.textlength && customizerFeedData.settings.include.includes('text')">
							...<a @click.prevent.default="$parent.expandPostText( singlePost.id )" v-html="$parent.printExpandText(singlePost.id)"></a>
						</span>
					</div>
					<span class="cff-post-item-date" v-if="!customizerFeedData.settings.include.includes('author') && customizerFeedData.settings.include.includes('date')">
						<span class="cff-post-item-date-before" v-if="$parent.valueIsEnabled(customizerFeedData.settings.beforedateenabled)">{{customizerFeedData.settings.beforedate}}</span>
						<span v-if="$parent.hasOwnNestedProperty(singlePost, 'created_time')" v-html="$parent.printDate(singlePost.created_time)"></span>
						<span class="cff-post-item-date-after" v-if="$parent.valueIsEnabled(customizerFeedData.settings.afterdateenabled)">{{customizerFeedData.settings.afterdate}}</span>
					</span>
				</div>
			</div>

			<cff-post-meta-component :single-post="singlePost" :customizer-feed-data="customizerFeedData" :translated-text="translatedText"></cff-post-meta-component>
		</div>
	</article>
</script>

<script type="text/x-template" id="cff-post-thumb-layout-component">
	<article class="cff-post-item-ctn" v-if="$parent.checkShowPost(singlePost)" :data-post-layout="customizerFeedData.settings.layout" :data-post-type="$parent.getPostTypeTimeline(singlePost)">
		<div class="cff-post-item-content cff-fb-fs">
			<div class="cff-post-item-sides  cff-fb-fs">
				<div class="cff-post-item-side cff-post-item-left">
					<cff-post-media-component :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-media-component>
					<cff-iframe-media-component v-if="$parent.processIframeAndLinkAndVideo( singlePost ) !== false" :single-post="singlePost" :postmedia="$parent.processIframeAndLinkAndVideo( singlePost )" :customizer-feed-data="customizerFeedData"></cff-iframe-media-component>
				</div>

				<div class="cff-post-item-side cff-post-item-right">
					<cff-post-author-component :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-author-component>
					<div class="cff-post-item-text cff-fb-fs">
						<cff-post-event-detail-component :single-post="singlePost" :customizer-feed-data="customizerFeedData"></cff-post-event-detail-component>
						<span v-html="$parent.expandedPostText.includes(singlePost.id) ? $parent.printPostText( singlePost, true ) : $parent.printPostText( singlePost )" v-if="customizerFeedData.settings.include.includes('text')"></span>
						<span class="cff-post-item-text-expand" v-if="singlePost.message && singlePost.message.length > customizerFeedData.settings.textlength && customizerFeedData.settings.include.includes('text')">
							...<a @click.prevent.default="$parent.expandPostText( singlePost.id )" v-html="$parent.printExpandText(singlePost.id)"></a>
						</span>
					</div>
					<span class="cff-post-item-date" v-if="!customizerFeedData.settings.include.includes('author') && customizerFeedData.settings.include.includes('date')">
						<span class="cff-post-item-date-before" v-if="$parent.valueIsEnabled(customizerFeedData.settings.beforedateenabled)">{{customizerFeedData.settings.beforedate}}</span>
						<span v-if="$parent.hasOwnNestedProperty(singlePost, 'created_time')" v-html="$parent.printDate(singlePost.created_time)"></span>
						<span class="cff-post-item-date-after" v-if="$parent.valueIsEnabled(customizerFeedData.settings.afterdateenabled)">{{customizerFeedData.settings.afterdate}}</span>
					</span>
				</div>
			</div>

			<cff-post-meta-component :single-post="singlePost" :customizer-feed-data="customizerFeedData" :translated-text="translatedText"></cff-post-meta-component>
		</div>
	</article>

</script>


<script type="text/x-template" id="cff-post-free-icon-component">
	<div class="cff-post-item-icon-ctn">
		<a :href="'https://www.facebook.com/'+singlePost.id" target="_blank" v-if="$parent.$parent.getPostTypeTimeline(singlePost) == 'videos'">
			<span style="padding-right: 5px;" class="fa fas fa-video-camera fa-video" aria-hidden="true"></span>
            {{translatedText.videotext}}
		</a>
		<a :href="'https://www.facebook.com/'+singlePost.id" target="_blank" v-if="['photos','albums'].includes($parent.$parent.getPostTypeTimeline(singlePost))">
			<span style="padding-right: 5px;" class="fa fas fa-picture-o fa-image" aria-hidden="true"></span>
            {{translatedText.phototext}}
		</a>
	</div>
</script>

<script type="text/x-template" id="cff-free-linkbox-component">
	<div class="cff-post-item-media-ctn cff-fb-fs">
		<div class="cff-post-item-link-ctn" v-if="(postmedia.type == 'link' || postmedia.type == 'video') && customizerFeedData.settings.include.includes('sharedlinks')" :data-linkbox="customizerFeedData.settings.disablelinkbox">
			<div class="cff-post-item-link-info cff-fb-fs">
				<a class="cff-post-item-link-a" :href="postmedia.args.unshimmedUrl" target="_blank" v-html="postmedia.args.title"></a>
				<div class="cff-post-item-link-small" v-html="postmedia.args.domain"></div>
				<div class="cff-post-item-link-description" v-if="customizerFeedData.settings.include.includes('desc')" v-html="(postmedia.args.description != null) ? (postmedia.args.description.substring(0, customizerFeedData.settings.desclength) + (postmedia.args.description.length > customizerFeedData.settings.desclength ? '...' : '')) : ''"></div>
			</div>
		</div>
	</div>
</script>