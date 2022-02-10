<div v-if="selected === 'app-2'">
    <div class="sb-tab-box sb-localization-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>
                {{feedsTab.localizationBox.title}}
                <span class="sb-tooltip-info" id="cff-tooltip locale-tooltip" v-html="tooltipHelpSvg"  @mouseover.prevent.default="toggleElementTooltip(feedsTab.localizationBox.tooltip, 'show', 'left')" @mouseleave.prevent.default="toggleElementTooltip('', 'hide')"></span>
            </h3>
        </div>
        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <div class="d-flex">
                    <select name="" id="cff-locales" class="cff-select size-md" v-model="model.feeds.selectedLocale">
                        <option v-for="(name, key) in locales" :value="key">{{name}}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-timezone-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{feedsTab.timezoneBox.title}}</h3>
        </div>
        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <div class="d-flex">
                    <select name="" id="cff-timezones" class="cff-select size-md" v-model="model.feeds.selectedTimezone">
                        <option v-for="(name, key) in timezones" :value="key">{{name}}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-caching-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{feedsTab.cachingBox.title}}</h3>
        </div>
        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <div class="mb-10 caching-form-fields-group">
                    <select id="cff-caching-options" class="cff-select size-sm mr-3" v-model="model.feeds.cachingType" v-if="licenseType == 'pro'">
                        <option value="background">{{feedsTab.cachingBox.inTheBackground}}</option>
                    </select>
                    <select id="cff-caching-intervals" class="cff-select size-sm mr-3" v-model="model.feeds.cronInterval" v-if="licenseType == 'pro'">
                        <option v-for="(name, key) in feedsTab.cachingBox.inTheBackgroundOptions" :value="key">{{name}}</option>
                    </select>
                    <select id="cff-caching-cron-time" class="cff-select size-xs mr-3" v-model="model.feeds.cronTime" v-if="model.feeds.cachingType === 'background' && model.feeds.cronInterval !== '30mins' && model.feeds.cronInterval !== '1hour' && licenseType == 'pro'" :disabled="licenseType == 'free'">
                        <option v-for="index in 12" :value="index">{{index}}:00</option>
                    </select>
                    <select id="cff-caching-cron-am-pm" class="cff-select size-xs mr-3" v-model="model.feeds.cronAmPm" v-if="model.feeds.cachingType === 'background' && model.feeds.cronInterval !== '30mins' && model.feeds.cronInterval !== '1hour' && licenseType == 'pro'" :disabled="licenseType == 'free'">
                        <option value="am">{{feedsTab.cachingBox.am}}</option>
                        <option value="pm">{{feedsTab.cachingBox.pm}}</option>
                    </select>
                    <input type="number" min="0" max="9999" class="cff-input-sm" v-model="model.feeds.cacheTime" v-if="licenseType == 'free'">
                    <select id="cff_cache_time_unit" class="cff-select size-sm mr-3" v-model="model.feeds.cacheTimeUnit" v-if="licenseType == 'free'">
                        <option v-for="(name, key) in feedsTab.cachingBox.timeUnits" :value="key">{{name}}</option>
                    </select>
                    <button type="button" class="cff-btn sb-btn-lg cff-caching-btn" @click="clearCache" >
                        <span v-html="clearCacheIcon()" :class="clearCacheStatus"></span>
                        {{feedsTab.cachingBox.clearCache}}
                    </button>
                </div>
                <div class="help-text help-text-green" v-html="cronNextCheck" v-if="licenseType == 'pro'"></div>
            </div>
        </div>
        <div v-if="licenseType == 'free'" class="cff-caching-pro-cta clearfix">
            <span>
                <a :href="links.proCachingLink" target="_blank">{{feedsTab.cachingBox.promoText}}
                    <span class="cff-upgrade-cta-icon">
                        <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.8332 0L0.658203 1.175L4.47487 5L0.658203 8.825L1.8332 10L6.8332 5L1.8332 0Z" fill="#0068A0"/>
                        </svg>
                    </span>
                </a>
            </span>
        </div>
    </div>
    <div class="sb-tab-box sb-gdpr-box clearfix">
        <div class="tab-label">
            <h3>
                {{feedsTab.gdprBox.title}}
                <span class="sb-tooltip-info gdpr-tooltip" id="cff-tooltip" v-html="tooltipHelpSvg"   @mouseover.prevent.default="toggleElementTooltip(feedsTab.gdprBox.tooltip, 'show', 'left')" @mouseleave.prevent.default="toggleElementTooltip('', 'hide')"></span>
            </h3>
        </div>
        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <div class="d-flex mb-10">
                    <select id="cff-gdpr-options" class="cff-select size-md" v-model="model.feeds.gdpr" @change="gdprOptions">
                        <option value="auto">{{feedsTab.gdprBox.automatic}}</option>
                        <option value="yes">{{feedsTab.gdprBox.yes}}</option>
                        <option value="no">{{feedsTab.gdprBox.no}}</option>
                    </select>
                </div>
                <div class="help-text" v-if="model.feeds.gdpr == 'auto'" :class="['gdpr-help-text-' + model.feeds.gdpr, {'sb-gdpr-active': model.feeds.gdprPlugin}]">
                    <span class="gdpr-active-icon" v-if="model.feeds.gdprPlugin">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.0003 1.66667C5.41699 1.66667 1.66699 5.41667 1.66699 10C1.66699 14.5833 5.41699 18.3333 10.0003 18.3333C14.5837 18.3333 18.3337 14.5833 18.3337 10C18.3337 5.41667 14.5837 1.66667 10.0003 1.66667ZM8.33366 14.1667L4.16699 10L5.34199 8.82501L8.33366 11.8083L14.6587 5.48334L15.8337 6.66667L8.33366 14.1667Z" fill="#59AB46"/>
                        </svg>
                    </span>
                    <div v-html="feedsTab.gdprBox.infoAuto" :class="{'sb-text-bold': model.feeds.gdprPlugin}"></div>
                    <span v-html="feedsTab.gdprBox.someFacebook" v-if="model.feeds.gdprPlugin"></span>
                    <span v-html="feedsTab.gdprBox.whatLimited" @click="gdprLimited" class="sb-text-bold sb-gdpr-bold" v-if="model.feeds.gdprPlugin"></span>
                </div>
                <div class="help-text" v-if="model.feeds.gdpr == 'yes'" :class="'gdpr-help-text-' + model.feeds.gdpr">
                    <span v-html="feedsTab.gdprBox.infoYes"></span>
                    <span v-html="feedsTab.gdprBox.whatLimited" @click="gdprLimited" class="sb-text-bold sb-gdpr-bold"></span>
                </div>
                <div class="help-text" v-html="feedsTab.gdprBox.infoNo" v-if="model.feeds.gdpr == 'no'" :class="'gdpr-help-text-' + model.feeds.gdpr"></div>
                <div class="sb-gdpr-info-tooltip" v-if="gdprInfoTooltip !== null">
                    <span class="sb-gdpr-info-headline">{{feedsTab.gdprBox.gdprTooltipFeatureInfo.headline}}</span>
                    <ul class="sb-gdpr-info-list">
                        <li v-for="feature in feedsTab.gdprBox.gdprTooltipFeatureInfo.features">{{feature}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-custom-css-box sb-reset-box-style clearfix" v-if="model.feeds.customCSS !== ''">
        <div class="tab-label">
            <h3>{{feedsTab.customCSSBox.title}}</h3>
        </div>
        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <div class="sb-custom-cssjs-notice" v-html="feedsTab.customCSSBox.message"></div>
                <div class="sb-disabled-custom-code"><textarea readonly v-html="model.feeds.customCSS"></textarea></div>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-custom-js-box clearfix" v-if="model.feeds.customJS !== ''">
        <div class="tab-label">
            <h3>{{feedsTab.customJSBox.title}}</h3>
        </div>
        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <div class="sb-custom-cssjs-notice" v-html="feedsTab.customJSBox.message"></div>
                <div class="sb-disabled-custom-code"><textarea readonly v-html="model.feeds.customJS"></textarea></div>
            </div>
        </div>
    </div>
</div>
<!-- todo: this is just demo content and will be replaced once I work on this -->