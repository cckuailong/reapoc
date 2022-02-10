<?php
/**
 * IXR - The Incutio XML-RPC Library
 *
 * Copyright (c) 2010, Incutio Ltd.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *  - Neither the name of Incutio Ltd. nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package IXR
 * @since 1.5
 *
 * @copyright  Incutio Ltd 2010 (http://www.incutio.com)
 * @version    1.7.4 7th September 2010
 * @author     Simon Willison
 * @link       http://scripts.incutio.com/xmlrpc/ Site/manual
 * 
 * Modified Nov 2014 by NextScripts.com to provide SSL compatiblity. 
 * Modified Oct 2016 by NextScripts.com - removed Server part due to PHP7 incompatibility.
 */

class NXS_XMLRPC_Value
{
    var $data;
    var $type;

    function __construct($data, $type = false)
    {
        $this->data = $data;
        if (!$type) {
            $type = $this->calculateType();
        }
        $this->type = $type;
        if ($type == 'struct') {
            // Turn all the values in the array in to new NXS_XMLRPC_Value objects
            foreach ($this->data as $key => $value) {
                $this->data[$key] = new NXS_XMLRPC_Value($value);
            }
        }
        if ($type == 'array') {
            for ($i = 0, $j = count($this->data); $i < $j; $i++) {
                $this->data[$i] = new NXS_XMLRPC_Value($this->data[$i]);
            }
        }
    }

    function calculateType()
    {
        if ($this->data === true || $this->data === false) {
            return 'boolean';
        }
        if (is_integer($this->data)) {
            return 'int';
        }
        if (is_double($this->data)) {
            return 'double';
        }

        // Deal with IXR object types base64 and date
        if (is_object($this->data) && is_a($this->data, 'NXS_XMLRPC_Date')) {
            return 'date';
        }
        if (is_object($this->data) && is_a($this->data, 'NXS_XMLRPC_Base64')) {
            return 'base64';
        }

        // If it is a normal PHP object convert it in to a struct
        if (is_object($this->data)) {
            $this->data = get_object_vars($this->data);
            return 'struct';
        }
        if (!is_array($this->data)) {
            return 'string';
        }

        // We have an array - is it an array or a struct?
        if ($this->isStruct($this->data)) {
            return 'struct';
        } else {
            return 'array';
        }
    }

    function getXml()
    {
        // Return XML for this value
        switch ($this->type) {
            case 'boolean':
                return '<boolean>'.(($this->data) ? '1' : '0').'</boolean>';
                break;
            case 'int':
                return '<int>'.$this->data.'</int>';
                break;
            case 'double':
                return '<double>'.$this->data.'</double>';
                break;
            case 'string':
                return '<string>'.htmlspecialchars($this->data).'</string>';
                break;
            case 'array':
                $return = '<array><data>'."\n";
                foreach ($this->data as $item) {
                    $return .= '  <value>'.$item->getXml()."</value>\n";
                }
                $return .= '</data></array>';
                return $return;
                break;
            case 'struct':
                $return = '<struct>'."\n";
                foreach ($this->data as $name => $value) {
                    $return .= "  <member><name>$name</name><value>";
                    $return .= $value->getXml()."</value></member>\n";
                }
                $return .= '</struct>';
                return $return;
                break;
            case 'date':
            case 'base64':
                return $this->data->getXml();
                break;
        }
        return false;
    }

    /**
     * Checks whether or not the supplied array is a struct or not
     *
     * @param unknown_type $array
     * @return boolean
     */
    function isStruct($array)
    {
        $expected = 0;
        foreach ($array as $key => $value) {
            if ((string)$key != (string)$expected) {
                return true;
            }
            $expected++;
        }
        return false;
    }
}

/**
 * NXS_XMLRPC_MESSAGE
 *
 * @package IXR
 * @since 1.5
 *
 */
class NXS_XMLRPC_Message
{
    var $message;
    var $messageType;  // methodCall / methodResponse / fault
    var $faultCode;
    var $faultString;
    var $methodName;
    var $params;

    // Current variable stacks
    var $_arraystructs = array();   // The stack used to keep track of the current array/struct
    var $_arraystructstypes = array(); // Stack keeping track of if things are structs or array
    var $_currentStructName = array();  // A stack as well
    var $_param;
    var $_value;
    var $_currentTag;
    var $_currentTagContents;
    // The XML parser
    var $_parser;

