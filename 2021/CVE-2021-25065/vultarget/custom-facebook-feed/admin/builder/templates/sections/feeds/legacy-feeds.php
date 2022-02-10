<div class="cff-fb-legacy-ctn cff-fb-fs" v-if="legacyFeedsList != null && legacyFeedsList.length > 0">
	<div class="cff-fb-lgc-top-new cff-fb-fs" v-if="allFeedsScreen.onboarding.active">
		<div class="cff-fb-lgc-gr">{{genericText.new}}</div> {{allFeedsScreen.onboarding.getStarted}}
	</div>
	<div class="cff-fb-lgc-ctn cff-fb-fs" v-bind:class="{ 'sb-onboarding-highlight' : viewsActive.onboardingStep === 2 && allFeedsScreen.onboarding.type === 'multiple' }">
		<div class="cff-fb-lgc-inf-ctn cff-fb-fs">
			<h4>{{allFeedsScreen.legacyFeeds.heading}}</h4>
			<div class="cff-fb-onbrd-tltp-parent" @click.prevent.default="openTooltipBig(this)">
				<div class="cff-fb-onbrd-infotxt sb-caption sb-lighter">{{allFeedsScreen.legacyFeeds.toolTip}}<div v-html="svgIcons['information']"></div></div>
				<div class="cff-fb-onbrd-tltp-elem" :data-active="viewsActive.enabledToolTip == this">
					<div class="cff-fb-popup-cls" @click.prevent.default="closeTooltipBig()">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.8346 1.34175L10.6596 0.166748L6.0013 4.82508L1.34297 0.166748L0.167969 1.34175L4.8263 6.00008L0.167969 10.6584L1.34297 11.8334L6.0013 7.17508L10.6596 11.8334L11.8346 10.6584L7.1763 6.00008L11.8346 1.34175Z" fill="#141B38"/>
                        </svg>
                    </div>
					<div class="cff-fb-onbrd-tltp-txt sb-small-p sb-lighter" v-for="legFeedTltp in allFeedsScreen.legacyFeeds.toolTipExpanded" v-html="legFeedTltp">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
                        </svg>
                    </div>
                    <div class="sb-pointer">
                        <svg width="21" height="11" viewBox="0 0 21 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.59642 0.811655C9.39546 -0.135356 10.8545 -0.135357 11.6536 0.811654L20.25 11H0L8.59642 0.811655Z" fill="white"/>
                        </svg>
                    </div>
				</div>
			</div>
        <a class="cff-fb-lgc-btn-stg cff-btn-grey cff-fb-hd-btn sb-button-standard" :href="builderUrl+'&feed_id=legacy'">
				<div v-html="svgIcons['cog']"></div>
				{{genericText.settings}}
			</a>
		</div>
        <div class="cff-legacy-table-wrap" v-bind:class="{ 'sb-onboarding-highlight' : viewsActive.onboardingStep === 3 && allFeedsScreen.onboarding.type === 'multiple' }">

		<table class="cff-fd-legacy-feed-ctn" v-if="viewsActive.legacyFeedsShown || feedsList === null || feedsList.length === 0">
			<thead class="cff-fd-lst-thtf cff-fd-lst-thead">
				<tr>
					<th>
						<div class="cff-fd-lst-chkbx"></div>
					</th>
					<th>
						<span class="sb-caption sb-lighter">{{allFeedsScreen.columns.nameText}}</span>
					</th>
					<th>
						<span class="sb-caption sb-lighter">{{allFeedsScreen.columns.shortcodeText}}</span>
					</th>
					<th>
						<span class="sb-caption sb-lighter">{{allFeedsScreen.columns.instancesText}}</span>
					</th>
					<th class="cff-fd-lst-act-th">
						<span class="sb-caption sb-lighter">{{allFeedsScreen.columns.actionsText}}</span>
					</th>
				</tr>
			</thead>
			<tbody  class="cff-fd-lst-tbody">
				<tr v-for="(legacyFeed, legacyFeedIndex) in legacyFeedsList">
					<td>
						<div class="cff-fd-lst-chkbx"></div>
					</td>
					<td>
                        <span class="sb-small-p sb-bold sb-lighter">{{legacyFeed.feed_name}}</span>
						<span class="cff-fd-lst-type sb-caption sb-lighter">{{legacyFeed.feed_type}}</span>
					</td>
					<td>
                        <div class="sb-flex-center">

                            <span class="cff-fd-lst-shortcode sb-caption sb-lighter">{{legacyFeed.shortcode}}</span>
                            <div class="cff-fd-lst-shortcode-cp cff-fd-lst-btn cff-fb-tltp-parent">
                                <div class="cff-fb-tltp-elem"><span>{{(genericText.copy +' '+ genericText.shortcode).replace(/ /g,"&nbsp;")}}</span></div>
                                <div v-html="svgIcons['copy']" @click.prevent.default="copyToClipBoard(legacyFeed.id)"></div>
                            </div>
                        </div>
					</td>
					<td class="sb-caption sb-lighter">
                        <div class="sb-instances-cell" v-if="typeof legacyFeed.instance_count !== 'undefined' && legacyFeed.instance_count !== false">
						    <span>{{genericText.usedIn}} <span class="cff-fb-view-instances cff-fb-tltp-parent" :data-active="legacyFeed.instance_count < 1 ? 'false' : 'true'" @click.prevent.default="viewFeedInstances(legacyFeed)">{{legacyFeed.instance_count + ' ' + (legacyFeed.instance_count !== 1 ? genericText.places : genericText.place)}} <div class="cff-fb-tltp-elem"><span>{{genericText.clickViewInstances.replace(/ /g,"&nbsp;")}}</span></div></span></span>
                        </div>
                    </td>
					<td class="cff-fd-lst-actions cff-fd-lst-dimmed cff-fb-onbrd-tltp-parent" data-tltp-pos="right" @click.prevent.default="openTooltipBig()">
						<div class="cff-fb-onbrd-tltp-elem cff-fb-onbrd-tltp-elem-2" data-active="false">
							<div class="cff-fb-popup-cls" @click.prevent.default="closeTooltipBig()"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
                                </svg>
                            </div>
							<div class="cff-fb-onbrd-tltp-txt sb-small-p sb-lighter" v-for="legFeedTltp in allFeedsScreen.legacyFeeds.toolTipExpandedAction" v-html="legFeedTltp"></div>
                            <div class="sb-pointer">
                                <svg width="21" height="11" viewBox="0 0 21 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.59642 0.811655C9.39546 -0.135356 10.8545 -0.135357 11.6536 0.811654L20.25 11H0L8.59642 0.811655Z" fill="white"/>
                                </svg>
                            </div>
                        </div>
						<div class="cff-fd-lst-btn cff-fb-tltp-parent">
							<div v-html="svgIcons['edit']"></div>
						</div>
						<div class="cff-fd-lst-btn cff-fb-tltp-parent">
							<div v-html="svgIcons['duplicate']"></div>
						</div>
						<div class="cff-fd-lst-btn cff-fd-lst-btn-delete cff-fb-tltp-parent">
							<div v-html="svgIcons['delete']"></div>
						</div>
					</td>

				</tr>
			</tbody>
			<tfoot class="cff-fd-lst-thtf cff-fd-lst-tfoot">
				<tr>
					<td>
						<div class="cff-fd-lst-chkbx"></div>
					</td>
					<td>
						<span>{{allFeedsScreen.columns.nameText}}</span>
					</td>
					<td>
						<span>{{allFeedsScreen.columns.shortcodeText}}</span>
					</td>
					<td>
						<span>{{allFeedsScreen.columns.instancesText}}</span>
					</td>
					<td>
						<span>{{allFeedsScreen.columns.actionsText}}</span>
					</td>
				</tr>
			</tfoot>
		</table>
        </div>

		<div v-if="feedsList !== null && feedsList.length > 0" class="cff-fd-legacy-feed-toggle cff-fb-fs" :data-active="viewsActive.legacyFeedsShown" @click.prevent.default="activateView('legacyFeedsShown')">
			<span>{{viewsActive.legacyFeedsShown ? allFeedsScreen.legacyFeeds.hide : allFeedsScreen.legacyFeeds.show}}</span>
            <svg v-if="! viewsActive.legacyFeedsShown" width="11" height="7" viewBox="0 0 11 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.675 0.158447L5.5 3.97511L9.325 0.158447L10.5 1.33345L5.5 6.33345L0.5 1.33345L1.675 0.158447Z" fill="#004D77"/>
            </svg>
            <svg v-else width="11" height="7" viewBox="0 0 11 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.325 6.84167L5.5 3.02501L1.675 6.84168L0.5 5.66668L5.5 0.666676L10.5 5.66668L9.325 6.84167Z" fill="#0068A0"/>
            </svg>

        </div>
	</div>
</div>