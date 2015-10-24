<?php

if (!defined('IN_QA')) {
    exit();
}
/**
 * 用户处理逻辑
 * @author andy
 *
 */
class userModel extends basemodel{
    
    function __construct(){
        parent::__construct('member');
    }
    
    /**
     * 登录
     * @param unknown $login_field 和登录类型  登录类型 tel、email、nickname 对应的字段值 
     * @param unknown $pwd
     * @param string $login_type  登录类型 tel、email、nickname
     * @return boolean
     */
    public function login($login_field,$pwd,$login_type='tel'){
        $colums = array('id', 'tel','pwd','salt','UserName', 'Sex','Birthday','Email','Address','Photo', 'status');
        if($login_type == 'tel'){
    	  $where = array('tel'=>$login_field);
        } else if($login_type == 'email'){
          $where = array('email'=>$login_field);
        } else if($login_type == 'nickname'){
          $where = array('nickname'=>$login_field);
        }
        $exists = $this->get($colums,$where);
    	//echo $this->last_query(); 
    	//$exists = $this->get('*',array('tel'=>$tel));//TODO,pwd salt
    	if($exists) {
    	    $repwd = quickapp_md5($pwd.$exists['salt']); 
    		if($exists['pwd'] === $repwd){ 
    			unset($exists['pwd']);
    			unset($exists['salt']);
    			return $exists;
    		} else { 
    		    $this->last_msg = '用户名或者密码错误';
    			return false;
    		}
    	} else { 
    	    $this->last_msg = '用户名或者密码错误~';
    		return false;
    	}
    }
    
    public function reg($params = array(),$reg_type = 'tel'){
        if(!$params || !$params['pwd'] ) {
            $this->last_msg ='参数缺少';
            return false;
        }
        $data = array();
        if($reg_type == 'tel'){
            if(!$params['tel']) {
                $this->last_msg ='参数缺少tel';
                return false;
            }
            $data['tel'] = $params['tel'];
        } else if($reg_type == 'email') {
            if( !$params['email']) {
                $this->last_msg ='参数缺少email';
                return false;
            }
            $data['tel'] = $params['email'];
        } else if($reg_type == 'nickname'){
            if(!$params['email']) {
                $this->last_msg ='参数缺少nickname';
                return false;
            }
            $data['nickname'] = $params['nickname'];
        } else {
            $this->last_msg ='不合法的注册';
            return false;
        }
        //查看是否已经注册
        $exists = $this->get('id',$data);
        if($exists){
            $this->last_msg ='用户已经存在';
            return false;
        }
        $salt = rand(100,999);
        $pwd = quickapp_md5($params['pwd'].$salt);
        $data['pwd'] = $pwd;
        $data['salt'] = $salt;
        $result = $this->insert($data);
        if($result) {
            $this->last_msg ='注册成功';
           return true; 
        } else {
            $this->last_msg ='注册失败';
            return false;
        }
    }
    
    
    
    
}