/**
 * Block: PMPro Checkout Button
 *
 * Add a styled link to the PMPro checkout page for a specific level.
 *
 */

/**
 * Block dependencies
 */
import Inspector from './inspector';

/**
 * Internal block libraries
 */
const { __ } = wp.i18n;
const {
    registerBlockType,
} = wp.blocks;
const {
    TextControl,
    SelectControl,
} = wp.components;

/**
 * Register block
 */
export default registerBlockType(
     'pmpro/checkout-button',
     {
         title: __( 'Membership Checkout Button', 'paid-memberships-pro' ),
         description: __( 'Displays a button-styled link to Membership Checkout for the specified level.', 'paid-memberships-pro' ),
         category: 'pmpro',
         icon: {
            background: '#2997c8',
            foreground: '#ffffff',
            src: 'migrate',
         },
         keywords: [ 
             __( 'pmpro', 'paid-memberships-pro' ), 
             __( 'buy', 'paid-memberships-pro' ),
             __( 'level', 'paid-memberships-pro' ),
         ],
         supports: {
         },
         attributes: {
             text: {
                 type: 'string',
                 default: 'Buy Now',
             },
             css_class: {
                 type: 'string',
                 default: 'pmpro_btn',
             },
             level: {
                  type: 'string'
             }
         },
         edit: props => {
             const { attributes: { text, level, css_class}, className, setAttributes, isSelected } = props;
             return [
                isSelected && <Inspector { ...{ setAttributes, ...props} } />,
                <div className={ className }>
                  <a class={css_class} >{text}</a>
                </div>,
                isSelected && <div className="pmpro-block-element">
                   <TextControl
                       label={ __( 'Button Text', 'paid-memberships-pro' ) }
                       value={ text }
                       onChange={ text => setAttributes( { text } ) }
                   />
                   <SelectControl
                       label={ __( 'Membership Level', 'paid-memberships-pro' ) }
                       value={ level }
                       onChange={ level => setAttributes( { level } ) }
                       options={ window.pmpro.all_level_values_and_labels }
                   />
                   <TextControl
                       label={ __( 'CSS Class', 'paid-memberships-pro' ) }
                       value={ css_class }
                       onChange={ css_class => setAttributes( { css_class } ) }
                   />
                   </div>,
            ];
         },
         save() {
           return null;
         },
       }
);