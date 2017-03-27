<?php
//预约模型
class OrderModel extends Model {
		
	public function ListProduct($list_product){
		
			$model_maintainclass = D('Maintainclass');
			$model_productsale = D('Productsale');
			if(is_array($list_product)){
				
				foreach($list_product AS $k=>$v){					
					$list_product[$k]['product_detail'] = unserialize($list_product[$k]['product_detail']);
					foreach($list_product[$k]['product_detail'] AS $kk=>$vv){
						echo $kk;
						$big = $list_product[$k]['product_detail'][$kk]['Big'];
						$midl =$list_product[$k]['product_detail'][$kk]['Midl'];
						$big_arr = $model_maintainclass->where("ItemID=$big")->find();
						$midl_arr = $model_maintainclass->where("ItemID=$midl")->find();
						$sale_arr = $model_productsale->getByProductsale_id($list_product[$k]['product_detail'][$kk]['sale']);
						$list_product[$k]['product_detail'][$kk]['Big'] = $big_arr['ItemName'];
						$list_product[$k]['product_detail'][$kk]['Midl'] = $midl_arr['ItemName'];
						$list_product[$k]['product_detail'][$kk]['sale'] =  $sale_arr['product_sale'];
						 $list_product[$k]['product_detail'][$kk]['front_sale'] = $list_product[$k]['product_detail'][$kk]['price']*$list_product[$k]['product_detail'][$kk]['quantity'];
						$list_product[$k]['product_detail'][$kk]['after_sale'] = $list_product[$k]['product_detail'][$kk]['price']*$list_product[$k]['product_detail'][$kk]['quantity']*$list_product[$k]['product_detail'][$kk]['sale'];
						$list_product[$k]['product_detail'][front_total] += $list_product[$k]['product_detail'][$kk]['front_sale'];
						$list_product[$k]['product_detail'][after_total] += $list_product[$k]['product_detail'][$kk]['after_sale'];
					}
				}
				return $list_product;
			}else{
				return false;
				
			}
		
		
	}
	
	
	
//特殊处理
	public function ListProductdetail_S($list_product){
			$model_maintainclass = D('Maintainclass');
			$model_productsale = D('Productsale');
			//dump($list_product);
			if(is_array($list_product)){
				
					$list_product['product_detail'] = unserialize($list_product['product_detail']);				
					//print_r($list_product);
					$str ='<table width=500 height=200 border=1>';
						$str .='<tr><td>项目</td><td>单价</td><td>数量</td><td>总价</td><td>折扣</td><td>折后总价</td><td>说明</td><tr>';
					foreach ($list_product['product_detail'] AS $k=>$v){
						//echo $k;
						$big = $list_product['product_detail'][$k]['Big'];
						$midl =$list_product['product_detail'][$k]['Midl'];
						$content =$list_product['product_detail'][$k]['content'];
						$quantity =$list_product['product_detail'][$k]['quantity'];
						$price =$list_product['product_detail'][$k]['price'];
						$big_arr = $model_maintainclass->where("ItemID=$big")->find();
						$midl_arr = $model_maintainclass->where("ItemID=$midl")->find();
						$sale_arr = $model_productsale->getByProductsale_id($list_product['product_detail'][$k]['sale']);
						$front_sale = $list_product['product_detail'][$k]['price']*$list_product['product_detail'][$k]['quantity'];
						$after_sale = $list_product['product_detail'][$k]['price']*$list_product['product_detail'][$k]['quantity']*$sale_arr['product_sale'];
						$front_total += $front_sale;
						$after_total += $after_sale;
						 $str .='<tr><td>'.$midl_arr['ItemName'].'</td><td>'.$price.'</td><td>'.$quantity.'</td><td>'.$front_sale.'</td><td>'.$sale_arr['product_sale'].'</td><td>'.$after_sale.'</td><td>'.$content.'</td><tr>';  
						$ajax_output[detail][] = array(
								'big'=>$big_arr['ItemName'],
								'midl'=>$midl_arr['ItemName'],
								'sale'=>$sale_arr['product_sale'],
							);
						$ajax_output[front_total]=$front_total;
						$ajax_output[after_total]=$after_total;
					
					}
					$sale_total = $front_total-$after_total;
					$str .='<tr><td colspan="8">通过携车网，为您节省</td></tr>';
					$str .='<tr><td colspan="8">'.$front_total.'-'.$after_total.'='.$sale_total.'</td></tr>';
					//echo $_REQUEST['ajax_type'];
					if($_REQUEST['ajax_type'] == 'order'){
					$str .='<tr><td colspan="8"><a href='.__URL__.'/addorder/s1/'.$_POST['s1'].'/s2/'.$_POST['s2'].'/brand_id/'.$_REQUEST['brand_id'].'/series_id/'.$_REQUEST['series_id'].'/model_id/'.$_REQUEST['model_id'].'/u_c_id/'.$_POST['u_c_id'].'/shop_id/'.$_POST['shop_id'].'/product_id/'.$list_product['product_id'].'/total_price/'.$after_total.' target=_blank>预约</a></td></tr>';
					}
					$str .='</table>';
					return $str;
				
			}else{
					return false;
				
			}
		
	}

}	
?>