    function __construct($message)
    {
        $this->message =& $message;
    }

    function parse()
    {
        // first remove the XML declaration
        // merged from WP #10698 - this method avoids the RAM usage of preg_replace on very large messages
        $header = preg_replace( '/<\?xml.*?\?'.'>/', '', substr($this->message, 0, 100), 1);
        $this->message = substr_replace($this->message, $header, 0, 100);
        if (trim($this->message) == '') {
            return false;
        }
        $this->_parser = xml_parser_create();
        // Set XML parser to take the case of tags in to account
        xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, false);
        // Set XML parser callback functions
        xml_set_object($this->_parser, $this);
        xml_set_element_handler($this->_parser, 'tag_open', 'tag_close');
        xml_set_character_data_handler($this->_parser, 'cdata');
        $chunk_size = 262144; // 256Kb, parse in chunks to avoid the RAM usage on very large messages
        do {
            if (strlen($this->message) <= $chunk_size) {
                $final = true;
            }
            $part = substr($this->message, 0, $chunk_size);
            $this->message = substr($this->message, $chunk_size);
            if (!xml_parse($this->_parser, $part, $final)) {
                return false;
            }
            if ($final) {
                break;
            }
        } while (true);
        xml_parser_free($this->_parser);

        // Grab the error messages, if any
        if ($this->messageType == 'fault') {
            $this->faultCode = $this->params[0]['faultCode'];
            $this->faultString = $this->params[0]['faultString'];
        }
        return true;
    }

    function tag_open($parser, $tag, $attr)
    {
        $this->_currentTagContents = '';
        $this->currentTag = $tag;
        switch($tag) {
            case 'methodCall':
            case 'methodResponse':
            case 'fault':
                $this->messageType = $tag;
                break;
                /* Deal with stacks of arrays and structs */
            case 'data':    // data is to all intents and puposes more interesting than array
                $this->_arraystructstypes[] = 'array';
                $this->_arraystructs[] = array();
                break;
            case 'struct':
                $this->_arraystructstypes[] = 'struct';
                $this->_arraystructs[] = array();
                break;
        }
    }

    function cdata($parser, $cdata)
    {
        $this->_currentTagContents .= $cdata;
    }

    function tag_close($parser, $tag)
    {
        $valueFlag = false;
        switch($tag) {
            case 'int':
            case 'i4':
                $value = (int)trim($this->_currentTagContents);
                $valueFlag = true;
                break;
            case 'double':
                $value = (double)trim($this->_currentTagContents);
                $valueFlag = true;
                break;
            case 'string':
                $value = (string)trim($this->_currentTagContents);
                $valueFlag = true;
                break;
            case 'dateTime.iso8601':
                $value = new NXS_XMLRPC_Date(trim($this->_currentTagContents));
                $valueFlag = true;
                break;
            case 'value':
                // "If no type is indicated, the type is string."
                if (trim($this->_currentTagContents) != '') {
                    $value = (string)$this->_currentTagContents;
                    $valueFlag = true;
                }
                break;
            case 'boolean':
                $value = (boolean)trim($this->_currentTagContents);
                $valueFlag = true;
                break;
            case 'base64':
                $value = base64_decode($this->_currentTagContents);
                $valueFlag = true;
                break;
                /* Deal with stacks of arrays and structs */
            case 'data':
            case 'struct':
                $value = array_pop($this->_arraystructs);
                array_pop($this->_arraystructstypes);
                $valueFlag = true;
                break;
            case 'member':
                array_pop($this->_currentStructName);
                break;
            case 'name':
                $this->_currentStructName[] = trim($this->_currentTagContents);
                break;
            case 'methodName':
                $this->methodName = trim($this->_currentTagContents);
                break;
        }

        if ($valueFlag) {
            if (count($this->_arraystructs) > 0) {
                // Add value to struct or array
                if ($this->_arraystructstypes[count($this->_arraystructstypes)-1] == 'struct') {
                    // Add to struct
                    $this->_arraystructs[count($this->_arraystructs)-1][$this->_currentStructName[count($this->_currentStructName)-1]] = $value;
                } else {
                    // Add to array
                    $this->_arraystructs[count($this->_arraystructs)-1][] = $value;
                }
            } else {
                // Just add as a paramater
                $this->params[] = $value;
            }
        }
        $this->_currentTagContents = '';
    }
}


