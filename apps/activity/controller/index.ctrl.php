<?php

quickservice::get_classLoader()->load_controller('common.cache');
class indexController extends cacheController{
    
	public function init_actions(){
		$this->actions[] =  'members';
	}
	
	/**
	 * 投票列表
	 */
	public function members() {
	    $model  = getmodel();
	    $res = $model->select('','*','member');
	    //if($model->error()) { 
	       // $this->show_json(0,'fail');
	    //} else  {
	        $this->show_json(1,'suc',$res);
	    //}
	}
	
    
}