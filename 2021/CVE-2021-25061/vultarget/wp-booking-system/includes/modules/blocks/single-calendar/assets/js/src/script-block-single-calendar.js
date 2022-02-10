const { ServerSideRender, PanelBody, SelectControl, TextControl }  = wp.components;

const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { __ } 				= wp.i18n;


/**
 * Block inspector controls options
 *
 */

// The options for the Calendars dropdown
var calendars = [];

calendars[0] = { value : 0, label : __( 'Select Calendar...', 'wp-booking-system' ) };

for( var i = 0; i < wpbs_calendars.length; i++ ) {

    calendars.push( { value : wpbs_calendars[i].id, label : wpbs_calendars[i].name } );

}

// The options for the Calendars dropdown
var forms = [];

forms[0] = { value : 0, label : __( 'Select Form...', 'wp-booking-system' ) };

for( var i = 0; i < wpbs_forms.length; i++ ) {

    forms.push( { value : wpbs_forms[i].id, label : wpbs_forms[i].name } );

}


// The option for the Language dropdown
var languages = [];

languages[0] = { value : 'auto', label : __( 'Auto', 'wp-booking-system' ) };

for( var i = 0; i < wpbs_languages.length; i++ ) {

    languages.push( { value : wpbs_languages[i].code, label : wpbs_languages[i].name } );

}


// Register the block
registerBlockType( 'wp-booking-system/single-calendar', {

    // The block's title
    title : 'Single Calendar',

    // The block's icon
    icon : 'calendar-alt',

    // The block category the block should be added to
    category : 'wp-booking-system',

    // The block's attributes, needed to save the data
    attributes : {

        id : {
            type : 'string'
        },

        form_id : {
            type : 'string'
        },

        title : {
            type : 'string'
        },

        legend : {
            type : 'string'
        },

        language : {
            type    : 'string',
            default : 'auto'
        }

    },

    edit : function( props ) {

        return [

            <ServerSideRender 
                block      = "wp-booking-system/single-calendar"
                attributes = { props.attributes } />,

            <InspectorControls key="inspector">

                <PanelBody
                    title       = { __( 'Calendar', 'wp-booking-system' ) }
                    initialOpen = { true } >

                    <SelectControl
                        value    = { props.attributes.id }
                        options  = { calendars }
                        onChange = { (new_value) => props.setAttributes( { id : new_value } ) } />

                </PanelBody>

                <PanelBody
                    title       = { __( 'Form', 'wp-booking-system' ) }
                    initialOpen = { true } >

                    <SelectControl
                        value    = { props.attributes.form_id }
                        options  = { forms }
                        onChange = { (new_value) => props.setAttributes( { form_id : new_value } ) } />

                </PanelBody>

                <PanelBody
                    title       = { __( 'Calendar Options', 'wp-booking-system' ) }
                    initialOpen = { true } >

                    <SelectControl
                        label   = { __( 'Display Calendar Title', 'wp-booking-system' ) }
                        value   = { props.attributes.title }
                        options = {[
                            { value : 'yes', label : __( 'Yes', 'wp-booking-system' ) },
                            { value : 'no',  label : __( 'No', 'wp-booking-system' ) }
                        ]}
                        onChange = { (new_value) => props.setAttributes( { title : new_value } ) } />

                    <SelectControl
                        label   = { __( 'Display Legend', 'wp-booking-system' ) }
                        value   = { props.attributes.legend }
                        options = {[
                            { value : 'yes', label : __( 'Yes', 'wp-booking-system' ) },
                            { value : 'no',  label : __( 'No', 'wp-booking-system' ) }
                        ]}
                        onChange = { (new_value) => props.setAttributes( { legend : new_value } ) } />

                    
                    <SelectControl
                        label   = { __( 'Language', 'wp-booking-system' ) }
                        value   = { props.attributes.language }
                        options = { languages }
                        onChange = { (new_value) => props.setAttributes( { language : new_value } ) } />

                </PanelBody>

            </InspectorControls>
        ];
    },

    save : function() {
        return null;
    }

});


jQuery( function($) {

    /**
     * Runs every 250 milliseconds to check if a calendar was just loaded
     * and if it was, trigger the window resize to show it
     *
     */
    setInterval( function() {

        $('.wpbs-container-loaded').each( function() {

            if( $(this).attr( 'data-just-loaded' ) == '1' ) {
                $(window).trigger( 'resize' );
                $(this).attr( 'data-just-loaded', '0' );
            }

        });

    }, 250 );

});