(function() {
    jQuery( document ).on( 'tinymce-editor-setup', function( event, editor ) {
        editor.addButton( 'wp_statistic_tc_button', {
            text: '',
            tooltip: editor.getLang('wp_statistic_tinymce_plugin.insert'),
            icon: 'icon-statistic dashicons-chart-pie',
            onclick: function() {
                editor.windowManager.open({
                    title: editor.getLang('wp_statistic_tinymce_plugin.insert'),
                    minWidth: 500,
                    minHeight: 480,
                    body: [
                        {
                            type: 'listbox',
                            name: 'stat',
                            label: editor.getLang('wp_statistic_tinymce_plugin.stat'),
                            'values': [
                                {text: editor.getLang('wp_statistic_tinymce_plugin.usersonline'), value: 'usersonline'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.visits'), value: 'visits'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.visitors'), value: 'visitors'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.pagevisits'), value: 'pagevisits'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.searches'), value: 'searches'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.postcount'), value: 'postcount'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.pagecount'), value: 'pagecount'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.commentcount'), value: 'commentcount'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.spamcount'), value: 'spamcount'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.usercount'), value: 'usercount'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.postaverage'), value: 'postaverage'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.commentaverage'), value: 'commentaverage'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.useraverage'), value: 'useraverage'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.lpd'), value: 'lpd'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.referrer'), value: 'referrer'},
                            ]
                        },
                        {
                            type   : 'container',
                            html   : '<div class="wp-statistic-mce-desc">' + editor.getLang('wp_statistic_tinymce_plugin.help_stat') + ' </div>'
                        },
                        {
                            type: 'listbox',
                            name: 'time',
                            label: editor.getLang('wp_statistic_tinymce_plugin.time'),
                            'values': [
                                {text: editor.getLang('wp_statistic_tinymce_plugin.se'), value: '0'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.today'), value: 'today'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.yesterday'), value: 'yesterday'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.week'), value: 'week'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.month'), value: 'month'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.year'), value: 'year'},
                                {text: editor.getLang('wp_statistic_tinymce_plugin.total'), value: 'total'}
                            ]
                        },
                        {
                            type   : 'container',
                            html   : '<div class="wp-statistic-mce-desc">' + editor.getLang('wp_statistic_tinymce_plugin.help_time') + '</div>'
                        },
                        {
                            type: 'textbox',
                            name: 'provider',
                            label: editor.getLang('wp_statistic_tinymce_plugin.provider'),
                        },
                        {
                            type   : 'container',
                            html   : '<div class="wp-statistic-mce-desc">' + editor.getLang('wp_statistic_tinymce_plugin.help_provider') + '</div>'
                        },
                        {
                            type: 'textbox',
                            name: 'format',
                            label: editor.getLang('wp_statistic_tinymce_plugin.format'),
                        },
                        {
                            type   : 'container',
                            html   : '<div class="wp-statistic-mce-desc">' + editor.getLang('wp_statistic_tinymce_plugin.help_format') + '</div>'
                        },
                        {
                            type: 'textbox',
                            name: 'id',
                            label: editor.getLang('wp_statistic_tinymce_plugin.id'),
                        },
                        {
                            type   : 'container',
                            html   : '<div class="wp-statistic-mce-desc">' + editor.getLang('wp_statistic_tinymce_plugin.help_id') + '</div>'
                        },
                        ],
                    onsubmit: function( e ) {
                        var wp_statistice_shortcode = '[wpstatistics stat=' + e.data.stat;
                        if(e.data.time !=='0') {wp_statistice_shortcode +=' time=' + e.data.time;}
                        var wp_statistic_type = ["provider", "format", "id"];
                        wp_statistic_type.forEach(function(entry) {
                            if(e.data[entry] !=='') {
                                wp_statistice_shortcode +=' ' + entry + '=' + e.data[entry];
                            }
                        });
                        wp_statistice_shortcode +=']';
                        editor.insertContent(wp_statistice_shortcode);
                    }
                });
            }
        });
    });
})();