/**
 * Javascript functions for Infinite Table
 *
 * @package Simple Admin Pages
 */

jQuery(document).ready(function ($) {

  // disable options where not required initially
  $('.sap-infinite-table table tbody tr').each((idx_tr, tr) => {
    let val = $(tr).find('[data-name="cf_type"]').val();
    
    if(!['dropdown', 'checkbox', 'radio'].includes(val)) {
      $(tr).find('[data-name="cf_options"]').val('').prop('readonly', true);
    }
  });

  // process fields
  $('.sap-parent-form').on('submit', function (ev) {
    var _form = $(this), ignore;

    $('.sap-infinite-table').each( function() {

      var main_input = $(this).find('#sap-infinite-table-main-input');

      var main_input_val = [];

      $(this).find('table tbody tr').each((idx_tr, tr) => {
        let record = {}; ignore = false;
  
        $(tr).find('td').each((idx_td, td) => {
          let elm = $(td).find('select, input, textarea, checkbox');
  
          ignore =  'cf_field_name' == elm.data('name') && elm.val().length < 1 ? true : ignore;
  
          if(!ignore) {
            
            if ( elm.prop( 'type' ) == 'checkbox' ) { record[ elm.data('name') ] = elm.is( ':checked' ); }
            else { record[elm.data('name')] = elm.val(); }
          }
        });
  
        !ignore ? main_input_val.push(record) : null;
      }); 

      main_input.val(JSON.stringify(main_input_val));

    });
  });

  // Add new field
  $('.sap-infinite-table-add-row .sap-new-admin-add-button').on('click', function (ev) { 
    let rowid = 1;
    let _list = [];
    $( this ).parents( 'tfoot' ).siblings( 'tbody' ).find( 'tr td' ).each((i, x) => {
      let f_type = $(x).data( 'field-type' );
      if( 'id' == f_type ) {
        _list.push( parseInt( $(x).find( '.sap-infinite-table-id-html' ).eq(0).html() ) );
      }
    });

    _list.sort();
    if( 0 < _list.length ) {
      rowid = _list[ _list.length - 1 ] + 1;
    }
    
    let _template_tr = $( this ).parents( 'tfoot' ).find( '.sap-infinite-table-row-template' ).clone();
    _template_tr
      .hide()
      .removeClass()
      .addClass( 'sap-infinite-table-row' );
    
    $( this ).parents( 'table' ).first().find( 'tbody' ).append( _template_tr );
    _template_tr.find( '.sap-infinite-table-id-html' ).eq(0).siblings( 'input' ).val( rowid );
    _template_tr.find( '.sap-infinite-table-id-html' ).eq(0).html( rowid );
    _template_tr.fadeIn( 'fast' );
    _template_tr.find( '[data-name="cf_options"]' ).prop( 'readonly' , true );
  });

  // update options field
  $(document).on('change', '.sap-infinite-table-row [data-name="cf_type"]', function (ev) {
    let parent_tr = $(this).parents('tr').eq(0);
    
    if(!['dropdown', 'checkbox', 'radio'].includes($(this).val())) {
      parent_tr.find('[data-name="cf_options"]').val('').prop('readonly', true);
    }
    else {
      parent_tr.find('[data-name="cf_options"]').prop('readonly', false);
    }
  });

  // Remvoe field
  $(document).on('click', '.sap-infinite-table-row .sap-infinite-table-row-delete', function (ev) {
    let parent_tr = $(this).parents('tr').eq(0);
    parent_tr.fadeOut('fast', () => parent_tr.remove());
  });

  $('.sap-infinite-table table tbody').sortable({
    axis: 'y'
  });

})