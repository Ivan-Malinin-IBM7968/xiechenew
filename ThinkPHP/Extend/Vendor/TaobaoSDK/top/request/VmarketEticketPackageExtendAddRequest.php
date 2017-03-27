<?php
/**
 * TOP API: taobao.vmarket.eticket.package.extend.add request
 * 
 * @author auto create
 * @since 1.0, 2015.07.21
 */
class VmarketEticketPackageExtendAddRequest
{
	/** 
	 * 包扩展信息
	 **/
	private $packageExtend;
	
	private $apiParas = array();
	
	public function setPackageExtend($packageExtend)
	{
		$this->packageExtend = $packageExtend;
		$this->apiParas["package_extend"] = $packageExtend;
	}

	public function getPackageExtend()
	{
		return $this->packageExtend;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.package.extend.add";
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
