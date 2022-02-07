<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

function post_grid_layout_css($layout){

    $css = array();
    $css['flat'] = '.__ID__ a{text-decoration:none}
.__ID__ {
  vertical-align: top;
}
.__ID__ .layer-media{}
.__ID__ .layer-content {
}';

    $css['flip-x'] = '.__ID__ a{text-decoration:none}
.__ID__ {
  overflow: hidden;
  padding: 0;
  -webkit-perspective: 600px;
  perspective: 600px;
  position: relative;
  vertical-align: top;
  width: 100%;
}
.__ID__:hover .layer-media {
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  -webkit-transform: rotateX(180deg);
  transform: rotateX(180deg);
  z-index: 900;
}
.__ID__ .layer-media {
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  float: none;
  height: inherit;
  left: 0;
  text-align: center;
  top: 0;
  -webkit-transform: rotateX(0deg) rotateY(0deg);
  transform: rotateX(0deg) rotateY(0deg);
  -webkit-transform-style: preserve-3d;
  transform-style: preserve-3d;
  -webkit-transition: all 0.4s ease-in-out 0s;
  transition: all 0.4s ease-in-out 0s;
  width: inherit;
  z-index: 900;
}
.__ID__:hover .layer-content  {
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  -webkit-transform: rotateX(0deg) rotateY(0deg);
  transform: rotateX(0deg) rotateY(0deg);
  z-index: 1000;
}
.__ID__ .layer-content {
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  float: none;
  height: inherit;
  left: 0;
  padding: 0;
  position: absolute;
  top: 0;
  -webkit-transform: rotateX(-179deg);
  transform: rotateX(-179deg);
  -webkit-transform-style: preserve-3d;
  transform-style: preserve-3d;
  -webkit-transition: all 0.4s ease-in-out 0s;
  transition: all 0.4s ease-in-out 0s;
  width: inherit;
  z-index: 800;
  padding:10px;
}
';

    $css['flip-y'] = '.__ID__ {
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  overflow: hidden;
  padding: 0;
  -webkit-perspective: 600px;
  perspective: 600px;
  position: relative;
  vertical-align: top;
  width: 100%;
}
.__ID__:hover .layer-media {
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  -webkit-transform: rotateY(180deg);
  transform: rotateY(180deg);
  z-index: 900;
}
.__ID__ .layer-media {
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  float: none;
  height: inherit;
  left: 0;
  text-align: center;
  top: 0;
  -webkit-transform: rotateX(0deg) rotateY(0deg);
  transform: rotateX(0deg) rotateY(0deg);
  -webkit-transform-style: preserve-3d;
  transform-style: preserve-3d;
  -webkit-transition: all 0.4s ease-in-out 0s;
  transition: all 0.4s ease-in-out 0s;
  width: inherit;
  z-index: 900;
}
.__ID__:hover .layer-content  {
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  -webkit-transform: rotateX(0deg) rotateY(0deg);
  transform: rotateX(0deg) rotateY(0deg);
  z-index: 1000;
}
.__ID__ .layer-content {
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  float: none;
  height: inherit;
  left: 0;
  position: absolute;
  top: 0;
  -webkit-transform: rotateY(-179deg);
  transform: rotateY(-179deg);
  -webkit-transform-style: preserve-3d;
  transform-style: preserve-3d;
  -webkit-transition: all 0.4s ease-in-out 0s;
  transition: all 0.4s ease-in-out 0s;
  width: inherit;
  z-index: 800;
}';
    $css['zoomin'] = '
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: scale(1.5);
  -ms-transform: scale(1.5);
  transform: scale(1.5);
  opacity: 0;
  filter: alpha(opacity=0);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  width: 100%;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}
.__ID__ .layer-hover {
  display: none;
}';

    $css['zoomout'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: scale(0);
  -ms-transform: scale(0);
  transform: scale(0);
  opacity: 0;
  filter: alpha(opacity=0);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  width: 100%;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}';
    $css['thumbzoomcontentin'] = '
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: scale(1.5);
  -ms-transform: scale(1.5);
  transform: scale(1.5);
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-media {
  -webkit-transition: all 0.3s ease 0s;
  transition: all 0.3s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-media {
  filter: url(\'data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg"><filter id="filter"><feComponentTransfer color-interpolation-filters="sRGB"><feFuncR type="linear" slope="0.3" /><feFuncG type="linear" slope="0.3" /><feFuncB type="linear" slope="0.3" /></feComponentTransfer></filter></svg>#filter\');
  -webkit-filter: brightness(0.3);
  filter: brightness(0.3);

}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  margin-top: 30%;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  -webkit-transition: all 0.3s ease 0s;
  transition: all 0.3s ease 0s;
  width: 100%;
}';
    $css['spinrightzoom'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  opacity: 0.6;
  filter: alpha(opacity=60);
  -webkit-transform: rotate(20deg) scale(1.5);
  -ms-transform: rotate(20deg) scale(1.5);
  transform: rotate(20deg) scale(1.5);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  width: 100%;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}';
    $css['spinleft'] = '
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: rotate(-30deg);
  -ms-transform: rotate(-30deg);
  transform: rotate(-30deg);
  opacity: 0;
  filter: alpha(opacity=0);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  width: 100%;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}';
    $css['spinleftzoom'] = '
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  opacity: 0.7;
  filter: alpha(opacity=70);
  -webkit-transform: rotate(-30deg) scale(1.5);
  -ms-transform: rotate(-30deg) scale(1.5);
  transform: rotate(-30deg) scale(1.5);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  width: 100%;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}';
    $css['spinrightfast'] = '
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}

