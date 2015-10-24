<?php

quickservice::get_classLoader()->load_controller('common.jwt');
class publicController extends jwtController {
    
	//初始化action白名单
	public function _init_actions(){
	   $this->actions[] = 'auth';
	   $this->actions[] = 'request';
	   $this->actions[] = 'consume';
	   $this->actions[] = 'hlist';
	   $this->actions[] = 'test';
	}
    
	/**
	 * 消费红包
	 */
	public function consume(){
	    quickservice::get_classLoader()->load_class('request');
	    $hbid = request::post('hbid',false);
	    $hbtoken = request::post('token',false);
	    $plat = request::post('plat',false);
	    $uid = request::post('uid',false);
	    $out_order = request::post('out_order',false);
	    $money = request::post('money',false);
	    if(!$money || !$out_order || !$uid || !$plat || !$hbtoken || !$hbid ){
	        $this->show_json(0,'  vaild parameter  ');
	    }
	    $basemodel = getmodel();
	    $exists_hb = $basemodel->get(array('id'=>$hbid,'status'=>1),'*','hongbao');
	    if($exists_hb){
	        if($uid != $exists_hb['uid']){
	            $this->show_json(0,'uid is vaild ');
	        }
	        if($plat!=$exists_hb['plat']){
	            $this->show_json(0,'plat is vaild ');
	        }
	        if(intval($money)!=intval($exists_hb['money'])){
	            $this->show_json(0,'money is vaild ');
	        }
	        $token = _hongbao_create_token($uid, $plat, $exists_hb['salt']);
	        if($token != $exists_hb['token']) {
	            $this->show_json(0,'token is vaild ');
	        }
	        //消费
	        $ret = $basemodel->update(array(
	            'status'=>3,
	            'out_order'=>$out_order,
	        ),array('id'=>$hbid),'hongbao');
	        if($ret){
	            $this->show_json(200,'consume suc');
	            //记录日志
	            $basemodel->insert(array(
	                'uid'=>$uid,'plat'=>$plat,'haction'=>2,'hbid'=>$hbid,'create_time'=>TIMESTAMP,'result'=>'suc'
	            ),'hongbao_log');
	        } else {
	            //记录日志
	            $basemodel->insert(array(
	                'uid'=>$uid,'plat'=>$plat,'haction'=>2,'hbid'=>$hbid,'create_time'=>TIMESTAMP,'result'=>'fail'
	            ),'hongbao_log');
	            $this->show_json(200,'consume fail');
	        }
	    } else {
	        $this->show_json(0,'not exist  hongbao with id '+$hbid);
	    }
	    
	}
	
	/**
	 * 获取某一个用户的红包列表
	 */
	public function hlist(){
	    quickservice::get_classLoader()->load_class('request');
	    $plat = request::post('plat',false);
	    $uid = request::post('uid',false);
	    if(!$uid || !$plat  ){
	        $this->show_json(0,'vaild parameter !');
	    }
	    $basemodel = getmodel();
	    
	    $list = $basemodel->select(array('AND'=>array('uid'=>$uid),'plat'=>$plat,'status'=>1),'*','hongbao');//不分页暂时
	    if(!$list) {
	        $this->show_json(200,' fetch  list suc !',$list);
	    } else {
	        $this->show_json(0,' fetch  list failed !');
	    }
	}
	
	public function test(){
// 	    $basemodel = getmodel();
// 	    //记录日志
// 	    $basemodel->insert(array(
// 	        'uid'=>10,'plat'=>'plat','haction'=>1,'hbid'=>1,'create_time'=>TIMESTAMP,'result'=>1
// 	    ),'hongbao_log');
// 	    echo $basemodel->last_query();
	}
	
	/**
	 * 发放红包
	 */
	public function request(){ 
	    quickservice::get_classLoader()->load_class('request');
	    $plat = request::post('plat',false);
	    $uid = request::post('uid',false);
	    $cid = request::post('cid',false);
	    $money = request::post('money',false);
	    if(!$plat ||!$uid || !$cid ||!$money) {
	        $this->show_json(0,'vaild parameter!');
	    }
	    $basemodel = getmodel();
	    $exists_cid = $basemodel->get(array('id'=>$cid),'*', 'hongbao_cate');
	    if(!$exists_cid){
	        $this->show_json(0,'cate id is not right !');
	    }
	    if(intval($exists_cid['money']) != intval($money)) {
	        $this->show_json(0,'vaild money parameter!');
	    }
	    $map = array('money'=>$money,'uid'=>$uid,'plat'=>$plat,'cid'=>$cid);
	    $exists_hongbao = $basemodel->get(array('AND'=>$map),'*', 'hongbao');
	    if($exists_hongbao){
	        $this->show_json(0,'already have !');
	    }
	    //插入红包逻辑
	    $salt = rand(100,999);
	    $token = _hongbao_create_token($uid, $plat, $salt);
	    $map['token'] = $token;
	    $map['tokenfunc'] = 'quick_md5';
	    $map['salt'] = $salt;
	    $map['create_time'] = TIMESTAMP;
	    $map['expire_time'] = TIMESTAMP+($exists_cid['expire']*86400000);//TODO 准确性
	    $result = $basemodel->insert($map,'hongbao');
	    if($result){
	        //记录日志
	        $basemodel->insert(array(
	            'uid'=>$uid,'plat'=>$plat,'haction'=>1,'hbid'=>$result,'create_time'=>TIMESTAMP,'result'=>'suc'
	        ),'hongbao_log');
	        $this->show_json(200,'request success  !');
	        
	    } else {
	        //记录日志
	        $basemodel->insert(array(
	            'uid'=>$uid,'plat'=>$plat,'haction'=>1,'hbid'=>$result,'create_time'=>TIMESTAMP,'result'=>'fail'
	        ),'hongbao_log');
	        $this->show_json(0,'request fail  !');
	        
	    }
	}
	
	/**
	 * 普通测试接口
	 */
	public function plist(){
	    
	    //执行取数据逻辑
	    if($this->api_config['cache']) {
	        //执行缓存存储逻辑
	        //返回数据给用户
	        //下次直接读取缓存啦！！！
	    }
	}
    
}