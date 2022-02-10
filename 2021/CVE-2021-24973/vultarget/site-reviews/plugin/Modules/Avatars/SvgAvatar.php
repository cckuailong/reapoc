<?php

namespace GeminiLabs\SiteReviews\Modules\Avatars;

abstract class SvgAvatar
{
    /**
     * @param string $from
     * @return string
     */
    public function create($from)
    {
        return $this->save($this->generate($from), $this->filename($from));
    }

    /**
     * @param string $from
     * @return string
     */
    abstract public function generate($from);

    /**
     * @param string $from
     * @return string
     */
    abstract protected function filename($from);

    /**
     * @param string $contents
     * @param string $name
     * @return string
     */
    protected function save($contents, $name)
    {
        $uploadsDir = wp_upload_dir();
        $baseDir = trailingslashit($uploadsDir['basedir']);
        $baseUrl = trailingslashit($uploadsDir['baseurl']);
        $pathDir = trailingslashit(glsr()->id).trailingslashit('avatars');
        $filename = sprintf('%s.svg', $name);
        $filepath = $baseDir.$pathDir.$filename;
        if (!file_exists($filepath)) {
            wp_mkdir_p($baseDir.$pathDir);
            $fp = @fopen($filepath, 'wb');
            if (false === $fp) {
                return '';
            }
            mbstring_binary_safe_encoding();
            $dataLength = strlen($contents);
            $bytesWritten = fwrite($fp, $contents);
            reset_mbstring_encoding();
            fclose($fp);
            if ($dataLength !== $bytesWritten) {
                return '';
            }
            chmod($filepath, (fileperms(ABSPATH.'index.php') & 0777 | 0644));
        }
        return set_url_scheme($baseUrl.$pathDir.$filename);
    }
}
