var extensions_data = {
    genericText: cff_about.genericText,
    links: cff_about.links,
    extentions_bundle: cff_about.extentions_bundle,
    supportPageUrl: cff_about.supportPageUrl,
    plugins: cff_about.pluginsInfo,
    stickyWidget: false,
    socialWallActivated: cff_about.socialWallActivated,
    socialWallLinks: cff_about.socialWallLinks,
    recommendedPlugins: cff_about.recommendedPlugins,
    social_wall: cff_about.social_wall,
    aboutBox: cff_about.aboutBox,
    ajax_handler: cff_about.ajax_handler,
    nonce: cff_about.nonce,
    buttons: cff_about.buttons,
    icons: cff_about.icons,
    btnClicked: null,
    btnStatus: null,
    btnName: null,
}

var cffAbout = new Vue({
    el: "#cff-about",
    http: {
        emulateJSON: true,
        emulateHTTP: true
    },
    data: extensions_data,
    methods: {
        activatePlugin: function( plugin, name, index, type ) {
            this.btnClicked = index + 1;
            this.btnStatus = 'loading';
            this.btnName = name;

            let data = new FormData();
            data.append( 'action', 'cff_activate_addon' );
            data.append( 'nonce', this.nonce );
            data.append( 'plugin', plugin );
            data.append( 'type', 'plugin' );
            if ( this.extentions_bundle && type == 'extension' ) {
                data.append( 'extensions_bundle', this.extentions_bundle );
            }
            fetch(this.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if( data.success == true ) {
                    if ( name === 'social_wall' ) {
                        this.social_wall.activated = true;
                    } else if ( type === 'recommended_plugin' ) {
                        this.recommendedPlugins[name].activated = true;
                    } else {
                        this.plugins[name].activated = true;
                    }
                    this.btnClicked = null;
                    this.btnStatus = null;
                    this.btnName = null;
                }
            });
        },
        deactivatePlugin: function( plugin, name, index, type  ) {
            this.btnClicked = index + 1;
            this.btnStatus = 'loading';
            this.btnName = name;
            
            let data = new FormData();
            data.append( 'action', 'cff_deactivate_addon' );
            data.append( 'nonce', this.nonce );
            data.append( 'plugin', plugin );
            data.append( 'type', 'plugin' );
            if ( this.extentions_bundle && type == 'extension' ) {
                data.append( 'extensions_bundle', this.extentions_bundle );
            }
            fetch(this.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if( data.success == true ) {
                    if ( name === 'social_wall' ) {
                        this.social_wall.activated = false;
                    } else if ( type === 'recommended_plugin' ) {
                        this.recommendedPlugins[name].activated = false;
                    } else {
                        this.plugins[name].activated = false;
                    }
                    this.btnClicked = null;
                    this.btnName = null;
                    this.btnStatus = null;
                }
                return;
            });
        },
        installPlugin: function( plugin, name, index, type ) {
            this.btnClicked = index + 1;
            this.btnStatus = 'loading';
            this.btnName = name;

            let data = new FormData();
            data.append( 'action', 'cff_install_addon' );
            data.append( 'nonce', this.nonce );
            data.append( 'plugin', plugin );
            data.append( 'type', 'plugin' );
            fetch(this.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if( data.success == true ) {
                    if ( type === 'recommended_plugin' ) {
                        this.recommendedPlugins[name].installed = true;
                        this.recommendedPlugins[name].activated = true;
                    } else {
                        this.plugins[name].installed = true;
                        this.plugins[name].activated = true;
                    }
                    this.btnClicked = null;
                    this.btnName = null;
                    this.btnStatus = null;
                }
                return;
            });
        },
        buttonIcon: function() {
            if ( this.btnStatus == 'loading' ) {
                return this.icons.loaderSVG
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
    }
})