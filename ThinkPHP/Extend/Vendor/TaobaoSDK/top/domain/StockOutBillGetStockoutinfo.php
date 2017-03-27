<?php

/**
 * 出库单信息
 * @author auto create
 */
class StockOutBillGetStockoutinfo
{
	
	/** 
	 * 仓库订单编码，LBX号
	 **/
	public $cnOrderCode;
	
	/** 
	 * 仓库订单完成时间
	 **/
	public $confirmTime;
	
	/** 
	 * ERP订单号
	 **/
	public $orderCode;
	
	/** 
	 * 订单商品信息列表
	 **/
	public $orderItemList;
	
	/** 
	 * 单据类型 903 普通出库单 305 B2B出库单 901 退供出库单
	 **/
	public $orderType;
	
	/** 
	 * 包裹信息列表，包含每个包裹使用的快递信息
	 **/
	public $packageInfoList;
	
	/** 
	 * 入库单状态
	 **/
	public $status;	
}
?>