<?php

class APIUtils
{
    //Function to clean inputs
    public static function sanitizeInputs($data) {
        $sanitized_input = Array();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $sanitized_input[$key] = APIUtils::sanitizeInputs($value);
            }
        } 
        else {
            $sanitized_input = trim(strip_tags($data));
        }
        return $sanitized_input;
    }
    
    public static function isJson($string)  {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function generateUUID() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}

?>