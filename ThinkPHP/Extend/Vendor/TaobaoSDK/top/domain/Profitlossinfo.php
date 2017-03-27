<?php

/**
 * 损益信息
 * @author auto create
 */
class Profitlossinfo
{
	
	/** 
	 * 仓库订单编码
	 **/
	public $cnOrderCode;
	
	/** 
	 * 单据生成时间
	 **/
	public $createdTime;
	
	/** 
	 * 商品信息列表
	 **/
	public $orderItemList;
	
	/** 
	 * 订单类型： 701 盘点出库 702 盘点入库
	 **/
	public $orderType;
	
	/** 
	 * 备注
	 **/
	public $remark;
	
	/** 
	 * 仓库编码
	 **/
	public $storeCode;	
}
?>