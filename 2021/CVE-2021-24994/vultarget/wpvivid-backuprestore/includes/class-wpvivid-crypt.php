<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}

class WPvivid_crypt
{
    private $public_key;
    private $sym_key;

    private $rij;
    private $rsa;

    public function __construct($public_key)
    {
        $this->public_key=$public_key;
        include_once WPVIVID_PLUGIN_DIR . '/vendor/autoload.php';
        $this->rij= new Crypt_Rijndael();
        $this->rsa= new Crypt_RSA();
    }

    public function generate_key()
    {
        $this->sym_key = crypt_random_string(32);
        $this->rij->setKey($this->sym_key);
    }

    public function encrypt_message($message)
    {
        $this->generate_key();
        $key=$this->encrypt_key();
        $len=str_pad(dechex(strlen($key)),3,'0', STR_PAD_LEFT);
        $message=$this->rij->encrypt($message);
        if($message===false)
            return false;
        $message_len = str_pad(dechex(strlen($message)), 16, '0', STR_PAD_LEFT);
        return $len.$key.$message_len.$message;
    }

    public function encrypt_key()
    {
        $this->rsa->loadKey($this->public_key);
        return $this->rsa->encrypt($this->sym_key);
    }

    public function decrypt_message($message)
    {
        $len = substr($message, 0, 3);
        $len = hexdec($len);
        $key = substr($message, 3, $len);

        $cipherlen = substr($message, ($len + 3), 16);
        $cipherlen = hexdec($cipherlen);

        $data = substr($message, ($len + 19), $cipherlen);
        $rsa = new Crypt_RSA();
        $rsa->loadKey($this->public_key);
        $key=$rsa->decrypt($key);
        $rij = new Crypt_Rijndael();
        $rij->setKey($key);
        return $rij->decrypt($data);
    }

    public function encrypt_user_info($user,$pw)
    {
        $user_info['user']=$user;
        $user_info['pw']=$pw;
        $info=json_encode($user_info);
        $this->rsa->loadKey($this->public_key);
        return $this->rsa->encrypt($info);
    }

    public function encrypt_user_token($user,$token)
    {
        $user_info['user']=$user;
        $user_info['token']=$token;
        $info=json_encode($user_info);
        $this->rsa->loadKey($this->public_key);
        return $this->rsa->encrypt($info);
    }

    public function encrypt_token($token)
    {
        $this->rsa->loadKey($this->public_key);
        return $this->rsa->encrypt($token);
    }
}

class WPvivid_Crypt_File
{
    private $key;
    private $rij;

    public function __construct($key)
    {
        include_once WPVIVID_PLUGIN_DIR . '/vendor/autoload.php';
        $this->rij= new Crypt_Rijndael();
        $this->key=$key;
    }

    public function encrypt($file)
    {
        $encrypted_path = dirname($file).'/encrypt_'.basename($file).'.tmp';

        $data_encrypted = 0;
        $buffer_size = 2097152;

        $file_size = filesize($file);

        $this->rij->setKey($this->key);
        $this->rij->disablePadding();
        $this->rij->enableContinuousBuffer();

        if (file_exists($encrypted_path))
        {
            @unlink($encrypted_path);
        }
        $encrypted_handle = fopen($encrypted_path, 'wb+');

        $file_handle = fopen($file, 'rb');

        if($file_handle===false)
        {
            $ret['result']='failed';
            $ret['error']=$file.' file not found';
            return $ret;
        }

        while ($data_encrypted < $file_size)
        {
            $file_part = fread($file_handle, $buffer_size);

            $length = strlen($file_part);
            if (0 != $length % 16)
            {
                $pad = 16 - ($length % 16);
                $file_part = str_pad($file_part, $length + $pad, chr($pad));
            }

            $encrypted_data = $this->rij->encrypt($file_part);

            fwrite($encrypted_handle, $encrypted_data);

            $data_encrypted += $buffer_size;
        }

        fclose($encrypted_handle);
        fclose($file_handle);

        $result_path = $file.'.crypt';

        @rename($encrypted_path, $result_path);

        $ret['result']='success';
        $ret['file_path']=$result_path;
        return $ret;
    }

    public function decrypt($file)
    {
        $file_handle = fopen($file, 'rb');

        if($file_handle===false)
        {
            $ret['result']='failed';
            $ret['error']=$file.' file not found';
            return $ret;
        }

        $decrypted_path = dirname($file).'/decrypt_'.basename($file).'.tmp';

        $decrypted_handle = fopen($decrypted_path, 'wb+');

        $this->rij->setKey($this->key);
        $this->rij->disablePadding();
        $this->rij->enableContinuousBuffer();

        $file_size = filesize($file);
        $bytes_decrypted = 0;
        $buffer_size =2097152;

        while ($bytes_decrypted < $file_size)
        {
            $file_part = fread($file_handle, $buffer_size);

            $length = strlen($file_part);
            if (0 != $length % 16) {
                $pad = 16 - ($length % 16);
                $file_part = str_pad($file_part, $length + $pad, chr($pad));
            }

            $decrypted_data = $this->rij->decrypt($file_part);

            $is_last_block = ($bytes_decrypted + strlen($decrypted_data) >= $file_size);

            $write_bytes = min($file_size - $bytes_decrypted, strlen($decrypted_data));
            if ($is_last_block)
            {
                $is_padding = false;
                $last_byte = ord(substr($decrypted_data, -1, 1));
                if ($last_byte < 16)
                {
                    $is_padding = true;
                    for ($j = 1; $j<=$last_byte; $j++)
                    {
                        if (substr($decrypted_data, -$j, 1) != chr($last_byte))
                            $is_padding = false;
                    }
                }
                if ($is_padding)
                {
                    $write_bytes -= $last_byte;
                }
            }

            fwrite($decrypted_handle, $decrypted_data, $write_bytes);
            $bytes_decrypted += $buffer_size;
        }

        // close the main file handle
        fclose($decrypted_handle);
        // close original file
        fclose($file_handle);

        $fullpath_new = preg_replace('/\.crypt$/', '', $file, 1).'.decrypted.zip';

        @rename($decrypted_path, $fullpath_new);
        $ret['result']='success';
        $ret['file_path']=$fullpath_new;

        return $ret;
    }
}