/**
 * NXS_XMLRPC_Request
 *
 * @package IXR
 * @since 1.5
 */
class NXS_XMLRPC_Request
{
    var $method;
    var $args;
    var $xml;

    function __construct($method, $args)
    {
        $this->method = $method;
        $this->args = $args;
        $this->xml = <<<EOD
<?xml version="1.0"?>
<methodCall>
<methodName>{$this->method}</methodName>
<params>

EOD;
        foreach ($this->args as $arg) {
            $this->xml .= '<param><value>';
            $v = new NXS_XMLRPC_Value($arg);
            $this->xml .= $v->getXml();
            $this->xml .= "</value></param>\n";
        }
        $this->xml .= '</params></methodCall>';
    }

    function getLength()
    {
       
        return nxs_strLen($this->xml);
    }

    function getXml()
    {
        return $this->xml;
    }
}

/**
 * NXS_XMLRPC_Client
 *
 * @package IXR
 * @since 1.5
 *
 */
class NXS_XMLRPC_Client
{
    var $server;
    var $port;
    var $path;
    var $scheme;
    var $useragent;
    var $response;
    var $message = false;
    var $debug = false;
    var $timeout;

    // Storage place for an error message
    var $error = false;

    function __construct($server, $path = false, $port = 80, $timeout = 25)
    {
        if (!$path) {
            // Assume we have been given a URL instead
            $bits = parse_url($server);
            $this->server = $bits['host'];
            $this->scheme = isset($bits['scheme']) ? $bits['scheme'] : 'http';
            $this->port = isset($bits['port']) ? $bits['port'] : ($bits['scheme']=='https'?443:80);
            $this->path = isset($bits['path']) ? $bits['path'] : '/';

            // Make absolutely sure we have a path
            if (!$this->path) {
                $this->path = '/';
            }
        } else {
            $this->server = $server;
            $this->path = $path;
            $this->port = $port;
        }
        $this->useragent = 'The Incutio XML-RPC PHP Library';
        $this->timeout = $timeout;
    }

    function queryFS()
    {
        $args = func_get_args();
        $method = array_shift($args);
        $request = new NXS_XMLRPC_Request($method, $args);
        $length = $request->getLength();
        $xml = $request->getXml();
        $r = "\r\n";
        $request  = "POST {$this->path} HTTP/1.0$r";

        // Merged from WP #8145 - allow custom headers
        $this->headers['Host']          = $this->server;
        $this->headers['Content-Type']  = 'text/xml';
        $this->headers['User-Agent']    = $this->useragent;
        //$this->headers['Content-Length']= $length;

        foreach( $this->headers as $header => $value ) {
            $request .= "{$header}: {$value}{$r}";
        }
        $request .= $r;

        $request .= $xml;

        // Now send the request
        if ($this->debug) {
            echo '<pre class="NXS_XMLRPC_request">'.htmlspecialchars($request)."\n</pre>\n\n";
        }

        if ($this->timeout) {
            $fp = @fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
        } else {
            $fp = @fsockopen($this->server, $this->port, $errno, $errstr);
        }
        if (!$fp) {
            $this->error = new NXS_XMLRPC_Error(-32300, 'transport error - could not open socket');
            return false;
        }
        fputs($fp, $request);
        $contents = '';
        $debugContents = '';
        $gotFirstLine = false;
        $gettingHeaders = true;
        while (!feof($fp)) {
            $line = fgets($fp, 4096);
            if (!$gotFirstLine) {
                // Check line for '200'
                if (strstr($line, '200') === false) {
                    $this->error = new NXS_XMLRPC_Error(-32300, 'transport error - HTTP status code was not 200');
                    return false;
                }
                $gotFirstLine = true;
            }
            if (trim($line) == '') {
                $gettingHeaders = false;
            }
            if (!$gettingHeaders) {
                // merged from WP #12559 - remove trim
                $contents .= $line;
            }
            if ($this->debug) {
                $debugContents .= $line;
            }
        }
        if ($this->debug) {
            echo '<pre class="NXS_XMLRPC_response">'.htmlspecialchars($debugContents)."\n</pre>\n\n";
        }

        // Now parse what we've got back
        $this->message = new NXS_XMLRPC_Message($contents);
        if (!$this->message->parse()) {
            // XML error
            $this->error = new NXS_XMLRPC_Error(-32700, 'parse error. not well formed');
            return false;
        }

        // Is the message a fault?
        if ($this->message->messageType == 'fault') {
            $this->error = new NXS_XMLRPC_Error($this->message->faultCode, $this->message->faultString);
            return false;
        }

        // Message must be OK
        return true;
    }

