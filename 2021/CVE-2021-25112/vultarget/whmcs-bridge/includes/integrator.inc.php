<?php
if (!function_exists('cc_integrator_tags')) {
	function cc_integrator_tags($buffer,$prefix) {
		//page header & footer
		$tagslist='head';
		$tags=explode(',',$tagslist);
		foreach ($tags as $tag)
		{
			$buffer=str_replace('<'.$tag,'<!--'.$tag.':start--><div id="'.$prefix.$tag.'"',$buffer);
			$buffer=str_replace($tag.'>','div><!--'.$tag.':end-->',$buffer);
		}
		$buffer=str_replace('<body','<!--body:start--><div class="'.$prefix.'body"',$buffer);
		$buffer=str_replace('body>','div><!--body:end-->',$buffer);

		$buffer=preg_replace('/<html.*>/','<!--html:start-->',$buffer);
		$buffer=preg_replace('/<.html>/','<!--html:end-->',$buffer);
		$buffer=preg_replace('/<meta.*>/','<!--meta-->',$buffer);
		$buffer=preg_replace('/<title>.*<.title>/','<!--title-->',$buffer);
		$buffer=preg_replace('/<.DOCTYPE.*>/','<!--doctype-->',$buffer);
		return $buffer;
	}
}
if (!function_exists('cc_integrator_cut')) {
	function cc_integrator_cut(&$buffer,$stag,$etag) {
		$cutout="";
		if ($buffer && $before=cc_strstr($buffer,$stag,true)) {
			$i=cc_strstr($buffer,$stag,false);
			$cutout=cc_strstr(substr($i,strlen($stag)),$etag,true);
			$after=cc_strstr($i,$etag,false);
			$buffer=$before.substr($after,strlen($etag));
		}
		return $cutout;
	}
}
if (!function_exists('cc_strstr')) {
	function cc_strstr($haystack, $needle, $before_needle=FALSE) {
		//Find position of $needle or abort
		if(($pos=strpos($haystack,$needle))===FALSE) return FALSE;

		if ($before_needle) return substr($haystack,0,$pos-1);
		else return substr($haystack,$pos);
	}
}
?>