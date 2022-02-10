<?php
/**
 * Manages file operations<br/>
 * Version: 6|32
 * *** DO NOT CHANGE ***
 */
namespace WPDM\__;

class FileSystem
{
    function __construct()
    {

    }

    public static function mime_type($filename)
    {
        $filetype = wp_check_filetype($filename);
        return $filetype['type'];
    }

    public static function uploadFile($FILE)
    {

    }

    /**
     * @usage Download Given File
     * @param $filepath
     * @param $filename
     * @param int $speed
     * @param int $resume_support
     * @param array $extras
     */
    public static function downloadFile($filepath, $filename, $speed = 1024, $resume_support = 1, $extras = array())
    {

        if (isset($extras['package']))
            $package = $extras['package'];

        if (headers_sent($_filename, $_linenum)) {
            Messages::error("Headers already sent in $_filename on line $_linenum", 1);
        }

        if (substr_count($filepath, "../") > 0) {
            Messages::error("Please, no funny business, however, good try though!", 1);
        }

        if (__::is_url($filepath)) {
            header("location: " . $filepath);
            die();
        }

        if (WPDM()->fileSystem->isBlocked($filepath)) Messages::error("Invalid File Type ({$filepath})!", 1);

        $content_type = function_exists('mime_content_type') ? mime_content_type($filepath) : self::mime_type($filepath);

        $buffer = $speed ? $speed : 1024; // bytes

        $buffer *= 1024; // to bits

        $bandwidth = 0;

        if (function_exists('ini_set'))
            @ini_set('display_errors', 0);

        @session_write_close();

        if (function_exists('apache_setenv'))
            @apache_setenv('no-gzip', 1);

        if (function_exists('ini_set'))
            @ini_set('zlib.output_compression', 'Off');


        @set_time_limit(0);
        @session_cache_limiter('none');

        if (get_option('__wpdm_support_output_buffer', 1) == 1) {
            $pcl = ob_get_level();
            do {
                @ob_end_clean();
                if (ob_get_level() == $pcl) break;
                $pcl = ob_get_level();
            } while (ob_get_level() > 0);
        }

        if (strpos($filepath, '://')) {
            header("location: {$filepath}");
            die();
        }
        if (file_exists($filepath))
            $fsize = filesize($filepath);
        else
            $fsize = 0;
        $org_size = $fsize;

        nocache_headers();
        header("X-Robots-Tag: noindex, nofollow", true);
        header("Robots: none");
        header('Content-Description: File Transfer');

        if (strpos($_SERVER['HTTP_USER_AGENT'], "Safari") && !isset($extras['play']) && !get_option('__wpdm_open_in_browser', 0))
            $content_type = "application/octet-stream";

        header("Content-type: $content_type");

        $filename = apply_filters("wpdm_download_filename", $filename, $filepath, $extras);

        $filename = rawurlencode($filename);
        if (!isset($extras['play'])) {
            if (get_option('__wpdm_open_in_browser', 0) || wpdm_query_var('open') == 1)
                header("Content-disposition: inline;filename=\"{$filename}\"");
            else
                header("Content-disposition: attachment;filename=\"{$filename}\"");

            header("Content-Transfer-Encoding: binary");
        }

        if ((isset($extras['play']) && strpos($_SERVER['HTTP_USER_AGENT'], "Safari")) || get_option('__wpdm_download_resume', 1) == 2) {
            header("Content-Length: " . $fsize);
            header("Content-disposition: attachment;filename=\"{$filename}\"");
            TempStorage::set("download." . wpdm_get_client_ip(), 1, 60);
            readfile($filepath);
            return;
        }

        $file = @fopen($filepath, "rb");

        //check if http_range is sent by browser (or download manager)
        if (isset($_SERVER['HTTP_RANGE']) && $fsize > 0) {
            list($bytes, $http_range) = explode("=", $_SERVER['HTTP_RANGE']);

            $tmp = explode('-', $http_range);
            $tmp = array_shift($tmp);
            $set_pointer = intval($tmp);

            $new_length = $fsize - $set_pointer;

            header("Accept-Ranges: bytes");
            header("Accept-Ranges: 0-$fsize");
            $proto = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            header("{$proto} 206 Partial Content");

            header("Content-Length: $new_length");
            header("Content-Range: bytes $http_range-$fsize/$org_size");

            fseek($file, $set_pointer);

        } else {
            header("Content-Length: " . $fsize);
        }
        $packet = 1;

        if ($file) {
            while (!(connection_aborted() || connection_status() == 1) && $fsize > 0) {

                $parallel_download = (int)get_option('__wpdm_parallel_download', 1);
                if ($parallel_download === 0)
                    TempStorage::set("download." . wpdm_get_client_ip(), 1, 15);

                if ($fsize > $buffer)
                    echo fread($file, $buffer);
                else
                    echo fread($file, $fsize);
                if (function_exists('ob_get_level') && ob_get_level() > 0) @ob_flush();
                @flush();
                $fsize -= $buffer;
                $bandwidth += $buffer;
                if ($speed > 0 && ($bandwidth > $speed * $packet * 1024)) {
                    sleep(1);
                    $packet++;
                }


            }
            $package['downloaded_file_size'] = $fsize;
            //add_action('wpdm_download_completed', $package);
            @fclose($file);
        }

        return;

    }

