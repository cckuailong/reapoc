jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.wpuf_button', {
        init : function(editor, url) {
                var menuItem = [];
                var ds_img = wpuf_assets_url.url +'/images/ufp.png';
                $.each( wpuf_shortcodes, function( i, val ){
                    var tempObj = {
                            text : val.title,
                            onclick: function() {
                                editor.insertContent(val.content)
                            }
                        };
                        
                    menuItem.push( tempObj );
                } );
                // Register buttons - trigger above command when clickeditor
                editor.addButton('wpuf_button', {
                    title : 'WPUF shortcodes', 
                    classes : 'wpuf-ss',
                    type  : 'menubutton',
                    menu  : menuItem,
                    style : ' background-size : 22px; background-repeat : no-repeat; background-image: url( '+ ds_img +' );'
                });
        },   
    });

    // Register our TinyMCE plugin
    
    tinymce.PluginManager.add('wpuf_button', tinymce.plugins.wpuf_button);
});