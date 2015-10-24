<?php

class request {
    
    public  function __construct(){
        
    }
    
    public static function get($var,$default=false){
        if(!$var) return false;
        return (isset($_GET[$var]) && ''!=$_GET[$var])?$_GET[$var]:$default;
    }
    
    public static function post($var,$default=false){
        if(!$var) return false;
        return (isset($_POST[$var]) && ''!=$_POST[$var])?$_POST[$var]:$default;
    }
    
    // helper to try to sort out headers for people who aren't running apache
    public static function get_headers() {
        if (function_exists('apache_request_headers')) {
            // we need this to get the actual Authorization: header
            // because apache tends to tell us it doesn't exist
            $headers = apache_request_headers();
    
            // sanitize the output of apache_request_headers because
            // we always want the keys to be Cased-Like-This and arh()
            // returns the headers in the same case as they are in the
            // request
            $out = array();
            foreach ($headers AS $key => $value) {
            $key = str_replace(
                " ",
                    "-",
                    ucwords(strtolower(str_replace("-", " ", $key)))
            );
            $out[$key] = $value;
    }
    } else {
        // otherwise we don't have apache and are just going to have to hope
        // that $_SERVER actually contains what we need
        $out = array();
        if( isset($_SERVER['CONTENT_TYPE']) )
            $out['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        if( isset($_ENV['CONTENT_TYPE']) )
            $out['Content-Type'] = $_ENV['CONTENT_TYPE'];
    
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) == "HTTP_") {
                // this is chaos, basically it is just there to capitalize the first
                // letter of every word that is not an initial HTTP and strip HTTP
                // code from przemek
                $key = str_replace(
                    " ",
                    "-",
                    ucwords(strtolower(str_replace("_", " ", substr($key, 5))))
                );
                $out[$key] = $value;
            }
        }
    }
    return $out;
    }
}