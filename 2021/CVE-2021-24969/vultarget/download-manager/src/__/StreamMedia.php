<?php
/**
 * User: shahnuralam
 * Date: 1/24/18
 * Time: 3:14 PM
 * Since: 3.7.4
 */

namespace WPDM\__;

if (!defined('ABSPATH')) die();

class StreamMedia
{
    private $filePath;
    private $filePointer;
    private $chunk = 102400;
    private $start  = -1;
    private $eof    = -1;
    private $size   = 0;

    function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Open The File
     * @return $this
     */
    private function open()
    {
        if (!($this->filePointer = fopen($this->filePath, 'rb'))) {
            \WPDM\__\Messages::error(__( "Failed To Open File!" , "download-manager" ), 1);
        }
        return $this;

    }

    /**
     * Set Download Speed
     * @return $this
     */
    private function setSpeed(){
        $speed = get_option('__wpdm_download_speed', 10240);
        $speed = $speed > 0 ? $speed:10240;
        $speed = apply_filters('wpdm_download_speed', $speed);
        $this->chunk = $speed * 1024;
        return $this;
    }

    /**
     * Prepare and Set Download Headers
     * @return $this
     */
    private function setHeader()
    {
        ob_get_clean();

        $this->start = 0;
        $this->size  = filesize($this->filePath);

        $this->eof   = $this->size - 1;
        $mData = wp_check_filetype($this->filePath);
        set_time_limit(0);
        header("Content-Type: {$mData['type']}");
        header("Cache-Control: max-age=604800, public");
        header("Expires: ".gmdate('D, d M Y H:i:s', time()+604800) . ' GMT');
        header("Last-Modified: ".gmdate('D, d M Y H:i:s', @filemtime($this->filePath)) . ' GMT' );
        header("Accept-Ranges: 0-".$this->eof);

        if (isset($_SERVER['HTTP_RANGE'])) {


            $CPOF = $this->start;
            $EOF = $this->eof;

            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->eof/$this->size");
                exit;
            }
            if ($range == '-') {
                $CPOF = $this->size - substr($range, 1);
            }else{
                $range = explode('-', $range);
                $CPOF = $range[0];

                $EOF = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $EOF;
            }
            $EOF = ($EOF > $this->eof) ? $this->eof : $EOF;
            if ($CPOF > $EOF || $CPOF > $this->size - 1 || $EOF >= $this->size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->eof/$this->size");
                exit;
            }
            $this->start = $CPOF;
            $this->eof = $EOF;
            $length = $this->eof - $this->start + 1;
            fseek($this->filePointer, $this->start);
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: ".$length);
            header("Content-Range: bytes $this->start-$this->eof/".$this->size);
        }
        else
        {
            header("Content-Length: ".$this->size);
        }

        return $this;

    }


    /**
     * Calculate range and serve the chunk
     * @return $this
     */
    private function serve()
    {
        $i = $this->start;
        while(!feof($this->filePointer) && $i <= $this->eof) {
            $bytesToRead = $this->chunk;
            if(($i+$bytesToRead) > $this->eof) {
                $bytesToRead = $this->eof - $i + 1;
            }
            $data = fread($this->filePointer, $bytesToRead);
            echo $data;
            flush();
            $i += $bytesToRead;
        }
        return $this;
    }

    /**
     * Download is done!
     */
    private function end()
    {
        fclose($this->filePointer);
        exit;
    }



    /**
     * Start File Download
     */

    function start()
    {
        $this->open()
            ->setSpeed()
            ->setHeader()
            ->serve()
            ->end();
    }
}
