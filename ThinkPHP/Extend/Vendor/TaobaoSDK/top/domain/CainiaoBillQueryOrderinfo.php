<?php

/**
 * 订单信息
 * @author auto create
 */
class CainiaoBillQueryOrderinfo
{
	
	/** 
	 * 菜鸟订单编码
	 **/
	public $cnOrderCode;
	
	/** 
	 * 订单最后修改时间
	 **/
	public $modifiedTime;
	
	/** 
	 * ERP订单号
	 **/
	public $orderCode;
	
	/** 
	 * 单据类型 201 销售出库 501 退货入库 502 换货出库 503 补发出库 904 普通入库 903 普通出库单 306 B2B入库单 305 B2B出库单 601 采购入库 901 退供出库单 701 盘点出库 702 盘点入库 711 库存异动单
	 **/
	public $orderType;
	
	/** 
	 * 备注
	 **/
	public $remark;
	
	/** 
	 * 单据状态 WMS_ACCEPT 接单成功 WMS_REJECT 接单失败 WMS_CONFIRMED 仓库生产完成，注：此状态表示单据已经在WMS处理完成，可能通过获取单据详情接口获取单据详细信息。 WMS_CANCEL 取消仓库发货  -- WMS_FAILED 订单发货失败 TMS_SIGN 买家签收 TMS_REJECT 买家拒签 TMS_CANCEL 拦截派送
	 **/
	public $status;
	
	/** 
	 * 仓库编码
	 **/
	public $storeCode;	
}
?>