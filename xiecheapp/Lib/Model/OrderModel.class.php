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
	public function ListProductdetail_S($list_product,$sale_arr){
			if($sale_arr['product_sale'] == 0.00){
				$sale_arr['product_sale'] = '无折扣';
				$sale_value['product_sale'] = 1;
			}else{
				$sale_value['product_sale'] = $sale_arr['product_sale'];
				$sale_arr['product_sale'] = ($sale_arr['product_sale']*10).'折';
			}

			if($sale_arr['workhours_sale'] == 0.00){
				$sale_arr['workhours_sale'] = '无折扣';
				$sale_value['workhours_sale'] = 1;
			}else{
				$sale_value['workhours_sale'] = $sale_arr['workhours_sale'];
				$sale_arr['workhours_sale'] = ($sale_arr['workhours_sale']*10).'折';
			}
			$model_maintainclass = D('Maintainclass');
			$model_serviceitem = D('Serviceitem');
			//dump($list_product);
			if(is_array($list_product)){
				$i=0;
				$str .= '
				<style type="text/css"><!--
.STYLE4 {
	font-size: 24px;
	font-weight: bold;
}
.STYLE7 {color: #3399FF; font-weight: bold; }
.STYLE8 {
	font-size: 16px;
	color: #1D92C2;
}
.STYLE9 {color: #58595B}
.STYLE10 {
	color: black;
	font-size: 12px;
	font-weight: bold;
}
.submit_img{
	color: black;
	background：url("/Public/note/images/buttons1.gif") no-repeat scroll -275px -84px transparent;
}
.STYLE11 {font-size: 12px}
-->
</style>';
				$str .='<div>
						<table width="600px" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr><td align="center" valign="middle"><span class="STYLE4">费用明细</span></td></tr>';
				foreach($list_product AS $k=>$v){
					$i++;
					$service_name = $model_serviceitem->getById($list_product[$k]['service_id']);
					if ($list_product[$k]['product_detail']){
					    $list_detail = $list_product[$k]['product_detail'];
					    $list_detail = unserialize($list_detail);
					}else {
					    $list_detail = array();
					}
					//dump($list_detail);

					$str .='<tr height="10px"><td></td></tr>
					<tr>
						<td>
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td colspan="6" height="20"><strong>维修项目'.$i.'：'.$service_name['name'].'</strong></td>
								</tr>';
					if (!empty($list_detail)){			
					    $str .='<tr>
								<td width="25%" height="20" ><span class="STYLE7">零件明细</span></td>
								<td width="15%" ><span class="STYLE7">零件单价</span></td>
								<td width="15%" ><span class="STYLE7">零件数量</span></td>
								<td width="15%" ><span class="STYLE7">门市零件价格</span></td>
								<td width="15%" ><span class="STYLE7">折扣率</span></td>
								<td width="15%" ><span class="STYLE7">折后价格</span></td>
							</tr>';
							//循环出配件信息
							foreach($list_detail AS $kk=>$vv){
								$list_detail[$kk]['total'] = $list_detail[$kk]['quantity']*$list_detail[$kk]['price'];
								$all_total +=$list_detail[$kk]['total'];
								//echo $sale_value['workhours_sale'];
								$str .='<tr><td>';
								if($list_detail[$kk]['Midl_name'] != '工时费'){
									$list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['product_sale'];
									$str .='<tr height="18">
										<td height="18">'.$list_detail[$kk]['Midl_name'].'</td>
										<td>'.$list_detail[$kk]['price'].'</td>
										<td>'.$list_detail[$kk]['quantity'].'&nbsp'.$list_detail[$kk]['unit'].'</td>
										<td>'.$list_detail[$kk]['total'].'</td>
										<td>'.$sale_arr['product_sale'].'</td>
										<td>'.$list_detail[$kk]['after_sale_total'].'</td>
										</tr>';
										$product_price += $list_detail[$kk]['total'];
										$product_price_sale += $list_detail[$kk]['after_sale_total'];
									
								}else{
									$list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['workhours_sale'];

									
										$workhours_price += $list_detail[$kk]['total'];
										$workhours_price_sale += $list_detail[$kk]['after_sale_total'];
								}
								$all_after_total += $list_detail[$kk]['after_sale_total'];
							}
							//echo '<pre>';print_r($list_detail);
							$str .='	<Tr>
      <td width="25%" height="18" ><span class="STYLE7">工时明细</span></td>
          <td width="15%" ><span class="STYLE7">工时单价</span></td>
        <td width="15%" ><span class="STYLE7">工时数量</span></td>
        <td width="15%" ><span class="STYLE7">门市工时价格</span></td>
        <td width="15%" ><span class="STYLE7">折扣率</span></td>
        <td width="15%" ><span class="STYLE7">折后价格</span></td>
		</Tr><tr height="18">
										<td height="18">'.$list_detail[0]['Midl_name'].'</td>
										<td>'.$list_detail[0]['price'].'</td>
										<td>'.$list_detail[0]['quantity'].'&nbsp'.$list_detail[0]['unit'].'</td>
										<td>'.$list_detail[0]['total'].'</td>
										<td>'.$sale_arr['workhours_sale'].'</td>
										<td>'.$list_detail[0]['after_sale_total'].'</td>
										</tr></td></tr>';
					}else{
					    $str .='<tr>
									<td colspan="6" height="20">
									很抱歉，您所查询的这款车型或这个维修项目还没有维修保养价格明细，有可能您的车型不需要做这个项目（如某些车型不需要更换自动变速箱油，某些使用正时链条的车型不需要更换正时皮带），也有可能您这款车型或这个维修项目的价格明细还未收入到我们的数据库。我们将尽快完善我们的维修保养价格数据库，以为您提供全面的服务。
									</td>
								</tr>';
					}
					//零件配节省费用
					$product_price_save = $product_price-$product_price_sale;
					//零件配节省费用
					$workhours_price_save = $workhours_price-$workhours_price_sale;

					$save_total = $all_total-$all_after_total;
							
						$str .='</table></td></tr>';
						}
			
			$model = D('Shop');
			$getShop = $model->where("id = $_POST[shop_id]")->find();
			//dump($getShop);
			
			
			
			
			
			$str .= '
  <tr>
    <td height="20">&nbsp;</td>
  </tr>
  <tr>
    <td><table width="60%" border="0" align="left" cellpadding="0" cellspacing="0">
      <tr>
        <td height="20" colspan="6" nowrap><strong>通过携车网预约您所选择的维修保养项目，共为您<span class="STYLE8">节省</span>：</strong></td>
        </tr>
		<tr>
      <td width="25%" height="20"></td>
          <td width="25%"><strong>门市价</strong></td>
          <td width="25%"><strong>折后价</strong></td>
          <td width="25%"><strong>节省</strong></td>
		<tr height="20">
        <td width="136"><strong>零件费</strong></td>
        <td height="18" width="103">'.$product_price.'</td>
        <td width="110">'.$product_price_sale.'</td>
        <td width="113">'.$product_price_save.'</td>
		</tr>
      <tr height="18">
        <td><strong>工时费</strong></td>
        <td height="18">'.$workhours_price.'</td>
        <td>'.$workhours_price_sale.'</td>
        <td>'.$workhours_price_save.'</td>
      </tr>
      <tr height="18">
        <td><strong>合计(元)</strong></td>
        <td height="18">'.$all_total.' </td>
        <td>'.$all_after_total.'</td>
        <td><strong>'.$save_total.'</strong></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="20">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="middle"><img src="/Public/note/images/01.png"><span class="STYLE9"><span class="STYLE11">携车网所提供的汽车售后维修保养项目明细价格（包括但不限于零件费和/或工时费等费用），由与携车网签约的服务提供商提供。由于汽车售后维修保养项目的零件明细、零件价格、工时价格的不定期调整，携车网提供的明细价格仅供参考，不作为车主接受服务后的付款依据，最终付款以在服务提供商处实际发生额为准。但您享受到的折扣率不变。</span></span></td>
  </tr>
  <tr>
    <td height="20">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" align="left"><span class="STYLE10">您所选择的4S店信息：<br>
      4S店：<font color="blue">'.$getShop['shop_name'].'</font><br>
      地址：<font color="blue">'.$getShop['shop_address'].'</font><br>
      客服电话：<font color="blue">4006602822</font>		
    </span></td>
  </tr>
  <tr>
    <td height="40">&nbsp;</td>
  </tr>
';
if($_POST['ajax_type'] == 'order'){
    $url_str = '';
    if (isset($_POST['shop_id']) and !empty($_POST['shop_id'])){
        $url_str .= '/shop_id/'.$_POST['shop_id'];
    }
    if (isset($_POST['select_services']) and !empty($_POST['select_services'])){
        $url_str .= '/select_services/'.$_POST['select_services'];
    }
    if (isset($_POST['product_str']) and !empty($_POST['product_str'])){
        $url_str .= '/product_str/'.$_POST['product_str'];
    }
    if (isset($_POST['select_model_id']) and !empty($_POST['select_model_id'])){
        $url_str .= '/model_id/'.$_POST['select_model_id'];
    }
    if (isset($_POST['select_brand_id']) and !empty($_POST['select_brand_id'])){
        $url_str .= '/brand_id/'.$_POST['select_brand_id'];
    }
    if (isset($_POST['select_series_id']) and !empty($_POST['select_series_id'])){
        $url_str .= '/series_id/'.$_POST['select_series_id'];
    }
    if (isset($_POST['timesale_id']) and !empty($_POST['timesale_id'])){
        $url_str .= '/timesale_id/'.$_POST['timesale_id'];
    }
    if (isset($_POST['u_c_id']) and !empty($_POST['u_c_id'])){
        $url_str .= '/u_c_id/'.$_POST['u_c_id'];
    }
  $str .='<tr>
    <td height="20" align="center"><a href="'.__URL__.'/addorder'.$url_str.'" class="submit_img" style="background:url(\'/Public/note/images/buttons1.gif\') no-repeat scroll 186px -199px transparent;float: left;
    height: 42px;
    margin: 0 5px 0 0;
    width: 167px;
    padding-left: 183px;"></a></td>
  </tr>
  <tr>
    <td height="20">&nbsp;</td>
  </tr>';
}
  $str .= '  <tr>
    <td height="20">&nbsp;</td>
  </tr></table>';



					return $str;
				
			}else{
					return false;
				
			}
		
	}
	
//显示图片
	public function ListProductdetail_pic($folder,$product_imgname){
	    $model = D('Shop');
		$getShop = $model->where("id = $_POST[shop_id]")->find();
				$str .= '
				<html xmlns:wb="http://open.weibo.com/wb">
				<style type="text/css"><!--
                    .STYLE4 {
                    	font-size: 24px;
                    	font-weight: bold;
                    }
                    .STYLE7 {color: #3399FF; font-weight: bold; }
                    .STYLE8 {
                    	font-size: 16px;
                    	color: #1D92C2;
                    }
                    .STYLE9 {color: #58595B}
                    .STYLE10 {
                    	color: black;
                    	font-size: 12px;
                    	font-weight: bold;
                    }
                    .submit_img{
                    	color: black;
                    	background：url("/Public/note/images/buttons1.gif") no-repeat scroll -275px -84px transparent;
                    }
                    .STYLE11 {font-size: 12px}
                    -->
                </style>
                <script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js?appkey=" type="text/javascript" charset="utf-8"></script>
                ';
				$str .='<div>
						<table width="600px" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td><img src="/UPLOADS/Product/'.$folder.'/'.$product_imgname.'"></td>
						</tr>
				</table>
				<table width="600px" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td  width="10%">';
				$str .="";
				$str .='</td>
							<td  width="10%"><div id="qqwb_share__" data-appkey="801213454" data-icon="0" data-counter="1" data-counter_pos="right" data-content="携车网|预约4S店|维修保养|4S保养维修" data-pic="http://www.xieche.net/UPLOADS/Product/'.$folder.'/'.$product_imgname.'"></div></td>
							<td width="80%"></td>
						</tr>
				</table>			
  <table  width="600px"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="left" valign="middle"><img src="/Public/note/images/01.png"><span class="STYLE9"><span class="STYLE11">携车网所提供的汽车售后维修保养项目明细价格（包括但不限于零件费和/或工时费等费用），由与携车网签约的服务提供商提供。由于汽车售后维修保养项目的零件明细、零件价格、工时价格的不定期调整，携车网提供的明细价格仅供参考，不作为车主接受服务后的付款依据，最终付款以在服务提供商处实际发生额为准。但您享受到的折扣率不变。</span></span></td>
  </tr>
  <tr>
    <td height="20" >&nbsp;</td>
  </tr>
  <tr>
    <td height="20" align="left"><span class="STYLE10">您所选择的4S店信息：<br>
      4S店：<font color="blue">'.$getShop['shop_name'].'</font><br>
      地址：<font color="blue">'.$getShop['shop_address'].'</font><br>
      客服电话：<font color="blue">4006602822</font>		
    </span></td>
  </tr>
  <tr>
    <td height="40">&nbsp;</td>
  </tr>
';
if($_POST['ajax_type'] == 'order'){
    $url_str = '';
    if (isset($_POST['shop_id']) and !empty($_POST['shop_id'])){
        $url_str .= '/shop_id/'.$_POST['shop_id'];
    }
    if (isset($_POST['select_services']) and !empty($_POST['select_services'])){
        $url_str .= '/select_services/'.$_POST['select_services'];
    }
    if (isset($_POST['product_str']) and !empty($_POST['product_str'])){
        $url_str .= '/product_str/'.$_POST['product_str'];
    }
    if (isset($_POST['select_model_id']) and !empty($_POST['select_model_id'])){
        $url_str .= '/model_id/'.$_POST['select_model_id'];
    }
    if (isset($_POST['select_brand_id']) and !empty($_POST['select_brand_id'])){
        $url_str .= '/brand_id/'.$_POST['select_brand_id'];
    }
    if (isset($_POST['select_series_id']) and !empty($_POST['select_series_id'])){
        $url_str .= '/series_id/'.$_POST['select_series_id'];
    }
    if (isset($_POST['timesale_id']) and !empty($_POST['timesale_id'])){
        $url_str .= '/timesale_id/'.$_POST['timesale_id'];
    }
    if (isset($_POST['u_c_id']) and !empty($_POST['u_c_id'])){
        $url_str .= '/u_c_id/'.$_POST['u_c_id'];
    }
  $str .='<tr>
    <td height="20" align="center"><a href="'.__URL__.'/addorder'.$url_str.'" class="submit_img" style="background:url(\'/Public/note/images/buttons1.gif\') no-repeat scroll 186px -199px transparent;float: left;
    height: 42px;
    margin: 0 5px 0 0;
    width: 167px;
    padding-left: 183px;"></a></td>
  </tr>
  <tr>
    <td height="20">&nbsp;</td>
  </tr>';
}
  $str .= '  <tr>
    <td height="20">&nbsp;</td>
  </tr></table>
  <script src="http://mat1.gtimg.com/app/openjs/openjs.js#autoboot=no&debug=no"></script>
  ';
	return $str;	
	}

}	











?>
