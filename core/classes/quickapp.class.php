<?php
if (!defined('IN_QA')) {
    exit();
}
/**
 * @author andy
 */
class quickapp {
    
    public static  function start() {
        quickservice::get_classLoader()->load_controller($_GET['ctrl']);
        $controller = $_GET['ctrl'].'Controller';
        quickservice::get_classLoader()->load('/apps/'.$_GET['app'].'/config.inc.php');
        $c = new $controller();        	 
        if(method_exists($c, $_GET['act'])) {
        	//load app下的函数
        	quickservice::get_classLoader()->load('/apps/'.$_GET['app'].'/app.func.php');   	
        	$c->valid_action($_GET['act']);//验证action合法性
            $c->{$_GET['act']}();
        } else {
            $c->action_404();
        }
    }   
}

class quickservice {
    
    /**
     * 类加载服务
     * @return ClassLoader
     */
    public static function get_classLoader(){
        return ClassLoader::instance();
    }
    /**
     * 文件缓存服务
     * @param unknown $expire
     * @param unknown $cache_path
     * @return fileCache
     */
    public static function get_file_cache($expire,$cache_path){
        quickservice::get_classLoader()->load_class('cache/fileCache');
        return new fileCache(array('cache_path'=>$cache_path,'expire'=>$expire));
    }
}


//jwt认证类封装
//https://github.com/firebase/php-jwt
class JWT_Auth {
    /**
     * HS256 默认算法
     * @param unknown $token
     * @param unknown $key
     * @return string
     */
    public static function encode($token,$key){
        quickservice::get_classLoader()->load('/library/php-jwt/JWT.PHP');
        return  \Firebase\JWT\JWT::encode($token, $key);
    }   
    
    public static function decode($jwt,$key){
        quickservice::get_classLoader()->load('/library/php-jwt/JWT.PHP');
        return  \Firebase\JWT\JWT::decode($jwt, $key, array('HS256'));
    }
}

/**
 *  实例化模型，支持实例化其他app下面的,user.user
 * @param unknown $model_name 类名
 * @return Ambigous <basemodel>|Ambigous <unknown, basemodel>|boolean
 */
function getmodel($model_name = ''){
    static $_model  = array();
    if(!$model_name){
        if(!isset($_model['basemodel'])) {  
            $_model['basemodel'] = new basemodel();//basemodel支持指定表名   
        }  
        return $_model['basemodel'];
    }
    
    if(strpos($model_name,'.')) {
        list($app,$model)    =  explode('.',$model_name);
        if(!isset($_model[$model_name])) {
            quickservice::get_classLoader()->load_model($model,$app);
        	$model.= 'model';
            $_model[$model_name] = new $model();
        }
        return $_model[$model_name];
    } else {
        $tmp = $model_name.'Model';
        if(!isset($_model[$tmp])) {
        	quickservice::get_classLoader()->load_model($model_name);
            $_model[$tmp] = new $tmp($model_name);
        }
        return $_model[$tmp];
    }
    return false;
}

class controller {
   protected $actions = array();
   protected $_view;
   protected $_return_array = array('code'=>-1,'msg'=>'unkonwn','data'=>array());
   function __construct() { 
       $this->init_actions();
       $this->init_auth();
       $this->init();
   }
   
   protected   function init(){}
   protected   function init_auth(){}
   protected   function init_actions(){}
   
   public function action_404(){
   		$this->show_json('0','404');
   }
   
   public function valid_action($action = ''){
   	  if(!in_array($action, $this->actions)){
   	  		$this->show_json('0','不合法的请求'.$action);
   	  }  
   }
   
   /**
    * 接口返回
    * @param number $api_code 200 代表成功，400客户端问题  500 服务器端问题 
    * @param string $msg ，和API code对应的消息，
    * @param unknown $data ,建议按照下面的格式来返回
    *  'data':{
    *      'status':0,  //接口业务说明状态码
    *      'msg':'用户名错误',//接口业务逻辑消息
    *      'info':'',//自定义返回数据
    *  }
    * 
    */
   public function show_json($api_code=200,$msg = false,$data = array()){
       if(!isset($api_code)) {
           trigger_error('Invalid response code');
       }
       $this->_return_array['code'] = $api_code;
       if($msg){
           $this->_return_array['msg'] = $msg;
       }
       if($data){
           $this->_return_array['data'] = $data;
       }
       exit(json_encode($this->_return_array));
   }
   
}

