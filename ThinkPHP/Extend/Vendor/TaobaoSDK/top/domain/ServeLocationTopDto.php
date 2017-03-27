<?php

/**
 * 预约的服务地址
 * @author auto create
 */
class ServeLocationTopDto
{
	
	/** 
	 * 详细地址
	 **/
	public $addressDetail;
	
	/** 
	 * 所在城市，所属的地区ID(3级)
	 **/
	public $cityAreaId;
	
	/** 
	 * 所在乡镇，所属的地区ID(4级)
	 **/
	public $districtAreaId;
	
	/** 
	 * 纬度
	 **/
	public $lat;
	
	/** 
	 * 径度
	 **/
	public $lng;
	
	/** 
	 * 服务范围ID
	 **/
	public $serveRangeId;
	
	/** 
	 * 所在街道，所属的地区ID(5级)
	 **/
	public $streetAreaId;	
}
?>