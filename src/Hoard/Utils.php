<?php

namespace Hoard;

class Utils
{


    /**
     * Dechunk an http 'transfer-encoding: chunked' message
     *
     * @param string $chunk the encoded message
     * @return string the decoded message.  If $chunk wasn't encoded properly it will be returned unmodified.
     */
    public static function http_chunked_decode($chunk)
    {
        $pos = 0;
        $len = strlen($chunk);
        $dechunk = null;

        while (($pos < $len) && ($chunkLenHex = substr($chunk,$pos, ($newlineAt = strpos($chunk,"\n",$pos+1))-$pos))) {
            if (! self::is_hex($chunkLenHex)) {
                trigger_error('Value is not properly chunk encoded', E_USER_WARNING);
                return $chunk;
            }
            $pos = $newlineAt + 1;
            $chunkLen = hexdec(rtrim($chunkLenHex, "\r\n"));
            $dechunk .= substr($chunk, $pos, $chunkLen);
            $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
        }
        return $dechunk;
    }


    /**
     * Determine if a string can represent a number in hexadecimal
     *
     * @param string $hex
     * @return boolean true if the string is a hex, otherwise false
     */
    public static function is_hex($hex) {
        $hex = strtolower(trim(ltrim($hex, '0')));
        if (empty($hex)) {
            $hex = 0;
        }
        $dec = hexdec($hex);
        return ($hex === dechex($dec));
    }

}
