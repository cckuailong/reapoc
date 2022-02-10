<?php

/*
	Name			:	phpA/B v1.2
	Author			:	Brian Cray
	License			:	GPL 2.0
	License URL		:	http://www.gnu.org/licenses/gpl-2.0.html
	URL				:	phpabtest.com
*/

class phpab
{
	private $variations = array();
	private $current_variation = '!unset';
	private $current_variation_key;
	private $test_name;
	private $test_ran = FALSE;
	private $trial_mode = FALSE;
	private $content;
	private $tag = 'phpab';
	private $auto_ga = TRUE;
	private $ga_slot = 1;
	private $test_domain;
	private $detect_bots = TRUE;
	private $is_bot = FALSE;
	private $version = '1.2';
	
	function __construct ($n, $t = FALSE)
	{
		if($this->detect_bots == TRUE)
		{
			$bots = array('googlebot', 'msnbot', 'slurp', 'ask jeeves', 'crawl', 'ia_archiver', 'lycos');
			foreach($bots as $botname)
			{
				if(stripos($_SERVER['HTTP_USER_AGENT'], $botname) !== FALSE)
				{
					$this->trial_mode = TRUE;
					$this->is_bot = TRUE;
					break;
				}
			}
		}
		
		if($this->is_bot == FALSE)
		{	
			$this->trial_mode = $t;
		}
		
		ob_start(array($this, 'execute'));
		
		$this->test_domain = '.' . $_SERVER['HTTP_HOST'];
		
		$n = trim(strtolower($n));
		$n = preg_replace('/[^a-z0-9 _]*/', '', $n);
		$n = str_replace(' ', '_', $n);
		$this->test_name = $n;
	}
	
	function __destruct ()
	{
		if (ob_get_contents()) ob_end_flush();
	}
	
	private function grab_content ()
	{
		if(empty($this->content))
		{
			$this->content = ob_get_contents();
		}
	}
	
	private function setup_ga ()
	{
		$try_auto = FALSE;
		$sync = '{' . $this->tag . ' ' . $this->test_name . ' ga_sync}';
		$async = '{' . $this->tag . ' ' . $this->test_name . ' ga_async}';
		$syncPos = strpos($this->content, $sync);
		if($syncPos !== FALSE)
		{
			$this->content = str_replace($sync, 'pageTracker._setCustomVar(' . $this->ga_slot . ', "' . $this->test_name . '", "' . $this->current_variation . '", 3);', $this->content);
		}
		else
		{
			$asyncPos = strpos($this->content, $async);
			if($asyncPos !== FALSE)
			{
				$this->content = str_replace($async, '_gaq.push(["_setCustomVar", ' . $this->ga_slot . ', "' . $this->test_name . '", "' . $this->current_variation . '", 3]);', $this->content);
			}
			else
			{
				$try_auto = TRUE;
			}
		}
		
		if($this->auto_ga == TRUE && $try_auto == TRUE)
		{
			$sync = strpos($this->content, 'pageTracker._trackPageview');
			if($sync === FALSE)
			{
				$async = preg_match('/_gaq\.push\(\[[\'\"]_trackPageview[\'\"]\]\)/', $this->content, $matches, PREG_OFFSET_CAPTURE);
				if($async == FALSE)
				{
          $auto_fail = TRUE;
					$async = FALSE;
				}
				else
				{
					$auto_fail = FALSE;
					$async = $matches[0][1];
				}
			}
			else
			{
				$auto_fail = FALSE;
			}
			
			if($auto_fail === FALSE && $sync !== FALSE)
			{
				$this->content = substr($this->content, 0, $sync - 1) . 'pageTracker._setCustomVar(' . $this->ga_slot . ', "' . $this->test_name . '", "' . $this->current_variation . '", 3);' . substr($this->content, $sync);
			}
			elseif($auto_fail === FALSE && $async !== FALSE)
			{
				$this->content = substr($this->content, 0, $async - 1) . ' _gaq.push(["_setCustomVar", ' . $this->ga_slot . ', "' . $this->test_name . '", "' . $this->current_variation . '", 3]); ' . substr($this->content, $async);
			}
		}
	}
	
	private function record_user_segment ()
	{
		$cookie_domain = (($colon_position = strrpos($this->test_domain, ":")) === false) ? $this->test_domain : substr($this->test_domain, 0, $colon_position);
        	setcookie($this->tag . '-' . $this->test_name, $this->current_variation, time() + (60 * 60 * 24 * 365), '/', $cookie_domain);
	}
	
	public function set_domain ($d)
	{
		$this->test_domain = !empty($d) ? $d : '.' . $_SERVER['HTTP_HOST'];
	}
	
