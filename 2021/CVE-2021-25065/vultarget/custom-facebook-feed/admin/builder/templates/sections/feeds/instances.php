<div class="sb-fs-boss cff-fb-center-boss" v-if="viewsActive.instanceFeedActive != null && (checkObjectArrayElement(feedsList, viewsActive.instanceFeedActive, 'id') || checkObjectArrayElement(legacyFeedsList, viewsActive.instanceFeedActive, 'feed_id'))">
	<div class="cff-fb-popup-inside cff-fb-popup-feedinst">
		<div class="cff-fb-popup-cls" @click.prevent.default="switchScreen('instanceFeedActive', null)"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
            </svg>
        </div>
		<div class="cff-fb-source-top cff-fb-fs">
			<h3>{{viewsActive.instanceFeedActive.feed_name}}</h3>
			<div class="cff-fb-fdinst-type sb-small">TimeLine</div>
		</div>
		<div class="cff-fb-inst-tbl-ctn cff-fb-fs">
			<table>
				<thead class="cff-fd-lst-thtf cff-fd-lst-thead">
					<tr>
						<th>
							<span class="sb-caption sb-lighter">{{genericText.page}}</span>
						</th>
						<th>
							<span class="sb-caption sb-lighter">{{genericText.location}}</span>
						</th>
						<th>
							<span class="sb-caption sb-lighter">{{allFeedsScreen.columns.shortcodeText}}</span>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody  class="cff-fd-lst-tbody">
					<tr v-for="(instance, instanceIndex) in viewsActive.instanceFeedActive.location_summary">
						<td><a :href="instance.link" class="cff-fd-lst-name sb-small-p sb-bold">{{instance.page_text}}</a></td>
                        <td><span class="cff-fd-lst-shortcode sb-caption sb-lighter">{{instance.html_location}}</span></td>
						<td>
							<div class="cff-fb-inst-tbl-shrtc">
                                <div class="sb-flex-center">
                                    <span class="cff-fd-lst-shortcode sb-caption sb-lighter">{{instance.shortcode}}</span>
                                    <div class="cff-fd-lst-shortcode-cp cff-fd-lst-btn cff-fb-tltp-parent">
                                        <div class="cff-fb-tltp-elem"><span>{{(genericText.copy +' '+ genericText.shortcode).replace(/ /g,"&nbsp;")}}</span></div>
                                        <div v-html="svgIcons['copy']" @click.prevent.default="copyToClipBoard(instance.shortcode)"></div>
                                    </div>
                                </div>
							</div>
						</td>
						<td>
                            <a :href="instance.link" class="cff-fd-lst-btn sb-button-no-border sb-icon-small sb-dark-hover">
                                <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.3332 0L0.158203 1.175L3.97487 5L0.158203 8.825L1.3332 10L6.3332 5L1.3332 0Z" fill="#8C8F9A"/>
                                </svg>
                            </a>
						</td>
					</tr>
				</tbody>
				<tfoot class="cff-fd-lst-thtf cff-fd-lst-tfoot">
					<tr>
						<td>
							<span>{{genericText.page}}</span>
						</td>
						<td>
							<span>{{genericText.location}}</span>
						</td>
						<td>
							<span>{{allFeedsScreen.columns.shortcodeText}}</span>
						</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>