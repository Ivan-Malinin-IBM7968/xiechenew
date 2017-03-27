<?php

/**
 * 商品信息
 * @author auto create
 */
class ReturnBillGetInventoryitem
{
	
	/** 
	 * 批次号
	 **/
	public $batchCode;
	
	/** 
	 * 失效日期，保质期商品使用
	 **/
	public $dueDate;
	
	/** 
	 * 库存类型1 可销售库存 101残次品
	 **/
	public $inventoryType;
	
	/** 
	 * 数量
	 **/
	public $itemQty;
	
	/** 
	 * 生产地区，仓库采集的商品
	 **/
	public $produceArea;
	
	/** 
	 * 生产编码，同一商品可能因商家不同有不同编码，仓库采集的商品信息，可供保质期商品使用
	 **/
	public $produceCode;
	
	/** 
	 * 生产日期，保质期商品使用
	 **/
	public $produceDate;	
}
?>