    /**
     * Set the query to send to the XML-RPC Server
     * @since 0.1.0
     */
    function query()
    {
        $args = func_get_args();
        $method = array_shift($args);
        $request = new NXS_XMLRPC_Request($method, $args);
        $length = $request->getLength();
        $xml = $request->getXml();

        if ($this->debug) {
            echo '<pre>'.htmlspecialchars($xml)."\n</pre>\n\n";
        }

        //This is where we deviate from the normal query()
        //Rather than open a normal sock, we will actually use the cURL
        //extensions to make the calls, and handle the SSL stuff.

        //Since 04Aug2004 (0.1.3) - Need to include the port (duh...)
        //Since 06Oct2004 (0.1.4) - Need to include the colon!!!
        //        (I swear I've fixed this before... ESP in live... But anyhu...)
        $curl=curl_init($this->scheme.'://' . $this->server . ':' . $this->port . $this->path);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.44 Safari/537.36"); 

        //Since 23Jun2004 (0.1.2) - Made timeout a class field
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);

        if ($this->debug) {
            curl_setopt($curl, CURLOPT_VERBOSE, 1);
        }


        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($curl, CURLOPT_PORT, $this->port);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml","Content-length: {$length}"));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        global $nxs_skipSSLCheck; if ($nxs_skipSSLCheck===true) curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        // Call cURL to do it's stuff and return us the content
        $contents = curl_exec($curl); $err = curl_errno($curl); $errmsg = curl_error($curl); 
        curl_close($curl);

        // Check for 200 Code in $contents
        if (!strstr($contents, '200 OK')) {
            //There was no "200 OK" returned - we failed
            $this->error = new NXS_XMLRPC_Error(-32300, 'transport error - HTTP status code was not 200 - '.$err.' - '.$errmsg.' | '.print_r($contents, true));
            return false;
        }

        if ($this->debug) {
            echo '<pre>'.htmlspecialchars($contents)."\n</pre>\n\n";
        }
        // Now parse what we've got back
        // Since 20Jun2004 (0.1.1) - We need to remove the headers first
        // Why I have only just found this, I will never know...
        // So, remove everything before the first <
        $contents = substr($contents,strpos($contents, '<'));

        $this->message = new NXS_XMLRPC_Message($contents);
        if (!$this->message->parse()) {
            // XML error
            $this->error = new NXS_XMLRPC_Error(-32700, 'parse error. not well formed');
            return false;
        }
        // Is the message a fault?
        if ($this->message->messageType == 'fault') {
            $this->error = new NXS_XMLRPC_Error($this->message->faultCode, $this->message->faultString);
            return false;
        }

        // Message must be OK
        return true;
    }
    
    function getResponse()
    {
        // methodResponses can only have one param - return that
        return $this->message->params[0];
    }

    function isError()
    {
        return (is_object($this->error));
    }

    function getErrorCode()
    {
        return $this->error->code;
    }

    function getErrorMessage()
    {
        return $this->error->message;
    }
}


/**
 * NXS_XMLRPC_Error
 *
 * @package IXR
 * @since 1.5
 */
class NXS_XMLRPC_Error
{
    var $code;
    var $message;

    function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = htmlspecialchars($message);
    }

    function getXml()
    {
        $xml = <<<EOD
<methodResponse>
  <fault>
    <value>
      <struct>
        <member>
          <name>faultCode</name>
          <value><int>{$this->code}</int></value>
        </member>
        <member>
          <name>faultString</name>
          <value><string>{$this->message}</string></value>
        </member>
      </struct>
    </value>
  </fault>
</methodResponse>

EOD;
        return $xml;
    }
}

/**
 * NXS_XMLRPC_Date
 *
 * @package IXR
 * @since 1.5
 */
class NXS_XMLRPC_Date {
    var $year;
    var $month;
    var $day;
    var $hour;
    var $minute;
    var $second;
    var $timezone;

    function __construct($time)
    {
        // $time can be a PHP timestamp or an ISO one
        if (is_numeric($time)) {
            $this->parseTimestamp($time);
        } else {
            $this->parseIso($time);
        }
    }

