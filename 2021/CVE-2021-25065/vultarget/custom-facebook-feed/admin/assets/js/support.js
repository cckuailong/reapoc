var support_data = {
    genericText: cff_support.genericText,
    articles: cff_support.articles,
    links: cff_support.links,
    system_info: cff_support.system_info,
    system_info_n: cff_support.system_info_n,
    exportFeed: 'none',
    stickyWidget: false,
    feeds: cff_support.feeds,
    supportUrl: cff_support.supportUrl,
    socialWallActivated: cff_support.socialWallActivated,
    socialWallLinks: cff_support.socialWallLinks,
    siteSearchUrl: cff_support.siteSearchUrl,
    siteSearchUrlWithArgs: null,
    searchKeywords: null,
    buttons: cff_support.buttons,
    links: cff_support.links,
    supportPageUrl: cff_support.supportPageUrl,
    systemInfoBtnStatus: 'collapsed',
    copyBtnStatus: null,
    ajax_handler: cff_support.ajax_handler,
    nonce: cff_support.nonce,
    icons: cff_support.icons,
    images: cff_support.images,
    svgIcons : cff_support.svgIcons,
    notificationElement : {
        type : 'success', // success, error, warning, message
        text : '',
        shown : null
    }
}

var cffsupport = new Vue({
    el: "#cff-support",
    http: {
        emulateJSON: true,
        emulateHTTP: true
    },
    data: support_data,
    methods: {
        copySystemInfo: function() {
            let self = this;
            const el = document.createElement('textarea');
			el.className = 'cff-fb-cp-clpboard';
			el.value = self.system_info_n;
			document.body.appendChild(el);
			el.select();
			document.execCommand('copy');
			document.body.removeChild(el);
            this.notificationElement =  {
                type : 'success',
                text : this.genericText.copiedToClipboard,
                shown : "shown"
            };

            setTimeout(function() {
                this.notificationElement.shown =  "hidden";
            }.bind(self), 3000);
        },
        expandSystemInfo: function() {
            this.systemInfoBtnStatus = ( this.systemInfoBtnStatus == 'collapsed' ) ? 'expanded' : 'collapsed';
        },
        expandBtnText: function() {
            if ( this.systemInfoBtnStatus == 'collapsed' ) {
                return this.buttons.expand;
            } else if ( this.systemInfoBtnStatus == 'expanded' ) {
                return this.buttons.collapse;
            }
        },
        exportFeedSettings: function() {
            // return if no feed is selected
            if ( this.exportFeed === 'none' ) {
                return;
            }

            let url = this.ajax_handler + '?action=cff_export_settings_json&feed_id=' +  + this.exportFeed + '&nonce=' + this.nonce;
            window.location = url;
        },
        searchDoc: function() {
            let self = this;
            let searchInput = document.getElementById('cff-search-doc-input');
            searchInput.addEventListener('keyup', function ( event ) {
                let url = new URL( self.siteSearchUrl );
                let search_params = url.searchParams;
                if ( self.searchKeywords ) {
                    search_params.set('search', self.searchKeywords);
                }
                search_params.set('plugin', 'facebook');
                url.search = search_params.toString();
                self.siteSearchUrlWithArgs = url.toString();

                if ( event.key === 'Enter' ) {
                    window.open( self.siteSearchUrlWithArgs, '_blank');
                }
            })
        },
        searchDocStrings: function() {
            let self = this;
            let url = new URL( this.siteSearchUrl );
            let search_params = url.searchParams;
            setTimeout(function() {
                search_params.set('search', self.searchKeywords);
                search_params.set('plugin', 'facebook');
                url.search = search_params.toString();
                self.siteSearchUrlWithArgs = url.toString();
            }, 10);
        },
        goToSearchDocumentation: function() {
            if ( this.searchKeywords !== null && this.siteSearchUrlWithArgs !== null ) {
                window.open( this.siteSearchUrlWithArgs, '_blank');
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
    },
})