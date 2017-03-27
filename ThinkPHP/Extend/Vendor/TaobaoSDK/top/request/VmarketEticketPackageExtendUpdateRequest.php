<?php
/**
 * TOP API: taobao.vmarket.eticket.package.extend.update request
 * 
 * @author auto create
 * @since 1.0, 2015.07.21
 */
class VmarketEticketPackageExtendUpdateRequest
{
	/** 
	 * 包扩展信息更新
	 **/
	private $packageExtendUpdate;
	
	private $apiParas = array();
	
	public function setPackageExtendUpdate($packageExtendUpdate)
	{
		$this->packageExtendUpdate = $packageExtendUpdate;
		$this->apiParas["package_extend_update"] = $packageExtendUpdate;
	}

	public function getPackageExtendUpdate()
	{
		return $this->packageExtendUpdate;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.package.extend.update";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
