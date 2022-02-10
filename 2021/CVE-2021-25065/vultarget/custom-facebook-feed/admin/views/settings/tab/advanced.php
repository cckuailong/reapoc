<div v-if="selected === 'app-4'">
    <div class="sb-tab-box sb-optimize-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{advancedTab.optimizeBox.title}}</h3>
        </div>

        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <label for="disable-resize" class="cff-checkbox">
                    <input type="checkbox" name="disable-resize" id="disable-resize" v-model="model.advanced.cff_disable_resize" :disabled="licenseType == 'free'">
                    <span class="toggle-track">
                        <div class="toggle-indicator"></div>
                    </span>
                    <button type="button" class="cff-btn ml-10 optimize-image-btn" @click="clearImageResizeCache()" :disabled="licenseType == 'free'">
                        <span v-html="clearImageResizeCacheIcon()" :class="optimizeCacheStatus" v-if="optimizeCacheStatus !== null"></span>
                        {{advancedTab.optimizeBox.reset}}
                    </button>
                </label>
                <span class="help-text">
                    {{advancedTab.optimizeBox.helpText}}
                </span>
            </div>
        </div>
        <div v-if="licenseType == 'free'" class="cff-caching-pro-cta clearfix">
            <span>
                <a :href="links.optimizeImagesLink" target="_blank">{{advancedTab.optimizeBox.promoText}}
                    <span class="cff-upgrade-cta-icon">
                        <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.8332 0L0.658203 1.175L4.47487 5L0.658203 8.825L1.8332 10L6.8332 5L1.8332 0Z" fill="#0068A0"/>
                        </svg>
                    </span>
                </a>
            </span>
        </div>
    </div>
    <div class="sb-tab-box sb-usage-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{advancedTab.usageBox.title}}</h3>
        </div>

        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <label for="usage-tracking" class="cff-checkbox">
                    <input type="checkbox" name="usage-tracking" id="usage-tracking" v-model="model.advanced.usage_tracking">
                    <span class="toggle-track">
                        <div class="toggle-indicator"></div>
                    </span>
                </label>
                <span class="help-text" v-html="advancedTab.usageBox.helpText"></span>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-ajax-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{advancedTab.ajaxBox.title}}</h3>
        </div>

        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <label for="ajax-box-settings" class="cff-checkbox">
                    <input type="checkbox" id="ajax-box-settings" v-model="model.advanced.cff_ajax">
                    <span class="toggle-track">
                        <div class="toggle-indicator"></div>
                    </span>
                </label>
                <span class="help-text">
                    {{advancedTab.ajaxBox.helpText}}
                </span>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-show-credit-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{advancedTab.showCreditBox.title}}</h3>
        </div>

        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <label for="show-credit" class="cff-checkbox">
                    <input type="checkbox" name="show-credit" id="show-credit" v-model="model.advanced.cff_show_credit">
                    <span class="toggle-track">
                        <div class="toggle-indicator"></div>
                    </span>
                </label>
                <span class="help-text">
                    {{advancedTab.showCreditBox.helpText}}
                </span>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-fix-text-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{advancedTab.fixTextBox.title}}</h3>
        </div>

        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <label for="fix-text-shortening" class="cff-checkbox">
                    <input type="checkbox" name="fix-text-shortening" id="fix-text-shortening" v-model="model.advanced.cff_format_issue">
                    <span class="toggle-track">
                        <div class="toggle-indicator"></div>
                    </span>
                </label>
                <span class="help-text">
                    {{advancedTab.fixTextBox.helpText}}
                </span>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-admin-error-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{advancedTab.adminErrorBox.title}}</h3>
        </div>

        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <label for="disable-admin-error" class="cff-checkbox">
                    <input type="checkbox" name="disable-admin-error" id="disable-admin-error" v-model="model.advanced.disable_admin_notice">
                    <span class="toggle-track">
                        <div class="toggle-indicator"></div>
                    </span>
                </label>
                <span class="help-text">
                    {{advancedTab.adminErrorBox.helpText}}
                </span>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-feed-issue-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{advancedTab.feedIssueBox.title}}</h3>
        </div>

        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <label for="enable-email-report" class="cff-checkbox">
                    <input type="checkbox" name="enable-email-report" id="enable-email-report" v-model="model.advanced.enable_email_report">
                    <span class="toggle-track">
                        <div class="toggle-indicator"></div>
                    </span>
                </label>
                <div class="items-center feed-issues-fields" v-if="model.advanced.enable_email_report">
                    <span class="help-text">
                        {{advancedTab.feedIssueBox.sendReport}}
                    </span>
                    <select id="cff-send-report" class="cff-select size-sm mr-3" v-model="model.advanced.email_notification">
                        <option v-for="(name, key) in advancedTab.feedIssueBox.weekDays" :value="name.val">{{name.label}}</option>
                    </select>
                    <span class="help-text">
                        {{advancedTab.feedIssueBox.to}}
                    </span>
                    <input type="text" name="report-emails" id="report-emails" class="cff-form-field" :placeholder="advancedTab.feedIssueBox.placeholder" v-model="model.advanced.email_notification_addresses">
                </div>
                <div class="help-text">
                    <span v-html="advancedTab.feedIssueBox.helpText"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-optimize-box sb-dpa-clear-box-style clearfix">
        <div class="tab-label">
            <h3>{{advancedTab.dpaClear.title}}</h3>
        </div>
        <div class="cff-tab-form-field">
            <button type="button" class="cff-btn" @click="dpaReset()">
                <span v-html="dpaResetStatusIcon()" :class="dpaResetStatus" v-if="dpaResetStatus !== null"></span>
                {{advancedTab.dpaClear.clear}}
            </button>
            <span class="help-text">
                {{advancedTab.dpaClear.helpText}}
            </span>
        </div>
    </div>
</div>
<!-- todo: this is just demo content and will be replaced once I work on this -->