<div class="cff-fb-section-wh cff-fb-sglelm-ctn cff-fb-section-videos" v-if="checkCreationFeedTypeChosen('videos')">
	<div class="cff-fb-section-wh-insd cff-fb-fs">
		<div class="cff-fb-sec-heading cff-fb-fs cff-fb-sglelm-left">
			<h4>{{addVideosPostScreen.mainHeading}}</h4>
			<span class="sb-caption sb-lighter">{{addVideosPostScreen.description}}</span>
		</div>
	</div>
	<div class="cff-fb-videotype-chooser cff-fb-fs">
		<div class="cff-fb-srcs-item" v-for="(videoType, videoTypeIndex) in addVideosPostScreen.sections" @click.prevent.default="changeVideoSource(videoType.id)" :data-active="videoType.id == videosTypeInfo.type">
			<div class="cff-fb-srcs-item-chkbx">
				<div class="cff-fb-srcs-item-chkbx-ic"></div>
			</div>
			<div class="cff-fb-srcs-item-inf">
				<div class="cff-fb-srcs-item-name">
					<span class="sb-small-p sb-bold" v-html="videoType.heading"></span>
				</div>
				<div class="cff-fb-srcs-item-description" v-html="videoType.description"></div>
			</div>
		</div>
	</div>
	<div class="cff-fb-section-wh-insd cff-fb-section-video-playlist cff-fb-fs" v-show="videosTypeInfo.type == 'playlist'">
		<div class="cff-fb-sec-heading cff-fb-fs cff-fb-sglelm-left">
			<strong>{{addVideosPostScreen.inputLabel}}</strong>
			<input type="text" class="cff-fb-wh-inp cff-fb-fs" placeholder="https://" v-model="videosTypeInfo.playListUrl">
			<span class="sb-caption sb-lighter">{{addVideosPostScreen.inputDescription}}</span>
			<strong class="cff-fb-sglelm-errormsg" v-show="videosTypeInfo.playListUrlError">{{addVideosPostScreen.errorMessage}}</strong>
		</div>
	</div>

</div>