    /**
     * @usage Download any content as a file
     * @param $filename
     * @param $content
     */
    public function downloadData($filename, $content)
    {
        @ob_end_clean();
        nocache_headers();
        $filetype = wp_check_filetype($filename);
        header("X-Robots-Tag: noindex, nofollow", true);
        header("Robots: none");
        header("Content-Description: File Transfer");
        header("Content-Type: {$filetype['type']}");
        header("Content-disposition: attachment;filename=\"$filename\"");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: " . strlen($content));
        echo $content;
    }

    /**
     * @usage Sends download headers only
     * @param $filename
     * @param int $size
     */
    public function downloadHeaders($filename, $size = null)
    {
        @ob_end_clean();
        $filetype = wp_check_filetype($filename);
        header("Content-Description: File Transfer");
        header("Content-Type: {$filetype['type']}");
        header("Content-disposition: attachment;filename=\"$filename\"");
        header("Content-Transfer-Encoding: Binary");
        if ($size)
            header("Content-Length: " . $size);
    }


    /**
     * @usage Download any content as a file
     * @param $filename
     * @param $content
     */
    public static function mkDir($path, $mode = 0777, $recur = false)
    {
        $success = true;
        if (!file_exists($path))
            $success = mkdir($path, $mode, $recur);
        return $success;
    }

    /**
     * @usage Create ZIP from given file list
     * @param $files
     * @param $zipname
     * @return bool|string
     */
    public static function zipFiles($files, $zipname)
    {

        if(!class_exists('ZipArchive'))
            Messages::fullPage('Error!', "<div class='card bg-danger text-white p-4'>".__( "<b>Zlib</b> is not active! Failed to initiate <b>ZipArchive</b>" , "download-manager" )."</div>", 'error');


        $zipped = (basename($zipname) === $zipname) ? WPDM_CACHE_DIR . sanitize_file_name($zipname) : $zipname;

        if (substr_count($zipname, '.zip') <= 0) $zipped .= '.zip';

        if (file_exists($zipped))
            unlink($zipped);

        if (count($files) < 1) return false;

        $zip = new \ZipArchive();
        if ($zip->open($zipped, \ZIPARCHIVE::CREATE) !== TRUE) {
            return false;
        }
        foreach ($files as $file) {
            $file = trim($file);
            $filename = wp_basename($file);
            $file = WPDM()->fileSystem->absPath($file);
            /*if (file_exists(UPLOAD_DIR . $file)) {
                $fnm = preg_replace("/^[0-9]+?wpdm_/", "", $file);
                $zip->addFile(UPLOAD_DIR . $file, $fnm);
            } else if (file_exists($file)) {
                $fname = basename($file);
                $zip->addFile($file, $fname);
            }*/
            //else if (file_exists(WP_CONTENT_DIR . end($tmp = explode("wp-content", $file)))) //path fix on site move
            //    $zip->addFile(WP_CONTENT_DIR . end($tmp = explode("wp-content", $file)), wpdm_basename($file));
            $zip->addFile($file, $filename);
        }
        $zip->close();

        return $zipped;
    }

