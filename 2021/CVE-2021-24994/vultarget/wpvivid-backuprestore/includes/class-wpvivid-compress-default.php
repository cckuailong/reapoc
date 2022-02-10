<?php
abstract class Wpvivid_Compress_Default{
    public $last_error = '';

    abstract public function compress($data);
    abstract public function extract($files,$path = '');
    abstract public function extract_by_files($files,$zip,$path = '');
    abstract public function get_include_zip($files,$allpackages);
    abstract public function listcontent($path);
    abstract public function listnum($path , $includeFolder = false);

    public function getLastError(){
        return $this -> last_error;
    }
    public function getBasename($basename){
        $basename = basename($basename);
        $arr = explode('.',$basename);
        return $arr[0];
    }
    public function _in_array($file,$lists){
        foreach ($lists as $item){
            if(strstr($file,$item)){
                return true;
            }
        }
        return false;
    }
    public function filesplit($max_size,$files){
        $packages = array();
        if($max_size == 0 || empty($max_size)){
            $packages[] = $files;
        }else{
            $sizenum = 0;
            $max_size = str_replace('M', '', $max_size);
            $size = $max_size * 1024 * 1024;
            $package = array();
            $flag = false;

            usort($files, function ($a, $b)
            {
                $a_size=filesize($a);
                $b_size=filesize($b);
                if ($a_size == $b_size)
                    return 0;

                if ($a_size < $b_size)
                    return 1;
                else
                    return -1;
            });

            foreach ($files as $file)
            {
                $sizenum += filesize($file);
                if($sizenum > $size)
                {
                    if(empty($package))
                    {
                        $package[] = $file;
                        $packages[] = $package;
                        $package = array();
                        $sizenum = 0;
                    }
                    else
                    {
                        $packages[] = $package;
                        $package = array();
                        $package[] = $file;
                        $sizenum = filesize($file);
                    }

                }else{
                    $package[] = $file;
                }
            }
            if(!empty($package))
                $packages[] = $package;
        }
        return $packages;
    }
}