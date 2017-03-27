<?php

/**
 * SN回传记录请求对象
 * @author auto create
 */
class WlbWmsSnRecordConfrimRequest
{
	
	/** 
	 * 商品列表
	 **/
	public $itemList;
	
	/** 
	 * ERP订单编码
	 **/
	public $orderCode;
	
	/** 
	 * 订单类型
	 **/
	public $orderType;
	
	/** 
	 * 外部业务编码，一个合作伙伴中要求唯一多次确认时，每次传入要求唯一
	 **/
	public $outBizCode;
	
	/** 
	 * 货主ID
	 **/
	public $ownerUserId;
	
	/** 
	 * 仓库编码
	 **/
	public $storeCode;
	
	/** 
	 * 仓储订单编码，LBX号
	 **/
	public $storeOrderCode;	
}
?>