    function parseTimestamp($timestamp)
    {
        $this->year = date('Y', $timestamp);
        $this->month = date('m', $timestamp);
        $this->day = date('d', $timestamp);
        $this->hour = date('H', $timestamp);
        $this->minute = date('i', $timestamp);
        $this->second = date('s', $timestamp);
        $this->timezone = '';
    }

    function parseIso($iso)
    {
        $this->year = substr($iso, 0, 4);
        $this->month = substr($iso, 4, 2);
        $this->day = substr($iso, 6, 2);
        $this->hour = substr($iso, 9, 2);
        $this->minute = substr($iso, 12, 2);
        $this->second = substr($iso, 15, 2);
        $this->timezone = substr($iso, 17);
    }

    function getIso()
    {
        return $this->year.$this->month.$this->day.'T'.$this->hour.':'.$this->minute.':'.$this->second.$this->timezone;
    }

    function getXml()
    {
        return '<dateTime.iso8601>'.$this->getIso().'</dateTime.iso8601>';
    }

    function getTimestamp()
    {
        return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
    }
}

/**
 * NXS_XMLRPC_Base64
 *
 * @package IXR
 * @since 1.5
 */
class NXS_XMLRPC_Base64
{
    var $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    function getXml()
    {
        return '<base64>'.base64_encode($this->data).'</base64>';
    }
}

/**
 * NXS_XMLRPC_ClientMulticall
 *
 * @package IXR
 * @since 1.5
 */
class NXS_XMLRPC_ClientMulticall extends NXS_XMLRPC_Client
{
    var $calls = array();

    function __construct($server, $path = false, $port = 80)
    {
        parent::__construct($server, $path, $port);
        $this->useragent = 'The Incutio XML-RPC PHP Library (multicall client)';
    }

    function addCall()
    {
        $args = func_get_args();
        $methodName = array_shift($args);
        $struct = array(
            'methodName' => $methodName,
            'params' => $args
        );
        $this->calls[] = $struct;
    }

    function query()
    {
        // Prepare multicall, then call the parent::query() method
        return parent::query('system.multicall', $this->calls);
    }
}

/**
 * Client for communicating with a XML-RPC Server over HTTPS.
 *
 * @author Jason Stirk <jstirk@gmm.com.au> (@link http://blog.griffin.homelinux.org/projects/xmlrpc/)
 * @version 0.2.0 26May2005 08:34 +0800
 * @copyright (c) 2004-2005 Jason Stirk
 * @package IXR
 */
class NXS_XMLRPC_ClientSSL extends NXS_XMLRPC_Client
{
    /**
     * Filename of the SSL Client Certificate
     * @access private
     * @since 0.1.0
     * @var string
     */
    var $_certFile;

    /**
     * Filename of the SSL CA Certificate
     * @access private
     * @since 0.1.0
     * @var string
     */
    var $_caFile;

    /**
     * Filename of the SSL Client Private Key
     * @access private
     * @since 0.1.0
     * @var string
     */
    var $_keyFile;

    /**
     * Passphrase to unlock the private key
     * @access private
     * @since 0.1.0
     * @var string
     */
    var $_passphrase;

    /**
     * Constructor
     * @param string $server URL of the Server to connect to
     * @since 0.1.0
     */
    function __construct($server, $path = false, $port = 443, $timeout = false)
    {
        parent::__construct($server, $path, $port, $timeout);
        $this->useragent = 'The Incutio XML-RPC PHP Library for SSL';

        // Set class fields
        $this->_certFile=false;
        $this->_caFile=false;
        $this->_keyFile=false;
        $this->_passphrase='';
    }

    /**
     * Set the client side certificates to communicate with the server.
     *
     * @since 0.1.0
     * @param string $certificateFile Filename of the client side certificate to use
     * @param string $keyFile Filename of the client side certificate's private key
     * @param string $keyPhrase Passphrase to unlock the private key
     */
    function setCertificate($certificateFile, $keyFile, $keyPhrase='')
    {
        // Check the files all exist
        if (is_file($certificateFile)) {
            $this->_certFile = $certificateFile;
        } else {
            die('Could not open certificate: ' . $certificateFile);
        }

        if (is_file($keyFile)) {
            $this->_keyFile = $keyFile;
        } else {
            die('Could not open private key: ' . $keyFile);
        }

        $this->_passphrase=(string)$keyPhrase;
    }

