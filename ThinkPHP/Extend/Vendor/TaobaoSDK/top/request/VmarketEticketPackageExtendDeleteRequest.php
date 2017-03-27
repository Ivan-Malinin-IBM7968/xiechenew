<?php
/**
 * TOP API: taobao.vmarket.eticket.package.extend.delete request
 * 
 * @author auto create
 * @since 1.0, 2015.07.21
 */
class VmarketEticketPackageExtendDeleteRequest
{
	/** 
	 * 包扩展信息id
	 **/
	private $packageExtendId;
	
	private $apiParas = array();
	
	public function setPackageExtendId($packageExtendId)
	{
		$this->packageExtendId = $packageExtendId;
		$this->apiParas["package_extend_id"] = $packageExtendId;
	}

	public function getPackageExtendId()
	{
		return $this->packageExtendId;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.package.extend.delete";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->packageExtendId,"packageExtendId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
