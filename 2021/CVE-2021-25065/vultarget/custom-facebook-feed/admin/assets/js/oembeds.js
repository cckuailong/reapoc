var oembeds_data = {
    nonce: cff_oembeds.nonce,
    genericText: cff_oembeds.genericText,
    images: cff_oembeds.images,
    modal: cff_oembeds.modal,
    links: cff_oembeds.links,
    supportPageUrl: cff_oembeds.supportPageUrl,
    socialWallActivated: cff_oembeds.socialWallActivated,
    socialWallLinks: cff_oembeds.socialWallLinks,
    stickyWidget: false,
    facebook: cff_oembeds.facebook,
    instagram: cff_oembeds.instagram,
    connectionURL: cff_oembeds.connectionURL,
    isIntagramActivated: cff_oembeds.instagram.active,
    instagramInstallBtnText: null,
    fboEmbedLoader: false,
    instaoEmbedLoader: false,
    openInstaInstaller: false,
    loaderSVG: cff_oembeds.loaderSVG,
    checkmarkSVG: cff_oembeds.checkmarkSVG,
    installerStatus: null
}

var cffoEmbeds = new Vue({
    el: "#cff-oembeds",
    http: {
        emulateJSON: true,
        emulateHTTP: true
    },
    data: oembeds_data,
    methods: {
        openInstallModal: function() {
            this.openInstaInstaller = true
        },
        closeModal: function() {
            this.openInstaInstaller = false
        },
        isoEmbedsEnabled: function() {
            if ( this.facebook.doingOembeds && this.instagram.doingOembeds ) {
                return true;
            }
            return;
        },
        InstagramShouldInstallOrEnable: function() {
            // if the plugin is activated and installed then just enable oEmbed
            if( this.isIntagramActivated ) {
                this.enableInstagramOembed();
                return;
            }
            // if the plugin is not activated and installed then open the modal to install and activate the plugin
            if( !this.isIntagramActivated ) {
                this.openInstallModal();
                return;
            }
        },
        installInstagram: function() {
            this.installerStatus = 'loading';
            let data = new FormData();
            data.append( 'action', cff_oembeds.instagram.installer.action );
            data.append( 'nonce', cff_oembeds.nonce );
            data.append( 'plugin', cff_oembeds.instagram.installer.plugin );
            data.append( 'type', 'plugin' );
            fetch(cff_oembeds.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if( data.success == false ) {
                    this.installerStatus = 'error'
                }
                if( data.success == true ) {
                    this.isIntagramActivated = true;
                    this.installerStatus = 'success'
                }
                this.instagramInstallBtnText = data.data.msg;
                setTimeout(function() {
                    this.installerStatus = null;
                }.bind(this), 3000);
                return;
            });
        },
        enableFboEmbed: function() {
            this.fboEmbedLoader = true;
            window.location = this.connectionURL;
            return;
        },
        enableInstagramOembed: function() {
            this.instaoEmbedLoader = true;
            window.location = this.connectionURL;
            return;
        },
        disableFboEmbed: function() {
            this.fboEmbedLoader = true;
            let data = new FormData();
            data.append( 'action', 'disable_facebook_oembed' );
            data.append( 'nonce', this.nonce );
            fetch(cff_oembeds.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if( data.success == true ) {
                    this.fboEmbedLoader = false;
                    this.facebook.doingOembeds = false;
                    // get the updated connection URL after disabling oEmbed
                    this.connectionURL = data.data.connectionUrl;
                }
                return;
            });
        },
        disableInstaoEmbed: function() {
            this.instaoEmbedLoader = true;
            let data = new FormData();
            data.append( 'action', 'disable_instagram_oembed' );
            data.append( 'nonce', this.nonce );
            fetch(cff_oembeds.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if( data.success == true ) {
                    this.instaoEmbedLoader = false;
                    this.instagram.doingOembeds = false;
                    // get the updated connection URL after disabling oEmbed
                    this.connectionURL = data.data.connectionUrl;
                }
                return;
            });
        },
        installButtonText: function( buttonText = null ) {
            if ( buttonText ) {
                return buttonText;
            } else if ( this.instagram.installer.nextStep == 'free_install' ) {
                return this.modal.install;
            } else if ( this.instagram.installer.nextStep == 'free_activate' ) {
                return this.modal.activate;
            }
        },
        installIcon: function() {
            if ( this.isIntagramActivated ) {
                return;
            }
            if( this.installerStatus == null ) {
                return this.modal.plusIcon;
            } else if( this.installerStatus == 'loading' ) {
                return this.loaderSVG;
            } else if( this.installerStatus == 'success' ) {
                return this.checkmarkSVG;
            } else if( this.installerStatus == 'error' ) {
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
    },
    created() {
        // Display the "Install" button text on modal depending on condition
        if ( this.instagram.installer.nextStep == 'free_install' ) {
            this.instagramInstallBtnText = this.modal.install;
        } else if ( this.instagram.installer.nextStep == 'free_activate' || this.instagram.installer.nextStep == 'pro_activate' ) {
            this.instagramInstallBtnText = this.modal.activate;
        }
    }
})
