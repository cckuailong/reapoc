( function( $ ) {
    $('.formSubmissionsTab').css('display','block');
    
    $('.entryViewBtn').on('click',function(){

      $(this).parent().parent().addClass('activeViewedEntry')
      $('.formFieldsPreviewTable').html('');

      thisEntryFields = $(this).parent().siblings().clone();

      $.each(thisEntryFields, function(index, value){
        $('.formFieldsPreviewTable').append(value);
      });

      $('.formFieldsPreviewTable td').wrap('<tr></tr>');

      $('.pb_preview_fields_container').show();

      $('.formEntriesPreviewClose').on('click', function(){
        
        $('.pb_preview_fields_container').hide();

        $('.ulpb_form_data_table tr').removeClass('activeViewedEntry');
      });

    });
    
  }( jQuery ) );

( function( $ ) {
  $(document).ready(function(){
    $('.ulpb_form_data_table').after('<div id="formDataNav" class="w3-bar w3-blue"></div>');
    var rowsShown = 15;
    var rowsTotal = $('.ulpb_form_data_table tbody tr').length;
    var numPages = rowsTotal/rowsShown;
    for(i = 0;i < numPages;i++) {
        var pageNum = i + 1;
        $('#formDataNav').append('<a href="#" rel="'+i+'">'+pageNum+'</a> ');
    }
    $('.ulpb_form_data_table tbody tr').hide();
    $('.topHeaderRow_formTable').show();
    $('.ulpb_form_data_table tbody tr').slice(0, rowsShown).show();
    $('#formDataNav a:first').addClass('active');
    $('#formDataNav a').on('click', function(){

        $('#formDataNav a').removeClass('active');
        $(this).addClass('active');
        var currPage = $(this).attr('rel');
        var startItem = currPage * rowsShown;
        var endItem = startItem + rowsShown;
        $('.ulpb_form_data_table tbody tr').css('opacity','0.0').hide().slice(startItem, endItem).
        css('display','table-row').animate({opacity:1}, 300);
    });
  });

}( jQuery ) );