( function( $ ) { 

$( document ).on('click',".addNewRowVisible", function() {


    pageBuilderApp.rowList.add( {
        rowID: 'ulpb_Row'+Math.floor((Math.random() * 200000) + 100),
        rowHeight: 100,
        columns: 0,
        rowData: {
          rowCustomClass:'',
          bg_color: '#fff',
          bg_img: '',
          margin: {
            rowMarginTop: 0,
            rowMarginBottom: 0,
            rowMarginLeft: 0,
            rowMarginRight: 0,
          },
          marginTablet:{
            rMTT:'',
            rMBT:'',
            rMLT:'',
            rMRT:'',
          },
          marginMobile:{
            rMTM:'',
            rMBM:'',
            rMLM:'',
            rMRM:'',
          },
          padding:{
            rowPaddingTop: 0,
            rowPaddingBottom: 0,
            rowPaddingLeft: 0,
            rowPaddingRight: 0,
          },
          paddingTablet:{
            rPTT:'',
            rPBT:'',
            rPLT:'',
            rPRT:'',
          },
          paddingMobile:{
            rPTM:'',
            rPBM:'',
            rPLM:'',
            rPRM:'',
          },
          video:{
            rowBgVideoEnable: 'false',
            rowBgVideoLoop: 'loop',
            rowVideoMpfour: '',
            rowVideoWebM: '',
            rowVideoThumb: '',
          },
          customStyling: '',
          customJS: '',
          rowBackgroundType:'solid',
          rowGradient:{
            rowGradientColorFirst: '#dd9933',
            rowGradientLocationFirst:'40',
            rowGradientColorSecond:'#eeee22',
            rowGradientLocationSecond:'60',
            rowGradientType:'linear',
            rowGradientPosition:'top left',
            rowGradientAngle:'135',
          },
          rowHoverOptions: {
            rowBgColorHover:'',
            rowBackgroundTypeHover:'',
            rowHoverTransitionDuration:'',
            rowGradientHover:{
              rowGradientColorFirstHover: '',
              rowGradientLocationFirstHover:'',
              rowGradientColorSecondHover:'',
              rowGradientLocationSecondHover:'',
              rowGradientTypeHover:'linear',
              rowGradientPositionHover:'top left',
              rowGradientAngleHover:'',
            }
          },
          rowOverlayBackgroundType: '',
          rowOverlayGradient:{
            rowOverlayGradientColorFirst:  '',
            rowOverlayGradientLocationFirst: '',
            rowOverlayGradientColorSecond: '',
            rowOverlayGradientLocationSecond: '',
            rowOverlayGradientType: '',
            rowOverlayGradientPosition: '',
            rowOverlayGradientAngle: '',
          },
          rowHideOnDesktop:'',
          rowHideOnTablet:'',
          rowHideOnMobile:'',
          bgSTop: {
            rbgstType: 'none',
            rbgstColor:'#e3e3e3',
            rbgstWidth:'100',
            rbgstWidtht:'',
            rbgstWidthm:'',
            rbgstHeight:'200',
            rbgstHeightt:'',
            rbgstHeightm:'',
            rbgstFlipped:'none',
            rbgstFront:'back',
          },
          bgSBottom: {
            rbgsbType: 'none',
            rbgsbColor:'#e3e3e3',
            rbgsbWidth:'100',
            rbgsbWidtht:'',
            rbgsbWidthm:'',
            rbgsbHeight:'200',
            rbgsbHeightt:'',
            rbgsbHeightm:'',
            rbgsbFlipped:'none',
            rbgsbFront:'back',
          },
        }
    });


  $('#newRowClose').on('click',function(){
      $('.new_row_div').slideUp();
      $('#ulpb_row_controls').hide();
  });

    $('.ulpb_row_controls').hide();
  

});


$('.addNewGlobalRowVisible').on('click', function(){

  $('.insert_Global_row').show(15);
        
      $('.addNewGlobalRowClosebutton').one('click',function(){
                $('.globalRowRetrievedAttributes').val('');
                selectGlobalRowToInsert = $('.selectGlobalRowToInsert').val();

                if (selectGlobalRowToInsert != '') {
                  getGlobalRowDataFromDb(selectGlobalRowToInsert);
                }
                
                retrievedGlobalRowAttributes = $('.globalRowRetrievedAttributes').val();
                
                if (retrievedGlobalRowAttributes != '') {
                  pageBuilderApp.rowList.add(  JSON.parse(retrievedGlobalRowAttributes));
                }

      $('.insert_Global_row').hide(150);


  });

});
  



}( jQuery ) );