.__ID__:hover .layer-media {
  -webkit-transform: rotate(180deg);
  -ms-transform: rotate(180deg);
  transform: rotate(180deg);
  opacity: 0;
  filter: alpha(opacity=0);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  width: 100%;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}';
    $css['spinleftfast'] = '
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: rotate(-180deg);
  -ms-transform: rotate(-180deg);
  transform: rotate(-180deg);
  opacity: 0;
  filter: alpha(opacity=0);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  width: 100%;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}';
    $css['thumbgoright'] = '
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  opacity: 0;
  filter: alpha(opacity=0);
  -webkit-transform: scale(0.5) translateX(100%);
  -ms-transform: scale(0.5) translateX(100%);
  transform: scale(0.5) translateX(100%);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}';
    $css['thumbgotop'] = '
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  opacity: 0;
  filter: alpha(opacity=0);
  top: -100%;
  -webkit-transform: translateY(-100%) scale(0.6);
  -ms-transform: translateY(-100%) scale(0.6);
  transform: translateY(-100%) scale(0.6);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}
';
    $css['thumbgobottom'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  bottom: -100%;
  opacity: 0;
  filter: alpha(opacity=0);
  -webkit-transform: scale(0.5) translateY(100%);
  -ms-transform: scale(0.5) translateY(100%);
  transform: scale(0.5) translateY(100%);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  width: 100%;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}';
    $css['thumbmiddle'] = '
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: scale(0.9);
  -ms-transform: scale(0.9);
  transform: scale(0.9);
  opacity: 0.3;
  filter: alpha(opacity=30);
  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
  z-index:10;
  -webkit-transform: scale(0.9);
  -ms-transform: scale(0.9);
  transform: scale(0.9);
  padding:5px;
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  width: 100%;
}';
    $css['thumbskew'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: scale(0.9) skew(5deg);
  -ms-transform: scale(0.9) skew(5deg);
  transform: scale(0.9) skew(5deg);
  opacity: 0.3;
  filter: alpha(opacity=30);
  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
  z-index:10;
  -webkit-transform: scale(0.9) ;
  -ms-transform: scale(0.9) ;
  transform: scale(0.9) ;
  padding:5px;
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  width: 100%;
}
';
    $css['contentbottom'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}

