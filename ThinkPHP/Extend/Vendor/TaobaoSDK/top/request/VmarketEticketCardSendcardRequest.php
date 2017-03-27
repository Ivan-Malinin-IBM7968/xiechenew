<?php
/**
 * TOP API: taobao.vmarket.eticket.card.sendcard request
 * 
 * @author auto create
 * @since 1.0, 2015.07.25
 */
class VmarketEticketCardSendcardRequest
{
	/** 
	 * 实际金额
	 **/
	private $actualValue;
	
	/** 
	 * 卡ID
	 **/
	private $cardId;
	
	/** 
	 * 卡内等级
	 **/
	private $cardLevel;
	
	/** 
	 * 码商Id,不填则为sellerId
	 **/
	private $codemerchantId;
	
	/** 
	 * 膨胀金额
	 **/
	private $expandValue;
	
	/** 
	 * 订单id
	 **/
	private $orderId;
	
	/** 
	 * 安全字段
	 **/
	private $token;
	
	/** 
	 * 买家nick
	 **/
	private $userNick;
	
	private $apiParas = array();
	
	public function setActualValue($actualValue)
	{
		$this->actualValue = $actualValue;
		$this->apiParas["actual_value"] = $actualValue;
	}

	public function getActualValue()
	{
		return $this->actualValue;
	}

	public function setCardId($cardId)
	{
		$this->cardId = $cardId;
		$this->apiParas["card_id"] = $cardId;
	}

	public function getCardId()
	{
		return $this->cardId;
	}

	public function setCardLevel($cardLevel)
	{
		$this->cardLevel = $cardLevel;
		$this->apiParas["card_level"] = $cardLevel;
	}

	public function getCardLevel()
	{
		return $this->cardLevel;
	}

	public function setCodemerchantId($codemerchantId)
	{
		$this->codemerchantId = $codemerchantId;
		$this->apiParas["codemerchant_id"] = $codemerchantId;
	}

	public function getCodemerchantId()
	{
		return $this->codemerchantId;
	}

	public function setExpandValue($expandValue)
	{
		$this->expandValue = $expandValue;
		$this->apiParas["expand_value"] = $expandValue;
	}

	public function getExpandValue()
	{
		return $this->expandValue;
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

	public function setToken($token)
	{
		$this->token = $token;
		$this->apiParas["token"] = $token;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function setUserNick($userNick)
	{
		$this->userNick = $userNick;
		$this->apiParas["user_nick"] = $userNick;
	}

	public function getUserNick()
	{
		return $this->userNick;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.card.sendcard";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->actualValue,"actualValue");
		RequestCheckUtil::checkNotNull($this->cardId,"cardId");
		RequestCheckUtil::checkNotNull($this->cardLevel,"cardLevel");
		RequestCheckUtil::checkNotNull($this->expandValue,"expandValue");
		RequestCheckUtil::checkNotNull($this->orderId,"orderId");
		RequestCheckUtil::checkNotNull($this->token,"token");
		RequestCheckUtil::checkNotNull($this->userNick,"userNick");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
