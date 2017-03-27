<?php

/**
 * 商品信息
 * @author auto create
 */
class InventoryProfitlossGetOrderitem
{
	
	/** 
	 * 批次号
	 **/
	public $batchCode;
	
	/** 
	 * 商品保质期信息，失效日期
	 **/
	public $dueDate;
	
	/** 
	 * 库存类型 1 可销售库存（正品）  101 残次
	 **/
	public $inventoryType;
	
	/** 
	 * 商家对商品的编码
	 **/
	public $itemCode;
	
	/** 
	 * 商品ID
	 **/
	public $itemId;
	
	/** 
	 * 商品数量
	 **/
	public $itemQty;
	
	/** 
	 * 生产地区
	 **/
	public $produceArea;
	
	/** 
	 * 生产编码，同一商品可能因商家不同有不同编码
	 **/
	public $produceCode;
	
	/** 
	 * 商品保质期信息，生产日期
	 **/
	public $produceDate;	
}
?>