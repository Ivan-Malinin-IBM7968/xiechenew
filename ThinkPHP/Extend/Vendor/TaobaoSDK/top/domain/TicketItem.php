<?php

/**
 * 新门票类目商品信息
 * @author auto create
 */
class TicketItem
{
	
	/** 
	 * 商品返点比例（只对B卖家开放，单位为%）
	 **/
	public $auctionPoint;
	
	/** 
	 * 商品状态（onsale：上架，instock：仓库）
	 **/
	public $auctionStatus;
	
	/** 
	 * 类目标识
	 **/
	public $catId;
	
	/** 
	 * 核销机具提供商
	 **/
	public $checkToolMerchant;
	
	/** 
	 * 商品所在地-城市
	 **/
	public $city;
	
	/** 
	 * 商品描述
	 **/
	public $description;
	
	/** 
	 * 商品对应的错误信息。针对get接口使用。
	 **/
	public $errMsg;
	
	/** 
	 * 商品电子凭证信息-在门票商品为电子凭证时必选
	 **/
	public $etc;
	
	/** 
	 * 商品是否有发票（有发票为true，没有发票为false）
	 **/
	public $haveInvoice;
	
	/** 
	 * 商品主图
	 **/
	public $image1;
	
	/** 
	 * 商品第一张多图
	 **/
	public $image2;
	
	/** 
	 * 商品第二张多图
	 **/
	public $image3;
	
	/** 
	 * 商品第三张多图
	 **/
	public $image4;
	
	/** 
	 * 商品第四张多图
	 **/
	public $image5;
	
	/** 
	 * 商品的标识
	 **/
	public $itemId;
	
	/** 
	 * 商品的上架时间（精确到分，格式为：yyyy-MM-dd HH:mm）
	 **/
	public $listTime;
	
	/** 
	 * 商品的物流运费模板-在产品规格使用到物流时必选
	 **/
	public $postageId;
	
	/** 
	 * 产品（具有产品规格的）标识
	 **/
	public $productId;
	
	/** 
	 * 商品是否橱窗推荐（橱窗推荐；true，不推荐：false）
	 **/
	public $promotedStatus;
	
	/** 
	 * 商品所在地-省份
	 **/
	public $prov;
	
	/** 
	 * 卖家客服电话
	 **/
	public $sellerCsPhone;
	
	/** 
	 * 宝贝所属的店铺分类列表-店铺分类标识请使用店铺相关接口获取获取，多个店铺分类标识之间通过逗号进行分隔，最多包含10个分类标识
	 **/
	public $shopCats;
	
	/** 
	 * 参见taobao.ticket.item.add文档描述
	 **/
	public $skus;
	
	/** 
	 * 商品是否为拍下减库存（拍下减库存为true，付款减库存为false）
	 **/
	public $subStockAtBuy;
	
	/** 
	 * 商品标题
	 **/
	public $title;
	
	/** 
	 * 商品视频-视频标识由多媒体中相关接口获取
	 **/
	public $videoId;
	
	/** 
	 * 商品是否参与店铺会员打折
	 **/
	public $vipPromoted;	
}
?>