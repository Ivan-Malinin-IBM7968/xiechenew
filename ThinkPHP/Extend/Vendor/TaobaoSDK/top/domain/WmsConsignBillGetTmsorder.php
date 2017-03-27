<?php

/**
 * 运单信息
 * @author auto create
 */
class WmsConsignBillGetTmsorder
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
	 * 包裹长度，单位：毫米
	 **/
	public $packageLength;
	
	/** 
	 * 包裹的包材信息列表
	 **/
	public $packageMaterialList;
	
	/** 
	 * 包裹重量，单位：克
	 **/
	public $packageWeight;
	
	/** 
	 * 包裹宽度，单位：毫米
	 **/
	public $packageWidth;
	
	/** 
	 * 快递公司服务编码
	 **/
	public $tmsCode;
	
	/** 
	 * 包裹里面的商品信息列表
	 **/
	public $tmsItemList;
	
	/** 
	 * 运单编码
	 **/
	public $tmsOrderCode;	
}
?>