.__ID__:hover .layer-media {
  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
}
.__ID__ .layer-content {
  background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
  bottom: 0;
  color: rgb(255, 255, 255);
  left: 0;
  position: absolute;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  width: 100%;
}
';
    $css['contentmiddle'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
}
.__ID__ .layer-content {
  background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
  bottom: 50%;
  color: rgb(255, 255, 255);
  left: 0;
  position: absolute;
  -webkit-transform: translate(0px, 50%);
  -ms-transform: translate(0px, 50%);
  transform: translate(0px, 50%);
  width: 100%;
}';
    $css['contentinbottom'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content {
  bottom: 0;
}
.__ID__ .layer-content {
  background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
  bottom: -100%;
  color: rgb(255, 255, 255);
  left: 0;
  position: absolute;
  -webkit-transition: all 0.5s ease 0s;
  transition: all 0.5s ease 0s;
  width: 100%;
}';
    $css['contentinleft'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content {
  left: 0;
}
.__ID__ .layer-content {
  background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
  color: rgb(255, 255, 255);
  height: 100%;
  left: -100%;
  position: absolute;
  top: 0;
  -webkit-transition: all 0.5s ease 0s;
  transition: all 0.5s ease 0s;
  width: 100%;
}
.__ID__ .layer-hover {
  display: none;
}
';
    $css['contentinright'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content {
  left: 0;
}
.__ID__ .layer-content {
  background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
  color: rgb(255, 255, 255);
  height: 100%;
  left: 100%;
  position: absolute;
  top: 0;
  -webkit-transition: all 0.5s ease 0s;
  transition: all 0.5s ease 0s;
  width: 100%;
}';
    $css['contentinrightfixtitle'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {

  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content {
  left: 0;
}
.__ID__ .layer-content {
  background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
  color: rgb(255, 255, 255);
  height: 100%;
  left: 100%;
  position: absolute;
  top: 0;
  -webkit-transition: all 0.5s ease 0s;
  transition: all 0.5s ease 0s;
  width: 100%;
}
.__ID__ .layer-hover {
  display: none;
}
.__ID__ .layer-media .title {
  margin: 0 auto;
  position: absolute;
  top: 50%;
  transition: all 1s ease 0s;
  width: 100%;
}
.__ID__:hover .layer-media .title {
  transform: scale(0);
}';
    $css['thumbgoleftconetntinright'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: translateX(-100%);
  -ms-transform: translateX(-100%);
  transform: translateX(-100%);
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content {
  left: 0;
}
.__ID__ .layer-content {
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  height: 100%;
  left: 100%;
  position: absolute;
  top: 0;
  -webkit-transition: all 0.5s ease 0s;
  transition: all 0.5s ease 0s;
  width: 100%;
}';
    $css['thumbgobottomconetntinright'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: translateY(100%);
  -ms-transform: translateY(100%);
  transform: translateY(100%);
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content {
  left: 0;
}
.__ID__ .layer-content {
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  height: 100%;
  left: 100%;
  position: absolute;
  top: 0;
  -webkit-transition: all 0.5s ease 0s;
  transition: all 0.5s ease 0s;
  width: 100%;
}';
    $css['thumbgotopconetntinright'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: translateY(-100%);
  -ms-transform: translateY(-100%);
  transform: translateY(-100%);
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content {
  left: 0;
}
.__ID__ .layer-content {
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  height: 100%;
  left: 100%;
  position: absolute;
  top: 0;
  -webkit-transition: all 0.5s ease 0s;
  transition: all 0.5s ease 0s;
  width: 100%;
}';
    $css['thumbgorightconetntinright'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: translateX(100%);
  -ms-transform: translateX(100%);
  transform: translateX(100%);
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content {
  left: 0;
}
.__ID__ .layer-content {
  background: rgba(255, 255, 255,0) none repeat scroll 0 0;
  height: 100%;
  left: 100%;
  position: absolute;
  top: 0;
  -webkit-transition: all 0.5s ease 0s;
  transition: all 0.5s ease 0s;
  width: 100%;
}';
    $css['halfthumbleft'] = '.__ID__{
  overflow: hidden;
  vertical-align: top;
}
.__ID__ .layer-media {
  display: inline-block;
  float: left;
  vertical-align: top;
  width: 50%;
}
.__ID__ .layer-content {
  display: inline-block;
  float: right;
  width: 50%;
}';

    $css['halfthumbright'] = '.__ID__ {
  overflow: hidden;
  vertical-align: top;
}
.__ID__ .layer-media {
  display: inline-block;
  float: right;
  vertical-align: top;
  width: 49%;
}
.__ID__ .layer-content {
  display: inline-block;
  width: 49%;
}';
    $css['contentborder'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
}
.__ID__ .layer-content {
  background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
  bottom: 50%;
  color: rgb(255, 255, 255);
  left: 0;
  padding: 40px;
  position: absolute;
  -webkit-transform: translate(0px, 50%);
  -ms-transform: translate(0px, 50%);
  transform: translate(0px, 50%);
}
.__ID__ .layer-hover {
  display: none;
}
.__ID__ .layer-content:before {
  border-bottom: 1px solid #fff;
  border-top: 1px solid #fff;
  bottom: 30px;
  left: 10px;
  right: 10px;
  top: 30px;
  -webkit-transform: scale(0, 1);
  -ms-transform: scale(0, 1);
  transform: scale(0, 1);
  -webkit-transform-origin: 0 0 0;
  -ms-transform-origin: 0 0 0;
  transform-origin: 0 0 0;
}
.__ID__ .layer-content:after {
  border-left: 1px solid #fff;
  border-right: 1px solid #fff;
  bottom: 10px;
  left: 30px;
  right: 30px;
  top: 10px;
  -webkit-transform: scale(1, 0);
  -ms-transform: scale(1, 0);
  transform: scale(1, 0);
  -webkit-transform-origin: 100% 0 0;
  -ms-transform-origin: 100% 0 0;
  transform-origin: 100% 0 0;
}
.__ID__:hover .layer-content:before, .__ID__:hover .layer-content:after {
  opacity: 1;
  filter: alpha(opacity=100);
  -webkit-transform: scale(1);
  -ms-transform: scale(1);
  transform: scale(1);
}
.__ID__ .layer-content:before, .__ID__ .layer-content:after {
  content: "";
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
}
.__ID__ .layer-content:before, .__ID__ .layer-content:after {
  -webkit-transition: opacity 0.35s ease 0s, -webkit-transform 0.35s ease 0s;
  transition: opacity 0.35s ease 0s, transform 0.35s ease 0s;
}
.__ID__ .layer-content .read_more {
  left: 0;
  position: absolute;
  text-align: center;
  width: 100%;
  z-index: 11;
}
.__ID__ .layer-content .title_link {
  left: 0;
  position: absolute;
  text-align: center;
  width: 100%;
  z-index: 11;
}
.__ID__ .layer-content .excerpt {
  margin-top: 40px;
}';
    $css['contentborderrounded'] = '.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
}
.__ID__ .layer-content {
  background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
  bottom: 50%;
  color: rgb(255, 255, 255);
  left: 0;
  padding: 40px;
  position: absolute;
  -webkit-transform: translate(0px, 50%);
  -ms-transform: translate(0px, 50%);
  transform: translate(0px, 50%);
}
.__ID__ .layer-hover {
  display: none;
}
.__ID__ .layer-content:before {
  border-bottom: 1px solid #fff;
  border-top: 1px solid #fff;
  border-radius: 15px;
  bottom: 30px;
  left: 30px;
  right: 30px;
  top: 30px;
  -webkit-transform: scale(0, 1);
  -ms-transform: scale(0, 1);
  transform: scale(0, 1);
  -webkit-transform-origin: 0 0 0;
  -ms-transform-origin: 0 0 0;
  transform-origin: 0 0 0;
}
.__ID__ .layer-content:after {
  border-left: 1px solid #fff;
  border-right: 1px solid #fff;
  border-radius: 15px;
  bottom: 30px;
  left: 30px;
  right: 30px;
  top: 30px;
  -webkit-transform: scale(1, 0);
  -ms-transform: scale(1, 0);
  transform: scale(1, 0);
  -webkit-transform-origin: 100% 0 0;
  -ms-transform-origin: 100% 0 0;
  transform-origin: 100% 0 0;
}
.__ID__:hover .layer-content:before, .__ID__:hover .layer-content:after {
  opacity: 1;
  filter: alpha(opacity=100);
  -webkit-transform: scale(1);
  -ms-transform: scale(1);
  transform: scale(1);
}
.__ID__ .layer-content:before, .__ID__ .layer-content:after {
  content: "";
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
}
.__ID__ .layer-content:before, .__ID__ .layer-content:after {
  -webkit-transition: opacity 0.35s ease 0s, -webkit-transform 0.35s ease 0s;
  transition: opacity 0.35s ease 0s, transform 0.35s ease 0s;
}
.__ID__ .layer-content{
  padding-bottom:65px;}
.__ID__ .layer-content .read_more {
  left: 0;
  position: absolute;
  text-align: center;
  width: 100%;
  z-index: 11;
}
.__ID__ .layer-content .title_link {
  left: 0;
  position: absolute;
  text-align: center;
  width: 100%;
  z-index: 11;
}
.__ID__ .layer-content .excerpt {
  margin-top: 40px;
}';

    $css['thumbgoleft'] = '.__ID__ a{text-decoration:none}
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  opacity: 0;
  filter: alpha(opacity=0);
  -webkit-transform: scale(0.5) translateX(-100%);
  -ms-transform: scale(0.5) translateX(-100%);
  transform: scale(0.5) translateX(-100%);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}
.__ID__ .layer-hover {
  display: none;
}';
    $css['thumbrounded'] = '.__ID__ a{text-decoration:none}
.__ID__ .layer-media {
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  border-radius: 50%;
  overflow: hidden;
}
.__ID__ .layer-media .thumb {
}';
    $css['contentbottom'] = '.__ID__ a{text-decoration:none}
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  z-index:9;
}
.__ID__ .layer-media {
  -webkit-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
}
.__ID__ .layer-content {
  background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
  bottom: 0;
  color: rgb(255, 255, 255);
  left: 0;
  position: absolute;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  width: 100%;
	padding: 10px;
}
';
    $css['spinright'] = '.__ID__ a{text-decoration:none}
.__ID__ {
  overflow: hidden;
  position: relative;
  vertical-align: top;
}
.__ID__:hover .layer-media {
  -webkit-transform: rotate(30deg);
  -ms-transform: rotate(30deg);
  transform: rotate(30deg);
  opacity: 0;
  filter: alpha(opacity=0);
}
.__ID__ .layer-media {
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
  left: 0;
  top: 0;
  width: 100%;
}
.__ID__:hover .layer-content{
  opacity: 1;
  filter: alpha(opacity=100);
}
.__ID__ .layer-content {
  left: 0;
  opacity: 0;
  filter: alpha(opacity=0);
  position: absolute;
  top: 0;
  width: 100%;
  -webkit-transition: all 1s ease 0s;
  transition: all 1s ease 0s;
}';


    return isset($css[$layout]) ? $css[$layout] : '';


}

