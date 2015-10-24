<?php


class indexController extends controller{
    
	//初始化action白名单
	public function init_actions(){
		$this->actions[] =  'index';
        
	}
	
	public function retuanCode(){
	    return array('code'=>1,'msg'=>'','data'=>array(
	        'hbID'=>'11',
	        'hbtoken'=>'xxxx',
	        'hbmoney'=>100,
	        'hbname'=>'注册红包',
	        'create_time'=>'',
	        'isused'=>'',
	        'limitId'=>1,
	        'limitDesc'=>'不限制',
	        'expire_time'=>1111111,
	        ''=>''
	    ));
	     
	}
	
	
	public function listHb(){
	    return array('code'=>1,'msg'=>'','data'=>array(
	        array(
	            'hbID'=>'11',
	            'hbtoken'=>'xxxx',
	            'hbmoney'=>100,
	            'hbname'=>'注册红包',
	            'create_time'=>'',
	            'isused'=>'',
	            'limitId'=>1,
	            'limitDesc'=>'不限制',
	            'expire_time'=>1111111,
	        ),
	        array(
	            'hbID'=>'11',
	            'hbtoken'=>'xxxx',
	            'hbmoney'=>100,
	            'hbname'=>'注册红包',
	            'create_time'=>'',
	            'isused'=>'',
	            'limitId'=>1,
	            'limitDesc'=>'不限制',
	            'expire_time'=>1111111,
	        ),
	    ));
	    
	   
	}
	
	public function pay(){
	    //hbid ,hbtoken,hbmoney~,limitId
	}
 
    
}