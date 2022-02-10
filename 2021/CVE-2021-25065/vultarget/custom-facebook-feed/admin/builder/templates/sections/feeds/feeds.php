<div class="cff-fd-lst-bigctn cff-fb-fs" v-if="feedsList != null && feedsList.length > 0">

	<div class="cff-fd-lst-bulk-ctn cff-fb-fs">
		<select class="cff-fd-lst-bulk-select cff-fb-select sb-caption" v-model="selectedBulkAction">
			<option value="false">{{allFeedsScreen.bulkActions}}</option>
			<option value="delete">{{genericText.delete}}</option>
		</select>
		<button class="cff-fd-lst-bulk-btn cff-btn-grey sb-button-small sb-button" @click.prevent.default="bulkActionClick()">{{genericText.apply}}</button>
		<div class="cff-fd-lst-pagination-ctn" v-if="feedPagination.feedsCount != null && feedPagination.feedsCount > 0">
			<span class="cff-fd-lst-count sb-caption">{{feedPagination.feedsCount +' '+ (feedPagination.feedsCount > 1 ? genericText.items : genericText.item)}}</span>
			<div class="cff-fd-lst-pagination" v-if="feedPagination.pagesNumber != null && feedPagination.pagesNumber > 1">
				<button class="cff-fd-lst-pgnt-btn cff-fd-pgnt-prev sb-btn-grey" :data-active="feedPagination.currentPage == 1 ? 'false' : 'true'" :disabled="feedPagination.currentPage == 1" @click.prevent.default="feedListPagination('prev')"><</button>
				<span class="cff-fd-lst-pgnt-info">
					{{feedPagination.currentPage}} of {{feedPagination.pagesNumber}}
				</span>
				<button class="cff-fd-lst-pgnt-btn cff-fd-pgnt-next sb-btn-grey" :data-active="feedPagination.currentPage == feedPagination.pagesNumber ? 'false' : 'true'" :disabled="feedPagination.currentPage == feedPagination.pagesNumber" @click.prevent.default="feedListPagination('next')">></button>
			</div>
		</div>
	</div>
    <div class="cff-table-wrap" v-bind:class="{ 'sb-onboarding-highlight' : viewsActive.onboardingStep === 2 && allFeedsScreen.onboarding.type === 'single' }">
	<table>
		<thead class="cff-fd-lst-thtf cff-fd-lst-thead">
			<tr>
				<th>
					<div class="cff-fd-lst-chkbx" @click.prevent.default="selectAllFeedCheckBox()" :data-active="checkAllFeedsActive()"></div>
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
			<tr v-for="(feed, feedIndex) in feedsList">
				<td>
					<div class="cff-fd-lst-chkbx" @click.prevent.default="selectFeedCheckBox(feed.id)" :data-active="feedsSelected.includes(feed.id)"></div>
				</td>
				<td>
					<a :href="builderUrl+'&feed_id='+feed.id" class="cff-fd-lst-name sb-small-p sb-bold">{{feed.feed_name}}</a>
					<span class="cff-fd-lst-type sb-caption sb-lighter">{{feed.settings.feedtype}}</span>
				</td>
				<td>
                    <div class="sb-flex-center">
                        <span class="cff-fd-lst-shortcode sb-caption sb-lighter">[custom-facebook-feed feed={{feed.id}}]</span>
                        <div class="cff-fd-lst-shortcode-cp cff-fd-lst-btn cff-fb-tltp-parent" @click.prevent.default="copyToClipBoard('[custom-facebook-feed feed='+feed.id+']')">
                            <div class="cff-fb-tltp-elem"><span>{{(genericText.copy +' '+ genericText.shortcode).replace(/ /g,"&nbsp;")}}</span></div>
                            <div v-html="svgIcons['copy']"></div>
                        </div>
                    </div>
				</td>
				<td class="sb-caption sb-lighter">
                    <div class="sb-instances-cell">
                        <span>{{genericText.usedIn}} <span class="cff-fb-view-instances cff-fb-tltp-parent" :data-active="feed.instance_count < 1 ? 'false' : 'true'" @click.prevent.default="feed.instance_count > 0 ? viewFeedInstances(feed) : checkAllFeedsActive()">{{feed.instance_count + ' ' + (feed.instance_count !== 1 ? genericText.places : genericText.place)}} <div class="cff-fb-tltp-elem" v-if="feed.instance_count > 0"><span>{{genericText.clickViewInstances.replace(/ /g,"&nbsp;")}}</span></div></span></span>
                    </div>
                </td>
				<td class="cff-fd-lst-actions">
                    <div class="sb-flex-center">
                        <a class="cff-fd-lst-btn cff-fb-tltp-parent":href="builderUrl+'&feed_id='+feed.id">
                            <div class="cff-fb-tltp-elem"><span>{{genericText.edit.replace(/ /g,"&nbsp;")}}</span></div>
                            <div v-html="svgIcons['edit']"></div>
                        </a>
                        <button class="cff-fd-lst-btn cff-fb-tltp-parent" @click.prevent.default="feedActionDuplicate(feed)">
                            <div class="cff-fb-tltp-elem"><span>{{genericText.duplicate.replace(/ /g,"&nbsp;")}}</span></div>
                            <div v-html="svgIcons['duplicate']"></div>
                        </button>
                        <button class="cff-fd-lst-btn cff-fd-lst-btn-delete cff-fb-tltp-parent" @click.prevent.default="openDialogBox('deleteSingleFeed', feed)">
                            <div class="cff-fb-tltp-elem"><span>{{genericText.delete.replace(/ /g,"&nbsp;")}}</span></div>
                            <div v-html="svgIcons['delete']"></div>
                        </button>
                    </div>
				</td>

			</tr>
		</tbody>
		<tfoot class="cff-fd-lst-thtf cff-fd-lst-tfoot">
			<tr>
				<td>
					<div class="cff-fd-lst-chkbx" @click.prevent.default="selectAllFeedCheckBox()" :data-active="checkAllFeedsActive()"></div>
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
</div>