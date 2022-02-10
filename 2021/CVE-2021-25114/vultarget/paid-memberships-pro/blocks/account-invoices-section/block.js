/**
 * Block: PMPro Membership Account: Invoices
 *
 * Displays the Membership Account > Invoices page section.
 *
 */

 /**
  * Internal block libraries
  */
 const { __ } = wp.i18n;
 const {
    registerBlockType
} = wp.blocks;

 /**
  * Register block
  */
 export default registerBlockType(
     'pmpro/account-invoices-section',
     {
         title: __( 'Membership Account: Invoices', 'paid-memberships-pro' ),
         description: __( 'Displays the member\'s invoices.', 'paid-memberships-pro' ),
         category: 'pmpro',
         icon: {
            background: '#2997c8',
            foreground: '#ffffff',
            src: 'archive',
         },
         keywords: [ __( 'pmpro', 'paid-memberships-pro' ) ],
         supports: {
         },
         attributes: {
         },
         edit() {
             return [
                <div className="pmpro-block-element">
                  <span className="pmpro-block-title">{ __( 'Paid Memberships Pro', 'paid-memberships-pro' ) }</span>
                  <span className="pmpro-block-subtitle"> { __( 'Membership Account: Invoices', 'paid-memberships-pro' ) }</span>
                </div>
            ];
         },
         save() {
           return null;
         },
       }
 );
