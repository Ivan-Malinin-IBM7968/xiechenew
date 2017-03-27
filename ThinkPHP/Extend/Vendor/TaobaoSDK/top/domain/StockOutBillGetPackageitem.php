<?php

/**
 * 包裹里面的商品信息
 * @author auto create
 */
class StockOutBillGetPackageitem
{
	
	/** 
	 * 库存类型1 可销售库存 101残次品
	 **/
	public $inventoryType;
	
	/** 
	 * ERP商品编码
	 **/
	public $itemCode;
	
	/** 
	 * 菜鸟商品编码
	 **/
	public $itemId;
	
	/** 
	 * 数量
	 **/
	public $itemQty;
	
	/** 
	 * ERP订单明细ID
	 **/
	public $orderItemId;	
}
?>