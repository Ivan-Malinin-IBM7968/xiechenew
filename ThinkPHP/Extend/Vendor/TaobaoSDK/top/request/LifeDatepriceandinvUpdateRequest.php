<?php
/**
 * TOP API: taobao.life.datepriceandinv.update request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeDatepriceandinvUpdateRequest
{
	/** 
	 * 日期价格和库存
	 **/
	private $datePriceAndInvList;
	
	/** 
	 * 商品ID
	 **/
	private $itemId;
	
	/** 
	 * 本次编辑是否清空之前的所有库存和价格信息
	 **/
	private $resetAll;
	
	private $apiParas = array();
	
	public function setDatePriceAndInvList($datePriceAndInvList)
	{
		$this->datePriceAndInvList = $datePriceAndInvList;
		$this->apiParas["date_price_and_inv_list"] = $datePriceAndInvList;
	}

	public function getDatePriceAndInvList()
	{
		return $this->datePriceAndInvList;
	}

	public function setItemId($itemId)
	{
		$this->itemId = $itemId;
		$this->apiParas["item_id"] = $itemId;
	}

	public function getItemId()
	{
		return $this->itemId;
	}

	public function setResetAll($resetAll)
	{
		$this->resetAll = $resetAll;
		$this->apiParas["reset_all"] = $resetAll;
	}

	public function getResetAll()
	{
		return $this->resetAll;
	}

	public function getApiMethodName()
	{
		return "taobao.life.datepriceandinv.update";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->itemId,"itemId");
		RequestCheckUtil::checkNotNull($this->resetAll,"resetAll");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
