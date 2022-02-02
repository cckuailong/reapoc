<?php
class wp_file_manager_files_restore {

   public function extract($source, $destination) {
      if (extension_loaded('zip') === true) {
            if (file_exists($source) === true) {
                $zip = new ZipArchive();
                $res = $zip->open($source);
                if ($res === TRUE) {
                    $zip->extractTo($destination);
                    $zip->close();
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
   }

}