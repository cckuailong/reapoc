<div class="cff-fb-slctsrc-ctn cff-fb-fs sb-box-shadow" v-if="viewsActive.selectedFeedSection == 'selectSource'" :data-multifeed="activeExtensions['multifeed'] ? 'active' : 'inactive'">
	<div class="cff-fb-slctsrc-content cff-fb-fs">
		<div class="cff-fb-sec-heading cff-fb-fs">
			<h4>{{selectSourceScreen.mainHeading}}</h4>
			<span class="sb-caption sb-lighter">{{selectSourceScreen.description}}</span>
		</div>
		<div class="cff-fb-srcslist-ctn cff-fb-fs">
			<div class="cff-fb-srcs-item cff-fb-srcs-new" @click.prevent.default="activateView('sourcePopup','creationRedirect')">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.66634 5.66634H5.66634V9.66634H4.33301V5.66634H0.333008V4.33301H4.33301V0.333008H5.66634V4.33301H9.66634V5.66634Z" fill="#0096CC"/>
                </svg>
                <span class="sb-small-p sb-bold">{{genericText.addNew}}</span>
			</div>
			<div :class="checkSourceForEvents(source) || checkTypeForGroup(source) ? ' cff-fb-onbrd-tltp-parent cff-fb-onbrd-tltp-center-top cff-fb-onbrd-tltp-hover ' : ''" class="cff-fb-srcs-item" v-for="(source, sourceIndex) in sourcesList" @click.prevent.default="selectSource(source)" :data-type="source.account_type" :data-active="selectedSources.includes(source.account_id)" :data-disabled="checkSourceForEvents(source) || checkTypeForGroup(source)">
				<div class="cff-fb-onbrd-tltp-elem">
					<p v-if="checkSourceForEvents(source)" class="cff-fb-onbrd-tltp-txt" v-for="eventsToolTipTxt in selectSourceScreen.eventsToolTip" v-html="eventsToolTipTxt.replace(/ /g,' ')"></p>
                    <p v-if="checkTypeForGroup(source)" class="cff-fb-onbrd-tltp-txt" v-for="groupsToolTipTxt in selectSourceScreen.groupsToolTip" v-html="groupsToolTipTxt.replace(/ /g,' ')"></p>
                </div>
				<div class="cff-fb-srcs-item-chkbx">
					<div class="cff-fb-srcs-item-chkbx-ic"></div>
				</div>
				<div class="cff-fb-srcs-item-avatar">
					<img :src="typeof source.avatar_url !== 'undefined' && source.account_type === 'group' ? source.avatar_url : 'https://graph.facebook.com/'+source.account_id+'/picture'">
				</div>
				<div class="cff-fb-srcs-item-inf">
                    <div class="cff-fb-srcs-item-name sb-small-p sb-bold"><span>{{source.username}}</span> <span class="cff-fb-srcs-item-name-event" v-if="source.privilege == 'events'">(events)</span></div>
                    <div class="cff-fb-left-boss">
                        <div class="cff-fb-srcs-item-type">
                            <div v-html="source.account_type == 'group' ? svgIcons['users'] : svgIcons['flag']"></div>
                            <span class="sb-small sb-lighter">{{source.account_type}}</span>
                        </div>
                        <div v-if="source.error !== ''" class="sb-source-error-wrap">
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.50008 0.666664C3.28008 0.666664 0.666748 3.28 0.666748 6.5C0.666748 9.72 3.28008 12.3333 6.50008 12.3333C9.72008 12.3333 12.3334 9.72 12.3334 6.5C12.3334 3.28 9.72008 0.666664 6.50008 0.666664ZM7.08342 9.41667H5.91675V8.25H7.08342V9.41667ZM7.08342 7.08333H5.91675V3.58333H7.08342V7.08333Z" fill="#D72C2C"/>
                            </svg>
                            <span v-html="genericText.errorSource"></span><a href="#" @click.prevent.default="activateView('sourcePopup')" v-html="genericText.reconnect"></a>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="cff-fb-mr-feeds cff-fb-fs" v-if="viewsActive.selectedFeedSection == 'selectSource'">
	<div class="cff-fb-mr-fd-img">
        <svg width="335" height="185" viewBox="0 0 335 185" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0)">
                <rect width="335" height="185" fill="white"/>
                <g opacity="0.3" filter="url(#filter0_f)">
                    <circle cx="128.5" cy="188.5" r="129.5" fill="#1B90EF"/>
                </g>
                <g opacity="0.1" filter="url(#filter1_f)">
                    <circle cx="58.5" cy="28.5" r="129.5" fill="url(#paint0_linear)"/>
                </g>
                <g filter="url(#filter2_d)">
                    <rect x="85.6269" y="-79.76" width="98.3855" height="64.6757" rx="1.37191" transform="rotate(15 85.6269 -79.76)" fill="white" stroke="#E8E8EB" stroke-width="0.391974"/>
                    <g clip-path="url(#clip1)">
                        <rect x="81.6348" y="-65.6123" width="98.7775" height="50.1727" transform="rotate(15 81.6348 -65.6123)" fill="#EFADAD"/>
                    </g>
                    <rect x="66.411" y="-8.04512" width="98.3855" height="64.6757" rx="1.37191" transform="rotate(15 66.411 -8.04512)" fill="white" stroke="#E8E8EB" stroke-width="0.391974"/>
                    <path d="M155.224 22.4831C155.651 22.312 156.056 22.0782 156.404 21.772C156.047 21.8284 155.645 21.835 155.263 21.7707C155.715 21.6446 156.095 21.3657 156.355 20.9601C155.943 21.0779 155.492 21.1283 155.06 21.1076C154.805 20.6398 154.383 20.2985 153.851 20.1558C152.821 19.88 151.764 20.4906 151.488 21.52C151.45 21.662 151.43 21.8087 151.427 21.9602C149.907 21.4577 148.722 20.3414 148.099 18.976C147.868 19.1994 147.693 19.4951 147.598 19.85C147.427 20.489 147.594 21.1425 148.019 21.6176C147.722 21.519 147.439 21.3672 147.229 21.1586L147.224 21.1764C146.981 22.0815 147.421 22.9981 148.225 23.4038C148.073 23.4013 147.886 23.3892 147.727 23.3464C147.603 23.3131 147.501 23.2669 147.381 23.2158C147.412 24.0231 147.962 24.741 148.773 24.9775C148.001 25.3032 147.122 25.3909 146.252 25.1579C146.093 25.1151 145.955 25.0593 145.818 25.0035C146.492 25.7547 147.387 26.318 148.452 26.6033C151.877 27.5211 154.497 25.1985 155.158 22.7316C155.182 22.6429 155.201 22.5719 155.224 22.4831Z" fill="#1B90EF"/>
                    <circle cx="79.5038" cy="13.5211" r="9.01541" transform="rotate(15 79.5038 13.5211)" fill="#D0D1D7"/>
                    <rect x="66.0264" y="28.9824" width="80.7467" height="5.48764" rx="0.728392" transform="rotate(15 66.0264 28.9824)" fill="#D0D1D7"/>
                    <rect x="63.3896" y="38.8262" width="47.0369" height="5.48764" rx="0.728392" transform="rotate(15 63.3896 38.8262)" fill="#D0D1D7"/>
                    <rect x="47.1952" y="63.6697" width="98.3855" height="64.6757" rx="1.37191" transform="rotate(15 47.1952 63.6697)" fill="white" stroke="#E8E8EB" stroke-width="0.391974"/>
                    <path d="M132.093 92.1284C130.958 91.824 129.805 92.5044 129.505 93.6225C129.201 94.7583 129.864 95.906 131 96.2103C132.118 96.5099 133.283 95.8521 133.587 94.7162C133.887 93.5981 133.211 92.428 132.093 92.1284ZM131.19 95.5004C130.462 95.3055 130.016 94.5581 130.215 93.8127C130.41 93.085 131.153 92.6563 131.898 92.856C132.626 93.051 133.055 93.7936 132.86 94.5213C132.66 95.2667 131.917 95.6954 131.19 95.5004ZM134.704 92.7517C134.775 92.4855 134.619 92.2154 134.353 92.1441C134.087 92.0728 133.817 92.2287 133.745 92.4949C133.674 92.7611 133.83 93.0311 134.096 93.1025C134.362 93.1738 134.632 93.0179 134.704 92.7517ZM135.924 93.5923C136.06 92.9439 136.07 92.3379 135.732 91.7528C135.394 91.1677 134.864 90.8736 134.235 90.6669C133.588 90.4554 131.618 89.9276 130.952 89.7871C130.303 89.6514 129.715 89.646 129.112 89.979C128.527 90.3168 128.233 90.8467 128.026 91.4761C127.815 92.1232 127.287 94.0932 127.146 94.7594C127.011 95.4079 127.005 95.9961 127.338 96.5989C127.694 97.1887 128.206 97.4781 128.835 97.6848C129.483 97.8963 131.453 98.4241 132.119 98.5646C132.767 98.7003 133.373 98.7105 133.958 98.3727C134.548 98.0172 134.838 97.505 135.044 96.8756C135.256 96.2285 135.784 94.2585 135.924 93.5923ZM134.007 97.3395C133.788 97.6612 133.432 97.8513 133.057 97.9029C132.467 97.9732 131.239 97.5871 130.671 97.4349C130.086 97.278 128.829 96.9984 128.372 96.6475C128.055 96.4104 127.86 96.0728 127.813 95.6798C127.738 95.1081 128.124 93.88 128.281 93.2943C128.433 92.7264 128.713 91.4698 129.068 90.9944C129.301 90.6953 129.638 90.5004 130.031 90.4535C130.603 90.3785 131.831 90.7646 132.417 90.9215C132.985 91.0737 134.241 91.3533 134.717 91.709C135.021 91.9236 135.211 92.2789 135.262 92.6541C135.333 93.2436 134.946 94.4717 134.794 95.0396C134.637 95.6253 134.358 96.8819 134.007 97.3395Z" fill="url(#paint1_linear)"/>
                    <rect x="42.7979" y="79.333" width="98.7775" height="48.6048" transform="rotate(15 42.7979 79.333)" fill="#FEF4EF"/>
                    <path d="M97.3184 93.9414L138.209 104.898L125.629 151.847L88.5936 126.503L97.3184 93.9414Z" fill="#FCE1D5"/>
                    <path d="M97.3188 93.9419L42.7979 79.333L34.0731 111.894L88.5941 126.503L97.3188 93.9419Z" fill="#F9BBA0"/>
                    <rect x="27.9794" y="135.386" width="98.3855" height="64.6757" rx="1.37191" transform="rotate(15 27.9794 135.386)" fill="white" stroke="#E8E8EB" stroke-width="0.391974"/>
                    <path d="M116.792 165.914C117.218 165.743 117.623 165.509 117.972 165.203C117.614 165.259 117.213 165.266 116.831 165.201C117.283 165.075 117.662 164.796 117.923 164.391C117.511 164.509 117.06 164.559 116.628 164.538C116.373 164.07 115.951 163.729 115.418 163.586C114.389 163.311 113.331 163.921 113.055 164.951C113.017 165.093 112.997 165.239 112.994 165.391C111.474 164.888 110.29 163.772 109.666 162.407C109.435 162.63 109.261 162.926 109.166 163.281C108.995 163.92 109.162 164.573 109.586 165.048C109.289 164.95 109.007 164.798 108.796 164.589L108.791 164.607C108.549 165.512 108.988 166.429 109.792 166.835C109.641 166.832 109.454 166.82 109.294 166.777C109.17 166.744 109.068 166.698 108.949 166.646C108.98 167.454 109.529 168.172 110.341 168.408C109.569 168.734 108.689 168.822 107.82 168.589C107.66 168.546 107.523 168.49 107.385 168.434C108.059 169.185 108.954 169.749 110.019 170.034C113.445 170.952 116.064 168.629 116.725 166.162C116.749 166.074 116.768 166.003 116.792 165.914Z" fill="#1B90EF"/>
                    <circle cx="41.0711" cy="156.952" r="9.01541" transform="rotate(15 41.0711 156.952)" fill="#D0D1D7"/>
                    <rect x="27.5947" y="172.413" width="80.7467" height="5.48764" rx="0.728392" transform="rotate(15 27.5947 172.413)" fill="#D0D1D7"/>
                    <rect x="24.957" y="182.257" width="47.0369" height="5.48764" rx="0.728392" transform="rotate(15 24.957 182.257)" fill="#D0D1D7"/>
                </g>
                <g filter="url(#filter3_d)">
                    <rect x="195.769" y="-73.092" width="98.3855" height="64.6757" rx="1.37191" transform="rotate(15 195.769 -73.092)" fill="white" stroke="#E8E8EB" stroke-width="0.391974"/>
                    <rect x="191.37" y="-57.4287" width="98.7775" height="48.6048" transform="rotate(15 191.37 -57.4287)" fill="#FEF4EF"/>
                    <path d="M245.892 -42.8203L286.782 -31.8637L274.203 15.085L237.167 -10.2592L245.892 -42.8203Z" fill="#FCE1D5"/>
                    <rect x="176.554" y="-1.37618" width="98.3855" height="64.6757" rx="1.37191" transform="rotate(15 176.554 -1.37618)" fill="white" stroke="#E8E8EB" stroke-width="0.391974"/>
                    <path d="M261.453 27.0825C260.317 26.7781 259.164 27.4585 258.865 28.5766C258.561 29.7124 259.223 30.8601 260.359 31.1644C261.477 31.464 262.642 30.8062 262.947 29.6703C263.246 28.5522 262.571 27.3821 261.453 27.0825ZM260.549 30.4545C259.822 30.2596 259.375 29.5122 259.575 28.7668C259.77 28.0391 260.512 27.6104 261.258 27.8101C261.985 28.0051 262.414 28.7477 262.219 29.4754C262.019 30.2208 261.277 30.6495 260.549 30.4545ZM264.063 27.7058C264.134 27.4396 263.978 27.1695 263.712 27.0982C263.446 27.0269 263.176 27.1828 263.105 27.449C263.033 27.7152 263.189 27.9852 263.455 28.0566C263.722 28.1279 263.992 27.972 264.063 27.7058ZM265.283 28.5464C265.419 27.898 265.429 27.292 265.091 26.7069C264.754 26.1218 264.224 25.8277 263.594 25.621C262.947 25.4095 260.977 24.8817 260.311 24.7412C259.663 24.6055 259.074 24.6001 258.472 24.9331C257.886 25.2709 257.592 25.8008 257.386 26.4302C257.174 27.0774 256.646 29.0473 256.506 29.7135C256.37 30.362 256.365 30.9502 256.698 31.553C257.053 32.1428 257.565 32.4322 258.195 32.6389C258.842 32.8504 260.812 33.3782 261.478 33.5187C262.127 33.6544 262.733 33.6646 263.318 33.3268C263.907 32.9713 264.197 32.4591 264.404 31.8297C264.615 31.1826 265.143 29.2126 265.283 28.5464ZM263.366 32.2936C263.147 32.6153 262.792 32.8054 262.416 32.857C261.827 32.9273 260.599 32.5412 260.031 32.389C259.445 32.2321 258.189 31.9525 257.731 31.6016C257.414 31.3645 257.219 31.0269 257.172 30.6339C257.097 30.0622 257.483 28.8341 257.64 28.2485C257.792 27.6805 258.072 26.4239 258.428 25.9486C258.66 25.6494 258.998 25.4545 259.391 25.4076C259.962 25.3326 261.19 25.7187 261.776 25.8756C262.344 26.0278 263.601 26.3074 264.076 26.6631C264.38 26.8777 264.57 27.233 264.622 27.6082C264.692 28.1977 264.306 29.4258 264.154 29.9937C263.997 30.5794 263.717 31.836 263.366 32.2936Z" fill="url(#paint2_linear)"/>
                    <rect x="172.154" y="14.2852" width="98.7775" height="48.6048" transform="rotate(15 172.154 14.2852)" fill="#FEF4EF"/>
                    <path d="M226.673 28.8936L267.564 39.8502L254.984 86.7988L217.948 61.4547L226.673 28.8936Z" fill="#FCE1D5"/>
                    <path d="M226.675 28.894L172.154 14.2852L163.43 46.8463L217.951 61.4552L226.675 28.894Z" fill="#F9BBA0"/>
                    <rect x="157.337" y="70.3387" width="98.3855" height="64.6757" rx="1.37191" transform="rotate(15 157.337 70.3387)" fill="white" stroke="#E8E8EB" stroke-width="0.391974"/>
                    <path d="M246.769 98.8958C246.776 98.4413 246.529 98.0138 246.15 97.7979C245.444 97.3804 242.426 96.572 242.426 96.572C242.426 96.572 239.392 95.7588 238.571 95.7673C238.135 95.7644 237.707 96.0113 237.486 96.4087C237.074 97.097 236.646 98.6943 236.646 98.6943C236.646 98.6943 236.223 100.274 236.231 101.094C236.223 101.549 236.475 101.958 236.855 102.174C237.565 102.574 240.6 103.387 240.6 103.387C240.6 103.387 243.617 104.196 244.433 104.205C244.87 104.208 245.292 103.979 245.513 103.581C245.931 102.875 246.354 101.296 246.354 101.296C246.354 101.296 246.782 99.6983 246.769 98.8958ZM240.13 101.169L240.9 98.2934L243.035 100.406L240.13 101.169Z" fill="#EB2121"/>
                    <g clip-path="url(#clip2)">
                        <rect x="153.345" y="84.4863" width="98.7775" height="50.1727" transform="rotate(15 153.345 84.4863)" fill="#EFADAD"/>
                        <path d="M191.49 108.726L206.118 122.975L186.325 128.002L191.49 108.726Z" fill="white"/>
                    </g>
                    <rect x="138.121" y="142.054" width="98.3855" height="64.6757" rx="1.37191" transform="rotate(15 138.121 142.054)" fill="white" stroke="#E8E8EB" stroke-width="0.391974"/>
                    <path d="M226.933 172.582C227.36 172.411 227.765 172.177 228.113 171.871C227.756 171.927 227.354 171.934 226.972 171.869C227.424 171.743 227.804 171.464 228.064 171.059C227.652 171.177 227.201 171.227 226.769 171.206C226.514 170.738 226.092 170.397 225.56 170.254C224.53 169.979 223.473 170.589 223.197 171.619C223.159 171.761 223.139 171.907 223.136 172.059C221.616 171.556 220.431 170.44 219.808 169.075C219.577 169.298 219.402 169.594 219.307 169.949C219.136 170.588 219.303 171.241 219.728 171.716C219.431 171.618 219.148 171.466 218.938 171.257L218.933 171.275C218.69 172.18 219.13 173.097 219.934 173.502C219.782 173.5 219.595 173.488 219.436 173.445C219.311 173.412 219.21 173.366 219.09 173.314C219.121 174.122 219.671 174.84 220.482 175.076C219.71 175.402 218.831 175.49 217.961 175.257C217.801 175.214 217.664 175.158 217.527 175.102C218.201 175.853 219.096 176.417 220.161 176.702C223.586 177.62 226.206 175.297 226.867 172.83C226.891 172.741 226.91 172.67 226.933 172.582Z" fill="#1B90EF"/>
                    <circle cx="151.213" cy="163.62" r="9.01541" transform="rotate(15 151.213 163.62)" fill="#D0D1D7"/>
                    <rect x="137.735" y="179.081" width="80.7467" height="5.48764" rx="0.728392" transform="rotate(15 137.735 179.081)" fill="#D0D1D7"/>
                </g>
            </g>
            <defs>
                <filter id="filter0_f" x="-75" y="-15" width="407" height="407" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                    <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                    <feGaussianBlur stdDeviation="37" result="effect1_foregroundBlur"/>
                </filter>
                <filter id="filter1_f" x="-145" y="-175" width="407" height="407" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                    <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                    <feGaussianBlur stdDeviation="37" result="effect1_foregroundBlur"/>
                </filter>
                <filter id="filter2_d" x="5.17286" y="-85.0987" width="181.555" height="315.216" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/>
                    <feOffset dy="0.728392"/>
                    <feGaussianBlur stdDeviation="2.91357"/>
                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/>
                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/>
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/>
                </filter>
                <filter id="filter3_d" x="115.314" y="-78.4308" width="181.556" height="315.216" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/>
                    <feOffset dy="0.728392"/>
                    <feGaussianBlur stdDeviation="2.91357"/>
                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/>
                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/>
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/>
                </filter>
                <linearGradient id="paint0_linear" x1="21.2688" y1="394.337" x2="566.787" y2="-162.513" gradientUnits="userSpaceOnUse">
                    <stop stop-color="white"/>
                    <stop offset="0.147864" stop-color="#F6640E"/>
                    <stop offset="0.443974" stop-color="#BA03A7"/>
                    <stop offset="0.733337" stop-color="#6A01B9"/>
                    <stop offset="1" stop-color="#6B01B9"/>
                </linearGradient>
                <linearGradient id="paint1_linear" x1="126.522" y1="108.888" x2="153.008" y2="97.2448" gradientUnits="userSpaceOnUse">
                    <stop stop-color="white"/>
                    <stop offset="0.147864" stop-color="#F6640E"/>
                    <stop offset="0.443974" stop-color="#BA03A7"/>
                    <stop offset="0.733337" stop-color="#6A01B9"/>
                    <stop offset="1" stop-color="#6B01B9"/>
                </linearGradient>
                <linearGradient id="paint2_linear" x1="255.881" y1="43.8426" x2="282.368" y2="32.1989" gradientUnits="userSpaceOnUse">
                    <stop stop-color="white"/>
                    <stop offset="0.147864" stop-color="#F6640E"/>
                    <stop offset="0.443974" stop-color="#BA03A7"/>
                    <stop offset="0.733337" stop-color="#6A01B9"/>
                    <stop offset="1" stop-color="#6B01B9"/>
                </linearGradient>
                <clipPath id="clip0">
                    <rect width="335" height="185" fill="white"/>
                </clipPath>
                <clipPath id="clip1">
                    <rect width="98.7775" height="50.1727" fill="white" transform="translate(81.6348 -65.6123) rotate(15)"/>
                </clipPath>
                <clipPath id="clip2">
                    <rect width="98.7775" height="50.1727" fill="white" transform="translate(153.345 84.4863) rotate(15)"/>
                </clipPath>
            </defs>
        </svg>

    </div>
	<div class="cff-fb-mr-fd-content">
		<div class="cff-fb-mr-fd-heading"><h3 v-html="selectSourceScreen.footer.heading"></h3></div>
		<div class="cff-fb-mr-fd-list">
			<button class="cff-btn-grey cff-fb-mr-fd-item" v-for="(plugin, pluginName, platIndex) in plugins" @click.prevent.default="activateViewOrRedirect('installPluginPopup', pluginName, plugin)">
                <div class="cff-icon-platform-wrap">
				<div class="cff-fb-mr-fd-ic" v-html="svgIcons[pluginName]" :style="'color:' + socialInfo.colorSchemes[pluginName] + ';'"></div>
				<div class="cff-fb-mr-fd-name sb-small-p sb-bold sb-dark-text">{{plugin['displayName']}}</div>
                </div>
				<div class="cff-fb-mr-fd-ch">
                    <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.3332 0L0.158203 1.175L3.97487 5L0.158203 8.825L1.3332 10L6.3332 5L1.3332 0Z" fill="#141B38"/>
                    </svg>
                </div>
			</button>
		</div>
	</div>
</div>