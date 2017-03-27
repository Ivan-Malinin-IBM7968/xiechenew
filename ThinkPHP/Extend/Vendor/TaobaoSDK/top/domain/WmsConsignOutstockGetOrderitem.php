<?php

/**
 * 商品信息
 * @author auto create
 */
class WmsConsignOutstockGetOrderitem
{
	
	/** 
	 * 商家对商品的编码
	 **/
	public $itemCode;
	
	/** 
	 * 商品ID
	 **/
	public $itemId;
	
	/** 
	 * 应发商品数量
	 **/
	public $itemQty;
	
	/** 
	 * 商品缺货数量
	 **/
	public $lackQty;
	
	/** 
	 * 失败原因（系统报缺，实物报缺)
	 **/
	public $reason;	
}
?>