var extensions_data = {
    genericText: cff_extensions.genericText,
    extentions_bundle: cff_extensions.extentions_bundle,
    links: cff_extensions.links,
    extensions: cff_extensions.extensions_info,
    socialWallActivated: cff_extensions.socialWallActivated,
    socialWallLinks: cff_extensions.socialWallLinks,
    plugins: cff_extensions.pluginsInfo,
    stickyWidget: false,
    supportPageUrl: cff_extensions.supportPageUrl,
    social_wall: cff_extensions.social_wall,
    ajax_handler: cff_extensions.ajax_handler,
    nonce: cff_extensions.nonce,
    buttons: cff_extensions.buttons,
    icons: cff_extensions.icons,
    btnClicked: null,
    btnStatus: null,
    btnName: null,
}

var cffExtensions = new Vue({
    el: "#cff-extensions",
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
                    } else if ( type === 'extension' ) {
                        this.extensions[name].activated = true;
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
                    } else if ( type === 'extension' ) {
                        this.extensions[name].activated = false;
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
        installPlugin: function( plugin, name, index ) {
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
                    this.plugins[name].installed = true;
                    this.plugins[name].open = true;
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