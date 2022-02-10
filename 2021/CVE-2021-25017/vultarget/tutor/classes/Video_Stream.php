<?php
/**
 * Created by PhpStorm.
 * User: themeum
 * Date: 24/9/18
 * Time: 4:03 PM
 */

namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;


/**
 * Class Video_Stream
 * @package TUTOR
 *
 * TUTOR Video Stream Class
 * @since v.1.0.0
 */

class Video_Stream {

	private $path = "";
	private $stream = "";
	private $buffer = 102400;
	private $start  = -1;
	private $end    = -1;
	private $size   = 0;

	private $videoFormats;

	function __construct($filePath) {
		$this->videoFormats = apply_filters('tutor_video_types', array("mp4"=>"video/mp4", "webm"=>"video/webm", "ogg"=>"video/ogg")) ;
		$this->path = $filePath;
	}

	/**
	 * Open stream
	 */
	private function open() {
		if (!($this->stream = fopen($this->path, 'rb'))) {
			die('Could not open stream for reading');
		}
	}

	/**
	 * Set proper header to serve the video content
	 */
	private function setHeader() {
		ob_get_clean();

		header("Content-Type: {$this->videoFormats[strtolower(pathinfo($this->path, PATHINFO_EXTENSION))]}");
		header("Cache-Control: max-age=2592000, public");
		header("Expires: ".gmdate('D, d M Y H:i:s', tutor_time()+2592000) . ' GMT');
		header("Last-Modified: ".gmdate('D, d M Y H:i:s', @filemtime($this->path)) . ' GMT' );
		$this->start = 0;
		$this->size  = filesize($this->path);
		$this->end   = $this->size - 1;
		header("Accept-Ranges: 0-".$this->end);

		if (isset($_SERVER['HTTP_RANGE'])) {
			$c_end = $this->end;
			list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);

			if ($range == '-') {
				$c_start = $this->size - substr($range, 1);
			}else{
				$range = explode('-', $range);
				$c_start = $range[0];

				$c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
			}
			$c_end = ($c_end > $this->end) ? $this->end : $c_end;
			if ($c_start > $c_end || $c_start > $this->size - 1 || $c_end >= $this->size) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $this->start-$this->end/$this->size");
				exit;
			}
			$this->start = $c_start;
			$this->end = $c_end;
			$length = $this->end - $this->start + 1;
			header('HTTP/1.1 206 Partial Content');
			header("Content-Length: ".$length);
			header("Content-Range: bytes $this->start-$this->end/".$this->size);
			header("Accept-Ranges: bytes");
		}
		else {
			header("Content-Length: ".$this->size);
		}

	}

	/**
	 * close currently opened stream
	 */
	private function end() {
		fclose($this->stream);
		exit;
	}

	/**
	 * perform the streaming of calculated range
	 */
	private function stream() {
		$i = $this->start;
		set_time_limit(0);
		while(!feof($this->stream) && $i <= $this->end) {
			$bytesToRead = $this->buffer;
			if(($i+$bytesToRead) > $this->end) {
				$bytesToRead = $this->end - $i + 1;
			}
			//$data = fread($this->stream, $bytesToRead);
			$data = @stream_get_contents($this->stream, $bytesToRead, $i);
			echo $data;
			flush();
			$i += $bytesToRead;
		}
	}

	/**
	 * Start streaming tutor video content
	 */
	function start() {
		$this->open();
		$this->setHeader();
		$this->stream();
		$this->end();
	}
}