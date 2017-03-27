<?php

/**
 * 配送发货单发件人信息
 * @author auto create
 */
class CnTmsLogisticsOrderSenderinfo
{
	
	/** 
	 * 发件人地址
	 **/
	public $senderAddress;
	
	/** 
	 * 发件人区县
	 **/
	public $senderArea;
	
	/** 
	 * 发件人城市
	 **/
	public $senderCity;
	
	/** 
	 * 发件人手机，手机与电话必须有一值不为空
	 **/
	public $senderMobile;
	
	/** 
	 * 发件人姓名
	 **/
	public $senderName;
	
	/** 
	 * 发件人电话，手机与电话必须有一值不为空
	 **/
	public $senderPhone;
	
	/** 
	 * 发件人省份
	 **/
	public $senderProvince;
	
	/** 
	 * 发件人邮编
	 **/
	public $senderZipCode;	
}
?>