jQuery(document).ready(function($){
    'use strict';

    /*========================================================================
     * Tutor WP Editor Button
     *======================================================================== */
    /**
     * editor.getLang translation support added
     * 
     * @since 1.9.7
     */
    tinymce.PluginManager.add('tutor_button', function( editor, url ) {
        editor.addButton( 'tutor_button', {
            text: editor.getLang('tutor_button.tutor_shortcode'),
            icon: false,
            type: 'menubutton',
            menu: [
                {
                    text: editor.getLang('tutor_button.student_registration_form'),
                    onclick: function() {
                        editor.insertContent('[tutor_student_registration_form]');
                    }
                },
                /*{
                    text: 'Student Dashboard',
                    onclick: function() {
                        editor.insertContent('[tutor_dashboard]');
                    }
                },*/{
                    text: editor.getLang('tutor_button.instructor_registration_form'),
                    onclick: function() {
                        editor.insertContent('[tutor_instructor_registration_form]');
                    }
                },
               /* {
                    text: 'Courses',
                    onclick: function() {
                        editor.insertContent('[tutor_course]');
                    }
                },*/



                {
                    text: editor.getLang('tutor_button.courses'),
                    onclick: function() {
                        editor.windowManager.open( {
                            title: editor.getLang('tutor_button.courses_shortcode'),
                            body: [
                                {
                                    type: 'textbox',
                                    name: 'id',
                                    label: editor.getLang('tutor_button.courses_separate_by'),
                                    value: ''
                                },{
                                    type: 'textbox',
                                    name: 'exclude_ids',
                                    label: editor.getLang('tutor_button.exclude_course_ids'),
                                    value: ''
                                },
                                {
                                    type: 'textbox',
                                    name: 'category',
                                    label: editor.getLang('tutor_button.category_ids'),
                                    value: ''
                                },
                                {type: 'listbox',
                                    name: 'orderby',
                                    label: editor.getLang('tutor_button.order_by'),
                                    onselect: function(e) {

                                    },
                                    'values': [
                                        {text: 'ID', value: 'ID'},
                                        {text: 'title', value: 'title'},
                                        {text: 'rand', value: 'rand'},
                                        {text: 'date', value: 'date'},
                                        {text: 'menu_order', value: 'menu_order'},
                                        {text: 'post__in', value: 'post__in'},
                                    ]
                                },
                                {type: 'listbox',
                                    name: 'order',
                                    label: editor.getLang('tutor_button.order'),
                                    onselect: function(e) {

                                    },
                                    'values': [
                                        {text: 'DESC', value: 'DESC'},
                                        {text: 'ASC', value: 'ASC'}
                                    ]
                                },
                                ,{
                                    type: 'textbox',
                                    name: 'count',
                                    label: editor.getLang('tutor_button.count'),
                                    value: '6',
                                }
                            ],
                            onsubmit: function( e ) {
                                editor.insertContent( '[tutor_course id="' + e.data.id + '" exclude_ids="'+e.data.exclude_ids+'" category="'+e.data.category+'" orderby="'+e.data.orderby+'" order="'+e.data.order+'" count="'+e.data.count+'"]');
                            }
                        });
                    }
                }


            ]
        });
    });

});
