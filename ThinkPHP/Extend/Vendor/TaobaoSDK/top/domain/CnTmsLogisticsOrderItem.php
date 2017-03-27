<?php

/**
 * 发货商品信息
 * @author auto create
 */
class CnTmsLogisticsOrderItem
{
	
	/** 
	 * 扩展字段 K:V;
	 **/
	public $extendFields;
	
	/** 
	 * 发货商品名称
	 **/
	public $itemName;
	
	/** 
	 * 商品单价，单位分
	 **/
	public $itemPrice;
	
	/** 
	 * 发货商品商品数量
	 **/
	public $itemQty;
	
	/** 
	 * ERP订单明细编码
	 **/
	public $orderItemId;
	
	/** 
	 * 发货商品商品数量
	 **/
	public $quantity;
	
	/** 
	 * 备注
	 **/
	public $remark;
	
	/** 
	 * 子交易号
	 **/
	public $subTradeId;	
}
?>