<?php
/**
 * TOP API: taobao.vmarket.eticket.package.base.update request
 * 
 * @author auto create
 * @since 1.0, 2015.07.21
 */
class VmarketEticketPackageBaseUpdateRequest
{
	/** 
	 * 更新包基本信息
	 **/
	private $packageBaseUpdate;
	
	private $apiParas = array();
	
	public function setPackageBaseUpdate($packageBaseUpdate)
	{
		$this->packageBaseUpdate = $packageBaseUpdate;
		$this->apiParas["package_base_update"] = $packageBaseUpdate;
	}

	public function getPackageBaseUpdate()
	{
		return $this->packageBaseUpdate;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.package.base.update";
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
