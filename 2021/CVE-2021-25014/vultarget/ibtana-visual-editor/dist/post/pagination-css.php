<?php

if ( ( $attributes['postLayout'] == 'grid' ) && isset( $attributes['isPaginationEnabled'] ) && ( $attributes['isPaginationEnabled'] == true ) ) {

  $paginationTypography =  ( $attributes['paginationTypography'] !== '' ) ? $attributes['paginationTypography'] : 'Open Sans';
  $paginationTypography_url = str_replace( " ", "+", $paginationTypography );

  $paginationFontWeight = isset( $attributes['paginationFontWeight'] ) ? $attributes['paginationFontWeight'] : 400;

  $paginationFontStyle  =  isset( $attributes['paginationFontStyle'] ) ? $attributes['paginationFontStyle'] : '';

  $wraper_before .= '
  @import url("https://fonts.googleapis.com/css2?family=' . $paginationTypography_url . ':wght@' . $paginationFontWeight . '&display=swap");
  #ive-posttype-carousel' . $uniqueID . ' .ive-pagination-wrap, #ive-posttype-carousel' . $uniqueID . ' .ive-loadmore {
    letter-spacing: ' . $attributes['paginationLetterSpacing'] . 'px;
    text-transform: ' . $attributes['paginationTextTransform'] . ';
    font-family:    ' . $paginationTypography . ';
    font-weight:    ' . $paginationFontWeight . ';
    font-style:     ' . $paginationFontStyle . ';
  }
  ';


  $paginationBorderWidth            = $attributes['paginationBorderWidth'];
  $paginationBorderRadius           = $attributes['paginationBorderRadius'];
  $paginationBorderType             = $attributes['paginationBorderType'];


  $paginationTextColor              = $attributes['paginationTextColor'];
  $paginationTextHoverColor         = $attributes['paginationTextHoverColor'];
  $paginationTextActiveColor        = $attributes['paginationTextActiveColor'];

  $paginationBackgroundColor        = $attributes['paginationBackgroundColor'];
  $paginationBackgroundHovColor     = $attributes['paginationBackgroundHovColor'];
  $paginationBackgroundActiveColor  = $attributes['paginationBackgroundActiveColor'];

  $paginationBorderColor            = $attributes['paginationBorderColor'];
  $paginationBorderHovColor         = $attributes['paginationBorderHovColor'];
  $paginationBorderActiveColor      = $attributes['paginationBorderActiveColor'];


  $wraper_before .=  '
  #ive-posttype-carousel' . $uniqueID . ' .ive-pagination-wrap {
      margin-top: 40px;
      margin-bottom: 40px;
  }
  #ive-posttype-carousel' . $uniqueID . ' .ive-pagination, #ive-posttype-carousel' . $uniqueID . ' .ive-loadmore {
      margin: 30px 0px 0px 0px;
  }
  #ive-posttype-carousel' . $uniqueID . ' .ive-loadmore, #ive-posttype-carousel' . $uniqueID . ' .ive-next-prev-wrap ul, #ive-posttype-carousel' . $uniqueID . ' .ive-pagination {
      text-align: left;
  }
  .ive-pagination-wrap .ive-pagination {
      margin: 0 -2px;
      padding: 0;
      list-style: none;
  }
  .ive-pagination-wrap .ive-pagination li {
      padding: 0 2px;
      display: inline-block;
      font-size: 14px;
      line-height: 20px;
      margin: 0 0 4px;
  }
  #ive-posttype-carousel' . $uniqueID . ' .ive-pagination li a:hover,
  #ive-posttype-carousel' . $uniqueID . ' .ive-next-prev-wrap ul li a:hover,
  #ive-posttype-carousel' . $uniqueID . ' .ive-loadmore-action:hover {
      background-image: none;
      background-color: ' . $paginationBackgroundHovColor . ';
      color: ' . $paginationTextHoverColor . ';
      border: ' . $paginationBorderWidth . 'px ' . $paginationBorderType . ' ' . $paginationBorderHovColor . ';
  }
  #ive-posttype-carousel' . $uniqueID . ' .ive-pagination li.pagination-active a {
    background-color: ' . $paginationBackgroundActiveColor . ';
    color: ' . $paginationTextActiveColor . ';
    border: ' . $paginationBorderWidth . 'px ' . $paginationBorderType . ' ' . $paginationBorderActiveColor . ';
  }
  #ive-posttype-carousel' . $uniqueID . ' .ive-pagination li a,
  #ive-posttype-carousel' . $uniqueID . ' .ive-next-prev-wrap ul li a,
  #ive-posttype-carousel' . $uniqueID . ' .ive-loadmore-action {
      background-image: none;
      background-color: ' . $paginationBackgroundColor . ';
      color: ' . $paginationTextColor . ';
      text-decoration: none;
      padding: 8px 14px 8px 14px;
      font-size: 14px;
      line-height: 20px !important;
      border: ' . $paginationBorderWidth . 'px ' . $paginationBorderType . ' ' . $paginationBorderColor . ';
      border-radius: ' . $paginationBorderRadius . 'px;
  }
  #ive-posttype-carousel' . $uniqueID . ' .ive-pagination li a svg {
    fill: ' . $paginationTextColor . ';
  }
  #ive-posttype-carousel' . $uniqueID . '.ive-loading-active .ive-loadmore .ive-loadmore-action svg {
    fill: ' . $paginationTextActiveColor . ';
  }
  .ive-loadmore .ive-loadmore-action {
      -webkit-transition: .4s;
      transition: .4s;
      cursor: pointer;
  }
  .ive-spin {
      -webkit-animation: ive-spin 1s linear infinite;
      animation: ive-spin 1s linear infinite;
          animation-name: ive-spin;
          animation-duration: 1s;
          animation-timing-function: linear;
          animation-delay: 0s;
          animation-iteration-count: infinite;
          animation-direction: normal;
          animation-fill-mode: none;
          animation-play-state: running;
      font-size: inherit;
      width: auto;
      height: auto;
      vertical-align: middle;
      margin-left: 1px;
      position: relative;
      top: -1;
  }
  .ive-loadmore .ive-loadmore-action.ive-disable, .ive-spin {
      display: none;
  }
  .ive-product-btn, .ive-spin {
      line-height: 0;
  }
  .ive-loading-active .ive-spin {
    display: inline-block;
  }
  @keyframes ive-spin {
     0% {
       -webkit-transform: rotate(0deg);
       transform: rotate(0deg);
    }
     to {
       -webkit-transform: rotate(1turn);
       transform: rotate(1turn);
    }
  }
  .ive-loadmore .ive-loadmore-action svg {
      width: 16px;
  }
  .ive-pagination-wrap .ive-pagination li a {
      text-decoration: none;
      display: inline-block;
      -webkit-transition: .4s;
      transition: .4s;
      padding: 10px 20px;
  }
  .ive-pagination-wrap .ive-pagination li.ive-prev-page-numbers svg {
      margin-right: 4px;
  }
  .ive-pagination-wrap .ive-pagination li.ive-next-page-numbers svg {
      margin-left: 4px;
  }
  .ive-pagination-wrap .ive-pagination li a svg {
      width: 10px;
      display: inline-block;
  }
  ';

  // Pagination Alignment for desktop
  if ( isset( $attributes['deskPaginationAlign'] ) ) {
    $wraper_before .=  '
    @media screen and (min-width: 1025px ) {
      #ive-posttype-carousel' . $uniqueID . ' .ive-loadmore, #ive-posttype-carousel' . $uniqueID . ' .ive-next-prev-wrap ul, #ive-posttype-carousel' . $uniqueID . ' .ive-pagination {
          text-align: ' . $attributes['deskPaginationAlign'] . ';
      }
    }
    ';
  }

  // Pagination Alignment for tablet
  if ( isset( $attributes['tabPaginationAlign'] ) ) {
    $wraper_before .=  '
    @media screen and (min-width: 768px) and (max-width: 1024px) {
      #ive-posttype-carousel' . $uniqueID . ' .ive-loadmore, #ive-posttype-carousel' . $uniqueID . ' .ive-next-prev-wrap ul, #ive-posttype-carousel' . $uniqueID . ' .ive-pagination {
          text-align: ' . $attributes['tabPaginationAlign'] . ';
      }
    }
    ';
  }

  // Pagination Alignment for mobile
  if ( isset( $attributes['mobPaginationAlign'] ) ) {
    $wraper_before .=  '
    @media screen and (max-width: 767px) {
      #ive-posttype-carousel' . $uniqueID . ' .ive-loadmore, #ive-posttype-carousel' . $uniqueID . ' .ive-next-prev-wrap ul, #ive-posttype-carousel' . $uniqueID . ' .ive-pagination {
          text-align: ' . $attributes['mobPaginationAlign'] . ';
      }
    }
    ';
  }

}
