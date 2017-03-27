<?php

/**
 * 服务订单
 * @author auto create
 */
class OrderDO
{
	
	/** 
	 * 服务地址
	 **/
	public $buyerAddress;
	
	/** 
	 * 联系人姓名
	 **/
	public $buyerName;
	
	/** 
	 * 买家昵称
	 **/
	public $buyerNick;
	
	/** 
	 * 联系手机号
	 **/
	public $buyerPhone;
	
	/** 
	 * 订单创建时间
	 **/
	public $createDate;
	
	/** 
	 * 订单ID
	 **/
	public $orderId;
	
	/** 
	 * 服务商品列表
	 **/
	public $orderItemDolist;
	
	/** 
	 * 实际付款金额，分
	 **/
	public $price;
	
	/** 
	 * 退款状态，为空则无退款。
	 **/
	public $refundStatus;
	
	/** 
	 * 买家备注
	 **/
	public $remark;
	
	/** 
	 * 服务范围
	 **/
	public $serveLocationTopDto;
	
	/** 
	 * 买家预约申请时间
	 **/
	public $serviceApplyDate;
	
	/** 
	 * 服务内容描述
	 **/
	public $serviceContent;
	
	/** 
	 * 预约上门时间
	 **/
	public $serviceDate;
	
	/** 
	 * 服务KEY
	 **/
	public $serviceKey;
	
	/** 
	 * 服务状态(未付款，未预约，已预约，服务中，待确认，已完成)
	 **/
	public $serviceStatus;
	
	/** 
	 * 该字段已废弃
	 **/
	public $status;	
}
?>