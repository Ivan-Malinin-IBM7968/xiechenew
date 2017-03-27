<?php

/**
 * 库存点数
 * @author auto create
 */
class PointInventoryTopDto
{
	
	/** 
	 * 库存数
	 **/
	public $quantity;
	
	/** 
	 * 时间:一天中的小时的秒数 	 * 比如，临晨1点=3600，下午17：30=17.5*3600=63000)
	 **/
	public $time;	
}
?>