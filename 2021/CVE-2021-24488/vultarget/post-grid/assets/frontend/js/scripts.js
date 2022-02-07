jQuery(document).ready(function($){

    $(document).on('keyup', '.post-grid .nav-search .search', function(e){

        var keyword = $(this).val();
        var grid_id = $(this).attr('grid_id');
        var key = e.which;



        if(key == 13){
            // the enter key code
            var is_reset = 'yes';
            $('#post-grid-'+grid_id+' .search-icon').html('<i class="fas fa-spin fa-spinner"></i>');
            $('.pagination').fadeOut();
            $.ajax({
                type: 'POST',
                context: this,
                url:post_grid_ajax.post_grid_ajaxurl,
                data: {"action": "post_grid_ajax_search", "grid_id":grid_id,"keyword":keyword,"is_reset":is_reset,},
                success: function(data){

                    console.log(data);

                    $('#post-grid-'+grid_id+' .grid-items').html(data);
                    $('#post-grid-'+grid_id+' .search-icon').html('<i class="fas fa-search"></i>');
                }
            });
        }
        else{
            var is_reset = 'no';
            if(keyword.length>3){
                $('#post-grid-'+grid_id+' .search-icon').html('<i class="fas fa-spin fa-spinner"></i>');

                $('.pagination').fadeOut();

                $.ajax({
                    type: 'POST',
                    context: this,
                    url:post_grid_ajax.post_grid_ajaxurl,
                    data: {"action": "post_grid_ajax_search", "grid_id":grid_id,"keyword":keyword,"is_reset":is_reset,},
                    success: function(data){

                        $('#post-grid-'+grid_id+' .grid-items').html(data);
                        $('#post-grid-'+grid_id+' .search-icon').html('<i class="fas fa-search"></i>');
                    }
                });
            }
        }
    })


});






