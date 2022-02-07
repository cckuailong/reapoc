/**
  * Simple Job Board Shortcode Builder JS File - V 1.0.0
  *
  * @author PressTigers <support@presstigers.com>, 2016
  *
  * Actions List
  * - Tinymce Button Callback (Fired onclick & onsubmit events)
  * @since 2.8.0 - Added Job Layout attribute to display the job listing in different layouts
  */
(function ($) {
    'use strict';

    $(function () {
        tinymce.PluginManager.add('sjb_shortcodes_mce_button', function (editor, url) {
            editor.addButton('sjb_shortcodes_mce_button', {
                title: 'Simple Job Board',
                icon: 'icon sjb-icon',
                onclick: function () {
                    editor.windowManager.open({
                        title: 'Insert Simple Job Board Shortcode',
                        body: [
                            // Number of jobs
                            {
                                type: 'textbox',
                                subtype: 'number',
                                name: 'job_posts',
                                label: 'Posts',
                            },
                            // Job category
                            {
                                type: 'textbox',
                                name: 'job_category',
                                label: 'Category',
                            },
                            // Job Type
                            {
                                type: 'textbox',
                                name: 'job_type',
                                label: 'Type',
                            },
                            // Job Location                                   
                            {
                                type: 'textbox',
                                name: 'job_location',
                                label: 'Location',
                            },
                            // Job Search                                   
                            {
                                type: 'listbox',
                                name: 'job_search',
                                label: 'Search',
                                values: [
                                    {text: 'True', value: 'true'},
                                    {text: 'False', value: 'false'},
                                ]
                            },
                            // Job Listing Layout
                            {
                                type: 'listbox',
                                name: 'job_layout',
                                label: 'Layout',
                                values: [
                                    {text: 'List', value: 'list'},
                                    {text: 'Grid', value: 'grid'},
                                ]
                            },
                        ],
                        onsubmit: function (e) {

                            // If user enter number less than -1
                            if (e.data.job_posts < -1) {

                                // Change value with -1
                                e.data.job_posts = -1;
                            }
                            editor.insertContent('[jobpost posts="' + e.data.job_posts + '" category="' + e.data.job_category + '" type="' + e.data.job_type + '" location="' + e.data.job_location + '" search="' + e.data.job_search + '" layout="' + e.data.job_layout + '" ]');
                        }
                    });
                }
            });
        });
    });
})(jQuery);