<div class="cff-fb-extensions-pp-ctn sb-fs-boss cff-fb-center-boss" v-if="viewsActive.extensionsPopupElement != null && viewsActive.extensionsPopupElement != false">
	<div class="cff-fb-extensions-popup cff-fb-popup-inside" v-if="viewsActive.extensionsPopupElement != null && viewsActive.extensionsPopupElement != false" :data-getext-view="viewsActive.extensionsPopupElement">
        <div class="cff-fb-popup-cls" @click.prevent.default="activateView('extensionsPopupElement')">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
            </svg>
        </div>
        <div>
            <div class="cff-fb-extpp-top cff-fb-fs" :class="iscustomizerScreen && customizerScreens.popupBackButton.includes(viewsActive.extensionsPopupElement) ? 'cff-fb-extpp-top-fdtype' : ''">
                <div class="cff-fb-extpp-info">
                    <div v-if="iscustomizerScreen && customizerScreens.popupBackButton.includes(viewsActive.extensionsPopupElement)" class="cff-fb-slctf-back cff-fb-hd-btn cff-btn-grey" @click.prevent.default="activateView('feedtypesPopup')"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.3415 1.18184L5.1665 0.00683594L0.166504 5.00684L5.1665 10.0068L6.3415 8.83184L2.52484 5.00684L6.3415 1.18184Z" fill="#141B38"></path></svg> <span>Back</span></div>
                    <div class="cff-fb-extpp-head cff-fb-fs"><h2 v-html="extensionsPopup[viewsActive.extensionsPopupElement].heading"></h2></div>
                    <div class="cff-fb-extpp-desc cff-fb-fs sb-caption" v-html="extensionsPopup[viewsActive.extensionsPopupElement].description"></div>
                    <div class="cff-fb-fs">
                        <div class="cff-fb-extpp-lite-btn" href="" target="_blank" v-if="customizerScreens.popupBackButton.includes(viewsActive.extensionsPopupElement)">
                            <svg width="18" height="17" viewBox="0 0 18 17" fill="none"><path d="M16.843 8.15001L9.34297 0.650006C9.03048 0.340071 8.60809 0.166321 8.16797 0.166672H2.33464C1.89261 0.166672 1.46869 0.342267 1.15612 0.654828C0.843564 0.967388 0.667969 1.39131 0.667969 1.83334V7.66667C0.667795 7.8866 0.711149 8.10439 0.795533 8.30748C0.879917 8.51057 1.00366 8.69496 1.15964 8.85001L8.65964 16.35C8.97212 16.6599 9.39452 16.8337 9.83464 16.8333C10.276 16.8315 10.6985 16.6547 11.0096 16.3417L16.843 10.5083C17.156 10.1972 17.3328 9.77465 17.3346 9.33334C17.3348 9.11341 17.2915 8.89563 17.2071 8.69253C17.1227 8.48944 16.9989 8.30505 16.843 8.15001ZM9.83464 15.1667L2.33464 7.66667V1.83334H8.16797L15.668 9.33334L9.83464 15.1667ZM4.41797 2.66667C4.6652 2.66667 4.90687 2.73998 5.11243 2.87734C5.31799 3.01469 5.47821 3.20991 5.57282 3.43832C5.66743 3.66673 5.69218 3.91806 5.64395 4.16054C5.59572 4.40301 5.47667 4.62574 5.30185 4.80056C5.12704 4.97537 4.90431 5.09442 4.66183 5.14265C4.41936 5.19089 4.16802 5.16613 3.93962 5.07152C3.71121 4.97691 3.51598 4.8167 3.37863 4.61114C3.24128 4.40557 3.16797 4.1639 3.16797 3.91667C3.16797 3.58515 3.29967 3.26721 3.53409 3.03279C3.76851 2.79837 4.08645 2.66667 4.41797 2.66667Z" fill="#0068A0"/></svg>
                            {{genericText.liteFeedUsers}}
                        </div>
                    </div>
                </div>
                <div class="cff-fb-extpp-img" v-html="extensionsPopup[viewsActive.extensionsPopupElement].img">
                </div>
            </div>
            <div class="cff-fb-extpp-bottom cff-fb-fs">
                <div v-if="typeof extensionsPopup[viewsActive.extensionsPopupElement].bullets !== 'undefined'" class="cff-extension-bullets">
                    <h4>{{extensionsPopup[viewsActive.extensionsPopupElement].bullets.heading}}</h4>
                    <div class="cff-extension-bullet-list">
                        <div class="cff-extension-single-bullet" v-for="bullet in extensionsPopup[viewsActive.extensionsPopupElement].bullets.content">
                            <svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="4" height="4" fill="#0096CC"/>
                            </svg>
                            <span class="sb-small-p">{{bullet}}</span>
                        </div>
                    </div>
                </div>
                <div class="cff-fb-extpp-btns cff-fb-fs">
                    <a class="cff-fb-extpp-get-btn cff-btn-orange" :href="extensionsPopup[viewsActive.extensionsPopupElement].buyUrl" target="_blank" class="cff-fb-fs-link" v-html="viewsActive.extensionsPopupElement == 'socialwall' ? genericText.seeTheDemo : genericText.seeProDemo"></a>
                </div>
            </div>
        </div>
    </div>
</div>