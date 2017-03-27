<?php
// 配件模型
class ProductModel extends CommonModel {
	public $_link = array(
        'Productrelation'=> HAS_ONE,
    );
	
	public function item_detail_distribute($data,$mainclass){
		if(is_array($data)){
			if($data['TXT_BigClass']){
				foreach($data['TXT_BigClass'] AS $key=>$val){
						$detail_maintain[]=array(		
							'Big' => $data['TXT_BigClass'][$key],
							'Midl' => $data['TXT_MidlClass'][$key],
							'Midl_name'=>$mainclass[$data['TXT_MidlClass'][$key]]['ItemName'],
							'content' => $data['content'][$key],
							'price' => $data['price'][$key],
							'quantity' => $data['quantity'][$key],
						);			
					

				}
				return serialize($detail_maintain);
			}else{
				return '';
			}	
		}
	}



	public function getProduct($where){
		$list = $this->where($where)->join('xc_productrelation ON xc_productrelation.product_id = xc_product.id')->select();
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
}

?>