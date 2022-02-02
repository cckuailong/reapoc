<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('DupArchiveHeaderU')) {
class DupArchiveHeaderU
{
    const MaxStandardHeaderFieldLength = 128;
    
    public static function readStandardHeaderField($archiveHandle, $ename)
    {
        $expectedStart = '<'.$ename.'>';
        $expectedEnd = '</'.$ename.'>';

        $startingElement = fread($archiveHandle, strlen($expectedStart));

        if($startingElement !== $expectedStart) {
            throw new Exception("Invalid starting element. Was expecting {$expectedStart} but got {$startingElement}");
        }

        //return DupLiteSnapLibStreamU::streamGetLine($archiveHandle, self::MaxStandardHeaderFieldLength, $expectedEnd);

        $headerString = stream_get_line($archiveHandle, self::MaxStandardHeaderFieldLength, $expectedEnd);

        if ($headerString === false) {
            throw new Exception('Error reading line.');
        }

        return $headerString;
    }
}
}