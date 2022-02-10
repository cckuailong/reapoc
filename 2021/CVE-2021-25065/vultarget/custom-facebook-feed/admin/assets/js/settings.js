var cffSettings;

// Declaring as global variable for quick prototyping
var settings_data = {
    adminUrl: cff_settings.admin_url,
    nonce: cff_settings.nonce,
    ajaxHandler: cff_settings.ajax_handler,
    model: cff_settings.model,
    feeds: cff_settings.feeds,
    links: cff_settings.links,
    tooltipName: null,
    sourcesList : cff_settings.sources,
    isDevSite: cff_settings.isDevSite,
    dialogBoxPopupScreen   : cff_settings.dialogBoxPopupScreen,
    selectSourceScreen      : cff_settings.selectSourceScreen,

    socialWallActivated: cff_settings.socialWallActivated,
    socialWallLinks: cff_settings.socialWallLinks,
    stickyWidget: false,
    exportFeed: 'none',
    locales: cff_settings.locales,
    timezones: cff_settings.timezones,
    genericText: cff_settings.genericText,
    generalTab: cff_settings.generalTab,
    feedsTab: cff_settings.feedsTab,
    translationTab: cff_settings.translationTab,
    advancedTab: cff_settings.advancedTab,
    upgradeUrl: cff_settings.upgradeUrl,
    footerUpgradeUrl: cff_settings.footerUpgradeUrl,
    supportPageUrl: cff_settings.supportPageUrl,
    licenseKey: cff_settings.licenseKey,
    licenseType: cff_settings.licenseType,
    licenseStatus: cff_settings.licenseStatus,
    licenseErrorMsg: cff_settings.licenseErrorMsg,
    extensionsLicense: cff_settings.extensionsLicense,
    extensionsLicenseKey: cff_settings.extensionsLicenseKey,
    extensionFieldHasError: false,
    cronNextCheck: cff_settings.nextCheck,
    currentView: null,
    selected: null,
    current: 0,
    sections: ["General", "Feeds", "Translation", "Advanced"],
    indicator_width: 0,
    indicator_pos: 0,
    forwards: true,
    currentTab: null,
    import_file: null,
    gdprInfoTooltip: null,
    loaderSVG: cff_settings.loaderSVG,
    checkmarkSVG: cff_settings.checkmarkSVG,
    uploadSVG: cff_settings.uploadSVG,
    exportSVG: cff_settings.exportSVG,
    reloadSVG: cff_settings.reloadSVG,
    tooltipHelpSvg: cff_settings.tooltipHelpSvg,
    tooltip : {
        text : '',
        hover : false
    },

    cogSVG: cff_settings.cogSVG,
    deleteSVG: cff_settings.deleteSVG,
    svgIcons : cff_settings.svgIcons,

    testConnectionStatus: null,
    btnStatus: null,
    uploadStatus: null,
    clearCacheStatus: null,
    optimizeCacheStatus: null,
    dpaResetStatus: null,
    pressedBtnName: null,
    loading: false,
    hasError: cff_settings.hasError,
    dialogBox : {
        active : false,
        type : null,
        heading : null,
        description : null
    },
    sourceToDelete : {},
    newManualSourcePopup : cff_settings.newManualSourcePopup,
    viewsActive : {
        sourcePopup : false,
        sourcePopupScreen : 'redirect_1',
        sourcePopupType : 'creation',
        instanceSourceActive : null,
    },
    //Add New Source
    newSourceData        : cff_settings.newSourceData ? cff_settings.newSourceData : null,
    sourceConnectionURLs : cff_settings.sourceConnectionURLs,
    returnedApiSourcesList : [],
    addNewSource : {
        typeSelected        : 'page',
        manualSourceID      : null,
        manualSourceToken   : null
    },
    selectedFeed : 'none',
    expandedFeedID : null,
    notificationElement : {
        type : 'success', // success, error, warning, message
        text : '',
        shown : null
    },
    selectedSourcesToConnect : [],

    //Loading Bar
    fullScreenLoader : false,
    appLoaded : false,
    previewLoaded : false,
    loadingBar : true,
    notificationElement : {
        type : 'success', // success, error, warning, message
        text : '',
        shown : null
    }
};

