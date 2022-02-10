<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
include_once WPVIVID_PLUGIN_DIR.'/vendor/autoload.php';
class WPvivid_Net_SFTP extends Net_SFTP
{
    function get($remote_file, $local_file = false, $offset = 0, $length = -1, $callback = null)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $remote_file = $this->_realpath($remote_file);
        if ($remote_file === false) {
            return false;
        }

        $packet = pack('Na*N2', strlen($remote_file), $remote_file, NET_SFTP_OPEN_READ, 0);
        if (!$this->_send_sftp_packet(NET_SFTP_OPEN, $packet)) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                $handle = substr($response, 4);
                break;
            case NET_SFTP_STATUS:
                $this->_logError($response);
                return false;
            default:
                user_error('Expected SSH_FXP_HANDLE or SSH_FXP_STATUS');
                return false;
        }

        if (is_resource($local_file)) {
            $fp = $local_file;
            $stat = fstat($fp);
            $res_offset = $stat['size'];
        } else {
            $res_offset = 0;
            if ($local_file !== false) {
                $fp = fopen($local_file, 'wb');
                if (!$fp) {
                    return false;
                }
            } else {
                $content = '';
            }
        }

        $fclose_check = $local_file !== false && !is_resource($local_file);

        $start = $offset;
        $read = 0;
        while (true) {
            $i = 0;

            while ($i < NET_SFTP_QUEUE_SIZE && ($length < 0 || $read < $length)) {
                $tempoffset = $start + $read;

                $packet_size = $length > 0 ? min($this->max_sftp_packet, $length - $read) : $this->max_sftp_packet;
                $packet = pack('Na*N3', strlen($handle), $handle, $tempoffset / 4294967296, $tempoffset, $packet_size);
                if (!$this->_send_sftp_packet(NET_SFTP_READ, $packet)) {
                    if ($fclose_check) {
                        fclose($fp);
                    }
                    return false;
                }
                $packet = null;
                $read+= $packet_size;
                $i++;
            }

            if (!$i) {
                break;
            }

            $clear_responses = false;
            while ($i > 0) {
                $i--;

                if ($clear_responses) {
                    $this->_get_sftp_packet();
                    continue;
                } else {
                    $response = $this->_get_sftp_packet();
                }

                switch ($this->packet_type) {
                    case NET_SFTP_DATA:
                        $temp = substr($response, 4);
                        $offset+= strlen($temp);
                        if ($local_file === false) {
                            $content.= $temp;
                        } else {
                            fputs($fp, $temp);
                        }

                        if( is_callable($callback)){
                            call_user_func_array($callback,array($offset));
                        }

                        $temp = null;
                        break;
                    case NET_SFTP_STATUS:
                        $this->_logError($response);
                        $clear_responses = true;
                        break;
                    default:
                        if ($fclose_check) {
                            fclose($fp);
                        }
                        user_error('Expected SSH_FX_DATA or SSH_FXP_STATUS');
                }
                $response = null;
            }

            if ($clear_responses) {
                break;
            }
        }

        if ($length > 0 && $length <= $offset - $start) {
            if ($local_file === false) {
                $content = substr($content, 0, $length);
            } else {
                ftruncate($fp, $length + $res_offset);
            }
        }

        if ($fclose_check) {
            fclose($fp);
        }

        if (!$this->_close_handle($handle)) {
            return false;
        }

        return isset($content) ? $content : true;
    }
}