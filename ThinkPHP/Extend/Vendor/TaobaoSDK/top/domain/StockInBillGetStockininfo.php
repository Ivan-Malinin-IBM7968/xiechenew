<?php

/**
 * 入库单商品信息
 * @author auto create
 */
class StockInBillGetStockininfo
{
	
	/** 
	 * 菜鸟订单编码
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
	 * 入库单信息
	 **/
	public $orderItemList;
	
	/** 
	 * 单据类型：  904 普通入库 306 B2B入库单 601 采购入库
	 **/
	public $orderType;
	
	/** 
	 * 货主ID
	 **/
	public $ownerUserId;
	
	/** 
	 * 入库单状态 WMS_ACCEPT 接单成功 WMS_REJECT 接单失败 WMS_CONFIRMED 仓库生产完成
	 **/
	public $status;	
}
?>