// The tab component
Vue.component("tab", {
    props: ["section", "index"],
    template: `
        <a class='tab' :id='section.toLowerCase().trim()' @click='emitWidth($el);changeComponent(index);activeTab(section)'>{{section}}</a>
    `,
    created: () => {
        let urlParams = new URLSearchParams(window.location.search);
        let view = urlParams.get('view');
        if ( view === null ) {
            view = 'general';
        }
        settings_data.currentView = view;
        settings_data.currentTab = settings_data.sections[0];
        settings_data.selected = "app-1";
    },
    methods: {
        emitWidth: function(el) {
            settings_data.indicator_width = jQuery(el).outerWidth();
            settings_data.indicator_pos = jQuery(el).position().left;
        },
        changeComponent: function(index) {
            var prev = settings_data.current;
            if (prev < index) {
                settings_data.forwards = false;
            } else if (prev > index) {
                settings_data.forwards = true;
            }
            settings_data.selected = "app-" + (index + 1);
            settings_data.current = index;

            // get the pro cta banner offset
            let ctaOffset = jQuery('.cff-settings-cta').offset();
            // position them to bottom so during change component they don't appear at top
            jQuery('.cff-settings-cta, .cff-save-button').css({"position": "absolute", "top": ctaOffset.top + 'px'})
            // remove the added styles shortly after to set it where it should be
            setTimeout(function() {
                jQuery('.cff-settings-cta, .cff-save-button').removeAttr('style')
            }, 400);
        },
        activeTab: function(section) {
            this.setView(section.toLowerCase().trim());
            settings_data.currentTab = section;
        },
        setView: function(section) {
            history.replaceState({}, null, settings_data.adminUrl + 'admin.php?page=cff-settings&view=' + section);
        }
    }
});

