var el = wp.element.createElement,
    registerBlockType = wp.blocks.registerBlockType,
    withSelect = wp.data.withSelect;

/**
 * Block for student registration
 *
 */


registerBlockType( 'tutor-gutenberg/student-registration', {
    title: 'Student Registration',
    icon: 'welcome-learn-more',
    category: 'tutor',
    edit: function( props ) {
        var dataHtml;
        jQuery.ajax({
            url : ajaxurl,
            type : 'POST',
            async: false,
            data : {shortcode: 'tutor_student_registration_form',  action : 'render_block_tutor'},
            success: function (response) {
                dataHtml = response.data;
            },
        });

        return el('div', {
            dangerouslySetInnerHTML: {
                __html: dataHtml
            }
        })
    },

    save: function() {
        return null;
        //return el( 'div', { }, '[tutor_student_registration_form]' );
    },
} );

/*
registerBlockType( 'tutor-gutenberg/student-dashboard', {
    title: 'Tutor Student Dashboard',
    icon: 'welcome-learn-more',
    category: 'tutor',
    edit: function( props ) {
        var dataHtml;
        jQuery.ajax({
            url : ajaxurl,
            type : 'POST',
            async: false,
            data : {shortcode: 'tutor_dashboard',  action : 'render_block_tutor'},
            success: function (response) {
                dataHtml = response.data;
            },
        });
        return el('div', {
            dangerouslySetInnerHTML: {
                __html: dataHtml
            }
        })
    },
    save: function() {
        return null;
    },
} );
*/


//tutor_instructor_registration_form

registerBlockType( 'tutor-gutenberg/instructor-registration', {
    title: 'Instructor Registration',
    icon: 'welcome-learn-more',
    category: 'tutor',
    edit: function( props ) {
        var dataHtml;
        jQuery.ajax({
            url : ajaxurl,
            type : 'POST',
            async: false,
            data : {shortcode: 'tutor_instructor_registration_form',  action : 'render_block_tutor'},
            success: function (response) {
                dataHtml = response.data;
            },
        });
        return el('div', {
            dangerouslySetInnerHTML: {
                __html: dataHtml
            }
        })
    },
    save: function() {
        return null;
    },
} );
