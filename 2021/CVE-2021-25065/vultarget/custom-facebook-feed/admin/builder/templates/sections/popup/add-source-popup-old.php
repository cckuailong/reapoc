<script type="text/x-template" id="sb-add-source-component">
    <div class="cff-fb-source-ctn sb-fs-boss cff-fb-center-boss" v-if="viewsActive.sourcePopup">
        <!--START Source Popup on the Customizer-->
        <div class="cff-fb-source-popup cff-fb-popup-inside cff-fb-source-pp-customizer" v-if="viewsActive.sourcePopupType == 'customizer'" :data-multifeed="$parent.activeExtensions['multifeed'] ? 'active' : 'inactive'">
            <div class="cff-fb-popup-cls" @click.prevent.default="$parent.closeSourceCustomizer()">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
                </svg>
            </div>
            <div class="cff-fb-source-top cff-fb-fs">
                <h3>{{selectSourceScreen.updateHeading}}</h3>
                <div class="cff-fb-srcs-desc">{{selectSourceScreen.updateDescription}}</div>
                <div class="cff-fb-srcslist-ctn cff-fb-fs">
                    <div class="cff-fb-srcs-item cff-fb-srcs-new" @click.prevent.default="$parent.activateView('sourcePopupType', 'creation')">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.66634 5.66634H5.66634V9.66634H4.33301V5.66634H0.333008V4.33301H4.33301V0.333008H5.66634V4.33301H9.66634V5.66634Z" fill="#0096CC"/>
                        </svg>
                        <span class="sb-small-p sb-bold">{{genericText.addNew}}</span>
                    </div>
                    <div class="cff-fb-srcs-item" v-for="(source, sourceIndex) in sourcesList" @click.prevent.default="$parent.selectSourceCustomizer(source)" :data-type="source.account_type"
                         :data-active="$parent.isSourceActiveCustomizer(source)"
                         :data-test="(Array.isArray($parent.customizerFeedData.settings.sources.map) || $parent.customizerFeedData.settings.sources instanceof Object ) && $parent.customizerFeedData.settings.sources.map(s => s.account_id).includes(source.account_id)"
                    >
                        <div class="cff-fb-srcs-item-chkbx">
                            <div class="cff-fb-srcs-item-chkbx-ic"></div>
                        </div>
                        <div class="cff-fb-srcs-item-avatar">
                            <img :src="typeof source.avatar_url !== 'undefined' && source.account_type === 'group' ? source.avatar_url : 'https://graph.facebook.com/'+source.account_id+'/picture'">
                        </div>
                        <div class="cff-fb-srcs-item-inf">
                            <div class="cff-fb-srcs-item-name"><span class="sb-small-p sb-bold" v-html="source.username"></span></div>
                            <div class="cff-fb-left-boss">
                                <div class="cff-fb-srcs-item-type">
                                    <div v-html="source.account_type == 'group' ? svgIcons['users'] : svgIcons['flag']"></div>
                                    <span class="sb-small sb-lighter" v-html="source.account_type"></span>
                                </div>
                                <div v-if="source.error !== ''" class="sb-source-error-wrap">
                                    <span v-html="genericText.invalid"></span><a href="#" @click.prevent.default="$parent.activateView('sourcePopupType', 'creation')" v-html="genericText.reconnect"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cff-fb-srcs-update-ctn cff-fb-fs">
                    <button class="cff-fb-srcs-update sb-btn cff-fb-fs sb-btn-orange" @click.prevent.default="$parent.activateView('sourcePopup', 'updateCustomizer', 'feedFlyPreview')">
                        <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.08058 8.36133L14.0355 0.406383L15.8033 2.17415L6.08058 11.8969L0.777281 6.59357L2.54505 4.8258L6.08058 8.36133Z" fill="white"/>
                        </svg>
                        <span>{{genericText.update}}</span>
                    </button>
                </div>
            </div>

            <div v-if="!$parent.activeExtensions['multifeed']" class="cff-fb-srcs-update-footer cff-fb-fs">
                <div class="cff-fb-srcs-update-footer-image">
                    <svg width="171" height="107" viewBox="0 0 171 107" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#clip0)"> <g style="mix-blend-mode:multiply" opacity="0.3" filter="url(#filter0_f)"> <circle cx="-52.5141" cy="81.0831" r="129.5" transform="rotate(27.5614 -52.5141 81.0831)" fill="url(#paint0_linear)"/> </g> <g style="mix-blend-mode:multiply" opacity="0.5" filter="url(#filter1_f)"> <circle cx="-0.490694" cy="175.917" r="129.5" transform="rotate(27.5614 -0.490694 175.917)" fill="#52D1FF"/> </g> <g filter="url(#filter2_d)"> <g clip-path="url(#clip1)"> <rect x="28.3672" y="27.6296" width="98.778" height="65.0678" rx="1.5679" transform="rotate(-3 28.3672 27.6296)" fill="white"/> <path d="M123.358 30.4864C123.227 27.9727 121.083 26.0427 118.569 26.1745C116.056 26.3062 114.126 28.4496 114.257 30.9634C114.377 33.2386 116.127 35.042 118.328 35.2578L118.161 32.0835L117.006 32.1441L116.936 30.823L118.092 30.7624L118.04 29.7716C117.981 28.634 118.626 27.9562 119.653 27.9023C120.167 27.8754 120.686 27.9402 120.686 27.9402L120.744 29.0595L120.176 29.0893C119.607 29.1191 119.442 29.4774 119.461 29.8443L119.505 30.6884L120.771 30.622L120.639 31.9537L119.574 32.0095L119.741 35.1838C121.907 34.7391 123.478 32.7617 123.358 30.4864Z" fill="#006BFA"/> <rect x="33.2227" y="43.8606" width="90.7776" height="44.6095" transform="rotate(-3 33.2227 43.8606)" fill="#F6966B"/> <path d="M85.0273 41.146L123.879 39.1099L126.213 83.6583L86.6466 72.0425L85.0273 41.146Z" fill="#86D0F9"/> <path d="M85.0245 41.1458L33.2227 43.8606L34.8419 74.7571L86.6437 72.0423L85.0245 41.1458Z" fill="#FCE1D5"/> </g> <rect x="28.5732" y="27.8151" width="98.386" height="64.6758" rx="1.37191" transform="rotate(-3 28.5732 27.8151)" stroke="#E8E8EB" stroke-width="0.391974"/> </g> <g filter="url(#filter3_d)"> <rect x="38" y="17" width="98.778" height="65.0678" rx="1.5679" fill="white"/> <path d="M132.714 24.8243C132.714 22.3071 130.674 20.2676 128.157 20.2676C125.64 20.2676 123.6 22.3071 123.6 24.8243C123.6 27.1026 125.254 28.9951 127.44 29.3259V26.1472H126.283V24.8243H127.44V23.8321C127.44 22.6929 128.12 22.0498 129.149 22.0498C129.663 22.0498 130.178 22.1417 130.178 22.1417V23.2625H129.608C129.039 23.2625 128.855 23.6116 128.855 23.9791V24.8243H130.123L129.921 26.1472H128.855V29.3259C131.042 28.9951 132.714 27.1026 132.714 24.8243Z" fill="#006BFA"/> <rect x="42" y="33.4629" width="90.7776" height="44.6095" fill="#43A6DB"/> <path d="M93.8789 33.4634L132.784 33.4634V78.0729L93.8789 64.4023V33.4634Z" fill="#86D0F9"/> <path d="M93.8729 33.4629L42 33.4629L42 64.4018H93.8729V33.4629Z" fill="#B5E5FF"/> <rect x="38.196" y="17.196" width="98.386" height="64.6758" rx="1.37191" stroke="#E8E8EB" stroke-width="0.391974"/> </g> <rect x="119.5" y="65.5" width="25" height="25" rx="12.5" fill="#E34F0E"/> <path d="M136.375 78.625H132.625V82.375H131.375V78.625H127.625V77.375H131.375V73.625H132.625V77.375H136.375V78.625Z" fill="white"/> </g> <defs> <filter id="filter0_f" x="-301.238" y="-167.64" width="497.446" height="497.446" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/> <feGaussianBlur stdDeviation="37" result="effect1_foregroundBlur"/> </filter> <filter id="filter1_f" x="-249.215" y="-72.8064" width="497.446" height="497.446" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/> <feGaussianBlur stdDeviation="37" result="effect1_foregroundBlur"/> </filter> <filter id="filter2_d" x="25.3672" y="22.46" width="110.048" height="78.1482" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dx="1" dy="4"/> <feGaussianBlur stdDeviation="2"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <filter id="filter3_d" x="35" y="17" width="106.778" height="73.0678" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dx="1" dy="4"/> <feGaussianBlur stdDeviation="2"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <linearGradient id="paint0_linear" x1="-89.7454" y1="446.921" x2="455.773" y2="-109.929" gradientUnits="userSpaceOnUse"> <stop stop-color="white"/> <stop offset="0.147864" stop-color="#F6640E"/> <stop offset="0.443974" stop-color="#BA03A7"/> <stop offset="0.733337" stop-color="#6A01B9"/> <stop offset="1" stop-color="#6B01B9"/> </linearGradient> <clipPath id="clip0"> <path d="M0 0H171V105C171 106.105 170.105 107 169 107H0V0Z" fill="white"/> </clipPath> <clipPath id="clip1"> <rect x="28.3672" y="27.6296" width="98.778" height="65.0678" rx="1.5679" transform="rotate(-3 28.3672 27.6296)" fill="white"/> </clipPath> </defs> </svg>
                </div>
                <div class="cff-fb-srcs-update-footer-txt">
                    <h4>{{selectSourceScreen.updateFooter}}</h4>
                </div>
                <div class="cff-fb-srcs-update-footer-btn">
                    <a :href="links.multifeedCTA" target="_blank" class="cff-fb-hd-btn cff-btn-grey sb-button-standard sb-button-right-icon">
                        {{genericText.learnMore}}
                        <svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.67109 0.5L0.496094 1.675L4.31276 5.5L0.496094 9.325L1.67109 10.5L6.67109 5.5L1.67109 0.5Z" fill="#141B38"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <!--END Source Popup on the Customizer-->

        <div class="cff-fb-source-popup cff-fb-popup-inside"  v-if="viewsActive.sourcePopupType != 'customizer'">
            <div class="cff-fb-popup-cls" @click.prevent.default="$parent.activateView('sourcePopup')">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
                </svg>
            </div>
            <!-- Step One Select Source -->
            <div class="cff-fb-source-step1 cff-fb-fs" v-if="viewsActive.sourcePopupScreen == 'step_1'">
                <div class="cff-fb-source-top cff-fb-fs">
                    <div class=" cff-fb-fs">
                        <div class="cff-fb-src-back-top" @click.prevent.default="$parent.activateView('sourcePopup', 'updateCustomizer')">
                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.27398 1.44L4.33398 0.5L0.333984 4.5L4.33398 8.5L5.27398 7.56L2.22065 4.5L5.27398 1.44Z" fill="#434960"/>
                            </svg>
                            {{selectSourceScreen.mainHeading}}
                        </div>
                    </div>
                    <h3>{{selectSourceScreen.modal.addNew}}</h3>
                    <div class="cff-fb-stp1-elm cff-fb-fs">
                        <div class="cff-fb-stp1-elm-ic">1</div>
                        <div class="cff-fb-stp1-elm-txt">
                            <div class="cff-fb-stp1-elm-head sb-small-p sb-bold sb-dark-text">{{selectSourceScreen.modal.selectSourceType}}</div>
                        </div>
                        <div class="cff-fb-stp1-elm-act cff-fb-stp-src-ctn">
                            <div class="cff-fb-stp-src-type sb-small-p sb-dark-text" :data-active="addNewSource.typeSelected == 'page'" @click.prevent.default="addNewSource.typeSelected = 'page'">
                                <div class="cff-fb-chbx-round"></div>{{selectSourceScreen.page}}
                            </div>
                            <div class="cff-fb-stp-src-type sb-small-p sb-dark-text" :data-disabled="typeof window.cffSelectedFeed !== 'undefined' && window.cffSelectedFeed === 'photos' ? true : false" :data-active="addNewSource.typeSelected == 'group'" @click.prevent.default="typeof window.cffSelectedFeed !== 'undefined' && window.cffSelectedFeed === 'photos' ? addNewSource.typeSelected = 'page' : addNewSource.typeSelected = 'group'">
                                <div class="cff-fb-chbx-round"></div>{{selectSourceScreen.group}}

                                <div class="cff-fb-onbrd-tltp-elem cff-no-groups-tooltip sb-tr-2" v-if="typeof window.cffSelectedFeed !== 'undefined' && window.cffSelectedFeed === 'photos'">
                                    <div class="cff-fb-onbrd-tltp-txt sb-small-p sb-lighter">
                                        {{selectSourceScreen.modal.noGroupTooltip}}
                                    </div>
                                    <div class="sb-pointer sb-bottom-pointer">
                                        <svg width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.4142 8.58579C10.6332 9.36683 9.36684 9.36683 8.58579 8.58579L0 0L20 0L11.4142 8.58579Z" fill="white"/>
                                        </svg>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="cff-fb-stp1-elm cff-fb-fs" v-if="(addNewSource.typeSelected == 'group' && selectedFeed == 'events') || selectedFeed != 'events'">
                        <div class="cff-fb-stp1-elm-ic">2</div>
                        <div class="cff-fb-stp1-elm-txt">
                            <div class="cff-fb-stp1-elm-head sb-small-p sb-bold sb-dark-text">{{selectSourceScreen.modal.connectAccount}}</div>
                            <div class="cff-fb-stp1-elm-desc sb-caption sb-caption-lighter">{{selectSourceScreen.modal.connectAccountDescription}}</div>
                        </div>
                        <div class="cff-fb-stp1-elm-act">
                            <button class="sb-btn cff-fb-stp1-connect sb-btn-blue" @click.prevent.default="processFBConnect()">
                                <a class="cff-fb-fs-link"></a>
                                <div v-html="svgIcons['facebook']"></div>
                                {{selectSourceScreen.modal.connect}}
                            </button>
                        </div>
                    </div>

                    <div class="cff-fb-fs" v-if="(addNewSource.typeSelected == 'page' && selectedFeed == 'events')">
                        <!--Add Event Source-->
                        <div class="cff-fb-stp1-elm cff-fb-stp1-event cff-fb-fs">
                            <div class="cff-fb-stp1-elm-ic">2</div>
                            <div class="cff-fb-stp1-elm-txt">
                                <div class="cff-fb-stp1-elm-head sb-small-p sb-bold sb-dark-text">{{selectSourceScreen.modal.enterEventToken}}</div>
                                <div class="cff-fb-stp1-elm-desc sb-caption sb-caption-lighter" v-html="selectSourceScreen.modal.enterEventTokenDescription"></div>
                            </div>
                            <div class="cff-fb-stp1-elm-act"></div>
                        </div>
                        <div class="cff-fb-fs">
                            <div class="cff-fb-source-inputs cff-fb-fs">
                                <div class="cff-fb-source-inp-label cff-fb-fs">{{selectSourceScreen.modal.fbPageID}}</div>
                                <input type="text" class="cff-fb-source-inp cff-fb-fs" v-model="addNewSource.manualSourceID" :placeholder="selectSourceScreen.modal.enterID">
                                <div class="cff-fb-source-inp-label cff-fb-fs">{{selectSourceScreen.modal.eventAccessToken}}</div>
                                <input type="text" class="cff-fb-source-inp cff-fb-fs" v-model="addNewSource.manualSourceToken" :placeholder="selectSourceScreen.modal.enterToken">
                            </div>
                            <button class="cff-fb-source-btn cff-fb-fs sb-btn-blue sb-account-connection-button" @click.prevent.default="addSourceManually(true)">
                                <div class="cff-fb-icon-success"></div>
                                {{selectSourceScreen.modal.addSource}}
                            </button>
                        </div>
                    </div>


                </div>
                <div class="cff-fb-source-bottom cff-fb-fs" v-if="(addNewSource.typeSelected == 'group' && selectedFeed == 'events') || selectedFeed != 'events'">
                    <div class="cff-manual-question">
                        <svg width="20" height="15" viewBox="0 0 20 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.6004 7.4998L15.0748 12.0254L13.9436 10.8942L17.338 7.4998L13.9436 4.1054L15.0748 2.9742L19.6004 7.4998ZM2.66279 7.4998L6.05719 10.8942L4.92599 12.0254L0.400391 7.4998L4.92599 2.9742L6.05639 4.1054L2.66279 7.4998ZM8.23079 14.6998H6.52839L11.77 0.299805H13.4724L8.23079 14.6998Z" fill="#141B38"/>
                        </svg>

                        <div class="cff-fb-source-btm-hd sb-small-p sb-bold sb-dark-text">{{selectSourceScreen.modal.alreadyHave}}</div>
                    </div>
                    <button class="cff-fb-hd-btn cff-fb-src-add-manual sb-btn-grey" @click.prevent.default="$parent.switchScreen('sourcePopupScreen','step_3')">
                        <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.8327 7.33317H6.83268V12.3332H5.16602V7.33317H0.166016V5.6665H5.16602V0.666504H6.83268V5.6665H11.8327V7.33317Z" fill="#141B38"/>
                        </svg>
                        <span class="sb-small-p sb-bold sb-dark-text">{{selectSourceScreen.modal.addManuallyLink}}</span>
                    </button>
                </div>
            </div>

            <!-- Step Two Show Pages Connected to -->
            <div class="cff-fb-source-step2 cff-fb-fs" v-if="viewsActive.sourcePopupScreen == 'step_2'">
                <div class="cff-fb-source-top cff-fb-fs">
                    <div class=" cff-fb-fs">
                        <div class="cff-fb-src-back-top" @click.prevent.default="$parent.switchScreen('sourcePopupScreen','step_1')">
                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.27398 1.44L4.33398 0.5L0.333984 4.5L4.33398 8.5L5.27398 7.56L2.22065 4.5L5.27398 1.44Z" fill="#434960"/>
                            </svg>
                            {{selectSourceScreen.modal.addNew}}
                        </div>
                    </div>
                    <div v-if="typeof $parent.newSourceData !== 'undefined' && typeof $parent.newSourceData.error !== 'undefined'" class="cff-groups-connect-actions">
                        <div class="sb-alert">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.99935 0.666504C4.39935 0.666504 0.666016 4.39984 0.666016 8.99984C0.666016 13.5998 4.39935 17.3332 8.99935 17.3332C13.5993 17.3332 17.3327 13.5998 17.3327 8.99984C17.3327 4.39984 13.5993 0.666504 8.99935 0.666504ZM9.83268 13.1665H8.16602V11.4998H9.83268V13.1665ZM9.83268 9.83317H8.16602V4.83317H9.83268V9.83317Z" fill="#995C00"/>
                            </svg>
                            <span><strong v-html="genericText.errorNotice"></strong></span><br>
                            <span class="sb-caption"><span v-html="genericText.error"></span> <span v-html="typeof $parent.newSourceData.error.code !== 'undefined' ? $parent.newSourceData.error.code : ''"></span><br><span v-html="$parent.newSourceData.error.message"></span></span>
                            <br><span class="sb-caption" v-html="genericText.errorDirections"></span>
                        </div>
                    </div>
                    <div v-if="typeof $parent.newSourceData === 'undefined' || typeof $parent.newSourceData.error === 'undefined'" >
                        <h3>{{addNewSource.typeSelected === 'page' ? selectSourceScreen.modal.selectPage : selectSourceScreen.modal.selectGroup}}</h3>
                        <div class="cff-fb-source-account-info cff-fb-fs">
                            <span class="sb-small-p sb-bold">{{selectSourceScreen.modal.showing}} <strong>{{selectSourceScreen.modal.facebook}} {{addNewSource.typeSelected === 'page' ? selectSourceScreen.modal.pages : selectSourceScreen.modal.groups}}</strong> {{selectSourceScreen.modal.connectedTo}}</span>
                            <img :src="$parent.hasOwnNestedProperty(newSourceData,'user.picture.data.url') ? newSourceData.user.picture.data.url : ''"> <strong v-if="$parent.hasOwnNestedProperty(newSourceData,'user.name')" v-html="newSourceData.user.name"></strong>
                            <button class="cff-fb-hd-btn cff-fb-src-change sb-btn-grey" @click="processFBConnect()">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.5 12.3749V15.4999H3.625L12.8417 6.2832L9.71667 3.1582L0.5 12.3749ZM15.8417 3.2832L12.7167 0.158203L10.6083 2.27487L13.7333 5.39987L15.8417 3.2832Z" fill="#141B38"/>
                                </svg>
                                <span class="sb-small-p sb-bold sb-dark-text">{{genericText.change}}</span>
                            </button>
                        </div>
                        <div class="cff-fb-source-list cff-fb-fs">
                            <div class="cff-fb-srcs-item" v-for="(source, sourceIndex) in returnedApiSourcesList" @click.prevent.default="selectSourcesToConnect(source)" :data-active="selectedSourcesToConnect.includes(source.account_id)" >
                                <div class="cff-fb-srcs-item-chkbx">
                                    <div class="cff-fb-srcs-item-chkbx-ic"></div>
                                </div>
                                <div class="cff-fb-srcs-item-avatar">
                                    <img :src="returnGroupPageAvatar(source)">
                                </div>
                                <div class="cff-fb-srcs-item-inf" v-bind:class="{ 'sb-has-details' : source.account_type === 'group' }">
                                    <div class="cff-fb-srcs-item-name"><span class="sb-small-p sb-bold" v-html="source.username"></span></div>
                                    <div class="cff-fb-srcs-item-type" v-bind:class="{ 'sb-highlight-admin' : source.admin, 'sb-is-group' : source.account_type === 'group' }" v-if="source.account_type === 'group'">
                                        <div class="sb-details-wrap">
                                            <div class="cff-fb-srcs-item-svg" v-html="source.admin ? svgIcons['user_check'] : svgIcons['users']"></div>
                                            <span class="sb-small" v-html="source.admin ? genericText.admin : genericText.member"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button v-if="addNewSource.typeSelected !== 'group'" class="cff-fb-source-btn cff-fb-fs sb-btn-blue"  @click.prevent.default="addSourcesOnConnect()">
                            <div class="cff-fb-icon-success"></div>
                            {{genericText.add}}
                        </button>
                        <div class="cff-groups-connect-actions" v-if="addNewSource.typeSelected === 'group'">
                            <div class="sb-alert">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.99935 0.666504C4.39935 0.666504 0.666016 4.39984 0.666016 8.99984C0.666016 13.5998 4.39935 17.3332 8.99935 17.3332C13.5993 17.3332 17.3327 13.5998 17.3327 8.99984C17.3327 4.39984 13.5993 0.666504 8.99935 0.666504ZM9.83268 13.1665H8.16602V11.4998H9.83268V13.1665ZM9.83268 9.83317H8.16602V4.83317H9.83268V9.83317Z" fill="#995C00"/>
                                </svg>
                                <span class="sb-caption" v-html="selectSourceScreen.modal.disclaimer"></span>
                            </div>
                            <button class="cff-fb-source-btn cff-fb-source-btn-next cff-fb-fs sb-btn-blue"  @click.prevent.default="$parent.switchScreen('sourcePopupScreen','step_4')" :data-active="typeof window.cffSelected !== 'undefined' && window.cffSelected.length ? 'true' : 'false'">
                                <span>{{genericText.next}}</span>
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.3332 0.00683594L0.158203 1.18184L3.97487 5.00684L0.158203 8.83184L1.3332 10.0068L6.3332 5.00684L1.3332 0.00683594Z" fill="white"></path></svg>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Step Three Connect Manually-->
            <div class="cff-fb-source-step3 cff-fb-fs" v-if="viewsActive.sourcePopupScreen == 'step_3'">
                <div class="cff-fb-source-top cff-fb-fs">
                    <div class=" cff-fb-fs">
                        <div class="cff-fb-src-back-top" @click.prevent.default="$parent.switchScreen('sourcePopupScreen','step_1')">
                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.27398 1.44L4.33398 0.5L0.333984 4.5L4.33398 8.5L5.27398 7.56L2.22065 4.5L5.27398 1.44Z" fill="#434960"/>
                            </svg>
                            {{selectSourceScreen.modal.addNew}}
                        </div>
                    </div>
                    <h3>{{selectSourceScreen.modal.addManually}}</h3>
                    <div class="cff-fb-fs">
                        <div class="cff-fb-source-inp-label cff-fb-fs"><span class="sb-caption sb-caption-lighter">{{selectSourceScreen.modal.sourceType}}</span></div>
                        <div class="cff-fb-source-mnl-type cff-fb-fs">
                            <div class="cff-fb-stp1-elm-act cff-fb-stp-src-ctn">
                                <div class="cff-fb-stp-src-type" :data-active="addNewSource.typeSelected == 'page'" @click.prevent.default="addNewSource.typeSelected = 'page'">
                                    <div class="cff-fb-chbx-round"></div><span class="sb-small-p sb-dark-text">{{selectSourceScreen.page}}</span>
                                </div>
                                <div class="cff-fb-stp-src-type" :data-active="addNewSource.typeSelected == 'group'" @click.prevent.default="addNewSource.typeSelected = 'group'">
                                    <div class="cff-fb-chbx-round"></div><span class="sb-small-p sb-dark-text">{{selectSourceScreen.group}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cff-fb-source-inputs cff-fb-fs">
                        <div class="cff-fb-source-inp-label cff-fb-fs"><span class="sb-caption sb-caption-lighter">{{selectSourceScreen.modal.pageOrGroupID}}</span></div>
                        <input type="text" class="cff-fb-source-inp cff-fb-fs" v-model="addNewSource.manualSourceID" :placeholder="selectSourceScreen.modal.enterID">
                        <div class="cff-fb-source-inp-label cff-fb-fs"><span class="sb-caption sb-caption-lighter">{{selectSourceScreen.modal.accessToken}}</span></div>
                        <input type="text" class="cff-fb-source-inp cff-fb-fs" v-model="addNewSource.manualSourceToken" :placeholder="selectSourceScreen.modal.enterToken">
                    </div>
                    <button class="cff-fb-source-btn cff-fb-fs sb-btn-blue sb-account-connection-button" @click.prevent.default="addSourceManually()" :data-active="checkManualEmpty() && loadingAjax == false ? 'true' : 'false'">
                        <div v-if="loadingAjax === false" class="cff-fb-icon-success"></div>
                        <span v-if="loadingAjax === false">{{genericText.add}}</span>
                        <span v-if="loadingAjax" class="spinner" style="display: inline-block;visibility: visible;margin: 1px;"></span>
                    </button>

                </div>
            </div>

            <!-- Step Four Group Instructions -->
            <div class="cff-fb-source-step4 cff-fb-fs" v-if="viewsActive.sourcePopupScreen == 'step_4'">
                <div class="cff-fb-source-top cff-fb-fs">
                    <div class="cff-fb-fs">
                        <div class="cff-fb-src-back-top" @click.prevent.default="$parent.switchScreen('sourcePopupScreen','step_2')">
                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.27398 1.44L4.33398 0.5L0.333984 4.5L4.33398 8.5L5.27398 7.56L2.22065 4.5L5.27398 1.44Z" fill="#434960"/>
                            </svg>
                            {{selectSourceScreen.modal.selectGroup}}
                        </div>
                    </div>
                    <h3>{{selectSourceScreen.modal.addApp}}</h3>
                    <div v-if="typeof window.cffSelected !== 'undefined' && window.cffSelected.indexOf(true) > -1" class="sb-directions-wrap">
                        <div class="cff-fb-stp1-elm-desc sb-caption sb-caption-lighter">{{selectSourceScreen.modal.addAppDetails}}</div>
                        <div class="sb-numbered-steps-wrap">
                            <div class="sb-single-step" v-for="(addAppStep, index) in selectSourceScreen.modal.addAppSteps">
                                <div class="sb-step-number"><span class="sb-caption sb-bold" v-html="index + 1"></span></div>
                                <p class="sb-step-text sb-small-p"><span v-html="addAppStep"></span><span v-if="index === 0"><a id="cff-group-admin-link" :href="'https://www.facebook.com/groups/'+selectedSourcesToConnect[0]+'/apps/store'" target="_blank" rel="noopener noreferrer" v-html="$parent.genericText.clickingHere"></a></span></p>
                            </div>
                        </div>
                    </div>

                    <div v-if="typeof window.cffSelected !== 'undefined' && window.cffSelected.indexOf(false) > -1 " class="sb-directions-wrap">
                        <h4 v-if="typeof window.cffSelected !== 'undefined' && window.cffSelected.indexOf(false) > -1 && window.cffSelected.indexOf(true) > -1" v-html="selectSourceScreen.modal.notAdmin"></h4>
                        <div class="sb-directions-p sb-caption sb-caption-lighter" v-html="selectSourceScreen.modal.appMemberInstructions"></div>
                    </div>

                </div>
                <div class="sb-two-buttons-wrap">
                    <button class="cff-fb-source-btn cff-fb-fs sb-btn-blue" @click.prevent.default="addSourcesOnConnect()">
                        <div class="cff-fb-icon-success"></div>
                        {{genericText.done}}
                    </button>
                    <a href="https://smashballoon.com/display-facebook-group-feed/" target="_blank" data-icon="left" class="cff-fb-source-btn cff-fb-fs sb-btn-grey">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="sb-question-circle"><path d="M8.16797 14.0001H9.83464V12.3334H8.16797V14.0001ZM9.0013 0.666748C4.4013 0.666748 0.667969 4.40008 0.667969 9.00008C0.667969 13.6001 4.4013 17.3334 9.0013 17.3334C13.6013 17.3334 17.3346 13.6001 17.3346 9.00008C17.3346 4.40008 13.6013 0.666748 9.0013 0.666748ZM9.0013 15.6667C5.3263 15.6667 2.33464 12.6751 2.33464 9.00008C2.33464 5.32508 5.3263 2.33341 9.0013 2.33341C12.6763 2.33341 15.668 5.32508 15.668 9.00008C15.668 12.6751 12.6763 15.6667 9.0013 15.6667ZM9.0013 4.00008C7.15964 4.00008 5.66797 5.49175 5.66797 7.33342H7.33464C7.33464 6.41675 8.08464 5.66675 9.0013 5.66675C9.91797 5.66675 10.668 6.41675 10.668 7.33342C10.668 9.00008 8.16797 8.79175 8.16797 11.5001H9.83464C9.83464 9.62508 12.3346 9.41675 12.3346 7.33342C12.3346 5.49175 10.843 4.00008 9.0013 4.00008Z" fill="#141B38"></path></svg>
                        <span>{{genericText.help}}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</script>