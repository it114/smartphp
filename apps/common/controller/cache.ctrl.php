<?php

/**
 * 使用了缓存机制的控制器
 * @author andy
 *
 */
class cacheController extends controller{
    
    protected $api_config;
    
    /**
     * 数据缓存key，一般一个 app 、ctrl、act唯一对应一个缓存，但是对于特殊的例如分页。。。
     * @param unknown $key
     * @return string
     */
    protected function get_cache_key($key){
        return '~'.$_GET['app'].$_GET['ctrl'].$_GET['act'].$key;
    }
    
// 	public function _init_auth() {
//     	   //jwt认证
//     	   //验证连接者是否合法
//     	   global $_G_VARS;
//     	   $plat_service = $_GET['plat'];
//     	   $plat_key = $_GET['platToken'];
//     	   if($plat_service && $plat_key){
//     	       $plat_config = $_G_VARS['gconfig']['site']['client']['plat'][$plat_service];
//     	       if($plat_config){
//     	           if(! $plat_config['enable']){
//     	               $this->show_json(501,'连接服务被禁用');
//     	           }
//     	           if($plat_key!=md5($plat_config['token'])){
//     	               $this->show_json(502,'连接服务通信失败');
//     	           }
//     	       }
//     	   } else {
//     	       $this->show_json(401,'连接参数缺失');
//     	   }
//     	   //验证app下面是否存在API配置文件
//     	   $api_config = $_G_VARS['gconfig'][$_GET['app']]['apiconfig'][$_GET['ctrl']][$_GET['act']];
// 	       if($api_config){
// 	           if(!$api_config['enable']){
// 	               $this->show_json(500,'接口被禁用');
// 	           }
// 	           if($api_config['auth']){
// 	               //接口认证逻辑
// // 	               quickservice::get_classLoader()->load_class('request');
// // 	               $headers = request::get_headers();
// // 	               $client = $headers['x-sihai-client'];//客户端名称
// // 	               $x_sihai_token = $headers['x-sihai-token'];//jwt认证token
// // 	               if(!$x_sihai_token ||! $client){
// // 	                   $this->show_json(400,'认证失败');
// // 	               } else {
// // 	                   //$_gconfig['site']['client']['api_config']
// // 	                   $client_config = $_G_VARS['gconfig']['site']['client']['api_config'][$client];
// // 	                   if(!$client_config){
// // 	                       $this->show_json(400,'客户端非法');
// // 	                   }
// // 	                   if(!$client_config['key']){
// // 	                       $this->show_json(500,'key配置非法');
// // 	                   }
// // 	                   $token_array = JWT_Auth::decode($x_sihai_token, $client_config['key']);
// // 	                   //这里可以得到登录认证之后保存的信息
// // 	                   //判定token array中的信息是否正确
// // 	                   //认证通过
// // 	                   //TODO 
// // 	               }
// 	           } else {
// 	               //echo 'no auth';//
// 	           }
// 	           if($api_config['cache']) {
//     	           //判定接口是否有缓存，有且没有过期，直接返回缓存数据
//     	           //针对两种情况1、直接app、act、ctrl可以确定的
//     	           //根据get参数中是否有分页参数page来判断，所以约定如果分页，分页参数必须是page
//     	           if(empty($api_config['exp']))  $api_config['exp'] = $_G_VARS['gconfig']['cache']['file']['expire'];
//     	           if($api_config['cache'] && !$_GET['page']){
//     	               $cache_key = $this->get_cache_key();
//     	           } else {
//     	               //分页情况缓存处理，分页参数限制死了 page和limit
//     	               $tmp = $_GET['page'].'_'.$_GET['limit'];
//     	               $cache_key = $this->get_cache_key($tmp);
//     	           }
//     	           $cache_value = quickservice::get_file_cache($api_config['exp'])->get($cache_key);
//     	           if($cache_value) {
//     	               $this->show_json(200,'suc from cache',$cache_value);//缓存的时候要缓存data
//     	           } else {
//     	               //执行接口逻辑。
//     	           }
// 	           } else {
// 	               //echo 'no cache ';
// 	           }
// 	       }
// 	}
 
    
}