    /**
     * @usage Create ZIP from given dir path
     * @param $files
     * @param $zipname
     * @return bool|string
     */
    public static function zipDir($dir, $zipname = '')
    {

        if ($zipname === '') $zipname = basename($dir);

        $zipped = WPDM_CACHE_DIR . sanitize_file_name($zipname) . '.zip';

        $base_folder = sanitize_file_name($zipname);

        $rootPath = realpath($dir);

        $zip = new \ZipArchive();
        $zip->open($zipped, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);


        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
            if (!$file->isDir() && !strstr($file->getRealPath(), ".git") && !strstr($file->getRealPath(), ".DS")) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, $base_folder."/".$relativePath);
            }
        }

        $zip->close();

        return $zipped;
    }

    /**
     * UnZip a zip file
     * @param $zip_file
     * @param string $dir
     * @return bool
     */
    public static function unZip($zip_file, $dir = ''){
        $zip = new \ZipArchive();
        $res = $zip->open($zip_file);
        if($dir === '')
            $dir = str_replace(".zip", "", $zip_file);
        if(!file_exists($dir))
            mkdir($dir, 0755, true);
        if ($res === TRUE) {
            $zip->extractTo($dir);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $dir
     * @param bool|true $recur
     * @return array
     */
    public static function scanDir($dir, $recur = true, $abspath = true, $filter = null, $md5_index = false)
    {
        $dir = realpath($dir) . "/";
        if ($dir === '/' || $dir === '') return array();
        $tmpfiles = file_exists($dir) ? array_diff(scandir($dir), array(".", "..", ".DS_Store")) : array();
        $files = array();
        foreach ($tmpfiles as $file) {
            if (is_dir($dir . $file) && $recur == true)
                $files = array_merge($files, self::scanDir($dir . $file, true, $filter, $md5_index));
            else {
                if (!$filter || substr_count($file, $filter) > 0) {
                    $path = $abspath ? $dir . $file : $file;
                    if($md5_index)
                        $files[md5($path)] = $path;
                    else
                        $files[] = $path;
                }
            }
        }
        return $files;
    }


    /**
     * Get directory size
     * @param $dir
     * @return string
     */
    function dirSize($dir)
    {
        $bytestotal = 0;
        $path = realpath($dir);
        if ($path !== false) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object) {
                try {
                    $bytestotal += $object->getSize();
                } catch (\Exception $e) {

                }
            }
        }
        $bytestotal = $bytestotal / 1024;
        $bytestotal = $bytestotal / 1024;
        return number_format($bytestotal, 2);
    }

    /**
     * @param $dir
     * @param bool|true $recur
     * @return array
     */
    public static function listFiles($dir, $recur = true, $abspath = true)
    {
        $dir = realpath($dir) . "/";
        if ($dir == '/' || $dir == '') return array();
        $tmpfiles = file_exists($dir) ? array_diff(scandir($dir), array(".", "..")) : array();
        $files = array();
        foreach ($tmpfiles as $file) {
            if (is_dir($dir . $file) && $recur == true) $files = array_merge($files, self::scanDir($dir . $file, true));
            else if (!is_dir($dir . $file))
                $files[] = $abspath ? $dir . $file : $file;
        }
        return $files;
    }

    /**
     * @param $dir
     * @param bool|true $recur
     * @return array
     */
    public static function subDirs($dir, $abspath = true)
    {
        $dir = realpath($dir) . "/";
        if ($dir == '/' || $dir == '') return array();
        $tmpfiles = file_exists($dir) ? array_diff(scandir($dir), array(".", "..")) : array();
        $subdirs = array();
        foreach ($tmpfiles as $file) {
            if (is_dir($dir . $file)) $subdirs[] = $abspath ? $dir . $file : $file;

        }
        return $subdirs;
    }


    /**
     * @param $dir
     * @param bool|true $recur
     * @return array|bool
     */
    public static function deleteFiles($dir, $recur = true, $filter = '*')
    {
        $dir = realpath($dir) . "/";
        if ($dir == '/' || $dir == '') return array();
        $tmpfiles = file_exists($dir) ? array_diff(scandir($dir), array(".", "..")) : array();
        $files = array();
        foreach ($tmpfiles as $file) {
            if (is_dir($dir . $file) && $recur == true) $files = array_merge($files, self::scanDir($dir . $file, true));
            else {
                if(is_array($filter)){
                    $ext = isset($filter['ext']) ? $filter['ext'] : '*';
                    $expiretime = isset($filter['filetime']) ? $filter['filetime'] : null;
                    $delete = true;
                    $filetime = filectime($dir.$file);
                    if(!$filetime || !$expiretime || $filetime < $expiretime) {
                        if ($ext === '*' || substr_count($file, $ext) > 0) {
                            @unlink($dir . $file);
                        }
                    }
                } else {
                    if ($filter === '*' || substr_count($file, $filter) > 0)
                        @unlink($dir . $file);
                }
            }
        }
        return true;
    }

    /**
     * @param $src
     * @param $dst
     */
    public static function copyDir($src, $dst)
    {
        $src = realpath($src);
        $dir = opendir($src);

        $dst = realpath($dst) . '/' . basename($src);
        @mkdir($dst);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::copyDir($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Generates image thumbnail
     * @param $path
     * @param $width
     * @param $height
     * @param |null $crop
     * @param bool $usecache
     * @return string|string[]
     */
    public static function imageThumbnail($path, $width, $height, $crop = WPDM_USE_GLOBAL, $usecache = true)
    {
        $original_path = $path;

        if(!function_exists('get_home_path')) require_once ABSPATH . 'wp-admin/includes/file.php';
        $abspath = get_home_path();

        $abspath = str_replace("\\", "/", ABSPATH);
        $cachedir = str_replace("\\", "/", WPDM_CACHE_DIR);
        $path = str_replace("\\", "/", $path);
        if (is_ssl()) $path = str_replace("http://", "https://", $path);
        else  $path = str_replace("https://", "http://", $path);
        $path = str_replace(site_url('/'), $abspath, $path);

        $crop = $crop === WPDM_USE_GLOBAL ? get_option('__wpdm_crop_thumbs', false) : $crop;

        if (strpos($path, '.wp.com')) {
            $path = explode("?", $path);
            $path = $path[0] . "?resize={$width},{$height}";
            return $path;
        }

        if (strpos($path, '://')) return $path;
        if (!file_exists($path) && wpdm_is_url($original_path)) return $original_path;
        if (!file_exists($path)) return WPDM_BASE_URL . 'assets/images/404.jpg';


        $name_p = explode(".", $path);
        $ext = "." . end($name_p);
        $filename = basename($path);
        $thumbpath = $cachedir . str_replace($ext, "-{$width}x{$height}" . $ext, $filename);

        if (file_exists($thumbpath) && $usecache) {
            $thumbpath = str_replace($cachedir, WPDM_CACHE_URL, $thumbpath);
            return $thumbpath;
        }
        $image = wp_get_image_editor($path);

        $fullurl = str_replace($cachedir, WPDM_CACHE_URL, $path);
        if (!is_wp_error($image)) {
            //if ( is_wp_error( $image->resize( $width, $height, true ) ) ) return $fullurl;
            $image->resize($width, $height, $crop);
            $image->save($thumbpath);

        } else
            return str_replace(ABSPATH, home_url('/'), $path);

        $thumb_size = $image->get_size();
        if ($thumb_size['width'] < $width || $thumb_size['height'] < $height) {
            if ($height == 0) $height = $thumb_size['height'];
            $_image_back = imagecreatetruecolor($width, $height);
            $color = imagecolorallocatealpha($_image_back, 255, 255, 255, 127);
            imagefill($_image_back, 0, 0, $color);
            if (strstr($thumbpath, ".png"))
                $_image_top = imagecreatefrompng($thumbpath);
            if (strstr($thumbpath, ".gif"))
                $_image_top = imagecreatefromgif($thumbpath);
            if (strstr($thumbpath, ".jpg") || strstr($thumbpath, ".jpeg"))
                $_image_top = imagecreatefromjpeg($thumbpath);
            if (!isset($_image_top) || !$_image_top) return $thumbpath;
            $imgw = imagesx($_image_top);
            $imgh = imagesy($_image_top);
            $posx = (int)(($width - $imgw) / 2);
            $posy = (int)(($height - $imgh) / 2);
            imagecopy($_image_back, $_image_top, $posx, $posy, 0, 0, $imgw, $imgh);
            imagepng($_image_back, $thumbpath);
            imagedestroy($_image_back);
        }

        $thumbpath = str_replace("\\", "/", $thumbpath);
        $thumbpath = str_replace($cachedir, WPDM_CACHE_URL, $thumbpath);
        return $thumbpath;
    }

    /**
     * @param $pdf
     * @param $id
     * @return string
     * @usage Generates thumbnail from PDF file. [ From v4.1.3 ]
     */
    public static function pdfThumbnail($pdf, $id)
    {
        $pdfurl = '';
        if (strpos($pdf, "://")) {
            $pdfurl = $pdf;
            $pdf = str_replace(home_url('/'), ABSPATH, $pdf);
        }
        if ($pdf == $pdfurl) return '';
        if (file_exists($pdf)) $source = $pdf;
        else $source = UPLOAD_DIR . $pdf;
        if (!file_exists(WPDM_CACHE_DIR . "pdfthumbs/")) {
            @mkdir(WPDM_CACHE_DIR . "pdfthumbs/", 0755);
            @chmod(WPDM_CACHE_DIR . "pdfthumbs/", 0755);
        }
        $dest = WPDM_CACHE_DIR . "pdfthumbs/{$id}.png";
        $durl = WPDM_CACHE_URL . "pdfthumbs/{$id}.png";
        $ext = explode(".", $source);
        $ext = end($ext);
        $colors = WPDM()->setting->ui_colors;
        $color = is_array($colors['secondary']) && isset($colors['secondary']) ? str_replace("#", "", $colors['secondary']) : '6c757d';
        if ($ext != 'pdf') return '';
        if (file_exists($dest)) return $durl;
        if (!file_exists($source))
            $source = utf8_encode($source);
        $source = $source . '[0]';
        if (!class_exists('\Imagick')) return "https://via.placeholder.com/600x900/{$color}/FFFFFF?text=[+Imagick+Missing+]"; // "Error: Imagick is not installed properly";
        try {
            $image = new \imagick($source);
            $image->setResolution(800, 0);
            $image->setImageFormat("png");
            $image->writeImage($dest);
        } catch (\Exception $e) {
            return "https://via.placeholder.com/600x900/{$color}/FFFFFF?text=+";
            //return '';
        }
        return $durl;
    }

    /**
     * @usgae Block http access to a dir
     * @param $dir
     */
    public static function blockHTTPAccess($dir, $fileType = '*')
    {
        $cont = "RewriteEngine On\r\n<Files {$fileType}>\r\nDeny from all\r\n</Files>\r\n";
        @file_put_contents($dir . '/.htaccess', $cont);
        //@file_put_contents($dir . '/web.config', $_cont);
    }

    /**
     * @usage Google Doc Preview Embed
     * @param $url
     * @return string
     */
    public static function docViewer($url, $ID, $ext = '')
    {
        $doc_preview_html = "";
        if ($ext == 'pdf') {
            $doc_preview_html = '<iframe src="https://docs.google.com/viewer?url=' . urlencode($url) . '&embedded=true" width="100%" height="600" style="border: none;"></iframe>';
        }
        else {
            $doc_preview_html = '<iframe src="https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($url) . '&embedded=true" width="100%" height="600" style="border: none;"></iframe>';
        }
        $doc_preview_html = apply_filters('wpdm_doc_viewer', $doc_preview_html, $ID, $url, $ext);
        return $doc_preview_html;
    }

    /**
     * Retrieve absolute path of the given file ( $file ) assiciated with the given package id ( $pid )
     * @param $file
     * @param $pid
     * @return string
     */
    public static function fullPath($file, $pid)
    {
        $post = get_post($pid);
        $user = get_user_by('id', $post->post_author);
        $user_upload_dir = UPLOAD_DIR . $user->user_login . '/';
        if (file_exists(UPLOAD_DIR . $file))
            $fullpath = UPLOAD_DIR . $file;
        else if (file_exists($user_upload_dir . $file))
            $fullpath = $user_upload_dir . $file;
        else if (file_exists($file))
            $fullpath = $file;
        else
            $fullpath = '';
        return $fullpath;
    }

    /**
     * Get the file extension
     * @param $file
     * @return string
     */
    public static function fileExt($file)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $ext  = strtolower($ext);
        if($ext === '') $ext = "|=|";
        return $ext;
    }

    public static function mediaURL($pid, $fileID, $fileName = '')
    {
        if ($fileName == '') {
            $files = WPDM()->package->getFiles($pid);
            $fileName = wpdm_basename($files[$fileID]);
        }
        return WPDM()->package->expirableDownloadLink($pid, 5, 1800) . "&ind={$fileID}&file={$fileName}";
    }

    /**
     * Find or generate file type icon and returns the url
     * @param $filename_or_ext
     * @param string $color
     * @param bool $return
     * @return string File Type Icon URL
     */
    static function fileTypeIcon($filename_or_ext, $color = '#269def', $return = true)
    {
        $ext = $filename_or_ext;
        if(substr_count($ext, '.')) {
            $ext = self::fileExt($ext);
        }
        $upload_dir = wp_upload_dir();
        $_upload_dir = $upload_dir['basedir'];
        $_upload_url = $upload_dir['baseurl'];
        $file_type_icon_url = '';
        if(file_exists($_upload_dir."/wpdm-file-type-icons/".$ext.".svg"))
            $file_type_icon_url = $_upload_url."/wpdm-file-type-icons/".$ext.".svg";
        else if(file_exists(WPDM_BASE_DIR."assets/file-type-icons/".$ext.".svg"))
            $file_type_icon_url = WPDM_BASE_URL."assets/file-type-icons/".$ext.".svg";
        if($file_type_icon_url === '') {
            ob_start();
            $id = uniqid();
            $ext = strtoupper($ext);
            $color_rgba = wpdm_hex2rgb($color);
            $ext = substr($ext, 0, 3);
            ?>
            <svg id="Layer_<?= $id ?>" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512"
                 xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style
                        type="text/css">
                    .st_<?= $id ?>_0 {
                        fill: rgba(<?php echo $color_rgba; ?>, 0.3);
                    }

                    .st_<?= $id ?>_1 {
                        fill: rgba(<?php echo $color_rgba; ?>, 0.9);
                    }

                    .st_<?= $id ?>_2 {
                        fill: <?php echo $color; ?>;
                    }

                    .st_<?= $id ?>_3 {
                        fill: #FFFFFF;
                    }
                </style>
                <g id="XMLID_168_">
                    <g id="XMLID_83_">
                        <polygon class="st_<?= $id ?>_0" id="XMLID_87_" points="330.7,6 87.9,6 87.9,506 449.2,506 449.2,122.8   "/>
                        <polygon class="st_<?= $id ?>_1" id="XMLID_86_" points="330.7,6 449.2,122.8 330.7,122.8   "/>
                        <rect class="st_<?= $id ?>_1" height="156.1" id="XMLID_85_" width="329" x="62.8" y="298.8"/>
                        <polygon class="st_<?= $id ?>_2" id="XMLID_84_" points="62.8,454.9 87.9,476.1 87.9,454.9   "/>
                    </g>
                    <g xmlns="http://www.w3.org/2000/svg" id="XMLID_3113_">
                        <text x="20%" fill="white" style="font-family: sans-serif;font-size: 725%;font-weight: bold;"
                              y="82%"><?php echo $ext; ?></text>
                    </g>
                </g>
        </svg>
            <?php
            $file_type_icon_url = ob_get_clean();
            $file_type_icon_url = "data:image/svg+xml;base64," . base64_encode($file_type_icon_url);
        }
        $file_type_icon_url = apply_filters("wpdm_file_type_icon", $file_type_icon_url, $filename_or_ext);
        if($return) return $file_type_icon_url;
        echo $file_type_icon_url;
    }

    /**
     * Generates a quick download url for the given file
     * @param $file
     * @param int $expire
     * @return string|void
     */
    public static function instantDownloadURL($file, $expire = 3600)
    {
        $id = uniqid();
        TempStorage::set("__wpdm_instant_download_{$id}", $file, $expire);
        return home_url("/?wpdmidl={$id}");
    }

    /**
     * Get allowed file type for upload
     * @param bool $ARRAY
     * @return array|false|int[]|mixed|string|string[]|void
     */
    function getAllowedFileTypes($ARRAY = true)
    {
        $allowed_file_types = get_option("__wpdm_allowed_file_types", '');
        if($allowed_file_types === '' || !$allowed_file_types) {
            $wp_allowed_file_types = get_allowed_mime_types();
            $wp_allowed_file_exts = array_keys($wp_allowed_file_types);
            $wp_allowed_file_exts = implode(",", $wp_allowed_file_exts);
            $wp_allowed_file_exts = str_replace("|", ",", $wp_allowed_file_exts);
            $allowed_file_types = $wp_allowed_file_exts;
        }
        $wp_allowed_file_types_array = explode(",", $allowed_file_types);
        $wp_allowed_file_types_array = array_map("trim", $wp_allowed_file_types_array);
        return $ARRAY ? $wp_allowed_file_types_array : $allowed_file_types;
    }

    /**
     * Check for blocked file types
     * @param $filename
     * @param string $abspath
     * @return bool
     */
    function isBlocked($filename, $abspath = '')
    {
        $types = $this->getAllowedFileTypes();

        if(in_array('*', $types)) return false;
        $ext = null;
        if($abspath && file_exists($abspath)) {
            $mimes = wp_get_mime_types();
            foreach ($types as $type){
                if(!isset($mimes[$type]))
                    $mimes[$type] = 'application/'.$type;
            }
            $fileinfo = wp_check_filetype_and_ext($abspath, $filename, $mimes);
            $ext = wpdm_valueof($fileinfo,'ext');
        }

        if(!$ext)
            $ext = self::fileExt($filename);

        return !in_array($ext, $types);
    }


    /**
     * <p>Resolve absolute file path from the given relative path, check file in all possible wpdm upload dirs</p>
     * <p>Returns absolute path if file is found, returns false if file is not found</p>
     * @param $rel_path
     * @param null $pid
     * @return bool|string
     */
    function absPath($rel_path, $pid = null){
        $abs_path = false;

        $upload_dir = wp_upload_dir();
        $upload_base_url = $upload_dir['baseurl'];
        $upload_dir = $upload_dir['basedir'];

        if(__::is_url($rel_path)) {
            $abs_path = str_replace($upload_base_url, $upload_dir, $rel_path);
            if(__::is_url($abs_path))
                return $rel_path;
        }

        if(substr_count($rel_path, './'))
            return false;

        $fixed_abs_path = false;
        if(substr_count($rel_path, 'wp-content') > 0 && substr_count($rel_path, WP_CONTENT_DIR) === 0) {
            $rel_rel_path = explode("wp-content", $rel_path);
            $rel_rel_path = end($rel_rel_path);
            $fixed_abs_path = WP_CONTENT_DIR . $rel_rel_path;
        }

        $file_browser_root = get_option('_wpdm_file_browser_root', '');
        $network_upload_dir = explode("sites", UPLOAD_DIR);
        $network_upload_dir = $network_upload_dir[0];
        $network_upload_dir = $network_upload_dir."download-manager-files/";

        if(file_exists($rel_path))
            $abs_path = $rel_path;
        else if(file_exists(UPLOAD_DIR.$rel_path))
            $abs_path = UPLOAD_DIR.$rel_path;
        else if(file_exists($network_upload_dir.$rel_path))
            $abs_path = $network_upload_dir.$rel_path;
        else if(file_exists(ABSPATH.$rel_path))
            $abs_path = ABSPATH.$rel_path;
        else if(file_exists($file_browser_root.$rel_path))
            $abs_path = $file_browser_root.$rel_path;
        else if($fixed_abs_path && file_exists($fixed_abs_path))
            $abs_path = $fixed_abs_path;
        else if($pid){
            $user_upload_dir = null;
            $package = get_post($pid);
            if(is_object($package)){
                $author = get_user_by('id', $package->post_author);
                if($author)
                    $user_upload_dir = UPLOAD_DIR . $author->user_login . '/';
            }
            if($user_upload_dir && file_exists($user_upload_dir.$rel_path))
                $abs_path = $user_upload_dir.$rel_path;
        }

        $abs_path = str_replace('\\','/', $abs_path );
        if(!$abs_path) return null;
        $real_path = realpath($abs_path);
        return $real_path;
    }

    /**
     * Count pages in a PDF file
     * @param $path
     * @return int|string
     */
    function countPDFPages($path) {
        if(self::fileExt($path) !== 'pdf') return 1;
        $fp = @fopen(preg_replace("/\[(.*?)\]/i", "", $path), "r");
        $max = 0;
        if (!$fp) {
            return "Could not open file: $path";
        } else {
            while (!@feof($fp)) {
                $line = @fgets($fp, 255);
                if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
                    preg_match('/[0-9]+/', $matches[0], $matches2);
                    if ($max < $matches2[0]) {
                        $max = trim($matches2[0]);
                        break;
                    }
                }
            }
            @fclose($fp);
        }

        return $max;
    }

    function locateFile($file)
    {
        return $this->absPath($file);
    }
}
