<?php
/**
 * TOP API: taobao.vmarket.eticket.package.remove request
 * 
 * @author auto create
 * @since 1.0, 2015.07.21
 */
class VmarketEticketPackageRemoveRequest
{
	/** 
	 * 包id
	 **/
	private $packageId;
	
	private $apiParas = array();
	
	public function setPackageId($packageId)
	{
		$this->packageId = $packageId;
		$this->apiParas["package_id"] = $packageId;
	}

	public function getPackageId()
	{
		return $this->packageId;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.package.remove";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->packageId,"packageId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
