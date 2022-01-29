<?php


class RM_Email
{
    /**
    * Character set (default: utf-8)
    *
    * @var	string
    */
    public $charset		= 'UTF-8';
    
    /**
    * Message headers
    *
    * @var	string[]
    */
    public $headers		= array();
    
    /**
    * Message format.
    *
    * @var	string	'text' or 'html'
    */
    public $content_type	= 'html';
    
    /**
    * Final headers to send
    *
    * @var	string
    */
    public $header_str		= '';
    
    /**
    * Newline character sequence.
    */
    public $newline		= "\r\n";	
    
    /**
    * Subject header
    *
    * @var	string
    */
    public $subject		= '';

   /**
    * Message body
    *
    * @var	string
    */
    public $body		= '';
    
    /**
    * Attachment data
    *
    * @var	array
    */
    public $attachments		= array();
    
    public $to= array();
    public $from;
    public $useAdminFrom= true;
    public $from_name=null;
    public $options= null;    
    /**
    * The constructor can be passed an array of config values
    *
    * @param	array	$config = array()
    * @return	void
    */
    public function __construct($config = array())
    {
        $this->options = new RM_Options;
        $this->initialize($config);
    }
    
    /**
	 * Initialize preferences
	 *
	 * @param	array	$config
	 * @return	RM_Email
    */
    public function initialize(array $config = array())
    {
            $this->clear();

            foreach ($config as $key => $val)
            {
                    if (isset($this->$key))
                    {
                            $method = 'set_'.$key;

                            if (method_exists($this, $method))
                            {
                                    $this->$method($val);
                            }
                            else
                            {
                                    $this->$key = $val;
                            }
                    }
            }

            $this->charset = strtoupper($this->charset);
            return $this;
    }
    
    /**
    * Initialize the Email Data
    *
    * @param	bool
    * @return	void
    */
    public function clear($clear_attachments = FALSE)
    {
            $this->subject		= '';
            $this->body		= '';
            $this->headers		= array();
            $this->header_str	= '';
            $this->_attachments = array();
    }
    
    public function set_from_name($from_name){
        $this->from_name= $from_name;
    }
    /**
    * Set FROM
    *
    * @param	string	$from
    * @param	string	$name
    * @return	void
    */
   public function from($from, $name = '')
   {
        $this->set_header('From', $from);
        $this->from= $from;
   }
   
   /**
    * Add a Header Item
    *
    * @param	string
    * @param	string
    * @return	void
    */
   public function set_header($header, $value)
   {
        $this->headers[$header] = str_replace(array("\n", "\r"), '', $value);
   }
   
   /**
    * Set Content Type
    *
    * @param	string
    */
   public function set_content_type($type = 'text')
   {
           $this->content_type = ($type === 'html') ? 'html' : 'text';
   }
   
   /**
    * Build final headers
    *
    * @return	void
    */
   public function build_headers()
   {
           $this->write_headers();
   }
   
   /**
    * Build final body
    *
    * @return	void
    */
   public function set_body($body)
   {
           $this->body= trim($body);
   }
   
   /**
    * Write Headers as a string
    *
    * @return	void
    */
   public function write_headers()
   {      
           $this->header_str = '';
           foreach ($this->headers as $key => $val)
           {
                $val = trim($val);
                if ($val !== '')
                {
                   $this->header_str .= $key.': '.$val.$this->newline;
                }
           }
           
           if($this->content_type=="plain")
               $this->header_str .= 'Content-Type: text/plain; charset='.$this->charset.$this->newline;
           
           if($this->content_type=="html")
               $this->header_str .= 'Content-Type: text/html; charset='.$this->charset.$this->newline;
           
           $this->header_str = rtrim($this->header_str);
   }
   
   /**
    * Set Reply-to
    *
    * @param	string
    * @param	string
    */
   public function reply_to($replyto, $name = '')
   {
           if (preg_match('/\<(.*)\>/', $replyto, $match))
           {
                   $replyto = $match[1];
           }

           $this->set_header('Reply-To', $name.' <'.$replyto.'>');
   }
        
   /**
    * Send Email
    *
    * @param	bool	$auto_clear = TRUE
    * @return	bool
    */
   public function send($auto_clear = TRUE)
   {
        $this->build_headers();
        if (empty($this->to))
            return false;
        add_action('phpmailer_init', array($this,'config_phpmailer'));
        if(empty($this->attachments))
            return wp_mail($this->to, $this->subject, $this->body);
        else
           return wp_mail($this->to, $this->subject, $this->body, $this->header_str, $this->attachments);

   }
   
   public function config_phpmailer($phpmailer) 	
   {	
       $options = new RM_Options;
        if ($options->get_value_of('enable_smtp') == 'yes') {
            $phpmailer->isSMTP();
            $phpmailer->SMTPDebug = 0;
            $phpmailer->Host = $options->get_value_of('smtp_host');
            $phpmailer->SMTPAuth = $options->get_value_of('smtp_auth') == 'yes' ? true : false;
            $phpmailer->Port = $options->get_value_of('smtp_port');
            $phpmailer->Username = $options->get_value_of('smtp_user_name');
            $phpmailer->Password = $options->get_value_of('smtp_password');
            $phpmailer->SMTPSecure = ($options->get_value_of('smtp_encryption_type') == 'enc_tls') ? 'tls' : (($options->get_value_of('smtp_encryption_type') == 'enc_ssl') ? 'ssl' : '' );
            if(defined('REGMAGIC_ADDON')){
                $phpmailer->From = $options->get_value_of('smtp_senders_email');
            } else {
                $phpmailer->From = $options->get_value_of('smtp_user_name');
            }
            if(!empty($this->from_name))	
                $phpmailer->FromName = $this->from_name;
            else
                $phpmailer->FromName = $options->get_value_of('senders_display_name');
        }
        else
        {
            if($this->useAdminFrom){	
                $phpmailer->From = $options->get_value_of('senders_email');	
                $phpmailer->FromName = $options->get_value_of('senders_display_name');	
            } 
            else{	
                $phpmailer->From= $this->from;	
                if(!empty($this->from_name))	
                     $phpmailer->FromName = $this->from_name;	
            }
        }
        
        
        
        
        if(empty($phpmailer->AltBody))
            $phpmailer->AltBody = RM_Utilities::html_to_text_email($phpmailer->Body);

        return;	
    }
   
   
   /**
    * Set Recipients
    *
    * @param	string
    */
   public function to($to)
   {
       $to = trim($to);
       //$this->set_header('To', $to);
       $this->to= $to; 
   }
   
   /**
    * Set Body
    *
    * @param	string
    */
   public function message($body)
   {
        $body = trim($body);
        $this->body = rtrim(str_replace("\r", '', $body));
   }
   
   /**
    * Set Email Subject
    *
    * @param	string
    */
   public function subject($subject)
   {
        $subject = trim($subject);
        $this->subject= $subject;
        //$this->set_header('Subject', $subject);
   }
   
   /**
    * Assign file attachments
    *
    * @param	string	$file	Can be local path, URL or buffered content
    * @param	string	$disposition = 'attachment'
    * @param	string	$newname = NULL
    * @param	string	$mime = ''
    */
   public function attach($files)
   {
       $this->attachments= $files;
   }
   
    
    public function get_to()
    {
        return $this->to;
    }
    
     
    public function get_subject()
    {
        return $this->subject;
    }
    
    public function get_message()
    {
        return $this->body;
    }
    
    public function get_header()
    {
        return $this->header_str;
    }
        
}