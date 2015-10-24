<?php

class publicController extends controller {
    
	//初始化action白名单
	public function _init_actions(){
	   $this->actions[] = 'test';
	}
	
    
    
}