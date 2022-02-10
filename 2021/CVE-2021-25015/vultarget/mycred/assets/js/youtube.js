/**
 * YouTube Iframe API
 * @since 1.3.3
 * @version 1.0
 */
if (!window['YT']) {var YT = {loading: 0,loaded: 0};}if (!window['YTConfig']) {var YTConfig = {};}if (!YT.loading) {YT.loading = 1;(function(){var l = [];YT.ready = function(f) {if (YT.loaded) {f();} else {l.push(f);}};window.onYTReady = function() {YT.loaded = 1;for (var i = 0; i < l.length; i++) {try {l[i]();} catch (e) {}}};YT.setConfig = function(c) {for (var k in c) {if (c.hasOwnProperty(k)) {YTConfig[k] = c[k];}}};var a = document.createElement('script');a.src = 'https:' + '//s.ytimg.com/yts/jsbin/www-widgetapi-vflpUkZCc.js';a.async = true;var b = document.getElementsByTagName('script')[0];b.parentNode.insertBefore(a, b);})();}

/**
 * onYouTubeIframeAPIReady
 * Creates a player for YouTube Iframes
 * @since 1.3.3
 * @version 1.0
 */
function onYouTubeIframeAPIReady() {
	console.log( 'YouTube Iframe API' );
	
	// Listen for the ready event for any vimeo video players on the page
	var youtPlayers = document.querySelectorAll( 'iframe.mycred-youtube-video' ),
		youframes,
		yplayer;

	for (var i = 0, length = youtPlayers.length; i < length; i++) {
		yplayer = youtPlayers[i];
		var video_id = yplayer.getAttribute( 'data-vid' );
		youframes = new YT.Player( yplayer, { events : { 'onStateChange': 'mycred_vvideo_v' + video_id } } );
		console.log( video_id );
	}
}

/**
 * onYouTubePlayerReady
 * Old JS API used before 1.3.3
 * @since 1.0
 * @version 1.0
 */
function onYouTubePlayerReady( id ) {
	// Define Player
	var yplayer = document.getElementById( id );

	// Duration
	duration[ id ] = yplayer.getDuration();

	// Listen in on state changes
	yplayer.addEventListener( 'onStateChange', 'mycred_video_' + id );
	
	console.log( id );
}