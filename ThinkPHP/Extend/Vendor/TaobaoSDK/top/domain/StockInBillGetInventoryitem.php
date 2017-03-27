<?php

/**
 * 仓库收货商品信息
 * @author auto create
 */
class StockInBillGetInventoryitem
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
	 * 库存类型1 可销售库存 101残次品
	 **/
	public $inventoryType;
	
	/** 
	 * 数量
	 **/
	public $itemQty;
	
	/** 
	 * 生产地区，仓库采集的商品信息，供记录参考
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