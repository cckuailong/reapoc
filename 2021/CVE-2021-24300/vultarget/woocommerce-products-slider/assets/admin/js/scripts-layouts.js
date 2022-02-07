jQuery(document).ready(function($){

    $(document).on('click','.import-layout',function(){
        _this = this;
        post_id = $(this).attr('post_id');

        $(_this).addClass('updating-message');

        $.ajax({
            type: 'POST',
            context: _this,
            url:wcps_ajax.wcps_ajaxurl,
            data: {
                "action" 		: "wcps_ajax_fetch_block_hub_by_id",
                "wcps_ajax_nonce"	: wcps_ajax.ajax_nonce,
                "post_id" 		: post_id,
            },
            success: function( response ) {
                var data = JSON.parse( response );

                //console.log(wcps_ajax);
                //console.log(post_id);


                post_data = data['post_data'];
                download_count = data['download_count'];
                is_saved = data['is_saved'];

                console.log(is_saved);

                if(is_saved == 'yes'){
                    $(this).addClass('saved');
                    $(this).text('Saved');
                }else{
                    $(this).addClass('saved');
                    $(this).text('Not Saved');
                }



                $(_this).removeClass('updating-message');
            }
        });

    })



    $(document).on('mouseover','.block-thumb',function(){



        imgHeight = $(this).children('img').height();
        wrapHeight = $(this).height();

        if (imgHeight > wrapHeight) {
            var animationOffset = wrapHeight - imgHeight;
            var speed = 2000;
            $(this).children('img').animate({
                "marginTop": animationOffset + "px"
            }, speed);
        }


    })


    $(document).on('mouseout','.block-thumb',function(){



        imgHeight = $(this).children('img').height();
        wrapHeight = $(this).height();

        //console.log(wrapHeight);
        //console.log(imgHeight);

        if (imgHeight > wrapHeight) {

            animationOffset = 0;
            var speed = 1000;
            $(this).children('img').animate({
                "marginTop": animationOffset + "px"
            }, speed);

        }


    })



});