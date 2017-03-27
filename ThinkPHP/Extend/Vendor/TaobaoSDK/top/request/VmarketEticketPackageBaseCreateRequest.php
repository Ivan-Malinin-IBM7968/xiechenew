<?php
/**
 * TOP API: taobao.vmarket.eticket.package.base.create request
 * 
 * @author auto create
 * @since 1.0, 2015.07.21
 */
class VmarketEticketPackageBaseCreateRequest
{
	/** 
	 * 包基本信息
	 **/
	private $packageBase;
	
	private $apiParas = array();
	
	public function setPackageBase($packageBase)
	{
		$this->packageBase = $packageBase;
		$this->apiParas["package_base"] = $packageBase;
	}

	public function getPackageBase()
	{
		return $this->packageBase;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.package.base.create";
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
