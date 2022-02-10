<!--
<div id="sb-footer-banner" class="cff-fb-fs cff-bld-footer" :class="(viewsActive.pageScreen == 'welcome' && feedsList != null && feedsList.length != 0) ? 'cff-fb-full-wrapper' : 'cff-fb-wrapper'" v-if="(!viewsActive.footerDiabledScreens.includes(viewsActive.pageScreen) || (viewsActive.pageScreen == 'welcome' && feedsList != null && feedsList.length != 0)) && !iscustomizerScreen">
	<div class="sb-box-shadow">
		<div class="cff-bld-ft-content">
			<div class="cff-bld-ft-img"><svg width="178" height="158" viewBox="0 0 178 158" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0)"><rect width="178" height="166" transform="translate(0 -4)" fill="white"/><g opacity="0.2" filter="url(#filter0_f)"><circle cx="33" cy="199" r="125" fill="#E34F0E"/></g><g opacity="0.3" filter="url(#filter1_f)"><circle cx="-23.5784" cy="90.1233" r="98.0868" transform="rotate(-137.681 -23.5784 90.1233)" fill="url(#paint0_linear)"/></g><g filter="url(#filter2_dddd)"><rect x="23.907" y="19.1011" width="56.0099" height="56.0099" rx="3.31121" transform="rotate(4 23.907 19.1011)" fill="white"/><path d="M50.5825 39.0902C45.1148 38.7078 40.329 42.8587 39.9446 48.3563C39.597 53.3269 42.9471 57.707 47.6469 58.7849L48.1335 51.826L45.6084 51.6494L45.81 48.7664L48.3351 48.943L48.4888 46.7459C48.6632 44.2507 50.2404 42.9823 52.517 43.1415C53.6006 43.2173 54.7207 43.4854 54.7207 43.4854L54.549 45.9409L53.2964 45.8534C52.0637 45.7672 51.6224 46.5055 51.5675 47.2909L51.4368 49.1599L54.2005 49.3531L53.5516 52.2048L51.2352 52.0429L50.7486 59.0018C53.1171 58.7956 55.3339 57.7495 56.9987 56.0523C58.6636 54.3551 59.6668 52.1186 59.8273 49.7466C60.2118 44.249 56.0503 39.4725 50.5825 39.0902Z" fill="#006BFA"/><rect x="24.11" y="19.3346" width="55.5723" height="55.5723" rx="3.09242" transform="rotate(4 24.11 19.3346)" stroke="url(#paint1_linear)" stroke-width="0.437577"/></g><g filter="url(#filter3_dddd)"><rect x="78.0662" y="19" width="56.0099" height="56.0099" rx="3.31121" transform="rotate(4 78.0662 19)" fill="white"/><path d="M114.865 43.6525C114.075 43.9469 113.234 44.1179 112.372 44.1675C113.283 43.7017 114.018 42.9139 114.406 41.9321C113.546 42.3715 112.607 42.6555 111.629 42.7869C110.903 41.877 109.835 41.3028 108.582 41.2152C106.246 41.0518 104.204 42.8271 104.039 45.1832C104.015 45.5212 104.032 45.852 104.08 46.1651C100.553 45.7387 97.5209 43.8183 95.6034 41.0968C95.1917 41.6974 94.9315 42.4185 94.8773 43.1939C94.7737 44.6752 95.4276 46.0395 96.5286 46.8658C95.8228 46.8164 95.1806 46.5717 94.6248 46.2332L94.6227 46.263C94.4781 48.3308 95.8285 50.1635 97.7499 50.6874C97.1123 50.8149 96.4539 50.7928 95.8263 50.6229C96.0371 51.4892 96.5141 52.2677 97.1902 52.849C97.8663 53.4303 98.7076 53.785 99.5957 53.8634C98.0048 54.9514 96.0911 55.4658 94.1691 55.3221C93.8311 55.2985 93.4945 55.255 93.1592 55.1916C94.9633 56.5365 97.1607 57.3994 99.5665 57.5677C107.4 58.1155 112.16 51.9148 112.554 46.2781C112.567 46.0892 112.579 45.9103 112.583 45.7207C113.459 45.1826 114.228 44.4771 114.865 43.6525Z" fill="#1B90EF"/><rect x="78.2692" y="19.2335" width="55.5723" height="55.5723" rx="3.09242" transform="rotate(4 78.2692 19.2335)" stroke="url(#paint2_linear)" stroke-width="0.437577"/></g><g filter="url(#filter4_dddd)"><rect x="27.5424" y="69.1201" width="56.0099" height="56.0099" rx="3.31121" transform="rotate(4 27.5424 69.1201)" fill="white"/><path d="M51.0542 102.209L57.0933 99.2598L51.5234 95.4987L51.0542 102.209ZM64.5953 94.3561C64.7039 94.8919 64.7553 95.6035 64.7598 96.5029C64.7756 97.4031 64.7551 98.1772 64.7082 98.8482L64.7096 99.7924C64.5384 102.242 64.2335 104.03 63.8398 105.16C63.4898 106.147 62.7958 106.75 61.7697 106.959C61.2339 107.068 60.265 107.101 58.784 107.065C57.3246 107.042 55.9914 106.982 54.7611 106.896L52.9782 106.839C48.2921 106.511 45.3855 106.128 44.2555 105.735C43.2685 105.385 42.6652 104.691 42.456 103.665C42.3473 103.129 42.2959 102.417 42.2914 101.518C42.2757 100.617 42.2961 99.8434 42.343 99.1724L42.3416 98.2282C42.5129 95.7789 42.8177 93.9908 43.2114 92.8608C43.5614 91.8737 44.2554 91.2704 45.2816 91.0612C45.8174 90.9526 46.7862 90.9192 48.2672 90.9553C49.7266 90.9787 51.0599 91.0382 52.2901 91.1242L54.0731 91.1815C58.7592 91.5092 61.6657 91.8922 62.7957 92.2859C63.7827 92.6359 64.3861 93.3299 64.5953 94.3561Z" fill="#EB2121"/><rect x="27.7454" y="69.3536" width="55.5723" height="55.5723" rx="3.09242" transform="rotate(4 27.7454 69.3536)" stroke="url(#paint3_linear)" stroke-width="0.437577"/></g><g filter="url(#filter5_dddd)"><rect x="81.8024" y="69.1201" width="56.0099" height="56.0099" rx="3.31121" transform="rotate(4 81.8024 69.1201)" fill="white"/><path d="M108.16 93.6514C105.178 93.4429 102.632 95.6999 102.427 98.6357C102.218 101.618 104.428 104.161 107.411 104.369C110.347 104.575 112.936 102.368 113.145 99.3852C113.35 96.4494 111.096 93.8567 108.16 93.6514ZM107.541 102.505C105.631 102.372 104.154 100.723 104.291 98.766C104.424 96.8554 106.07 95.4252 108.027 95.562C109.937 95.6956 111.368 97.341 111.234 99.2516C111.097 101.209 109.452 102.639 107.541 102.505ZM114.977 93.9408C115.026 93.2418 114.506 92.6435 113.807 92.5946C113.108 92.5457 112.509 93.0658 112.461 93.7648C112.412 94.4638 112.932 95.0621 113.631 95.111C114.33 95.1599 114.928 94.6398 114.977 93.9408ZM118.431 95.4466C118.455 93.7625 118.186 92.2453 117.059 90.9489C115.932 89.6526 114.467 89.1755 112.796 88.965C111.079 88.7512 105.906 88.3895 104.175 88.3622C102.491 88.3381 101.02 88.6098 99.6775 89.7335C98.3812 90.8603 97.9041 92.3255 97.6936 93.9966C97.4798 95.7143 97.1181 100.887 97.0907 102.618C97.0666 104.302 97.3384 105.772 98.462 107.115C99.6355 108.415 101.054 108.889 102.725 109.099C104.443 109.313 109.615 109.675 111.346 109.702C113.03 109.726 114.548 109.458 115.844 108.331C117.143 107.157 117.617 105.739 117.828 104.068C118.042 102.35 118.403 97.1774 118.431 95.4466ZM115.464 105.729C115.073 106.638 114.278 107.285 113.367 107.596C111.929 108.057 108.677 107.689 107.186 107.585C105.648 107.477 102.376 107.389 101.064 106.735C100.158 106.297 99.5081 105.549 99.2005 104.591C98.7358 103.201 99.1037 99.9487 99.2112 98.4109C99.3155 96.9197 99.4038 93.6479 100.061 92.289C100.495 91.4296 101.243 90.7795 102.201 90.4719C103.592 90.0072 106.844 90.3751 108.382 90.4826C109.873 90.5869 113.145 90.6752 114.504 91.3322C115.366 91.7203 116.013 92.5148 116.324 93.4262C116.786 94.8633 116.418 98.1156 116.313 99.6068C116.206 101.145 116.118 104.416 115.464 105.729Z" fill="url(#paint4_linear)"/><rect x="82.0054" y="69.3536" width="55.5723" height="55.5723" rx="3.09242" transform="rotate(4 82.0054 69.3536)" stroke="url(#paint5_linear)" stroke-width="0.437577"/></g></g><defs><filter id="filter0_f" x="-166" y="0" width="398" height="398" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/><feGaussianBlur stdDeviation="37" result="effect1_foregroundBlur"/></filter><filter id="filter1_f" x="-195.666" y="-81.9644" width="344.175" height="344.175" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/><feGaussianBlur stdDeviation="37" result="effect1_foregroundBlur"/></filter><filter id="filter2_dddd" x="12.4344" y="17.588" width="74.9115" height="74.9115" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="0.749837"/><feGaussianBlur stdDeviation="0.468648"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.1137 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="1.80196"/><feGaussianBlur stdDeviation="1.12623"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0484671 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="3.39293"/><feGaussianBlur stdDeviation="2.12058"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.06 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="6.05242"/><feGaussianBlur stdDeviation="3.78276"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0715329 0"/><feBlend mode="normal" in2="effect3_dropShadow" result="effect4_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect4_dropShadow" result="shape"/></filter><filter id="filter3_dddd" x="66.5936" y="17.4869" width="74.9115" height="74.9115" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="0.749837"/><feGaussianBlur stdDeviation="0.468648"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.1137 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="1.80196"/><feGaussianBlur stdDeviation="1.12623"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0484671 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="3.39293"/><feGaussianBlur stdDeviation="2.12058"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.06 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="6.05242"/><feGaussianBlur stdDeviation="3.78276"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0715329 0"/><feBlend mode="normal" in2="effect3_dropShadow" result="effect4_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect4_dropShadow" result="shape"/></filter><filter id="filter4_dddd" x="16.0698" y="67.607" width="74.9115" height="74.9115" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="0.749837"/><feGaussianBlur stdDeviation="0.468648"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.1137 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="1.80196"/><feGaussianBlur stdDeviation="1.12623"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0484671 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="3.39293"/><feGaussianBlur stdDeviation="2.12058"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.06 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="6.05242"/><feGaussianBlur stdDeviation="3.78276"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0715329 0"/><feBlend mode="normal" in2="effect3_dropShadow" result="effect4_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect4_dropShadow" result="shape"/></filter><filter id="filter5_dddd" x="70.3298" y="67.607" width="74.9115" height="74.9115" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="0.749837"/><feGaussianBlur stdDeviation="0.468648"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.1137 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="1.80196"/><feGaussianBlur stdDeviation="1.12623"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0484671 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="3.39293"/><feGaussianBlur stdDeviation="2.12058"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.06 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="6.05242"/><feGaussianBlur stdDeviation="3.78276"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0715329 0"/><feBlend mode="normal" in2="effect3_dropShadow" result="effect4_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect4_dropShadow" result="shape"/></filter><linearGradient id="paint0_linear" x1="-51.7783" y1="367.218" x2="361.412" y2="-54.5547" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="0.147864" stop-color="#F6640E"/><stop offset="0.443974" stop-color="#BA03A7"/><stop offset="0.733337" stop-color="#6A01B9"/><stop offset="1" stop-color="#6B01B9"/></linearGradient><linearGradient id="paint1_linear" x1="29.1579" y1="19.1011" x2="79.9169" y2="72.1938" gradientUnits="userSpaceOnUse"><stop stop-color="#B5CBEC"/><stop offset="1" stop-color="#B6CFF4" stop-opacity="0.32"/></linearGradient><linearGradient id="paint2_linear" x1="83.3171" y1="19" x2="134.076" y2="72.0927" gradientUnits="userSpaceOnUse"><stop stop-color="#B5DBEC"/><stop offset="1" stop-color="#B6CFF4" stop-opacity="0.32"/></linearGradient><linearGradient id="paint3_linear" x1="32.7933" y1="69.1201" x2="83.5522" y2="122.213" gradientUnits="userSpaceOnUse"><stop stop-color="#ECB5B5"/><stop offset="1" stop-color="#F4B6B6" stop-opacity="0.32"/></linearGradient><linearGradient id="paint4_linear" x1="102.698" y1="128.311" x2="149.808" y2="86.5004" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="0.147864" stop-color="#F6640E"/><stop offset="0.443974" stop-color="#BA03A7"/><stop offset="0.733337" stop-color="#6A01B9"/><stop offset="1" stop-color="#6B01B9"/></linearGradient><linearGradient id="paint5_linear" x1="87.0533" y1="69.1201" x2="137.812" y2="122.213" gradientUnits="userSpaceOnUse"><stop stop-color="#BAB5EC"/><stop offset="1" stop-color="#F3B6F4" stop-opacity="0.32"/></linearGradient><clipPath id="clip0"><rect width="178" height="166" fill="white" transform="translate(0 -4)"/></clipPath></defs></svg></div>
			<div class="cff-bld-ft-txt">
				<h3 class="cff-bld-ft-title" v-html="mainFooterScreen.heading"></h3>
				<div class="cff-bld-ft-info sb-small-p" v-html="mainFooterScreen.description"></div>
			</div>
			<div class="cff-bld-ft-action">
				<a :href="links.allAccessBundle" target="_blank" class="sb-button-standard sb-button-right-icon sb-button cff-btn-grey">{{genericText.learnMore}}
            <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.33336 0L0.15836 1.175L3.97503 5L0.15836 8.825L1.33336 10L6.33336 5L1.33336 0Z" fill="#141B38"/>
            </svg>
        </a>
			</div>
		</div>
		<div v-if="pluginType !== 'pro'" class="cff-bld-ft-btm" v-html="mainFooterScreen.promo"></div>
	</div>
</div>
-->
<div class="cff-stck-wdg" v-if="viewsActive.pageScreen !== 'selectFeed' && ! iscustomizerScreen" :data-active="checkActiveView('footerWidget')">
	<?php
		$smashballoon_info = CustomFacebookFeed\Builder\CFF_Feed_Builder::get_smashballoon_info();
	?>
	<div class="cff-stck-pop">

		<div class="cff-stck-el cff-stck-el-upgrd cff-fb-fs sb-btn-orange">
			<div class="cff-stck-el-icon"><?php echo $icons[$smashballoon_info['upgrade']['icon']] ?></div>
			<div class="cff-stck-el-txt sb-small-p sb-bold" style="color: #fff;"><?php echo $smashballoon_info['upgrade']['name'] ?></div>
            <div class="cff-chevron">
                <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.3332 0L0.158203 1.175L3.97487 5L0.158203 8.825L1.3332 10L6.3332 5L1.3332 0Z" fill="white"/>
                </svg>
            </div>
            <a href="<?php echo esc_url($smashballoon_info['upgrade']['link']) ?>" target="_blank" class="cff-fs-a"></a>
		</div>

		<div class="cff-stck-title cff-fb-fs sb-small-p sb-bold sb-dark-text"><?php echo __('Our Feeds for other platforms','custom-facebook-feed') ?></div>

		<div class="cff-stck-el-list cff-fb-fs">
			<?php foreach ($smashballoon_info['platforms'] as $platform): ?>
				<div class="cff-stck-el cff-fb-fs">

					<div class="cff-stck-el-icon" style="color:<?php echo $smashballoon_info['colorSchemes'][$platform['icon']] ?>;"><?php echo $icons[$platform['icon']] ?></div>
					<div class="cff-stck-el-txt sb-small-text sb-small-p sb-dark-text"><?php echo $platform['name'] ?></div>
                    <div class="cff-chevron">
                        <svg width="7" height="10" viewBox="0 0 7 10" fill="#8C8F9A" xmlns="http://www.w3.org/2000/svg"><path d="M1.3332 0L0.158203 1.175L3.97487 5L0.158203 8.825L1.3332 10L6.3332 5L1.3332 0Z" fill="#8C8F9A"></path></svg>
                    </div>
					<a href="<?php echo esc_url($platform['link'] ) ?>" target="_blank" class="cff-fs-a"></a>
				</div>
			<?php endforeach ?>
		</div>
		<div class="cff-stck-follow cff-fb-fs">
			<span><?php echo __('Follow Us','custom-facebook-feed') ?></span>
			<div class="cff-stck-flw-links">
				<?php foreach ($smashballoon_info['socialProfiles'] as $social_key => $social): ?>
					<a href="<?php echo esc_url($social); ?>" target="_blank"  style="color:<?php echo $smashballoon_info['colorSchemes'][$social_key] ?>;"><?php echo $icons[$social_key] ?></a>
				<?php endforeach ?>
			</div>
		</div>
	</div>
	<div class="cff-stck-wdg-btn" @click.prevent.default="activateView('footerWidget')">
		<?php echo $icons['smash']; ?>
		<div class="cff-stck-wdg-btn-cls">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.501 1.77279L13.091 0.362793L7.50098 5.95279L1.91098 0.362793L0.500977 1.77279L6.09098 7.36279L0.500977 12.9528L1.91098 14.3628L7.50098 8.77279L13.091 14.3628L14.501 12.9528L8.91098 7.36279L14.501 1.77279Z" fill="#141B38"/>
            </svg>
        </div>
	</div>
</div>
<?php
	include_once CFF_BUILDER_DIR . 'templates/sections/popup/add-source-popup.php';
	include_once CFF_BUILDER_DIR . 'templates/sections/popup/extensions-popup.php';
	include_once CFF_BUILDER_DIR . 'templates/sections/popup/feedtypes-popup.php';
	include_once CFF_BUILDER_DIR . 'templates/sections/popup/embed-popup.php';
	include_once CFF_BUILDER_DIR . 'templates/sections/popup/confirm-dialog-popup.php';
    include_once CFF_BUILDER_DIR . 'templates/sections/popup/onboarding-popup.php';
    include_once CFF_BUILDER_DIR . 'templates/sections/popup/onboarding-customizer-popup.php';
	include_once CFF_BUILDER_DIR . 'templates/sections/popup/install-plugin-popup.php';
?>
<div class="sb-notification-ctn" :data-active="notificationElement.shown" :data-type="notificationElement.type">
	<div class="sb-notification-icon" v-html="svgIcons[notificationElement.type+'Notification']"></div>
	<span class="sb-notification-text" v-html="notificationElement.text"></span>
</div>

<div class="sb-full-screen-loader" :data-show="fullScreenLoader ? 'shown' :  'hidden'">
	<div class="sb-full-screen-loader-logo">
		<div class="sb-full-screen-loader-spinner"></div>
		<div class="sb-full-screen-loader-img" v-html="svgIcons['smash']"></div>
	</div>
	<div class="sb-full-screen-loader-txt">
		Loading...
	</div>
</div>

<sb-add-source-component
:sources-list="sourcesList"
:select-source-screen="selectSourceScreen"
:views-active="viewsActive"
:generic-text="genericText"
:selected-feed="selectedFeed"
:svg-icons="svgIcons"
:links="links"
ref="addSourceRef"
>
</sb-add-source-component>

<sb-confirm-dialog-component
:dialog-box.sync="dialogBox"
:source-to-delete="sourceToDelete"
:svg-icons="svgIcons"
:parent-type="'builder'"
:generic-text="genericText"
></sb-confirm-dialog-component>

<install-plugin-popup
:views-active="viewsActive"
:generic-text="genericText"
:svg-icons="svgIcons"
:plugins="plugins[viewsActive.installPluginModal]"
>
</install-plugin-popup>