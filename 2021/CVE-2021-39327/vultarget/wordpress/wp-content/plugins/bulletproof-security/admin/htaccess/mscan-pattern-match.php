<?php
/******************************************************* */
/* MScan Pattern Matching code 
/* Version: 2.0
/* This file is called once and deleted:
/* The MScan pattern matching code is saved to the WP DB
/* on BPS upgrades and new installations and then deleted.                          
/******************************************************* */

	## MScan File Scan patterns
	$js_pattern = '/(\|MakeFrameEx\||\|yahoo_api\||\|exec\||ww=window|ww\.document|visibility:hidden|rotatingtext\[\d\]=\"I\sMISS\sYOU\"|\(!l1l&&!ll1&&!lll\)|s(\W){2,6}c(\W){2,6}r|(\'|")i(\'|")(\.|\+|\s)(\+|\'|"|\.)(\s|f)(\'|")(f|\+|\.)|scr("|\')(|\s)\+(|\s)("|\')ipt|(\\\x(\d|\w[^a])(\d[^0]|\w))+|((%\d(\w|\d){1})+%)|%\d(\w|\d){3}|\(\'hideme\'\)|\["style"\]\["visibility"\]|useragent\.match\(\/(\^(\w|\d){1,}\.\*\|)+|xtrackPageview|document\.write\(\'<\'\+x\[\d\]\+\'>|\\\u00(\d|\w){5,}|(\\\x22(.*)\\\x22)+|(\$(\d){2}){2}|(0|1){8}|_0x(\d|\w){4}|lave(\(|\))|(\(|\))lave|\|iframe\|)/i';

	$htaccess_pattern = '/(RewriteCond\s%\{HTTP_REFERER\}\s(.*)[^!](google|yahoo|aol|bing|ask|facebook|twitter|msn)|ErrorDocument\s(400|403|404)\s(http|https|):|(RewriteCond\s%\{HTTP_USER_AGENT\}(.*\]\s*)){4}|RewriteRule(.*)(\w|\d){1,8}\.php\?(\w|\d){1,6}=(\$|)(\s|\d){1,3}|RewriteRule(.*)\(htm\|pdf\|jar\)|RewriteRule(.*)\{QUERY_STRING\})/i';

	// 4.8: New patterns added
	$php_pattern = '/(base64_decode\(|edoced_46esab|base\'\.\(\d{1,3}(|\s)(\*|\/)(|\s)\d{1,3}\)\.\'_de\'\.\'code|("|\')base(.*)\.(.*)64(.*)(_|\.|)decode("|\')|gzinflate\(|O0|ev("|\')(.*)\.("|\')al\(|lave(\(|\))|(\(|\))lave|preg_replace\(("|\')(\/(\w{1,}|\.\*))\/e|(\\\x(\d|\w){2,3}\\\x(\d|\w){2,3})|__halt_compiler|k2ll33d|\(!l1l&&!ll1&&!lll\)|\|iframe\||\|MakeFrameEx\||\|yahoo_api\||ww=window|ww\.document|ekibastos|scr("|\')(|\s)\+(|\s)("|\')ipt|\(\'hideme\'\)|\["style"\]\["visibility"\]|useragent\.match\(\/(\^(\w|\d){1,}\.\*\|)+|xtrackPageview|\$_COOKIE(|\s)\[str_replace\(.*\$_SERVER\[\'HTTP_HOST\'\]\)\]|\$_\w___\w|\'Windows-1251\'|document\.write\(\'<\'\+x\[\d\]\+\'>|\+(|\s)(\'|")\w(\'|")(|\s)\+|(\\\x22(.*)\\\x22)+|(|\[)_0x(\w|\d){1,6}\[\d{1,3}\]{1,2}|\\\142\\\141\\\x73|\\\u00(\d|\w){5,}|(\'|")i(\'|")(\.|\+|\s)(\+|\'|"|\.)(\s|f)(\'|")(f|\+|\.)|s(\W){2,6}c(\W){2,6}r|(\$\w{1,3}\{\d{1,2}\}(|\s)\.(|\s)){3}|\$<(\d|\w){2}>|\$_(\/\*)|%3C%21|%3Cscript%3E|%253C|(%\d(\w|\d)){5}|\$(\d|\w){1,}\[\'(\d|\w){1,}\'\]\[(\d){1,3}\](\s\.|\.)(\$|\s\$)|(\$(\w){2}\[\d{1,2}\]\.)+|(0|1){8}|_0x(\d|\w){4}|\(64\)(\s|)\.(\s|)(\'|")_(\'|")|([a-z0-9]){40,}\+|\$_REQUEST\[\'cmd\'\]|\$_GET\[\'cmd\'\]|system\(|shell_exec\(|passthru\(|exec\(|eval\(|ALREADY_RUN_|hastebin|((chr\()\d+\)\.){1,}|((\\\\|\\\\\\\)\d+[a-z]+([0-9]|)){1,}|\$([a-z0-9])+\{\d+\}(\,|\.){1,})/i';
		
	// 4.8: No longer scanning image files
	$image_pattern = '/(<\?php|eval\(|exec\(|popen\(|create_function\(|passthru\(|shell_exec\(|proc_open\(|pcntl_exec\(|fopen\(|fputs\(|file_put_contents\(|fwrite\(|gzinflate\(|base64_decode\(|isset|\$_REQUEST|\$_FILES|\$_GET|\$_POST|\$_SERVER|\$_SESSION|system\(|\'cmd\'|__halt_compiler|<script|javascript|function|createElement|<html>|visibility:|<textarea)/i';
		
	## MScan Database Scan patterns
	$search1 = 'eval(';
	$search2 = '(lave';		
	$search3 = 'base64_decode';		
	$search4 = 'edoced_46esab';
	$search5 = '<script';
	$search6 = '<iframe';
	$search7 = '<noscript';
	$search8 = 'display:';
	$search9 = 'visibility:';
	
	$eval_match = '/(eval\(|\(lave)/i';
	$base64_decode_match = '/(base64_decode|edoced_46esab)/i';
	$eval_text = 'eval( or (lave';
	$base64_decode_text = 'base64_decode or edoced_46esab';

?>