    function setCACertificate($caFile)
    {
        if (is_file($caFile)) {
            $this->_caFile = $caFile;
        } else {
            die('Could not open CA certificate: ' . $caFile);
        }
    }

    /**
     * Sets the connection timeout (in seconds)
     * @param int $newTimeOut Timeout in seconds
     * @returns void
     * @since 0.1.2
     */
    function setTimeOut($newTimeOut)
    {
        $this->timeout = (int)$newTimeOut;
    }

    /**
     * Returns the connection timeout (in seconds)
     * @returns int
     * @since 0.1.2
     */
    function getTimeOut()
    {
        return $this->timeout;
    }

    /**
     * Set the query to send to the XML-RPC Server
     * @since 0.1.0
     */
    function query()
    {
        $args = func_get_args();
        $method = array_shift($args);
        $request = new NXS_XMLRPC_Request($method, $args);
        $length = $request->getLength();
        $xml = $request->getXml();

        if ($this->debug) {
            echo '<pre>'.htmlspecialchars($xml)."\n</pre>\n\n";
        }

        //This is where we deviate from the normal query()
        //Rather than open a normal sock, we will actually use the cURL
        //extensions to make the calls, and handle the SSL stuff.

        //Since 04Aug2004 (0.1.3) - Need to include the port (duh...)
        //Since 06Oct2004 (0.1.4) - Need to include the colon!!!
        //        (I swear I've fixed this before... ESP in live... But anyhu...)
        $curl=curl_init('https://' . $this->server . ':' . $this->port . $this->path);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        //Since 23Jun2004 (0.1.2) - Made timeout a class field
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);

        if ($this->debug) {
            curl_setopt($curl, CURLOPT_VERBOSE, 1);
        }

        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($curl, CURLOPT_PORT, $this->port);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml","Content-length: {$length}"));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));                                    

        // Process the SSL certificates, etc. to use
        if (!($this->_certFile === false)) {
            // We have a certificate file set, so add these to the cURL handler
            curl_setopt($curl, CURLOPT_SSLCERT, $this->_certFile);
            curl_setopt($curl, CURLOPT_SSLKEY, $this->_keyFile);

            if ($this->debug) {
                echo "SSL Cert at : " . $this->_certFile . "\n";
                echo "SSL Key at : " . $this->_keyFile . "\n";
            }

            // See if we need to give a passphrase
            if (!($this->_passphrase === '')) {
                curl_setopt($curl, CURLOPT_SSLCERTPASSWD, $this->_passphrase);
            }

            if ($this->_caFile === false) {
                // Don't verify their certificate, as we don't have a CA to verify against
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            } else {
                // Verify against a CA
                curl_setopt($curl, CURLOPT_CAINFO, $this->_caFile);
            }
        }
        global $nxs_skipSSLCheck; if ($nxs_skipSSLCheck===true) curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // Call cURL to do it's stuff and return us the content
        $contents = curl_exec($curl); $err = curl_errno($curl); $errmsg = curl_error($curl);
        curl_close($curl);

        // Check for 200 Code in $contents
        if (!strstr($contents, '200 OK')) {
            //There was no "200 OK" returned - we failed
            $this->error = new NXS_XMLRPC_Error(-32300, 'transport error - HTTP status code was not 200! - '.$err.' - '.$errmsg.' | '.print_r($contents, true));
            return false;
        }

        if ($this->debug) {
            echo '<pre>'.htmlspecialchars($contents)."\n</pre>\n\n";
        }
        // Now parse what we've got back
        // Since 20Jun2004 (0.1.1) - We need to remove the headers first
        // Why I have only just found this, I will never know...
        // So, remove everything before the first <
        $contents = substr($contents,strpos($contents, '<'));

        $this->message = new NXS_XMLRPC_Message($contents);
        if (!$this->message->parse()) {
            // XML error
            $this->error = new NXS_XMLRPC_Error(-32700, 'parse error. not well formed');
            return false;
        }
        // Is the message a fault?
        if ($this->message->messageType == 'fault') {
            $this->error = new NXS_XMLRPC_Error($this->message->faultCode, $this->message->faultString);
            return false;
        }

        // Message must be OK
        return true;
    }
}

?>