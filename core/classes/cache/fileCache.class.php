<?php

class fileCache {
    
    private $cache_prefix = '~~~';
    
    private $cache_options = null;
    
    public function __construct($options='') {
        global $_G_VARS;
        if(!empty($options['cache_path'])) {
            $this->cache_options['cache_path'] = $options['cache_path'];
        } else {
           $this->cache_options['cache_path'] = $_G_VARS['gconfig']['cache']['file']['expire'];
        }
        if(!is_dir($this->cache_options['cache_path'])){
            mkdir($this->cache_options['cache_path'] ,0777,true);
        }
        $this->cache_options['expire'] = isset($options['expire'])?$options['expire']:$_G_VARS['gconfig']['cache']['file']['expire'];
    }
    
        
    private function filename($name) {
        $filename	=	$this->cache_prefix.md5($name).'.php';
        return $this->cache_options['cache_path'].$filename;
    }
    
    public function get($name) {
        $filename   =   $this->filename($name);
        if (! file_exists ( $filename ) || ! is_readable ( $filename )) { 
            return false;
        }
        $content    =   file_get_contents($filename);
        if( false !== $content) {
            $expire  =  (int)substr($content,8, 12);
            if($expire != 0 && time() > filemtime($filename) + $expire) {
                unlink($filename);
                return false;
            }
            $content   =  substr($content,20, -3);
            $content    =   unserialize($content);
            return $content;
        }
        else {
            return false;
        }
    }
    
    public function set($name,$value,$expire=null) {
        if(is_null($expire)) {
            $expire =  $this->cache_options['expire'];
        }
        $filename   =   $this->filename($name);
        $data   =   serialize($value);
        $data    = "<?php\n//".sprintf('%012d',$expire).$data."\n?>";
        $result  =   file_put_contents($filename,$data);
        return true;
    }
    
    public function remove($name) {
        return unlink($this->filename($name));
    }
    
    public function clear() {
        $path   =  $this->cache_options['cache_path'];
        if ( $dir = opendir( $path ) ){
            while ( $file = readdir( $dir )){
                $check = is_dir( $file );
                if ( !$check ){
                    unlink( $path . $file );
                }
            }
            closedir( $dir );
            return true;
        }
    }
}
    
   