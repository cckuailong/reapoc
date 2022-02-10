/**
 * Install Plugin Popup Popup
 *
 * @since 4.0
 */
 Vue.component('install-plugin-popup', {
    name: 'install-plugin-popup',
    template: '#install-plugin-popup',
    props: [
    'genericText',
    'svgIcons',
    'viewsActive',
    'plugins'
    ],
    data: function() {
        return{
            installerStatus: null
        }
    },
    methods : {
        /**
         * Install or Activate Plugin
         *
         * @since 4.0
         *
         * @return void
         */
         installOrActivatePlugin : function( plugin, pluginPath, action ){
            this.installerStatus = 'loading';
            let data = new FormData();
            data.append( 'action', action );
            data.append( 'nonce', cff_builder.nonce );
            data.append( 'plugin', pluginPath );
            data.append( 'type', 'plugin' );
            fetch(cff_builder.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if( data.success == true ) {             
                    this.installerStatus = null;
                    plugin.installed = true;
                    plugin.activated = true;
                }
                return;
            });
        },
    },
});
