<div class="cff-fb-section-wh cff-fb-sglelm-ctn" v-if="checkCreationFeedTypeChosen('singlealbum')">
	<div class="cff-fb-section-wh-insd cff-fb-fs">
		<div class="cff-fb-sec-heading cff-fb-fs cff-fb-sglelm-left">
			<h4>{{addFeaturedAlbumScreen.mainHeading}}</h4>
			<span>{{addFeaturedAlbumScreen.description}}</span>
			<div class="cff-fb-sglelm-inp-ctn cff-fb-fs">
				<span class="cff-fb-wh-label cff-fb-fs">{{addFeaturedAlbumScreen.URLorID}}</span>
				<div class="cff-fb-fs">
					<input type="text" class="cff-fb-wh-inp cff-fb-fs" placeholder="https://www.facebook.com/media/set/?vanity=username&set=a.1234567890123" v-model="singleAlbumFeedInfo.url">
					<div class="cff-fb-sglelm-error-icon cff-fb-fs" v-if="singleAlbumFeedInfo.isError">i</div>
					<div class="cff-fb-sglelm-errormsg" v-if="singleAlbumFeedInfo.isError" v-html="addFeaturedAlbumScreen.unable"></div>
				</div>
			</div>
		</div>
		<div class="cff-fb-sglelm-right">
			<div class="cff-fb-sglelm-img-ctn" v-if="!singleAlbumFeedInfo.isError && !singleAlbumFeedInfo.success">
				<img src="<?php echo CFF_BUILDER_URL .'assets/img/process-single-album.png' ?>">
				<strong>{{addFeaturedAlbumScreen.preview}}</strong>
				<span>{{addFeaturedAlbumScreen.previewDescription}}</span>
			</div>
			<div class="cff-fb-sglelm-img-ctn cff-fb-sglelm-img-errorctn" v-if="singleAlbumFeedInfo.isError">
				<img src="<?php echo CFF_BUILDER_URL .'assets/img/process-error-fetch.png' ?>">
				<span class="cff-fb-fs">{{addFeaturedAlbumScreen.couldNotFetch}}</span>
			</div>
			<div class="cff-fb-sglelm-preview cff-fb-fs"  v-if="!singleAlbumFeedInfo.isError && singleAlbumFeedInfo.success" :style="'background-image:url(' + (hasOwnNestedProperty(singleAlbumFeedInfo, 'info.thumbnail') ? singleAlbumFeedInfo.info.thumbnail : '') + ');'">
				<div class="cff-fb-sglelm-prev-info cff-fb-fs">
					<strong v-if="hasOwnNestedProperty(singleAlbumFeedInfo, 'info.title')">{{singleAlbumFeedInfo.info.title}}</strong>
					<span v-if="hasOwnNestedProperty(singleAlbumFeedInfo, 'info.description')">{{singleAlbumFeedInfo.info.description}}</span>
				</div>
			</div>
		</div>
	</div>
</div>