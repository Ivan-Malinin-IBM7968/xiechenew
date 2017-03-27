<?php

/**
 * 商品属性
 * @author auto create
 */
class WmsConsignBillGetInventoryitem
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
	 * 库存类型：1 可销售库存 (正品) 101 类型用来定义残次品 201 冻结类型库存 301 在途库存
	 **/
	public $inventoryType;
	
	/** 
	 * 数量
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