var cffSettings = new Vue({
    el: "#cff-settings",
    http: {
        emulateJSON: true,
        emulateHTTP: true
    },
    data: settings_data,
    created: function() {
        this.$nextTick(function() {
            let tabEl = document.querySelector('.tab');
            settings_data.indicator_width = tabEl.offsetWidth;
        });
        setTimeout(function(){
            settings_data.appLoaded = true;
        },350);
    },
    mounted: function(){
        var self = this;
        // set the current view page on page load
        let activeEl = document.querySelector('a.tab#' + settings_data.currentView);
        // we have to uppercase the first letter
        let currentView = settings_data.currentView.charAt(0).toUpperCase() + settings_data.currentView.slice(1);
        let viewIndex = settings_data.sections.indexOf(currentView) + 1;
        settings_data.indicator_width = activeEl.offsetWidth;
        settings_data.indicator_pos = activeEl.offsetLeft;
        settings_data.selected = "app-" + viewIndex;
        settings_data.current = viewIndex;
        settings_data.currentTab = currentView;

        setTimeout(function(){
            settings_data.appLoaded = true;
        },350);

        if(self.newManualSourcePopup != undefined && self.newManualSourcePopup == true){
            self.viewsActive.sourcePopupScreen = 'step_3';
            self.activateView('sourcePopup', 'creation');
        }

    },
    computed: {
        getStyle: function() {
            return {
                position: "absolute",
                bottom: "0px",
                left: settings_data.indicator_pos + "px",
                width: settings_data.indicator_width + "px",
                height: "2px"
            };
        },
        chooseDirection: function() {
            if (settings_data.forwards == true) {
                return "slide-fade";
            } else {
                return "slide-fade";
            }
        }
    },
    methods:  {
        activateLicense: function() {
            if (this.licenseType === 'free') {
                this.runOneClickUpgrade();
            } else {
                this.activateProLicense();
            }
        },
        activateProLicense: function() {
            this.hasError = false;
            this.loading = true;
            this.pressedBtnName = 'cff';

            let data = new FormData();
            data.append( 'action', 'cff_activate_license' );
            data.append( 'license_key', this.licenseKey );
            data.append( 'nonce', this.nonce );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if ( data.success == false ) {
                    this.licenseStatus = 'inactive';
                    this.hasError = true;
                    this.loading = false;
                    return;
                }
                if ( data.success == true ) {
                    let licenseData = data.data.licenseData;
                    this.licenseStatus = data.data.licenseStatus;
                    this.loading = false;
                    this.pressedBtnName = null;

                    if (
                        data.data.licenseStatus == 'inactive' ||
                        data.data.licenseStatus == 'invalid' ||
                        data.data.licenseStatus == 'expired'
                    ) {
                        this.hasError = true;
                        if( licenseData.error ) {
                            this.licenseErrorMsg = licenseData.errorMsg
                        }
                    }
                }
                return;
            });
        },
        runOneClickUpgrade: function() {
            this.hasError = false;
            this.loading = true;
            this.pressedBtnName = 'cff';

            let data = new FormData();
            data.append( 'action', 'cff_maybe_upgrade_redirect' );
            data.append( 'license_key', this.licenseKey );
            data.append( 'nonce', this.nonce );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if ( data.success === false ) {
                    this.licenseStatus = 'invalid';
                    this.hasError = true;
                    this.loading = false;
                    if( typeof data.data !== 'undefined' ) {
                        this.licenseErrorMsg = data.data.message
                    }
                    return;
                }
                if ( data.success === true ) {
                    window.location.href = data.data.url
                }
                return;
            });
        },
        deactivateLicense: function() {
            this.loading = true;
            this.pressedBtnName = 'cff';
            let data = new FormData();
            data.append( 'action', 'cff_deactivate_license' );
            data.append( 'nonce', this.nonce );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if ( data.success == true ) {
                    this.licenseStatus = data.data.licenseStatus ;
                    this.loading = false;
                    this.pressedBtnName = null;
                }
                return;
            });
        },

        /**
         * Activate Extensions License
         *
         * @since 4.0
         *
         * @param {object} extension
         */
        activateExtensionLicense: function( extension ) {
            let licenseKey = this.extensionsLicenseKey[extension.name];
            this.extensionFieldHasError = false;
            this.loading = true;
            this.pressedBtnName = extension.name;
            if ( ! licenseKey ) {
                this.loading = false;
                this.extensionFieldHasError = true;
                return;
            }
            let data = new FormData();
            data.append( 'action', 'cff_activate_extension_license' );
            data.append( 'license_key', licenseKey );
            data.append( 'extension_name', extension.name );
            data.append( 'extension_item_name', extension.itemName );
            data.append( 'nonce', this.nonce );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                this.loading = false;
                if ( data.success == true ) {
                    this.extensionFieldHasError = false;
                    this.pressedBtnName = null;
                    if ( data.data.licenseStatus == 'invalid' ) {
                        this.extensionFieldHasError = true;
                        this.notificationElement =  {
                            type : 'error',
                            text : this.genericText.invalidLicenseKey,
                            shown : "shown"
                        };
                    }
                    if ( data.data.licenseStatus == 'valid' ) {
                        this.notificationElement =  {
                            type : 'success',
                            text : this.genericText.licenseActivated,
                            shown : "shown"
                        };
                    }
                    extension.licenseStatus = data.data.licenseStatus;
                    extension.licenseKey = licenseKey;

                    setTimeout(function(){
                        this.notificationElement.shown =  "hidden";
                    }.bind(this), 3000);
                }
                return;
            });
        },

        licenseActiveAction: function(extension) {
            extension = typeof extension !== 'undefined' ? extension : false;
            if (this.licenseType === 'free') {
                this.runOneClickUpgrade();
            } else {
                if (typeof extension !== 'undefined') {
                    this.deactivateExtensionLicense(extension);
                } else {
                    this.deactivateLicense();
                }
            }

        },

        /**
         * Deactivate Extensions License
         *
         * @since 4.0
         *
         * @param {object} extension
         */
        deactivateExtensionLicense: function( extension ) {
            let licenseKey = this.extensionsLicenseKey[extension.name];
            this.extensionFieldHasError = false;
            this.loading = true;
            this.pressedBtnName = extension.name;
            let data = new FormData();
            data.append( 'action', 'cff_deactivate_extension_license' );
            data.append( 'extension_name', extension.name );
            data.append( 'extension_item_name', extension.itemName );
            data.append( 'nonce', this.nonce );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                this.loading = false;
                if ( data.success == true ) {
                    this.extensionFieldHasError = false;
                    this.pressedBtnName = null;
                    if ( data.data.licenseStatus == 'deactivated' ) {
                        this.notificationElement =  {
                            type : 'success',
                            text : this.genericText.licenseDeactivated,
                            shown : "shown"
                        };
                    }
                    extension.licenseStatus = data.data.licenseStatus;
                    extension.licenseKey = licenseKey;

                    setTimeout(function(){
                        this.notificationElement.shown =  "hidden";
                    }.bind(this), 3000);
                }
                return;
            });
        },
        testConnection: function() {
            this.testConnectionStatus = 'loading';
            let data = new FormData();
            data.append( 'action', 'cff_test_connection' );
            data.append( 'nonce', this.nonce );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if ( data.success == false ) {
                    this.testConnectionStatus = 'error';
                }
                if ( data.success == true ) {
                    this.testConnectionStatus = 'success';

                    setTimeout(function() {
                        this.testConnectionStatus = null;
                    }.bind(this), 3000);
                }
                return;
            });
        },
        testConnectionIcon: function() {
            if ( this.testConnectionStatus == 'loading' ) {
                return this.loaderSVG;
            } else if ( this.testConnectionStatus == 'success' ) {
                return '<i class="fa fa-check-circle"></i> ' + this.generalTab.licenseBox.connectionSuccessful;
            } else if ( this.testConnectionStatus == 'error' ) {
                return `<i class="fa fa-check-circle"></i> ${this.generalTab.licenseBox.connectionFailed} <a href="#">${this.generalTab.licenseBox.viewError}</a>`;
            }
        },
        importFile: function() {
            document.getElementById("import_file").click();
        },
        uploadFile: function( event ) {
            this.uploadStatus = 'loading';
            let file = this.$refs.file.files[0];
            let data = new FormData();
            data.append( 'action', 'cff_import_settings_json' );
            data.append( 'nonce', this.nonce );
            data.append( 'file', file );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                this.uploadStatus = null;
                this.$refs.file.files[0] = null;
                if ( data.success == false ) {
                    this.notificationElement =  {
                        type : 'error',
                        text : this.genericText.failedToImportFeed,
                        shown : "shown"
                    };
                }
                if ( data.success == true ) {
                    this.feeds = data.data.feeds;
                    this.notificationElement =  {
                        type : 'success',
                        text : this.genericText.feedImported,
                        shown : "shown"
                    };
                }
                setTimeout(function(){
                    this.notificationElement.shown =  "hidden";
                }.bind(this), 3000);
            });
        },
        exportFeedSettings: function() {
            // return if no feed is selected
            if ( this.exportFeed === 'none' ) {
                return;
            }

            let url = this.ajaxHandler + '?action=cff_export_settings_json&feed_id=' + this.exportFeed + '&nonce=' + this.nonce;
            window.location = url;
        },
        saveSettings: function() {
            this.btnStatus = 'loading';
            let data = new FormData();
            data.append( 'action', 'cff_save_settings' );
            data.append( 'nonce', this.nonce );
            data.append( 'model', JSON.stringify( this.model ) );
            data.append( 'cff_license_key', this.licenseKey );
            data.append( 'extensions_license_key', JSON.stringify( this.extensionsLicenseKey ) );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if ( data.success == false ) {
                    this.btnStatus = 'error';
                    return;
                }

                this.cronNextCheck = data.data.cronNextCheck;
                this.btnStatus = 'success';
                setTimeout(function() {
                    this.btnStatus = null;
                }.bind(this), 3000);
            });
        },
        clearCache: function() {
            this.clearCacheStatus = 'loading';
            let data = new FormData();
            data.append( 'action', 'cff_clear_cache' );
            data.append( 'model', JSON.stringify( this.model ) );
            data.append( 'nonce', this.nonce );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if ( data.success == false ) {
                    this.clearCacheStatus = 'error';
                    return;
                }

                this.cronNextCheck = data.data.cronNextCheck;
                this.clearCacheStatus = 'success';
                setTimeout(function() {
                    this.clearCacheStatus = null;
                }.bind(this), 3000);
            });
        },
        showTooltip: function( tooltipName ) {
            this.tooltipName = tooltipName;
        },
        hideTooltip: function() {
            this.tooltipName = null;
        },
        gdprOptions: function() {
            this.gdprInfoTooltip = null;
        },
        gdprLimited: function() {
            this.gdprInfoTooltip = this.gdprInfoTooltip == null ? true : null;
        },
        clearImageResizeCache: function() {
            this.optimizeCacheStatus = 'loading';
            let data = new FormData();
            data.append( 'action', 'cff_clear_image_resize_cache' );
            data.append( 'nonce', this.nonce );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if ( data.success == false ) {
                    this.optimizeCacheStatus = 'error';
                    return;
                }
                this.optimizeCacheStatus = 'success';
                setTimeout(function() {
                    this.optimizeCacheStatus = null;
                }.bind(this), 3000);
            });
        },
        dpaReset: function() {
            this.dpaResetStatus = 'loading';
            let data = new FormData();
            data.append( 'action', 'cff_dpa_reset' );
            data.append( 'nonce', this.nonce );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
                .then(response => response.json())
                .then(data => {
                    if ( data.success == false ) {
                        this.dpaResetStatus = 'error';
                        return;
                    }
                    this.dpaResetStatus = 'success';
                    setTimeout(function() {
                        this.dpaResetStatus = null;
                    }.bind(this), 3000);
                });
        },
        dpaResetStatusIcon: function() {
            if ( this.dpaResetStatus === null ) {
                return;
            }
            if ( this.dpaResetStatus == 'loading' ) {
                return this.loaderSVG;
            } else if ( this.dpaResetStatus == 'success' ) {
                return this.checkmarkSVG;
            } else if ( this.dpaResetStatus == 'error' ) {
                return `<i class="fa fa-times-circle"></i>`;
            }
        },
        saveChangesIcon: function() {
            if ( this.btnStatus == 'loading' ) {
                return this.loaderSVG;
            } else if ( this.btnStatus == 'success' ) {
                return this.checkmarkSVG;
            } else if ( this.btnStatus == 'error' ) {
                return `<i class="fa fa-times-circle"></i>`;
            }
        },
        importBtnIcon: function() {
            if ( this.uploadStatus === null ) {
                return this.uploadSVG;
            }
            if ( this.uploadStatus == 'loading' ) {
                return this.loaderSVG;
            } else if ( this.uploadStatus == 'success' ) {
                return this.checkmarkSVG;
            } else if ( this.uploadStatus == 'error' ) {
                return `<i class="fa fa-times-circle"></i>`;
            }
        },
        clearCacheIcon: function() {
            if ( this.clearCacheStatus === null ) {
                return this.reloadSVG;
            }
            if ( this.clearCacheStatus == 'loading' ) {
                return this.loaderSVG;
            } else if ( this.clearCacheStatus == 'success' ) {
                return this.checkmarkSVG;
            } else if ( this.clearCacheStatus == 'error' ) {
                return `<i class="fa fa-times-circle"></i>`;
            }
        },
        clearImageResizeCacheIcon: function() {
            if ( this.optimizeCacheStatus === null ) {
                return;
            }
            if ( this.optimizeCacheStatus == 'loading' ) {
                return this.loaderSVG;
            } else if ( this.optimizeCacheStatus == 'success' ) {
                return this.checkmarkSVG;
            } else if ( this.optimizeCacheStatus == 'error' ) {
                return `<i class="fa fa-times-circle"></i>`;
            }
        },

        /**
         * Toggle Sticky Widget view
         *
         * @since 4.0
         */
        toggleStickyWidget: function() {
            this.stickyWidget = !this.stickyWidget;
        },

        printUsedInText: function( usedInNumber ){
            if(usedInNumber == 0){
                return this.genericText.sourceNotUsedYet;
            }
            return this.genericText.usedIn + ' ' + usedInNumber + ' ' +(usedInNumber == 1 ? this.genericText.feed : this.genericText.feeds);
        },

        /**
         * Delete Source Ajax
         *
         * @since 4.0
        */
        deleteSource : function(sourceToDelete){
            var self = this;
             let data = new FormData();
            data.append( 'action', 'cff_feed_saver_manager_delete_source' );
            data.append( 'nonce', this.nonce );
            data.append( 'source_id', sourceToDelete.id);
            fetch(self.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                self.sourcesList = data;
            });
        },

        /**
         * Check if Value is Empty
         *
         * @since 4.0
         *
         * @return boolean
         */
        checkNotEmpty : function(value){
            return value != null && value.replace(/ /gi,'') != '';
        },

        /**
         * Activate View
         *
         * @since 4.0
        */
        activateView : function(viewName, sourcePopupType = 'creation', ajaxAction = false){
            var self = this;
            self.viewsActive[viewName] = (self.viewsActive[viewName] == false ) ? true : false;
            if(viewName == 'sourcePopup' && sourcePopupType == 'creationRedirect'){
                setTimeout(function(){
                    self.$refs.addSourceRef.processFBConnect()
                },3500);
            }
        },

        /**
         * Switch & Change Feed Screens
         *
         * @since 4.0
         */
        switchScreen: function(screenType, screenName){
            this.viewsActive[screenType] = screenName;
        },

        /**
         * Parse JSON
         *
         * @since 4.0
         *
         * @return jsonObject / Boolean
         */
        jsonParse : function(jsonString){
            try {
                return JSON.parse(jsonString);
            } catch(e) {
                return false;
            }
        },


        /**
         * Ajax Post Action
         *
         * @since 4.0
         */
        ajaxPost : function(data, callback){
            var self = this;
            data['nonce'] = self.nonce;
            data['settings_page'] = true;

          self.$http.post(self.ajaxHandler,data).then(callback);
        },

        /**
         * Check if Object has Nested Property
         *
         * @since 4.0
         *
         * @return boolean
         */
        hasOwnNestedProperty : function(obj,propertyPath) {
          if (!propertyPath){return false;}var properties = propertyPath.split('.');
          for (var i = 0; i < properties.length; i++) {
            var prop = properties[i];
            if (!obj || !obj.hasOwnProperty(prop)) {
              return false;
            } else {
              obj = obj[prop];
            }
          }
          return true;
        },

        /**
         * Show Tooltip on Hover
         *
         * @since 4.0
         */
        toggleElementTooltip : function(tooltipText, type, align = 'center'){
            var self = this,
                target = window.event.currentTarget,
                tooltip = (target != undefined && target != null) ? document.querySelector('.sb-control-elem-tltp-content') : null;
            if(tooltip != null && type == 'show'){
                self.tooltip.text = tooltipText;
                var position = target.getBoundingClientRect(),
                    left = position.left + 7,
                    top = position.top - 15;
                tooltip.style.left = left + 'px';
                tooltip.style.top = top + 'px';
                tooltip.style.textAlign = align;
                self.tooltip.hover = true;
            }
            if(type == 'hide'){
                self.tooltip.hover = false;
            }
        },

        /**
         * Hover Tooltip
         *
         * @since 4.0
         */
        hoverTooltip : function(type){
            this.tooltip.hover = type;
        },

        /**
         * Open Dialog Box
         *
         * @since 4.0
        */
        openDialogBox : function(type, args = []){
            var self = this,
                heading = self.dialogBoxPopupScreen[type].heading,
                description = self.dialogBoxPopupScreen[type].description;

            switch (type) {
                case "deleteSource":
                    self.sourceToDelete = args;
                    heading = heading.replace("#", self.sourceToDelete.username);
                break;
            }
            self.dialogBox = {
                active : true,
                type : type,
                heading : heading,
                description : description
            };
        },


        /**
         * Confirm Dialog Box Actions
         *
         * @since 4.0
         */
        confirmDialogAction : function(){
            var self = this;
            switch (self.dialogBox.type) {
                case 'deleteSource':
                    self.deleteSource(self.sourceToDelete);
                    break;
            }
        },

        /**
         * Display Feed Sources Settings
         *
         * @since 4.0
         *
         * @param {object} source
         * @param {int} sourceIndex
         */
        displayFeedSettings: function(source, sourceIndex) {
            this.expandedFeedID = sourceIndex + 1;
        },

        /**
         * Hide Feed Sources Settings
         *
         * @since 4.0
         *
         * @param {object} source
         * @param {int} sourceIndex
         */
        hideFeedSettings: function() {
            this.expandedFeedID = null;
        },

		/**
		 * Copy text to clipboard
		 *
		 * @since 4.0
		 */
         copyToClipBoard : function(value){
			var self = this;
			const el = document.createElement('textarea');
			el.className = 'cff-fb-cp-clpboard';
			el.value = value;
			document.body.appendChild(el);
			el.select();
			document.execCommand('copy');
			document.body.removeChild(el);
			self.notificationElement =  {
				type : 'success',
				text : this.genericText.copiedClipboard,
				shown : "shown"
			};
			setTimeout(function(){
				self.notificationElement.shown =  "hidden";
			}, 3000);
		},


        /**
         * View Source Instances
         *
         * @since 4.0
         */
        viewSourceInstances : function(source){
            var self = this;
            self.viewsActive.instanceSourceActive = source;
        },
    }
});