class basemodel {
    
    protected $db_type      = 'mysql';
    protected $db_username     = '';
    protected $db_password     = '';
    protected $db_name      = '';
    protected $db_port         = 3306;
    protected $db_host         = '127.0.0.1';
    protected $database;
    protected $table_prefix = '';
    protected $table_name = '';
    protected $real_table_name = '';
    public $last_msg = '';
    
    function __construct($table_name = ''){
        $this->table_name = $table_name;
        global $_G_VARS;
        $config = $_G_VARS['gconfig']['db'];
        //[db_type] => mysql [db_host] => localhost [db_username] => test [db_password] => test
        // [db_port] => 3306 [db_name] => quickapp [charset] => utf8 [table_prefix] => qa_ 
        if($config['db_type'])     $this->db_type      = $config['db_type'];
        if($config['db_username']) $this->db_username  = $config['db_username'];
        if($config['db_password']) $this->db_password  = $config['db_password'];
        if($config['db_name'])     $this->db_name      = $config['db_name'];
        if($config['db_port'])     $this->db_port      = $config['db_port'];
        if($config['db_host'])     $this->db_host      = $config['db_host'];
        if($config['table_prefix'])$this->table_prefix = $config['table_prefix'];
        
        $this->initdb();
        
        unset($config);
    }
    
    /**
     * 忽略前缀
     * @param unknown $full_table_name
     * @return boolean
     */
    protected function set_real_tablename($real_table_name){
        if(!$real_table_name){
            return false;
        }
        $this->real_table_name =  $real_table_name;
    }
    
    public function get_tablename(){
        if($this->real_table_name){
            return $this->real_table_name;
        }
        return $this->table_prefix.$this->table_name;
    }
    
    private function initdb(){
        ClassLoader::instance()->load_class('medoo');
        if(!$this->database){
            try {
                $this->database = new medoo(array(
                    // required
                    'database_type' => $this->db_type,
                    'database_name' => $this->db_name,
                    'server' => $this->db_host,
                    'username' => $this->db_username,
                    'password' => $this->db_password,
                    'charset' => 'utf8',
                
                    // optional
                    'port' => $this->db_port,
                    // driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
                    'option' => array(
                        PDO::ATTR_CASE => PDO::CASE_NATURAL
                    )
                ));
            }catch (Exception $e) {
                //trigger_error("Can't connect to database ".$this->database);
                exit('connect database error ');
            }
        }
    }
    
    public function getdb(){
        return $this->database;
    }
    
    public function get($where,$columns='*',$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->get($this->get_tablename(),$columns,$where);   
    }
    
    /**
     * You can use "*" as columns parameter to fetch all columns, 
     * but to increase the performance, providing the target columns is much better.
     * @param string $columns
     * @param unknown $where
     * @return Ambigous <boolean, multitype:>
     */
    public function select( $where,$columns='*',$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->select($this->get_tablename(),$columns,$where);
    }
    
    public function insert( $data,$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->insert($this->get_tablename(), $data);
    }
    
    public function update($data, $where=null,$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->update($this->get_tablename(), $data,$where);
    }
    
    public function delete($where,$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->delete($this->get_tablename(), $where);
    }
    
    //TODO :Other 
    public function replace( $column, $search, $replace, $where,$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->replace($this->get_tablename(), $column,$search,$replace,$where);
    }
    
    public function has( $where,$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->has($this->get_tablename(), $where);
    }
    
    public function count($where,$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->count($this->get_tablename(), $where);
    }
    
    public function max($column, $where,$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->max($this->get_tablename(), $column, $where);
    }
    
    public function min( $column, $where,$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->min($this->get_tablename(), $column, $where);
    }
    
    public function avg( $column, $where,$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->avg($this->get_tablename(), $column, $where);
    }
    
