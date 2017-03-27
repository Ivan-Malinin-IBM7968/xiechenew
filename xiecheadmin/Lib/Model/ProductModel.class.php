<?php
// 配件模型
class ProductModel extends CommonModel {
	
	public function item_detail_distribute($data){
		if(is_array($data)){
			if($data['TXT_BigClass']){

				foreach($data['TXT_BigClass'] AS $key=>$val){
					if($data['price'][$key]){
						$detail_maintain[]=array(		
							'Big' => $data['TXT_BigClass'][$key],
							'Big_name'=>
							'Midl' => $data['TXT_MidlClass'][$key],
							'content' => $data['content'][$key],
							'price' => $data['price'][$key],
							'quantity' => $data['quantity'][$key],
							'sale' => $data['sale'][$key],
						);			
					}else{
						$detail_maintain[]=array(		
							'Big' => $data['TXT_BigClass'][$key],
							'Midl' => $data['TXT_MidlClass'][$key],
							'content' => $data['content'][$key],
						);
	
					}

				}
				return serialize($detail_maintain);
			}else{
				return '';
			}	
		}
	}
	
}

?>