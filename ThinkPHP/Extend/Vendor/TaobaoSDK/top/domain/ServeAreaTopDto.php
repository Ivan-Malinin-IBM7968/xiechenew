<?php

/**
 * 服务区域
 * @author auto create
 */
class ServeAreaTopDto
{
	
	/** 
	 * 区域ID
	 **/
	public $areaId;
	
	/** 
	 * 下属区域ID集合
	 **/
	public $childAreaIds;
	
	/** 
	 * 名称
	 **/
	public $name;
	
	/** 
	 * 父级区域ID
	 **/
	public $parentAreaId;
	
	/** 
	 * 区域所在层级，      * 1 - 国家      * 2 - 省/直辖市      * 3 - 地级市      * 4 - 区/县      * 5 - 乡/镇/街道      * 6 - 村/社区
	 **/
	public $serveAreaLevel;	
}
?>