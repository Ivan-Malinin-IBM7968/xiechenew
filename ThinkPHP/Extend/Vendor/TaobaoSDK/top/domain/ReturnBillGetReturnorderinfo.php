<?php

/**
 * 销退订单信息
 * @author auto create
 */
class ReturnBillGetReturnorderinfo
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
	 * 单据类型： 501 退货入库
	 **/
	public $orderType;	
}
?>