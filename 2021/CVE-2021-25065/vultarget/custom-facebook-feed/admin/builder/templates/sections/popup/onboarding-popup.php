<div class="sb-onboarding-overlay sb-fs-boss cff-fb-center-boss" v-if="viewsActive.onboardingPopup && viewsActive.pageScreen === 'welcome'">
</div>
<div v-for="tooltip in allFeedsScreen.onboarding.tooltips"
     v-if="viewsActive.onboardingPopup && viewsActive.pageScreen === 'welcome'"
     v-bind:id="'sb-onboarding-tooltip-' + allFeedsScreen.onboarding.type + '-' + tooltip.step" class="cff-fb-popup-inside sb-onboarding-tooltip cff-fb-source-top" v-bind:class="'sb-onboarding-tooltip-' + tooltip.step" v-bind:data-step="tooltip.step">
    <div class="cff-fb-popup-cls" @click.prevent.default="onboardingClose()">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
        </svg>
    </div>
    <div class="sb-onboarding-top-row">
        <p class="sb-standard-p sb-bold">{{tooltip.heading}}</p>
        <div class="wp-clearfix"></div>
        <p class="sb-onboarding-step">{{tooltip.p}}</p>
    </div>
    <div class="sb-onboarding-bottom-row">
        <div class="sb-step-counter-wrap">
            <span>{{tooltip.step}}/{{allFeedsScreen.onboarding.tooltips.length}}</span>
        </div>
        <div class="sb-previous-next-wrap">
            <div class="cff-fb-wrapper">
                <div class="cff-onboarding-previous cff-fb-hd-btn cff-btn-grey sb-button-small sb-button-left-icon" v-bind:data-active="tooltip.step === 1 ? 'false' : 'true'" @click.prevent.default="onboardingPrev">
                    <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.27203 0.94L4.33203 0L0.332031 4L4.33203 8L5.27203 7.06L2.2187 4L5.27203 0.94Z" fill="#141B38"/>
                    </svg>{{genericText.previous}}
                </div>
                <div v-if="allFeedsScreen.onboarding.tooltips.length > tooltip.step" class="cff-onboarding-next cff-fb-hd-btn cff-btn-grey sb-button-small sb-button-right-icon" @click.prevent.default="onboardingNext">{{genericText.next}}
                    <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.66656 0L0.726562 0.94L3.7799 4L0.726562 7.06L1.66656 8L5.66656 4L1.66656 0Z" fill="#141B38"/>
                    </svg>
                </div>
                <div v-if="allFeedsScreen.onboarding.tooltips.length === tooltip.step" class="cff-onboarding-finish sb-button-small cff-btn-grey cff-fb-hd-btn" @click.prevent.default="onboardingClose">{{genericText.finish}}</div>
            </div>
        </div>
    </div>
    <div class="sb-pointer" v-bind:class="{ 'sb-bottom-pointer' : typeof tooltip.pointer === 'undefined' || tooltip.pointer === 'bottom' }">
        <svg v-if="typeof tooltip.pointer !== 'undefined' && tooltip.pointer === 'top'" width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.58578 1.41421C9.36683 0.633166 10.6332 0.633165 11.4142 1.41421L20 10H0L8.58578 1.41421Z" fill="white"/>
        </svg>
        <svg v-else width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.4142 8.58579C10.6332 9.36683 9.36684 9.36683 8.58579 8.58579L0 0L20 0L11.4142 8.58579Z" fill="white"/>
        </svg>
    </div>
</div>