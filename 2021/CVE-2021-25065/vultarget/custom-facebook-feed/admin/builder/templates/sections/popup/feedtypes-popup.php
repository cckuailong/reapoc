<div class="cff-fb-feedtypes-pp-ctn sb-fs-boss cff-fb-center-boss" v-if="viewsActive.feedtypesPopup">
	<div class="cff-fb-feedtypes-popup cff-fb-popup-inside">
		<div class="cff-fb-popup-cls" @click.prevent.default="activateView('feedtypesPopup')"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
            </svg>
        </div>
        <div class="cff-fb-source-top cff-fb-fs">
            <h3>{{selectFeedTypeScreen.updateHeading}}</h3>
            <div class="cff-fb-types cff-fb-fs">
                <div class="cff-fb-types-list cff-fb-types-list-free">
                    <div class="cff-fb-type-el" v-for="(feedTypeEl, feedTypeIn) in feedTypes" :data-active="choosedFeedTypeCustomizer(feedTypeEl.type)" @click.prevent.default="chooseFeedType('normal', feedTypeEl, true)">
                        <div class="cff-fb-type-el-img cff-fb-fs" v-html="svgIcons[feedTypeEl.icon]"></div>
                        <div class="cff-fb-type-el-info cff-fb-fs">
                            <p class="sb-small-p sb-bold sb-dark-text">{{feedTypeEl.title}}</p>
                            <span class="sb-caption sb-lightest">{{feedTypeEl.description}}</span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="cff-fb-adv-types cff-fb-fs">
                <h4>{{selectFeedTypeScreen.advancedHeading}}<span class="sb-breadcrumb-pro-label">PRO</span></h4>
                <div class="cff-fb-types-list cff-fb-types-list-pro cff-fb-fs">
                    <div class="cff-fb-type-el-pro" v-for="(advFeedTypeEl, advFeedTypeIn) in advancedFeedTypes" @click.prevent.default="chooseFeedType('advanced', advFeedTypeEl)">
                        <div class="cff-fb-type-el-pro-img"  v-html="svgIcons[advFeedTypeEl.icon +'Free']"></div>
                        <span>{{advFeedTypeEl.title}}</span>
                    </div>

                </div>
            </div>
            <div class="cff-fb-srcs-update-ctn cff-fb-fs">
                <button class="cff-fb-srcs-update cff-fb-btn cff-fb-fs cff-btn-orange" @click.prevent.default="updateFeedTypeCustomizer()">
                    <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.08058 8.36133L14.0355 0.406383L15.8033 2.17415L6.08058 11.8969L0.777281 6.59357L2.54505 4.8258L6.08058 8.36133Z" fill="white"/>
                    </svg>

                    <span>{{genericText.update}}</span>
                </button>
            </div>
        </div>
	</div>
</div>