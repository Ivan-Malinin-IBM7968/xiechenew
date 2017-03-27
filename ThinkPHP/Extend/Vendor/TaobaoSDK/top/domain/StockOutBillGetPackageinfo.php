<?php

/**
 * 包裹信息
 * @author auto create
 */
class StockOutBillGetPackageinfo
{
	
	/** 
	 * 包裹号
	 **/
	public $packageCode;
	
	/** 
	 * 包裹高度，单位：毫米
	 **/
	public $packageHeight;
	
	/** 
	 * 包裹里面的商品信息列表
	 **/
	public $packageItemList;
	
	/** 
	 * 包裹长度，单位：毫米
	 **/
	public $packageLength;
	
	/** 
	 * 包裹质量，单位：克
	 **/
	public $packageWeight;
	
	/** 
	 * 包裹宽度,单位：毫米
	 **/
	public $packageWidth;
	
	/** 
	 * 快递公司服务编码
	 **/
	public $tmsCode;
	
	/** 
	 * 运单编码
	 **/
	public $tmsOrderCode;	
}
?>