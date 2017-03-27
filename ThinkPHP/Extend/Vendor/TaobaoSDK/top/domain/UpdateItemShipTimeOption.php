<?php

/**
 * 批量更新商品/SKU发货时间的备选项
 * @author auto create
 */
class UpdateItemShipTimeOption
{
	
	/** 
	 * 0代表清空所有发货时间数据；1代表：固定发货时间；2代表：相对发货时间
	 **/
	public $shipTimeType;
	
	/** 
	 * 更新类型，默认更新sku，0表示更新sku，1表示更新商品维度，其他值均非法
	 **/
	public $updateType;	
}
?>