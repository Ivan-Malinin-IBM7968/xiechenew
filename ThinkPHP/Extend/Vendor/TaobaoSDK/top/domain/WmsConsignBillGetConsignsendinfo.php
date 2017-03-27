<?php

/**
 * 物流订单发货信息
 * @author auto create
 */
class WmsConsignBillGetConsignsendinfo
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
	 * 发票确认信息列表
	 **/
	public $invoinceConfirmList;
	
	/** 
	 * ERP订单编码
	 **/
	public $orderCode;
	
	/** 
	 * 订单商品信息列表
	 **/
	public $orderItemList;
	
	/** 
	 * 订单类型 201 销售出库 502 换货出库 503 补发出库
	 **/
	public $orderType;
	
	/** 
	 * 订单状态 WMS_ACCEPT 接单成功 WMS_REJECT 接单失败 WMS_CONFIRMED 仓库生产完成
	 **/
	public $status;
	
	/** 
	 * 仓储编码
	 **/
	public $storeCode;
	
	/** 
	 * 运单信息列表
	 **/
	public $tmsOrderList;	
}
?>