<?php

/**
 * sku详情
 * @author auto create
 */
class ArticleItemViewUnit
{
	
	/** 
	 * 需要支付的价格，单位：元
	 **/
	public $actualPrice;
	
	/** 
	 * 用户是否可以购买
	 **/
	public $canSub;
	
	/** 
	 * 周期数，如1，3，6，12。对于周期型和周期计量型返回。
	 **/
	public $cycNum;
	
	/** 
	 * 1-年，2-月，3-日。对于周期型和周期计量型返回。
	 **/
	public $cycUnit;
	
	/** 
	 * 错误码
	 **/
	public $errorCode;
	
	/** 
	 * 错误文案
	 **/
	public $errorMsg;
	
	/** 
	 * 收费项目code
	 **/
	public $itemCode;
	
	/** 
	 * 收费项目名称
	 **/
	public $itemName;
	
	/** 
	 * 原价，单位：元
	 **/
	public $originPrice;
	
	/** 
	 * 优惠，单位：元
	 **/
	public $promPrice;
	
	/** 
	 * 数量。对于周期计量型返回计量数。
	 **/
	public $quantity;	
}
?>