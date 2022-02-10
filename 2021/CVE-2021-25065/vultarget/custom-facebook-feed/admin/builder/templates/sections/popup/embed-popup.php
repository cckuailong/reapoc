<div class="cff-fb-embed-ctn sb-fs-boss cff-fb-center-boss" v-if="viewsActive.embedPopup">
	<div class="cff-fb-embed-popup cff-fb-popup-inside">
		<div class="cff-fb-popup-cls" @click.prevent.default="activateView('embedPopup')"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
            </svg>
        </div>

		<h3 v-show="viewsActive.embedPopupScreen == 'step_1'">{{embedPopupScreen.heading}}</h3>

		<div class="cff-fb-embed-step-1 cff-fb-fs" v-show="viewsActive.embedPopupScreen == 'step_1'">
			<div class="cff-fb-embed-step-1-top cff-fb-fs">
				<h4 class="cff-fb-fs">{{embedPopupScreen.description}}</h4>
				<div class="cff-fb-embed-input-ctn cff-fb-fs">
					<input class="cff-fb-fs" type="text" :value="'[custom-facebook-feed feed='+ customizerFeedData.feed_info.id +']'">
					<button class="cff-fb-hd-btn cff-csz-btn-save cff-btn-orange sb-standard-p sb-bold" @click.prevent.default="copyToClipBoard('[custom-facebook-feed feed='+customizerFeedData.feed_info.id+']')">
						<div v-html="svgIcons['copy2']"></div>
						<span>{{genericText.copy}}</span>
					</button>
				</div>
			</div>

			<div class="cff-fb-embed-step-1-bottom cff-fb-fs">
				<h4>{{embedPopupScreen.description_2}}</h4>
				<div class="cff-fb-embed-btns-ctn cff-fb-fs">
					<a class="cff-fb-embed-btn cff-btn-grey" @click.prevent.default="switchScreen('embedPopupScreen','step_2')">
						<div class="sb-icon-label">
              <div v-html="svgIcons['addPage']"></div>
                  <span>{{embedPopupScreen.addPage}}</span>
              </div>
              <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1.83516 0L0.660156 1.175L4.47682 5L0.660156 8.825L1.83516 10L6.83516 5L1.83516 0Z" fill="#141B38"/>
              </svg>

          </a>
					<a :href="widgetsPageURL + '?cff_feed_id=' + customizerFeedData.feed_info.id" class="cff-fb-embed-btn cff-btn-grey">
              <div class="sb-icon-label">
                  <div v-html="svgIcons['addWidget']"></div>
                  <span>{{embedPopupScreen.addWidget}}</span>
              </div>
              <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1.83516 0L0.660156 1.175L4.47682 5L0.660156 8.825L1.83516 10L6.83516 5L1.83516 0Z" fill="#141B38"/>
              </svg>
					</a>
				</div>
			</div>
		</div>

		<div class="cff-fb-embed-step-2 cff-fb-fs" v-show="viewsActive.embedPopupScreen == 'step_2'">
			<div class="sb-embed-breadcrumb cff-fb-fs">
                <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.27203 0.94L4.33203 0L0.332031 4L4.33203 8L5.27203 7.06L2.2187 4L5.27203 0.94Z" fill="#434960"></path></svg>
                <a @click.prevent.default="switchScreen('embedPopupScreen','step_1')">{{embedPopupScreen.heading}}</a>
			</div>
			<h3>{{embedPopupScreen.addPage}}</h3>
			<div class="cff-fb-embed-step-2-list">
				<strong>{{embedPopupScreen.selectPage}}</strong>
				<div class="cff-fb-embed-step-2-pages cff-fb-fs">
					<div class="sb-control-toggle-set-ctn cff-fb-fs">
						<div class="sb-control-toggle-elm cff-fb-fs sb-tr-2" v-for="page in wordpressPageLists" :data-active="viewsActive.embedPopupSelectedPage == page.id" @click.prevent.default="switchScreen('embedPopupSelectedPage',page.id)">
							<div class="sb-control-toggle-deco sb-tr-1"></div>
							<div class="sb-control-toggle-icon" v-html="svgIcons['article_2']"></div>
							<div class="sb-control-label sb-small-p sb-dark-text">{{page.title}}</div>
						</div>
					</div>
				</div>
			</div>
			<div class="cff-fb-embed-step-2-action cff-fb-fs">
				<a class="cff-fb-srcs-update cff-fb-btn cff-fb-fs cff-btn-orange" :href="viewsActive.embedPopupSelectedPage != null ? adminPostURL + '?post='+viewsActive.embedPopupSelectedPage+'&action=edit&cff_wizard=' + customizerFeedData.feed_info.id : '#'" target="_blank" :data-active="viewsActive.embedPopupSelectedPage != null ? 'true' : 'false'">
          <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M5.58058 8.36158L13.5355 0.406627L15.3033 2.17439L5.58058 11.8971L0.277281 6.59381L2.04505 4.82604L5.58058 8.36158Z" fill="currentColor"/>
          </svg>

          <span>{{genericText.add}}</span>
				</a>
			</div>
		</div>

	</div>
</div>
