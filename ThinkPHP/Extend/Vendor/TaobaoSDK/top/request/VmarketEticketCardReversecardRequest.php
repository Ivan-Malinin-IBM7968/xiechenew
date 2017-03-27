<?php
/**
 * TOP API: taobao.vmarket.eticket.card.reversecard request
 * 
 * @author auto create
 * @since 1.0, 2015.07.17
 */
class VmarketEticketCardReversecardRequest
{
	/** 
	 * 买家昵称
	 **/
	private $buyerNick;
	
	/** 
	 * 卡ID
	 **/
	private $cardId;
	
	/** 
	 * 卡内等级
	 **/
	private $cardLevel;
	
	/** 
	 * 核销时的流水号
	 **/
	private $consumeSerialNum;
	
	/** 
	 * 操作员id
	 **/
	private $operatorId;
	
	/** 
	 * 用户冲正原因
	 **/
	private $reason;
	
	/** 
	 * 冲正金额
	 **/
	private $reverseValue;
	
	/** 
	 * 核销时的门店
	 **/
	private $storeId;
	
	/** 
	 * 安全字段
	 **/
	private $token;
	
	private $apiParas = array();
	
	public function setBuyerNick($buyerNick)
	{
		$this->buyerNick = $buyerNick;
		$this->apiParas["buyer_nick"] = $buyerNick;
	}

	public function getBuyerNick()
	{
		return $this->buyerNick;
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

	public function setConsumeSerialNum($consumeSerialNum)
	{
		$this->consumeSerialNum = $consumeSerialNum;
		$this->apiParas["consume_serial_num"] = $consumeSerialNum;
	}

	public function getConsumeSerialNum()
	{
		return $this->consumeSerialNum;
	}

	public function setOperatorId($operatorId)
	{
		$this->operatorId = $operatorId;
		$this->apiParas["operator_id"] = $operatorId;
	}

	public function getOperatorId()
	{
		return $this->operatorId;
	}

	public function setReason($reason)
	{
		$this->reason = $reason;
		$this->apiParas["reason"] = $reason;
	}

	public function getReason()
	{
		return $this->reason;
	}

	public function setReverseValue($reverseValue)
	{
		$this->reverseValue = $reverseValue;
		$this->apiParas["reverse_value"] = $reverseValue;
	}

	public function getReverseValue()
	{
		return $this->reverseValue;
	}

	public function setStoreId($storeId)
	{
		$this->storeId = $storeId;
		$this->apiParas["store_id"] = $storeId;
	}

	public function getStoreId()
	{
		return $this->storeId;
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

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.card.reversecard";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->buyerNick,"buyerNick");
		RequestCheckUtil::checkNotNull($this->cardId,"cardId");
		RequestCheckUtil::checkNotNull($this->cardLevel,"cardLevel");
		RequestCheckUtil::checkNotNull($this->consumeSerialNum,"consumeSerialNum");
		RequestCheckUtil::checkNotNull($this->operatorId,"operatorId");
		RequestCheckUtil::checkNotNull($this->reason,"reason");
		RequestCheckUtil::checkNotNull($this->reverseValue,"reverseValue");
		RequestCheckUtil::checkNotNull($this->storeId,"storeId");
		RequestCheckUtil::checkNotNull($this->token,"token");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
