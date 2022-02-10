<?php
// Pagination code starts here
if ( isset( $attributes['isPaginationEnabled'] ) && ( $attributes['isPaginationEnabled'] == true ) ) {

  // get_page_number
  $post_number  = $recent_posts->found_posts;
  $pageNum  = 1;
  if( $post_number > 0 ) {
    $post_per_page = $attributes['postscount'];
    $pages = floor( $post_number / $post_per_page );
    $pageNum  = $pages ? $pages : 1;
  }
  // get_page_number

  if ( $attributes['paginationType'] == 'pagination' ) {


    $paginationNav  = isset( $attributes['paginationNav'] ) ? $attributes['paginationNav'] : 'textArrow';
    $range = 1;
    $html = '';
    $showitems = 3;
    $paged = is_front_page() ? get_query_var('page') : get_query_var('paged');
    $paged = $paged ? $paged : 1;
    if($pageNum == '') {
      global $wp_query;
      $pageNum = $wp_query->max_num_pages;
      if( !$pageNum ) {
        $pageNum = 1;
      }
    }
    $data = ( $paged >= 3 ? [ ( $paged-1 ), $paged, ( $paged+1 ) ] : [ 1, 2, 3 ] );

    $wraper_after .=  '<div class="ive-pagination-wrap">';
    if( 1 != $pageNum ) {
      $html .= '<ul class="ive-pagination">';
      $display_none = 'style="display:none"';
      if( $pageNum > 4 ) {
        $html .= '<li class="ive-prev-page-numbers" ' . ( $paged==1 ? $display_none : "" ) . '>
          <a href="' . get_pagenum_link( $paged - 1 ) . '">' .
          '<svg enable-background="new 0 0 477.175 477.175" version="1.1" viewBox="0 0 477.18 477.18">
            <path d="m145.19 238.58 215.5-215.5c5.3-5.3 5.3-13.8 0-19.1s-13.8-5.3-19.1 0l-225.1 225.1c-5.3 5.3-5.3 13.8 0 19.1l225.1 225c2.6 2.6 6.1 4 9.5 4s6.9-1.3 9.5-4c5.3-5.3 5.3-13.8 0-19.1l-215.4-215.5z"/>
          </svg>' .
          ' ' .
          ( $paginationNav == 'textArrow' ? __( "Previous", "ibtana-visual-editor" ) : "" ) .
          '</a>
        </li>';
      }
      if( $pageNum > 4 ) {
        $html .= '<li class="ive-first-pages" '.( $paged < 2 ? $display_none : "" ).' data-current="1"><a href="' . get_pagenum_link( 1 ) .'">1</a></li>';
      }
      if( $pageNum > 4 ) {
        $html .= '<li class="ive-first-dot" ' . ( $paged < 2 ? $display_none : "" ) . '><a href="#">...</a></li>';
      }
      foreach ( $data as $i ) {
        if( $pageNum >= $i ) {
          $html .= ($paged == $i) ? '<li class="ive-center-item pagination-active" data-current="'.$i.'"><a href="' . get_pagenum_link( $i ) . '">'.$i.'</a></li>':'<li class="ive-center-item" data-current="'.$i.'"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
        }
      }
      if( $pageNum > 4 ) {
        $html .= '<li class="ive-last-dot" ' . ( $pageNum <= $paged + 1 ? $display_none : "" ) . '><a href="#">...</a></li>';
      }
      if( $pageNum > 4 ) {
      $html .= '<li class="ive-last-pages" '.( $pageNum <= $paged + 1 ? $display_none : "" ) . ' data-current="'.$pageNum.'">
        <a href="' . get_pagenum_link( $pageNum ) . '">' . $pageNum . '</a>
      </li>';
      }
      if ($paged != $pageNum) {
      $html .= '<li class="ive-next-page-numbers"><a href="' . get_pagenum_link( $paged + 1 ) .'">' .
          ( $paginationNav == 'textArrow' ? __( "Next", "ibtana-visual-editor" ) : "" ) .
          '<svg enable-background="new 0 0 477.175 477.175" version="1.1" viewBox="0 0 477.18 477.18">
            <path d="m360.73 229.08-225.1-225.1c-5.3-5.3-13.8-5.3-19.1 0s-5.3 13.8 0 19.1l215.5 215.5-215.5 215.5c-5.3 5.3-5.3 13.8 0 19.1 2.6 2.6 6.1 4 9.5 4s6.9-1.3 9.5-4l225.1-225.1c5.3-5.2 5.3-13.8 0.1-19z"/>
          </svg>'.
        '</a>
      </li>';
      }
      $html .= '</ul>';
    }
    $wraper_after .= $html;

    $wraper_after .=  '</div>';



  } elseif ( $attributes['paginationType'] == 'loadmore' ) {
    $wraper_after .= '<div class="ive-loadmore">';
      $wraper_after .= '<span class="ive-loadmore-action" data-pages="'.$pageNum.'" data-pagenum="1" data-blockid="'. $uniqueID .'" data-blockname="ive_ive-productscarousel" data-postid="' . $page_id . '">Load More <span class="ive-spin">'.
      '<svg enable-background="new 0 0 491.236 491.236" version="1.1" viewBox="0 0 491.24 491.24">
        <path d="m55.89 262.82c-3-26-0.5-51.1 6.3-74.3 22.6-77.1 93.5-133.8 177.6-134.8v-50.4c0-2.8 3.5-4.3 5.8-2.6l103.7 76.2c1.7 1.3 1.7 3.9 0 5.1l-103.6 76.2c-2.4 1.7-5.8 0.2-5.8-2.6v-50.3c-55.3 0.9-102.5 35-122.8 83.2-7.7 18.2-11.6 38.3-10.5 59.4 1.5 29 12.4 55.7 29.6 77.3 9.2 11.5 7 28.3-4.9 37-11.3 8.3-27.1 6-35.8-5-21.3-26.6-35.5-59-39.6-94.4zm299.4-96.8c17.3 21.5 28.2 48.3 29.6 77.3 1.1 21.2-2.9 41.3-10.5 59.4-20.3 48.2-67.5 82.4-122.8 83.2v-50.3c0-2.8-3.5-4.3-5.8-2.6l-103.7 76.2c-1.7 1.3-1.7 3.9 0 5.1l103.6 76.2c2.4 1.7 5.8 0.2 5.8-2.6v-50.4c84.1-0.9 155.1-57.6 177.6-134.8 6.8-23.2 9.2-48.3 6.3-74.3-4-35.4-18.2-67.8-39.5-94.4-8.8-11-24.5-13.3-35.8-5-11.8 8.7-14 25.5-4.8 37z"/>
      </svg>'.
      '</span></span>';
    $wraper_after .= '</div>';

    ob_start();
    ?>
    <script type="text/javascript">
      // *************************************
      // Loadmore Append
      // *************************************
      $('.ive-loadmore-action').on('click', function(e){
          e.preventDefault();

          let that    = $(this),
              parents = that.closest('.ive-block-wrapper'),
              paged   = parseInt(that.data('pagenum')),
              pages   = parseInt(that.data('pages'));

          if( that.hasClass( 'ive-disable' ) ){
              return
          }else{
              paged++;
              that.data('pagenum', paged);
              if(paged == pages){
                  $(this).addClass('ive-disable');
              }else{
                  $(this).removeClass('ive-disable');
              }
          }

          $.ajax({
              url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
              type: 'POST',
              data: {
                action:     'ive_load_more',
                paged:      paged,
                blockId:    that.data('blockid'),
                postId:     that.data('postid'),
                blockName:  that.data('blockname'),
                isAjax:     true,
                wpnonce:    '<?php echo wp_create_nonce( 'posttype_slider_nonce' ); ?>'
              },
              beforeSend: function() {
                  parents.addClass( 'ive-loading-active' );
              },
              success: function( data ) {
                $( 'div[id*="' + that.data('blockid') + '"] .row:first' ).append( data );
              },
              complete:function() {
                  parents.removeClass( 'ive-loading-active' );
              },
              error: function( xhr ) {
                parents.removeClass('ive-loading-active');
              },
          });
      });
    </script>
    <?php
    $wraper_after .=  ob_get_clean();
  }


}
// Pagination code ends here
