<div class="cff-fb-section-wh cff-fb-sglelm-ctn" v-if="checkCreationFeedTypeChosen('featuredpost')">
	<div class="cff-fb-section-wh-insd cff-fb-fs">
		<div class="cff-fb-sec-heading cff-fb-fs cff-fb-sglelm-left">
			<h4>{{addFeaturedPostScreen.mainHeading}}</h4>
			<span class="sb-caption sb-lighter">{{addFeaturedPostScreen.description}}</span>
			<div class="cff-fb-sglelm-inp-ctn cff-fb-fs">
				<span class="cff-fb-wh-label cff-fb-fs sb-caption sb-lighter">{{addFeaturedPostScreen.URLorID}}</span>
				<div class="cff-fb-fs">
					<input type="text" class="cff-fb-wh-inp cff-fb-fs" placeholder="https://facebook.com/user/post/id" v-model="featuredPostFeedInfo.url">
					<div class="cff-fb-sglelm-error-icon cff-fb-fs" v-if="featuredPostFeedInfo.isError">i</div>
					<div class="cff-fb-sglelm-errormsg" v-if="featuredPostFeedInfo.isError" v-html="addFeaturedPostScreen.unable"></div>
				</div>
			</div>
		</div>
		<div class="cff-fb-sglelm-right">
			<div class="cff-fb-sglelm-img-ctn cff-fb-sglelm-img-pf" v-if="!featuredPostFeedInfo.isError && !featuredPostFeedInfo.success">
				<img src="<?php echo CFF_BUILDER_URL .'assets/img/process-featured-post.png' ?>">
				<strong>{{addFeaturedPostScreen.preview}}</strong>
				<span>{{addFeaturedPostScreen.previewDescription}}</span>
			</div>
			<div class="cff-fb-sglelm-img-ctn cff-fb-sglelm-img-errorctn" v-if="featuredPostFeedInfo.isError">
				<img src="<?php echo CFF_BUILDER_URL .'assets/img/process-error-fetch.png' ?>">
				<span class="cff-fb-fs">{{addFeaturedPostScreen.couldNotFetch}}</span>
			</div>
			<div class="cff-fb-sglelm-preview cff-fb-fs"  v-if="!featuredPostFeedInfo.isError && featuredPostFeedInfo.success" :style="'background-image:url(' + (hasOwnNestedProperty(featuredPostFeedInfo, 'info.thumbnail') ? featuredPostFeedInfo.info.thumbnail : '') + ');'">
				<div class="cff-fb-sglelm-prev-info cff-fb-fs">
					<span v-if="hasOwnNestedProperty(featuredPostFeedInfo, 'info.description')">{{featuredPostFeedInfo.info.description}}</span>
				</div>
			</div>
		</div>
	</div>
</div>