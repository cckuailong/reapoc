<section id="cff-header-section" class="cff-preview-header-ctn cff-fb-fs cff-preview-section" :data-dimmed="!isSectionHighLighted('header')" v-if="valueIsEnabled(customizerFeedData.settings.showheader) && customizerFeedData.header && sourcesList.length">
    <!--Visual header-->
	<div class="cff-preview-header-visual cff-fb-fs" v-if="customizerFeedData.settings.headertype == 'visual'" :data-header-avatar="valueIsEnabled(customizerFeedData.settings.headername) ? 'shown' : 'hidden'">
		<div class="cff-preview-header-cover cff-fb-fs" v-if="valueIsEnabled(customizerFeedData.settings.headercover)">
			<img v-if="hasOwnNestedProperty(customizerFeedData,  'header.cover.source')" :src="customizerFeedData.header.cover.source">
			<div class="cff-preview-header-likebox" v-if="valueIsEnabled(customizerFeedData.settings.headerbio)">
				<div v-html="svgIcons['facebook']"></div>
				<span>{{customizerFeedData.header.fan_count}}</span>
			</div>
		</div>
		<div class="cff-preview-header-info-ctn cff-fb-fs">
			<div class="cff-preview-header-avatar" v-if="valueIsEnabled(customizerFeedData.settings.headername)" >
				<img v-if="hasOwnNestedProperty(customizerFeedData,  'header.picture.data.url')" :src="customizerFeedData.header.picture.data.url">
			</div>
			<div class="cff-preview-header-info">
				<h3 class="cff-preview-header-name" v-if="valueIsEnabled(customizerFeedData.settings.headername)" >{{customizerFeedData.header.name}}</h3>
				<div class="cff-preview-header-bio" v-if="valueIsEnabled(customizerFeedData.settings.headerbio)">{{customizerFeedData.header.about}}</div>
			</div>
		</div>
	</div>
	<!--Text header-->
	<div class="cff-preview-header-text cff-fb-fs" v-if="customizerFeedData.settings.headertype == 'text'">
		<h3 class="cff-preview-header-text-h cff-fb-fs">
			<div class="cff-preview-header-text-icon" v-if="valueIsEnabled(customizerFeedData.settings.headericonenabled)">
				<span class="cff-header-text-icon fa fab " :class="'fa-'+customizerFeedData.settings.headericon"></span>
			</div>
			<span class="cff-header-text" v-html="customizerFeedData.settings.headertext"></span>
		</h3>
	</div>

</section>

<svg width="24px" height="24px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="cff-screenreader" role="img" aria-labelledby="metaSVGid metaSVGdesc" alt="Comments Box SVG icons"><title id="metaSVGid">Comments Box SVG icons</title><desc id="metaSVGdesc">Used for the like, share, comment, and reaction icons</desc><defs><linearGradient id="angryGrad" x1="0" x2="0" y1="0" y2="1"><stop offset="0%" stop-color="#f9ae9e"></stop><stop offset="70%" stop-color="#ffe7a4"></stop></linearGradient><linearGradient id="likeGrad"><stop offset="25%" stop-color="rgba(0,0,0,0.05)"></stop><stop offset="26%" stop-color="rgba(255,255,255,0.7)"></stop></linearGradient><linearGradient id="likeGradHover"><stop offset="25%" stop-color="#a3caff"></stop><stop offset="26%" stop-color="#fff"></stop></linearGradient><linearGradient id="likeGradDark"><stop offset="25%" stop-color="rgba(255,255,255,0.5)"></stop><stop offset="26%" stop-color="rgba(255,255,255,0.7)"></stop></linearGradient></defs></svg>