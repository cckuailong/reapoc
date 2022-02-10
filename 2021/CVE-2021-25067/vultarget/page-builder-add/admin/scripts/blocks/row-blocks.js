	( function( $ ) {
        
        /*
        $(document).ready(function(){

            var isProActive = 'false';
            var pluginOpsTemplates = {

                
                'PT-0' : {
                    tempname: 0,
                    tempCat:'Blank',
                    isPro: false,
                },
                'PT-1' : {
                    tempname: 85,
                    tempCat:'Lead Generation',
                    isPro: false,
                },
                'PT-2' : {
                    tempname: 84,
                    tempCat:'Lead Generation',
                    isPro: false,
                },
                'PT-3' : {
                    tempname: 83,
                    tempCat:'Lead Generation',
                    isPro: false,
                },
                'PT-4' : {
                    tempname: 78,
                    tempCat:'Lead Generation',
                    isPro: false,
                },
                'PT-5' : {
                    tempname: 77,
                    tempCat:'Lead Generation',
                    isPro: false,
                },
                'PT-6' : {
                    tempname: 82,
                    tempCat:'Lead Generation',
                    isPro: false,
                },
                'PT-7' : {
                    tempname: 86,
                    tempCat:'Lead Generation',
                    isPro: false,
                },
                'PT-8' : {
                    tempname: 68,
                    tempCat:'Lead Generation',
                    isPro: false,
                },
                'PT-9' : {
                    tempname: 80,
                    tempCat:'Lead Generation',
                    isPro: false,
                },
                'PT-10' : {
                    tempname: 87,
                    tempCat:'Lead Generation',
                    isPro: false,
                },

            }


            templateImageDirectory = popb_admin_vars_data.plugin_url+'/images/templates/thumbs/';
            
            $.each(pluginOpsTemplates, function(index,value){

                var tempname = value['tempname'];

                $('.pluginops-template-container').append(

                    '<div id="card" class="temp-prev-'+tempname+' card tempPaca template-card" data-templateid="temp'+tempname+'">'+

                        '<div id="temp-prev-'+tempname+'" class="tempPrev tempPaca"> <p id="temp-prev-'+tempname+'"><b>Preview</b></p></div>'+

                        '<label for="temp-'+tempname+'"> <img src="'+templateImageDirectory+'template-'+tempname+'.png" data-img_src="https://ps.w.org/page-builder-add/assets/screenshot-'+tempname+'.png" class="card-img temp-prev-'+tempname+'" >'+
                        '<p class="card-desc"></p> </label>'+
                        '<div id="updateTemplate" class="updateTemplate" data-templateid="temp'+tempname+'">'+
                            '<i class="fa fa-download" aria-hidden="true" style="font-size: 11px;" data-templateid="temp'+tempname+'"></i> Insert'+
                        '</div>'+
                        '<span class="temp-cats-displayed">'+ value['tempCat'] +'</span>'+

                    '</div>'

                );


            });



            $(document).on('mouseover','.card',function(ev) {

              if ( $(ev.target).hasClass('updateTemplate') ) {

              }else{
                
                $('.card').children('.ui-effects-placeholder').remove();
                $('.card').css('background-color','#fff');

                $(this).children('.updateTemplate').css('opacity','1');

                if ( $(this).hasClass('tempPack1') || $(this).hasClass('tempPaca') ) {
                }else{

                }

              }
                
            });



            $(document).on('mouseover','.card-img',function(ev) {
              var tempprevbtn = $(ev.target).attr('class').split(' ')[1];
              $('#'+tempprevbtn).width($(ev.target).width());
              $('#'+tempprevbtn).height($(ev.target).height());
              var tempPhieght = $(ev.target).height();
              $('.tempPrev p').css('margin-top',tempPhieght/2);
              $('#'+tempprevbtn).slideDown(100);
            });
            $(document).on('mouseleave','.card',function(ev){
              $('.tempPrev').slideUp('100');
              $('.updateTemplate').css('opacity','0');
              $('.card').css('background-color','#fff');
            });

            $(document).on('click','.tempPrev',function(ev) {
              var ths_tempprev = $(ev.target).attr('id');
              if (typeof(ths_tempprev) == 'undefined') { var ths_tempprev = $(ev.target).parent().attr('id'); }
              $('.pb_preview_container').attr('style','display:block;overflow:auto;');
              $('.pb_temp_prev').append('<img src='+$('img.'+ths_tempprev).attr('data-img_src')+' class="pb_temp_prev_img" >');
            });

            $('.pb_preview_container').on('click',function(){
              $('.pb_preview_container').attr('style','display:none;');
              $('.pb_temp_prev').html(' ');
            });



        });
        */

        
        $(document).ready(function(){
            var isProActive = 'false';
            var rowBlockNames = {
                'RB-1':{
                    tempname : 1,
                    tempCat:'Header',
                    isPro:false,
                },
                'RB-2':{
                    tempname : 2,
                    tempCat:'Header',
                    isPro:false,
                },
                'RB-3':{
                    tempname : 3,
                    tempCat:'Header',
                    isPro:false,
                },
                'RB-4':{
                    tempname : 4,
                    tempCat:'Header',
                    isPro:false,
                },
                'RB-5':{
                    tempname : 5,
                    tempCat:'Feature',
                    isPro:false,
                },
                'RB-6':{
                    tempname : 6,
                    tempCat:'Feature Text',
                    isPro:false,
                },
                'RB-7':{
                    tempname : 7,
                    tempCat:'Feature',
                    isPro:false,
                },
                'RB-8':{
                    tempname : 8,
                    tempCat:'Feature Text',
                    isPro:false,
                },
                'RB-9':{
                    tempname : 9,
                    tempCat:'Text',
                    isPro:false,
                },
                'RB-10':{
                    tempname : 10,
                    tempCat:'Feature Text',
                    isPro:false,
                },
                'RB-11':{
                    tempname : 11,
                    tempCat:'Pricing',
                    isPro:false,
                },
                'RB-12':{
                    tempname : 12,
                    tempCat:'Pricing',
                    isPro:false,
                },
                'RB-13':{
                    tempname : 13,
                    tempCat:'Pricing',
                    isPro:false,
                },
                'RB-14':{
                    tempname : 14,
                    tempCat:'Pricing',
                    isPro:false,
                },
                'RB-15':{
                    tempname : 15,
                    tempCat:'Pricing',
                    isPro:false,
                },
                'RB-16':{
                    tempname : 16,
                    tempCat:'Testimonial',
                    isPro:false,
                },
                'RB-17':{
                    tempname : 17,
                    tempCat:'Feature',
                    isPro:false,
                },
                'RB-18':{
                    tempname : 18,
                    tempCat:'Footer Call To Action',
                    isPro:false,
                },
                'RB-19':{
                    tempname : 19,
                    tempCat:'Testimonial',
                    isPro:false,
                },
                'RB-20':{
                    tempname : 20,
                    tempCat:'Header',
                    isPro:false,
                },
                'RB-21':{
                    tempname : 21,
                    tempCat:'Call To Action',
                    isPro:false,
                },
                'RB-22':{
                    tempname : 22,
                    tempCat:'Call To Action',
                    isPro:false,
                },
                'RB-23':{
                    tempname : 23,
                    tempCat:'Feature',
                    isPro:false,
                },
                'RB-24':{
                    tempname : 24,
                    tempCat:'Feature',
                    isPro:false,
                },
                'RB-25':{
                    tempname : 25,
                    tempCat:'Testimonial',
                    isPro:false,
                },
                'RB-26':{
                    tempname : 26,
                    tempCat:'Pricing',
                    isPro:false,
                },
                'RB-27':{
                    tempname : 27,
                    tempCat:'Call To Action , Footer',
                    isPro:false,
                },
                'RB-28':{
                    tempname : 28,
                    tempCat:'Call To Action',
                    isPro:false,
                },
                'RB-29':{
                    tempname : 29,
                    tempCat:'Call To Action',
                    isPro:false,
                },
                'RB-30':{
                    tempname : 30,
                    tempCat:'Call To Action',
                    isPro:false,
                },
            };
            $.each(rowBlockNames, function(index,val){
                if (val['isPro'] == true  && isProActive == 'false') {
                    var insertBtn = '<div class="rowBlockProUpdateBtn" data-rowBlockName="'+'protemp'+'"> Pro <i class="fa fa-ban"></i> </div>';
                }else{
                    var insertBtn = '<div class="rowBlockUpdateBtn" data-rowBlockName="'+val['tempname']+'"> Insert <i class="fa fa-download" data-rowBlockName="'+val['tempname']+'" ></i> </div>';
                }

                $('#rowBlocksContainer').append(
                    '<div id="rowBlock" class="rowBlock-'+val['tempname']+' rowBlock template-card">'
                        +'<div id="rowBlock-'+val['tempname']+'" class="tempPrev"> <p id="rowBlock-'+val['tempname']+'"><b>Preview</b></p></div>'
                        +'<label for="rowBlock-'+val['tempname']+'"> <img src="'+pluginURL+'/images/templates/rowBlocks/'+val['tempname']+'.png" data-img_src="https://ps.w.org/page-builder-add/assets/screenshot-'+val['tempname']+'.png" class="card-img rowBlock-'+val['tempname']+'" loading="lazy" >'
                        +'<p class="card-desc"></p> </label>'
                        +insertBtn
                        +'<span class="block-cats-displayed">'+val['tempCat']+'</span>'
                    +'</div>'
                );
            });

            jQuery('.rowBlocksFilterSelector').on('change', function(){
                var WidgetSearchQuery =  jQuery(this).val();
                jQuery('.rowBlock').hide();
                
                jQuery('.rowBlock:contains("'+WidgetSearchQuery+'")').show();

                if (WidgetSearchQuery == 'All') {
                  jQuery('.rowBlock').show();
                }
            });


            if (isProActive == 'true') {
              $('.nonPremUserNotice').css('display','none');
            }
        });



    }( jQuery ) );
