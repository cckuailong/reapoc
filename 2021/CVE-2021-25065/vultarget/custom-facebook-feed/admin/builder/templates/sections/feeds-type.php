<div class="cff-fb-types-ctn cff-fb-fs sb-box-shadow" v-if="viewsActive.selectedFeedSection == 'feedsType'">
	<div class="cff-fb-types cff-fb-fs">
		<h4>{{selectFeedTypeScreen.feedTypeHeading}}</h4>
		<div class="cff-fb-types-list cff-fb-types-list-free">
			<div class="cff-fb-type-el" v-for="(feedTypeEl, feedTypeIn) in feedTypes" :data-active="selectedFeed === feedTypeEl.type" @click.prevent.default="chooseFeedType('normal', feedTypeEl)">
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
			<!--
			<div class="cff-fb-type-el" v-for="(advFeedTypeEl, advFeedTypeIn) in advancedFeedTypes" :data-active="selectedFeed === advFeedTypeEl.type" @click.prevent.default="chooseFeedType('advanced', advFeedTypeEl)">
				<div class="cff-fb-type-el-img cff-fb-fs"  v-html="svgIcons[advFeedTypeEl.icon]"></div>
				<div class="cff-fb-type-el-info cff-fb-fs">
                    <p class="sb-small-p sb-bold sb-dark-text">{{advFeedTypeEl.title}}<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.75321 13.7934L6.66654 11.2401C7.71321 10.8534 8.69321 10.3334 9.59988 9.72677L7.75321 13.7934ZM2.75988 7.33344L0.206543 6.24677L4.27321 4.4001C3.66654 5.30677 3.14654 6.28677 2.75988 7.33344ZM13.4065 0.593436C13.4065 0.593436 10.1065 -0.820564 6.33321 2.95344C4.87321 4.41344 3.99988 6.0201 3.43321 7.42677C3.24654 7.92677 3.37321 8.47344 3.73988 8.84677L5.15988 10.2601C5.52654 10.6334 6.07321 10.7534 6.57321 10.5668C8.25224 9.92657 9.77676 8.93825 11.0465 7.66677C14.8199 3.89344 13.4065 0.593436 13.4065 0.593436ZM8.69321 5.30677C8.17321 4.78677 8.17321 3.9401 8.69321 3.4201C9.21321 2.9001 10.0599 2.9001 10.5799 3.4201C11.0932 3.9401 11.0999 4.78677 10.5799 5.30677C10.0599 5.82677 9.21321 5.82677 8.69321 5.30677ZM3.82667 13.0001L5.58654 11.2401C5.35988 11.1801 5.13988 11.0801 4.93988 10.9401L2.88667 13.0001H3.82667ZM1 13.0001H1.94L4.45321 10.4934L3.50654 9.55344L1 12.0601V13.0001ZM1 11.1134L3.05988 9.0601C2.91988 8.8601 2.81988 8.64677 2.75988 8.41344L1 10.1734V11.1134Z" fill="#E34F0E"/>
                        </svg>
                    </p>
					<span class="sb-caption sb-lightest">{{advFeedTypeEl.description}}</span>
				</div>
			</div>
		-->

		</div>
	</div>
</div>