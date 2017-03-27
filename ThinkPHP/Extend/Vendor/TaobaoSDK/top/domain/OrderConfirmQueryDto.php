<?php

/**
 * 内购服务下单接口参数
 * @author auto create
 */
class OrderConfirmQueryDto
{
	
	/** 
	 * APPKEY，必填
	 **/
	public $appKey;
	
	/** 
	 * 周期数量，必填
	 **/
	public $cycNum;
	
	/** 
	 * 周期单位(必选 数值1:年 2:月， 3：天)，必填
	 **/
	public $cycUnit;
	
	/** 
	 * 设备类型，目前只支持PC，可选
	 **/
	public $deviceType;
	
	/** 
	 * 内购服务的规格CODE，必填
	 **/
	public $itemCode;
	
	/** 
	 * 使用该参数完成查询订单等操作，可选
	 **/
	public $outTradeCode;	
}
?>