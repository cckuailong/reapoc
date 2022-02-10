<div v-if="selected === 'app-1'">
    <div class="sb-tab-box sb-license-box clearfix">
        <div class="tab-label">
            <h3>{{generalTab.licenseBox.title}}</h3>
            <p>{{generalTab.licenseBox.description}}</p>
        </div>

        <div class="cff-tab-form-field d-flex">
            <div v-if="licenseType === 'free' && licenseStatus == 'valid'" class="cff-tab-field-inner-wrap cff-license" :class="['license-' + licenseStatus, 'license-type-' + licenseType, {'form-error': hasError, 'dev-site-license-field': isDevSite}]">
                <div class="upgrade-info">
                    <span v-html="generalTab.licenseBox.upgradeText1"></span>
                    <span v-html="generalTab.licenseBox.upgradeText2"></span>
                </div>
                <span v-if="!isDevSite" class="license-status" v-html="generalTab.licenseBox.freeText"></span>
                <div v-if="isDevSite">
                    <a :href="upgradeUrl" target="_blank" class="cff-btn cff-btn-blue cff-upgrade-license-btn">
                        <span v-html="svgIcons.rocket"></span>
                        {{genericText.upgradeToPro}}
                    </a>
                </div>
                <div v-else class="d-flex">
                    <div class="field-left-content">
                        <div class="sb-form-field">
                            <input type="password" name="license-key" id="license-key" class="cff-form-field" :placeholder="generalTab.licenseBox.inactiveFieldPlaceholder" v-model="licenseKey">
                        </div>
                        <div class="form-info d-flex justify-between">

                            <span class="cff-manage-license">
                                <a :href="links.manageLicense">{{generalTab.licenseBox.manageLicense}}</a>
                                <a :href="links.manageLicense" @click.prevent.default="deactivateLicense">{{generalTab.licenseBox.deactivate}}</a>
                            </span>
                            <span>
                                <span class="test-connection" @click="testConnection()" v-if="testConnectionStatus === null">
                                    {{generalTab.licenseBox.test}}
                                    <span v-html="testConnectionIcon()" :class="testConnectionStatus">
                                    </span>
                                </span>
                                <span v-html="testConnectionIcon()" class="test-connection"  :class="testConnectionStatus" v-if="testConnectionStatus !== null">
                                </span>
                                <span class="cff-upgrade">
                                    <a :href="upgradeUrl" target="_blank">{{generalTab.licenseBox.upgrade}}</a>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="field-right-content">
                        <button type="button" class="cff-btn" v-on:click="licenseActiveAction" :class="{loading: loading, 'sb-btn-blue': licenseType === 'free'}">
                            <span v-if="loading && pressedBtnName === 'cff'" v-html="loaderSVG"></span>
                            <span class="cff-license-action-text" v-if="licenseType === 'pro'">{{generalTab.licenseBox.deactivate}}</span>
                            <span class="cff-license-action-text" v-if="licenseType === 'free'">{{generalTab.licenseBox.installPro}}</span>
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="cff-tab-field-inner-wrap cff-license" :class="['license-' + licenseStatus, 'license-type-' + licenseType, {'form-error': hasError, 'dev-site-license-field': isDevSite}]">
                <div class="upgrade-info">
                    <span v-html="generalTab.licenseBox.upgradeText1"></span>
                    <span v-html="generalTab.licenseBox.upgradeText2"></span>
                </div>
                <span v-if="!isDevSite" class="license-status" v-html="generalTab.licenseBox.freeText"></span>
                <div class="mb-6" v-if="licenseErrorMsg !== null">
                    <span v-html="licenseErrorMsg" class="cff-error-text"></span>
                </div>
                <div v-if="isDevSite">
                    <a :href="upgradeUrl" target="_blank" class="cff-btn cff-btn-blue cff-upgrade-license-btn">
                        <span v-html="svgIcons.rocket"></span>
                        {{genericText.upgradeToPro}}
                    </a>
                </div>
                <div v-else class="d-flex">
                    <div class="field-left-content">
                        <div class="sb-form-field">
                            <input type="password" name="license-key" id="license-key" class="cff-form-field" :placeholder="generalTab.licenseBox.inactiveFieldPlaceholder" v-model="licenseKey">
                        </div>
                        <div class="form-info d-flex justify-between">

                            <span class="cff-manage-license">
                            </span>
                            <span>
                                <span class="test-connection" @click="testConnection()" v-if="testConnectionStatus === null">
                                    {{generalTab.licenseBox.test}}
                                    <span v-html="testConnectionIcon()" :class="testConnectionStatus">
                                    </span>
                                </span>
                                <span v-html="testConnectionIcon()" class="test-connection"  :class="testConnectionStatus" v-if="testConnectionStatus !== null">
                                </span>
                                <span class="cff-upgrade">
                                    <a :href="upgradeUrl" target="_blank">{{generalTab.licenseBox.upgrade}}</a>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="field-right-content">
                        <button type="button" class="cff-btn sb-btn-blue" v-on:click="activateLicense">
                            <span v-if="loading && pressedBtnName === 'cff'" class="cff-loader-svg" v-html="loaderSVG"></span>
                            {{generalTab.licenseBox.activate}}
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="licenseType === 'pro' && licenseStatus === 'valid'" class="cff-tab-field-inner-wrap cff-license cff-license" :class="['license-' + licenseStatus, 'license-type-' + licenseType, {'form-error': hasError}]">
                <span class="license-status" v-html="generalTab.licenseBox.activeText"></span>
                <div class="d-flex">
                    <div class="field-left-content">
                        <div class="sb-form-field">
                            <input type="password" name="license-key" id="license-key" class="cff-form-field" value="******************************" v-model="licenseKey">
                            <span class="field-icon fa fa-check-circle"></span>
                        </div>
                        <div class="form-info d-flex justify-between">
                            <span class="cff-manage-license">
                                <a :href="links.manageLicense" target="_blank">{{generalTab.licenseBox.manageLicense}}</a>
                            </span>
                            <span>
                                <span class="test-connection" @click="testConnection()" v-if="testConnectionStatus === null">
                                    {{generalTab.licenseBox.test}}
                                    <span v-html="testConnectionIcon()" :class="testConnectionStatus">
                                    </span>
                                </span>
                                <span v-html="testConnectionIcon()" class="test-connection"  :class="testConnectionStatus" v-if="testConnectionStatus !== null">
                                </span>

                                <span class="cff-upgrade">
                                    <a :href="upgradeUrl" target="_blank">{{generalTab.licenseBox.upgrade}}</a>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="field-right-content">
                        <button type="button" class="cff-btn" v-on:click="licenseActiveAction" :class="{loading: loading}">
                            <span v-if="loading && pressedBtnName === 'cff'" v-html="loaderSVG"></span>
                            {{generalTab.licenseBox.deactivate}}
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="licenseType === 'pro' && (
                licenseStatus === 'inactive' ||
                licenseStatus === 'site_inactive' ||
                licenseStatus === 'invalid' ||
                licenseStatus === 'deactivated' ||
                licenseStatus === 'no_activations_left' ||
                licenseStatus === 'expired')"
                class="cff-tab-field-inner-wrap cff-license"
                :class="['license-' + licenseStatus, 'license-type-' + licenseType, {'form-error': hasError}]"
                >
                <span class="license-status" v-html="generalTab.licenseBox.inactiveText"></span>
                <div class="d-flex">
                    <div class="field-left-content">
                        <div class="sb-form-field">
                            <input type="password" name="license-key" id="license-key" class="cff-form-field" :placeholder="generalTab.licenseBox.inactiveFieldPlaceholder" v-model="licenseKey">
                            <span class="field-icon field-icon-error fa fa-times-circle" v-if="licenseErrorMsg !== null"></span>
                        </div>
                        <div class="mb-6" v-if="licenseErrorMsg !== null">
                            <span v-html="licenseErrorMsg" class="cff-error-text"></span>
                        </div>
                        <div class="form-info d-flex justify-between">
                            <span></span>
                            <span>
                                <span class="test-connection" @click="testConnection()" v-if="testConnectionStatus === null">
                                    {{generalTab.licenseBox.test}}
                                    <span v-html="testConnectionIcon()" :class="testConnectionStatus">
                                    </span>
                                </span>
                                <span v-html="testConnectionIcon()" class="test-connection"  :class="testConnectionStatus" v-if="testConnectionStatus !== null">
                                </span>
                                <span class="cff-upgrade">
                                    <a :href="upgradeUrl" target="_blank">{{generalTab.licenseBox.upgrade}}</a>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="field-right-content">
                        <button type="button" class="cff-btn sb-btn-blue" v-on:click="activateLicense">
                            <span v-if="loading && pressedBtnName === 'cff'" v-html="loaderSVG"></span>
                            {{generalTab.licenseBox.activate}}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Extension license -->
            <div v-if="licenseType !== 'free'" class="cff-tab-field-inner-wrap" v-for="extension in extensionsLicense" :class="[extension.name + '-license', 'license-' + extension.licenseStatus]">
                <span class="license-status" v-html="extension.statusText"></span>

                <!-- If extensions license is valid -->
                <div class="d-flex" v-if="extension.licenseStatus !== false && extension.licenseStatus == 'valid'">
                    <div class="field-left-content">
                        <div class="sb-form-field">
                            <input type="password" class="cff-form-field" value="show pass" v-model="extension.licenseKey">
                            <span v-if="extension.licenseStatus == 'valid'" class="field-icon fa fa-check-circle"></span>
                        </div>
                        <div class="form-info d-flex justify-between">
                            <span class="cff-manage-license">
                                <a :href="links.manageLicense" target="_blank">{{generalTab.licenseBox.manageLicense}}</a>
                            </span>
                            <span>
                                <span class="cff-upgrade">
                                    <a :href="extension.upgradeUrl" target="_blank">{{generalTab.licenseBox.upgrade}}</a>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="field-right-content">
                        <button type="button" class="cff-btn" v-on:click="licenseActiveAction(extension)" :class="{loading: loading}">
                            <span v-if="loading && pressedBtnName === extension.name" v-html="loaderSVG"></span>
                            <span class="cff-license-action-text" v-if="licenseType === 'pro'">{{generalTab.licenseBox.deactivate}}</span>
                            <span class="cff-license-action-text" v-if="licenseType === 'free'">{{generalTab.licenseBox.installPro}}</span>
                        </button>
                    </div>
                </div>

                <!-- If extensions license is not valid -->
                <div class="d-flex" v-else>
                    <div class="field-left-content">
                        <div class="sb-form-field" :class="{'sb-field-error': extensionFieldHasError && pressedBtnName === extension.name}">
                            <input type="password" class="cff-form-field" :placeholder="generalTab.licenseBox.inactiveFieldPlaceholder" v-model="extensionsLicenseKey[extension.name]">
                        </div>
                        <div class="form-info d-flex justify-between">
                            <span class="cff-manage-license">
                                <a :href="links.manageLicense" target="_blank">{{generalTab.licenseBox.manageLicense}}</a>
                            </span>
                            <span>
                                <span class="cff-upgrade">
                                    <a :href="extension.upgradeUrl" target="_blank">{{generalTab.licenseBox.upgrade}}</a>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="field-right-content">
                        <button @click="activateExtensionLicense(extension)" type="button" class="cff-btn cff-btn-blue" :class="{loading: loading}">
                            <span v-if="loading && pressedBtnName === extension.name" v-html="loaderSVG"></span>
                            {{generalTab.licenseBox.activate}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sb-tab-box sb-manage-sources-box clearfix">
        <div class="tab-label">
            <h3>{{generalTab.manageSource.title}}</h3>
        </div>
        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <span class="help-text">
                    {{generalTab.manageSource.description}}
                </span>
                <div class="sb-sources-list">
                    <div class="sb-srcs-item sb-srcs-new" @click.prevent.default="activateView('sourcePopup','creationRedirect')">
                        <span class="add-new-icon">
                            <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.25 8H8.25V14H6.25V8H0.25V6H6.25V0H8.25V6H14.25V8Z" fill="#0068A0"/>
                            </svg>
                        </span>
                        <span>{{genericText.addSource}}</span>
                    </div>
                    <div class="sb-srcs-item" v-for="(source, sourceIndex) in sourcesList" :class="{expanded: expandedFeedID == sourceIndex + 1, 'sb-account-has-error' : source.error !== ''}">
                        <div class="cff-fb-srcs-item-ins">
                            <div class="sb-srcs-item-avatar">
                                <img :src="typeof source.avatar_url !== 'undefined' && source.account_type === 'group' ? source.avatar_url : 'https://graph.facebook.com/'+source.account_id+'/picture'">
                            </div>
                            <div class="sb-srcs-item-inf">
                                <div class="sb-srcs-item-name">{{source.username}}</div>
                                <div class="sb-srcs-item-used">
                                    <span v-html="printUsedInText(source.used_in)"></span>
                                    <div v-if="source.used_in > 0" class="sb-control-elem-tltp"><div class="sb-control-elem-tltp-icon" v-html="svgIcons['info']" @click.prevent.default="viewSourceInstances(source)"></div></div>
                                    <div v-if="source.error !== ''" class="sb-source-error-wrap">
                                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.50008 0.666664C3.28008 0.666664 0.666748 3.28 0.666748 6.5C0.666748 9.72 3.28008 12.3333 6.50008 12.3333C9.72008 12.3333 12.3334 9.72 12.3334 6.5C12.3334 3.28 9.72008 0.666664 6.50008 0.666664ZM7.08342 9.41667H5.91675V8.25H7.08342V9.41667ZM7.08342 7.08333H5.91675V3.58333H7.08342V7.08333Z" fill="#D72C2C"/>
                                        </svg>
                                        <span v-html="genericText.errorSource"></span><a href="#" @click.prevent.default="activateView('sourcePopup','creationRedirect')" v-html="genericText.reconnect"></a>
                                    </div>
									<div v-if="source.error === '' && typeof( source.needs_update ) !== 'undefined' && source.needs_update" class="sb-source-error-wrap">
										<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M6.50008 0.666664C3.28008 0.666664 0.666748 3.28 0.666748 6.5C0.666748 9.72 3.28008 12.3333 6.50008 12.3333C9.72008 12.3333 12.3334 9.72 12.3334 6.5C12.3334 3.28 9.72008 0.666664 6.50008 0.666664ZM7.08342 9.41667H5.91675V8.25H7.08342V9.41667ZM7.08342 7.08333H5.91675V3.58333H7.08342V7.08333Z" fill="#D72C2C"/>
										</svg>

										<span v-html="genericText.updateRequired"></span><a href="#" @click.prevent.default="activateView('sourcePopup','creationRedirect')" v-html="genericText.reconnect"></a>
									</div>
                                </div>
                            </div>
                            <div class="sb-srcs-item-actions">
                                <div class="sb-srcs-item-actions-btn sb-srcs-item-delete" @click.prevent.default="openDialogBox('deleteSource', source)" v-html="svgIcons['delete']"></div>
                                <div class="sb-srcs-item-actions-btn sb-srcs-item-cog" v-if="expandedFeedID != sourceIndex + 1" v-html="svgIcons['cog']" @click="displayFeedSettings(source, sourceIndex)"></div>
                                <div class="sb-srcs-item-actions-btn sb-srcs-item-angle-up" v-if="expandedFeedID == sourceIndex + 1" v-html="svgIcons['angleUp']" @click="hideFeedSettings()"></div>
                            </div>
                        </div>
                        <div class="cff-fb-srcs-info cff-fb-fs" v-if="expandedFeedID == sourceIndex + 1">
                            <div class="cff-fb-srcs-info-item">
                                <strong>{{genericText.id}}</strong>
                                <span>{{source.account_id}}</span>
                                <div class="cff-fb-srcs-info-icon" v-html="svgIcons['copy2']" @click.prevent.default="copyToClipBoard(source.account_id)"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="sb-tab-box sb-preserve-settings-box clearfix">
        <div class="tab-label">
            <h3>{{generalTab.preserveBox.title}}</h3>
        </div>

        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <label for="preserve-settings" class="cff-checkbox">
                    <input type="checkbox" name="preserve-settings" id="preserve-settings" v-model="model.general.preserveSettings">
                    <span class="toggle-track">
                        <div class="toggle-indicator"></div>
                    </span>
                </label>
                <span class="help-text">
                    {{generalTab.preserveBox.description}}
                </span>
            </div>
        </div>
    </div>

    <div class="sb-tab-box sb-import-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{generalTab.importBox.title}}</h3>
        </div>
        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <div class="d-flex mb-15">
                    <button type="button" class="cff-btn sb-btn-lg import-btn" id="import-btn" @click="importFile" :disabled="uploadStatus !== null">
                        <span class="icon" v-html="importBtnIcon()" :class="uploadStatus"></span>
                        {{generalTab.importBox.button}}
                    </button>
                    <div class="input-hidden">
                        <input id="import_file" type="file" value="import_file" ref="file" v-on:change="uploadFile">
                    </div>
                </div>
                <span class="help-text">
                    {{generalTab.importBox.description}}
                </span>
            </div>
        </div>
    </div>

    <div class="sb-tab-box sb-export-box clearfix">
        <div class="tab-label">
            <h3>{{generalTab.exportBox.title}}</h3>
        </div>
        <div class="cff-tab-form-field">
            <div class="sb-form-field">
                <div class="d-flex mb-15">
                    <select name="" id="cff-feeds-list" class="cff-select" v-model="exportFeed" ref="export_feed">
                        <option value="none" selected disabled>Select Feed</option>
                        <option v-for="feed in feeds" :value="feed.id">{{ feed.name }}</option>
                    </select>
                    <button type="button" class="cff-btn sb-btn-lg export-btn" @click="exportFeedSettings" :disabled="exportFeed === 'none'">
                        <span class="icon" v-html="exportSVG"></span>
                        {{generalTab.exportBox.button}}
                    </button>
                </div>
                <span class="help-text">
                    {{generalTab.exportBox.description}}
                </span>
            </div>
        </div>
    </div>
</div>