	public function set_ga_slot ($s)
	{
		$this->ga_slot = $s;
	}
	
	public function set_ga_mode ($m)
	{
		$this->ga_auto = $m;
	}
	
	public function set_tag ($t)
	{
		$this->tag = $t;
	}
	
	public function add_variation ($n, $v = '')
	{
		$n = trim(strtolower($n));
		$n = preg_replace('/[^a-z0-9 _]*/', '', $n);
		$n = str_replace(' ', '_', $n);
		
		array_push($this->variations, array('name' => $n, 'value' => $v));
	}
	
	public function get_user_segment ()
	{
		if($this->current_variation != '!unset' && $this->current_variation_key != -1)
		{
			return $this->current_variation;
		}
		
		if ($this->is_bot == TRUE)
		{
			$this->current_variation = 'control';
			return $this->current_variation;
		}
		
		if (get_magic_quotes_gpc() == TRUE)
		{
			$_COOKIE[$this->tag . '-' . $this->test_name] = stripslashes($_COOKIE[$this->tag . '-' . $this->test_name]);
		}

		if($this->trial_mode == FALSE)
		{
			$key = $this->tag . '-' . $this->test_name;
			if(array_key_exists($key, $_COOKIE))
			{
				$this->current_variation = $_COOKIE[$key];
			}
			
			if(empty($this->current_variation))
			{
				$this->current_variation = '!unset';
			}
		}
		else
		{
			$this->current_variation = '!unset';
		}
		
		array_unshift($this->variations, array('name' => 'control', 'value' => ''));
		
		$valid = FALSE;
		
		$this->current_variation_key = 0;
		foreach($this->variations as $n => $v)
		{
			if($v['name'] == $this->current_variation)
			{
				$valid = TRUE;
				break;
			}
			$this->current_variation_key++;
		}

		if($this->current_variation == '!unset' || $valid == FALSE)
		{
			srand((double)microtime() * 1000003);
			$this->current_variation_key = array_rand($this->variations);
			$this->current_variation = $this->variations[$this->current_variation_key]['name'];
		}
		
		return $this->current_variation;
	}
	
	public function execute ($buffer)
	{
		$this->content = $buffer;
		
		if($this->test_ran == FALSE)
		{
			$this->run_test();
		}
				
		if($this->trial_mode != TRUE)
		{
			$this->setup_ga();
		}
		
		$tmp = $this->content;
		$this->content = preg_replace('/<body([^>]*?)class="([^"]*?)"([^>]*?)>/i', '<body${1}class="${2} ' . $this->tag . '-' . $this->current_variation . '"${3}>', $this->content);
		if($tmp == $this->content)
		{
			$this->content = preg_replace('/<body([^>]*?)>/i', '<body${1} class="' . $this->tag . '-' . $this->current_variation . '">', $this->content);
		}
		unset($tmp);
		
		$pos = strrpos($this->content, '</body>');
		if($pos !== false)
		{
			$this->content = substr_replace($this->content, '<!--A/B tests active with phpA/B ' . $this->version . '--></body>', $pos, strlen('</body>'));
		}

		$this->content = str_replace('{' . $this->tag . ' ' . $this->test_name . ' current_varation}', $this->current_variation, $this->content);
		
		if($this->trial_mode != TRUE)
		{
			$this->record_user_segment();
		}
		
		return $this->content;
	}
	
	public function run_test ()
	{
		$this->get_user_segment();
		$this->grab_content();
		
		$open_tag = '{' . $this->tag . ' ' . $this->test_name . '}';
		$close_tag = '{/' . $this->tag . ' ' . $this->test_name . '}';
		$test_open = strpos($this->content, $open_tag);
		$test_close = strpos($this->content, $close_tag);
		
		while($test_open !== FALSE)
		{
			if($this->current_variation != 'control')
			{
				if($test_close === FALSE && $test_open !== FALSE)
				{
					$this->content = substr_replace($this->content, $this->variations[$this->current_variation_key]['value'], $test_open, strlen($open_tag));
				}
				elseif($test_close !== FALSE && $test_open !== FALSE)
				{
					$diff = $test_close + strlen($close_tag) - $test_open;
					$this->content = substr_replace($this->content, $this->variations[$this->current_variation_key]['value'], $test_open, $diff);
				}
				else
				{
				}
			}
			else
			{
				$this->content = str_replace($open_tag, $this->variations[$this->current_variation_key]['value'], $this->content);
				$this->content = str_replace($close_tag, '', $this->content);
			}
			
			$test_open = strpos($this->content, $open_tag, $test_open);
			$test_close = strpos($this->content, $close_tag, $test_open);
		}
		
		$this->test_ran = TRUE;
	}
}
