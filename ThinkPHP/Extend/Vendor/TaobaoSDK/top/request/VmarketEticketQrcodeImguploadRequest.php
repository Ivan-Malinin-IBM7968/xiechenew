<?php
/**
 * TOP API: taobao.vmarket.eticket.qrcode.imgupload request
 * 
 * @author auto create
 * @since 1.0, 2013.11.18
 */
class VmarketEticketQrcodeImguploadRequest
{
	/** 
	 * 二维码图片对应的码
	 **/
	private $code;
	
	/** 
	 * 码商ID
	 **/
	private $codeMerchantId;
	
	/** 
	 * 上传的图片byte[]  小于4K，图片尺寸400*400以内
	 **/
	private $imgBytes;
	
	/** 
	 * 淘宝交易订单号
	 **/
	private $orderId;
	
	private $apiParas = array();
	
	public function setCode($code)
	{
		$this->code = $code;
		$this->apiParas["code"] = $code;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function setCodeMerchantId($codeMerchantId)
	{
		$this->codeMerchantId = $codeMerchantId;
		$this->apiParas["code_merchant_id"] = $codeMerchantId;
	}

	public function getCodeMerchantId()
	{
		return $this->codeMerchantId;
	}

	public function setImgBytes($imgBytes)
	{
		$this->imgBytes = $imgBytes;
		$this->apiParas["img_bytes"] = $imgBytes;
	}

	public function getImgBytes()
	{
		return $this->imgBytes;
	}

	public function setOrderId($orderId)
	{
		$this->orderId = $orderId;
		$this->apiParas["order_id"] = $orderId;
	}

	public function getOrderId()
	{
		return $this->orderId;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.qrcode.imgupload";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->code,"code");
		RequestCheckUtil::checkMaxLength($this->code,20,"code");
		RequestCheckUtil::checkNotNull($this->codeMerchantId,"codeMerchantId");
		RequestCheckUtil::checkNotNull($this->imgBytes,"imgBytes");
		RequestCheckUtil::checkNotNull($this->orderId,"orderId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