    public function sum( $column, $where,$table_name = ''){
        if($table_name){
            $this->table_name = $table_name;
        }
        return $this->database->sum($this->get_tablename(), $column, $where);
    }
    
    public function query($query){
        return $this->database->database->query($query);
    }
    
    public function last_query(){
        return $this->database->last_query();
    }
    
    public function error(){
        return $this->database->error();
    }   
}

class ClassLoader {
    private $_class_cache = array();
    static public function instance() {
        static $loader;
        if(empty($loader)) {
            $loader = new ClassLoader();
        }
        return $loader;
    }
    
    /**
     * 
     * 加载框架下的class或者app下面的classes
     * 支持自classes开始的/分割的多层路径，例如加载user模块下classes下面 auth下的sina类：$class_or_path可为auth/sina即可
     * 
     * @param unknown $class_or_path
     * @return boolean
     */
    function load_class($class_or_path){
        if(isset($this->_class_cache['classes'][$class_or_path])) {
            return true;
        }
        $file = ROOT_PATH.'/core/classes/'.$class_or_path.'.class.php';  
        if(file_exists($file)) {
            include $file;
            $this->_class_cache['classes'][$class_or_path] = true;
            return true;
        } else {
            $file2 = ROOT_PATH.'/apps/'.$_GET['app'].'/classes/'.$class_or_path.'.class.php';
            if(file_exists($file2)) {
                include $file2;
                $this->_class_cache['classes'][$class_or_path] = true;
                return true;
            }
            trigger_error('Invalid  '.$file2,E_USER_ERROR);
            return false;
        }   
    }
    
    /**
     * 加载model，支持调用其他模块，例如user。auth
     * @param unknown_type $class_or_path
     * @param unknown_type $app app名称
     * @return boolean
     */
    function load_model($class_or_path,$app = '') {
        if (isset($this->_class_cache['model'][$class_or_path])) {
            return true;
        }
        if($app){
            $file = ROOT_PATH . '/apps/'.$app .'/model/' . $class_or_path . '.mod.php';
        } else {
            $file = ROOT_PATH . '/apps/'.$_GET['app'] .'/model/' . $class_or_path . '.mod.php';
        }
        
        if (file_exists($file)) {
            include $file;
            $this->_class_cache['model'][$class_or_path] = true;
            return true;
        } else {
            trigger_error('Invalid '.$file, E_USER_ERROR);
            return false;
        }
    }
    
    function load_controller($class_or_path=''){
        if(isset($this->_class_cache['controller'][$class_or_path])) {
            return true;
        }
        if(empty($class_or_path)){//默认规则
        	$class_or_path = $_GET['ctrl'];
        	$file = ROOT_PATH.'/apps/'.$_GET['app'].'/controller/'.$class_or_path.'.ctrl.php';
        } else if(strpos($class_or_path, '.')){//加载其他模块下面的controller
            list($app,$ctrl_name) = explode('.', $class_or_path);
            $file = ROOT_PATH.'/apps/'.$app.'/controller/'.$ctrl_name.'.ctrl.php';
        } else {
            $file = ROOT_PATH.'/apps/'.$_GET['app'].'/controller/'.$class_or_path.'.ctrl.php';
        }
        if(file_exists($file)) {
            include $file;
            $this->_class_cache['controller'][$class_or_path] = true;
            return true;
        }  else {
            trigger_error('Invalid '.$file, E_USER_ERROR);
            return false;
        }
    }
    
    /**
     * 特殊需求提供扩展
     * 基于根目录，提供无限制扩展,不要load function model api conroller classes 
     * 可以load 跟目录下的library中的任何文件
     */
    function load($class_with_full_name=''){
        if(isset($this->_class_cache['extension'][$class_with_full_name])){
            return true;
        }
        $file = ROOT_PATH.$class_with_full_name;
        if(file_exists($file)){
           include $file;
           $this->_class_cache['extension'][$class_with_full_name] = true; 
           return true;
        }
        trigger_error('Invalid '.$file, E_USER_ERROR);
        return false;
    }
    
}