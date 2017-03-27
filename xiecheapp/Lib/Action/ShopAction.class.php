<?php
//首页
class ShopAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		$this->ArticleModel = D('Article');//咨询
		$this->NoticeModel = D('Notice');//优惠卷信息
		$this->ShopModel = D('Shop');//商铺
		$this->Servicemembermodel = D('servicemember');//服务顾问表
		$this->Commentmodel = D('comment');//评论表
		$this->Servicefunmodel = D('servicefun');//配套设施
		$this->Servicefunimgmodel = D('servicefunimg');//配套设施图片
		$this->Ordermodel = D('order');//订单表
	}


	/*
	 * 判断条件
    *
    */
    function _filter(&$map){
        if (isset ( $_REQUEST ['shop_id'] ) && $_REQUEST ['shop_id'] != '') {
            $map['xc_shop.id'] = array('eq',$_REQUEST['shop_id']);
        }
	}
	
	/*
		@author:chf
		@function:店铺详情页显示好评 中评 差评的记录
		@time:2013-12-5
	*/
	function poscomment($shop_id,$comment_type){
		$model_comment = D('Comment');
		$model_commentreply = D('Commentreply');
		$model_member = D('Member');
		$model_order = D("Order");
		$model_serviceitem = D("Serviceitem");
		$map_c['comment_type'] = $comment_type;
		$map_c['shop_id'] = $shop_id;

		$comment = $model_comment->where($map_c)->order("create_time DESC")->limit('0,5')->select();
        // 当前页数据查询
		if ($comment){
			foreach ($comment as $key=>$val){
				//查询对应改评论里的对应汽车类型 get_car_info
				$order = $this->Ordermodel->where(array('id'=>$val['order_id']))->find();
				$comment[$key]['car'] = $this->get_car_info($order['brand_id'],$order['series_id'],$order['model_id']);
				if (!$val['comment']) {
					$comment[$key]['comment'] = "此用户没有填写评价内容";
				}
				
				$map_cr['comment_id'] = $val['id'];
				
				if (!$val['user_name']){
				   $memberinfo = $model_member->find($val['uid']);
				   $comment[$key]['user_name'] = substr($memberinfo['mobile'],0,5).'******';
				}
				$commentreply = $model_commentreply->where($map_cr)->order("create_time DESC")->select();
				$comment[$key]['commentreply'] = $commentreply;

				//服务项目
				$order_info = $model_order->find($val['order_id']);
				$service_map = array();
				$service_map['id'] = array('in',$order_info['service_ids']);
				$serviceitem = $model_serviceitem->where($service_map)->select();
				$comment[$key]['serviceitem'] = $serviceitem;
				$ajaxservcename = "";
				if($serviceitem){
					foreach($serviceitem as $sk=>$sv){
						$ajaxservcename.= "<li>".$sv['name']."</li>";
					
					}
				}
				//服务顾问
				if($val['servicemember_id'] != 0) {
					$servicemember = $this->Servicemembermodel->find($val['servicemember_id']);
					$comment[$key]['servicemember_name'] = $servicemember['name'];
				}
			}
			return $comment;
		}
	}
	
	/*
		@author:chf
		@function:AJAX HTML页面选择好评中评差评显示分页数
		@time:2013-12-5
	*/
	function get_page(){
		$model_comment = D('Comment');
		$model_commentreply = D('Commentreply');
		$model_member = D('Member');
		$model_order = D("Order");
		$model_serviceitem = D("Serviceitem");
		if($_GET['comment_type']){
			$map_c['comment_type'] = $_GET['comment_type'];
		}
		$map_c['shop_id'] = $_GET['shop_id'];

		$count = $model_comment->where($map_c)->count();

		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 5; // 设置每页记录数

		$p = new AjaxPage($count, $limitRows,"get_comment"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p->firstRow . "," . $p->listRows;

		$page = $p->show(); // 产生分页信息，AJAX的连接在此处生成

		echo "<div class='clear'></div>
					<div class='pagerArea'>
						<ul id='pager'>
							{$page}  
						</ul>
					</div>";
	}
	/*
		@author:chf
		@function:AJAX分页调用函数
		@time:2013-12-05
	*/
	function get_comment(){
		$model_comment = D('Comment');
		$model_commentreply = D('Commentreply');
		$model_member = D('Member');
		$model_order = D("Order");
		$model_serviceitem = D("Serviceitem");
	
		if($_GET['style'] =='good'){
			$map_c['comment_type'] = 1;

		}else if($_GET['style'] =='medium'){
			$map_c['comment_type'] = 2;

		}else if($_GET['style'] =='bad'){
			$map_c['comment_type'] = 3;

		}
		$map_c['shop_id'] = $_GET['shop_id'];

		$count = $model_comment->where($map_c)->count();

		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 5; // 设置每页记录数

		$p = new AjaxPage($count, $limitRows,"get_comment"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p->firstRow . "," . $p->listRows;

		$page = $p->show(); // 产生分页信息，AJAX的连接在此处生成

		$comment = $model_comment->where($map_c)->order("create_time DESC")->limit($limit_value)->select();

     
		$str = "<ul class='w830 comment-list' id='".$_GET['style']."' name='".$_GET['style']."'>";
		if ($comment){
			
			foreach ($comment as $key=>$val){
				//查询对应改评论里的对应汽车类型 get_car_info
				$order = $this->Ordermodel->where(array('id'=>$val['order_id']))->find();
				$car= $this->get_car_info($order['brand_id'],$order['series_id'],$order['model_id'],$type='1');

				$map_cr['comment_id'] = $val['id'];
				if (!$val['user_name']){
				   $memberinfo = $model_member->find($val['uid']);
				   $comment[$key]['user_name'] = substr($memberinfo['mobile'],0,5).'******';
				}
				$commentreply = $model_commentreply->where($map_cr)->order("create_time DESC")->select();
				$comment[$key]['commentreply'] = $commentreply;

				//服务项目
				$order_info = $model_order->find($val['order_id']);
				$service_map = array();
				$service_map['id'] = array('in',$order_info['service_ids']);
				$serviceitem = $model_serviceitem->where($service_map)->select();
				$comment[$key]['serviceitem'] = $serviceitem;
				$ajaxservcename = "";
				if($serviceitem){
					foreach($serviceitem as $sk=>$sv){
						$ajaxservcename.= "<dd>".$sv['name']."</dd>";
					}
				}
				//服务顾问
				
				if($val['servicemember_id']) {
					$servicemember_name = "";
					$servicemember = $this->Servicemembermodel->find($val['servicemember_id']);
					$comment[$key]['servicemember_name'] = $servicemember['name'];
					
					$comment[$key]['servicemember_name'] = "<dt>服务顾问:</dt><dd>".$servicemember['name']."</dd></dl>";
					
				}
				
				if($val['comment_type']=='1'){
					$comment_type = '好评';
				}elseif($val['comment_type']=='2'){
					$comment_type = '中评';
				}else{
					$comment_type = '差评';
				}
				$reply="";
				foreach($commentreply as $ck=>$cv){
					$reply.="<li class='single-reply'><div class='reply-avatar'><img src='../Public/new_2/images/reply-avatar.jpg' alt=".$cv['operator_name']." title=".$cv['operator_name']."></div>";
					$reply.="<dl class='reply-con'>";
					$reply.="<dt class='reply-info'><dt class='reply-info'>".$cv['operator_name']."&nbsp;回复:".date('Y-m-d H:i:s',$cv['create_time'])."</dt>";
					$reply.="<dd class='reply-content'>";
					$reply.=$cv['reply'];
					$reply.="</dd></dl><div class='clear'></div></li>";
				}

				//author:chfMB的AJAX分页麻痹眼乌珠瞎掉蛋碎的一塌糊涂↓
				
				$str.="<li class='w830 single-comment'><div class='avatar'>	<img src='../Public/new_2/images/comment-avatar.jpg' alt=".$comment[$key]['user_name']." title=".$comment[$key]['user_name']."></div>";
				$str.="<div class='comment-detail'>";
				$str.="<div class='top'></div>";
				$str.="<div class='center'>";
				$str.="<div class='comment-title'>";
				$str.="<dl class='comment-info users'>";
				$str.="<dt class='name'>".$comment[$key]['user_name']."&nbsp;</dt>";
				$str.="<dd class='time'>".date('Y-m-d H:i:s',$val['create_time'])."</dd>";
				$str.="<dd class='level'>".$comment_type."</dd>";
				$str.="</dl>";
				$str.="<dl class='comment-info items'><dt>维修项目:</dt>";
				$str.=$ajaxservcename."</dl>";

				$car_url = "/order/index-brand_id-{$order[brand_id]}-series_id-{$order[series_id]}-model_id-{$order[model_id]}.html";
				$str.="<dl class='comment-info cars'><dt>维修车辆:<a href='".$car_url."' target='_blank'>".$car."</a></dt><dd></dd></dl>";
				if ($comment[$key]['servicemember_name']) {
					$str.="<dl class='comment-info r-consultant'>";
					$str.=$comment[$key]['servicemember_name'];
					$str.= "</dl>";
				}
				$str.="</div>";
				$str.="<div class='clear'></div>";
				$str.="<div class='comment-content'>";
				if (!$val['comment']) {
					$val['comment'] = "此用户没有填写评价内容";
				}
				$str.=$val['comment'];
				$str.="</div>";
				$str.="</div>";
				$str.="<div class='bottom'></div><div class='clear'></div>";
				$str.="</div>";
				$str.="<ul class='comment-reply'>";
				$str.=$reply;
				$str.="</ul><div class='clear'></div></li>";
			}
			$str.="<div class='clear'></div><div class='pagerArea'><ul id='pager'>".$page."</ul>";
			echo $str;
		}
	}

	
	/*
		@author:chf
		@function:显示商铺详情页
		@time:2013-12-05
	*/
	public function index() {
		Header( "HTTP/1.1 301 Moved Permanently" );
		Header( "Location: http://www.xieche.com.cn/shopservice" );
		exit;
		$member_id = $_REQUEST['member_id'];
		$get_model = $_REQUEST['model_id'];//从搜索页面接值ZZCXX搞的^-^你门懂的
		$this->assign('get_model',$get_model);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$model_shopmain = D('Shop');
		$model_shop_fs_relation = D('shop_fs_relation');
		$FsModel = D('fs');
		$FsId = $model_shop_fs_relation->where(array('shopid'=>$_REQUEST['shop_id']))->select();
		
		if($FsId){
			foreach($FsId as $k=>$v){
				$fsname = $FsModel->where(array('fsid'=>$v['fsid']))->find();
				$datafsname.= $fsname['fsname'];
			}
		}
		
		$this->assign('fsname',$datafsname);
		$list = $model_shopmain->where($map)->join('xc_shopdetail ON xc_shop.id = xc_shopdetail.shop_id')->find();
		if(!$list) {
			$this->_empty();
			exit();
		}
	    if ($list['logo'] and file_exists("UPLOADS/Shop/Logo/".$list['logo'])){
            $list['have_logo'] = 1;
        }else {
            $list['have_logo'] = 0;
        }
    	
		$timesale_model = D('Timesale');
		$timesaleversion_model = D('Timesaleversion');
		$map_t['shop_id'] = $_REQUEST['shop_id'];
		$map_t['status'] = 1;
		$list_timesale = $timesale_model->where($map_t)->select();
		if ($list_timesale){
		    foreach ($list_timesale as $_k=>$_v){
				$query_array_timesale_id[] = $_v['id'];
		        $map_v['timesale_id'] = $_v['id'];
		        $map_v['status'] = 1;
		        $map_v['s_time'] = array('lt',time());
		        $map_v['e_time'] = array('gt',time());
		        if ($_v['week']){
		            $list_timesale[$_k]['week_name'] = explode(',',$_v['week']);
        			foreach($list_timesale[$_k]['week_name'] AS $kk=>$vv){
        			    if (trim($vv)=='0'){
        			        $list_timesale[$_k]['week_name'][$kk] = '日';
        			    }
        			    $list_timesale[$_k]['week_name_s'] .= '周'.$list_timesale[$_k]['week_name'][$kk].',';
        			}
		        }
		        if ($list_timesale[$_k]['week_name_s']){
		            $list_timesale[$_k]['week_name_s'] = substr($list_timesale[$_k]['week_name_s'],0,-1);
		        }
		        $list_timesale[$_k]['timesaleversion'] = $timesaleversion_model->where($map_v)->select();
		        if ($list_timesale[$_k]['timesaleversion']){
		            foreach ($list_timesale[$_k]['timesaleversion'] as $_kk=>$_vv){
		                if ($_vv['workhours_sale']>0){
		                    $list_timesale[$_k]['timesaleversion'][$_kk]['workhours_sale_str'] = round($_vv['workhours_sale']*10,1)."折";
		                }else if($_vv['workhours_sale'] == '-1'){
							 $list_timesale[$_k]['timesaleversion'][$_kk]['workhours_sale_str'] = "全免";
						}else{
		                    $list_timesale[$_k]['timesaleversion'][$_kk]['workhours_sale_str'] = "无折扣";
		                }
		                if ($_vv['product_sale']>0){
		                    $list_timesale[$_k]['timesaleversion'][$_kk]['product_sale_str'] = round($_vv['product_sale']*10,1)."折";
		                }else if($_vv['product_sale'] == '-1'){
							 $list_timesale[$_k]['timesaleversion'][$_kk]['product_sale_str'] = "全免";
						}else{
		                    $list_timesale[$_k]['timesaleversion'][$_kk]['product_sale_str'] = "无折扣";
		                }
		            }
		        }
		    }
		}
		//echo '<pre>';print_r($list_timesale);exit;
		$model_shop_fs_relation = D('Shop_fs_relation');
	    $fs_arr = $model_shop_fs_relation->where("shopid=$_REQUEST[shop_id]")->select();
	   
	    $fsids_arr = array();
	    if (!empty($fs_arr)){
	        $model_fs = D('Fs');
	        foreach ($fs_arr as $v){
	            $fs_info = $model_fs->find($v['fsid']);
	            $fsids_arr[] = $fs_info['fsname'];
				$fsids_id_arr[] = $v['fsid'];
	        }
	    }

		if($list['shop_class'] == '2') {
			$list['logo'] = "/UPLOADS/Brand/280/".$fsids_id_arr[0].".jpg";
		}

		if($_SESSION['username']== 'z'){
								print_r($query_array_timesale_id);
							}
		$timesaleversion_params['timesale_id'] = array('in', $query_array_timesale_id);
		$timesaleversion_params['status'] = 1;
		$timesaleversion_params['s_time'] = array('lt',time());
		$timesaleversion_params['e_time'] = array('gt',time());	
		$last_version = $timesaleversion_model->where($timesaleversion_params)->order('workhours_sale')->limit(1)->find();
		
		if ($last_version['workhours_sale']>0){
			$salesversion['workhours_sale'] = round($last_version['workhours_sale']*10,1)."折";
		}else if($last_version['workhours_sale'] == '-1') {
			
			$salesversion['workhours_sale'] = "全免";
		}else{
		
			$salesversion['workhours_sale'] = "无折扣";
		}
		
		if ($last_version['product_sale']>0){
			$salesversion['product_sale'] = round($last_version['product_sale']*10,1)."折";
		}else {
			$salesversion['product_sale'] = "无折扣";
		}
		$salesversion['id'] = $last_version['id'];

		if ($last_version['workhours_sale'] == '0.00'){
			$last_version['workhours_sale'] = 1;
		}
		if ($last_version['product_sale'] == '0.00'){
			$last_version['product_sale'] = 1;
		}

		if ($last_version['workhours_sale'] == '-1'){
			$last_version['workhours_sale'] = 0;
		}
		
		$model_product = D("Product");
		$model_product_version = D("Productversion");
		$model_serviceitem = D("Serviceitem");
		
		$param['service_id'] = 10;//保养项目ID
		$param['fsid'] = array('in',$fsids_id_arr);


		$service_name = $model_serviceitem->find($param['service_id']);
		$salesversion['name'] = $service_name['name'];

		$product_list = $model_product->where($param)->group("flag")->select();
		if($product_list) {
			foreach($product_list as $kk=>$vv) {
				$product_detail = $model_product_version->where("id = {$vv[versionid]} AND status = 0 ")->field("product_detail")->find();
				$car_name = $vv['flag']." ".$vv['emission']." ".$vv['shift'];
				$product_detail = unserialize($product_detail['product_detail']);
				//工时费 	零件费 	门市价 	折后价 	节省
				if($product_detail) {
					foreach ($product_detail as  $_v){
						$item_total = $_v['quantity']*$_v['price'];
						 if ($_v['Midl_name'] == '工时费'){
							$workhour = $item_total;
							$work_sales = $item_total * $last_version['workhours_sale'];
							
						 }else {
							$product += $item_total;
							$product_sales += $item_total * $last_version['product_sale'];
						 }
					}
					
					if($work_sales == 0 && $product_sales == 0) {
						$work_sales = $workhour;
						$product_sales = $product;
					}
					$result[] = array(
						'model_id' => $vv['model_id'],
						'car_name' => $car_name,
						'workhour' => round($workhour,2) ,
						'product' =>  round($product,2),
						'total' =>  round($workhour+$product,2),
						'sales_total' =>  round($work_sales+$product_sales,2),
						'save_total' => round( ($workhour+$product) - ($work_sales+$product_sales),2),
					);
					unset($product);
					unset($product_sales);
				}
				
				
			}
		}
		$_SESSION['SEO'] = "result";//SEO要记录的值在预约下单销毁
		$this->assign('salesversion',$salesversion);
		$this->assign('result',$result);
				
		$this->assign('member_id',$member_id);
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('list_timesale',$list_timesale);
		
		//店铺评价(店铺评价+服务顾问评价)
		$allcount = 0;
		$good = 0;
		$normal = 0;
		$bad = 0;

		$model_comment = D('Comment');
		$model_commentreply = D('Commentreply');
		$model_member = D('Member');
		$model_order = D("Order");
		$model_serviceitem = D("Serviceitem");

		$map_c['shop_id'] = $_REQUEST ['shop_id'];
		 // 计算总数
		$count = $model_comment->where($map_c)->count();

		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 5; // 设置每页记录数

		$p = new AjaxPage($count, $limitRows,"get_comment"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p->firstRow . "," . $p->listRows;

		$page = $p->show(); // 产生分页信息，AJAX的连接在此处生成

		$comment = $model_comment->where($map_c)->order("create_time DESC")->limit($limit_value)->select();

		if ($comment){
			foreach ($comment as $key=>$val){
				//查询对应改评论里的对应汽车类型 get_car_info
				$order = $this->Ordermodel->where(array('id'=>$val['order_id']))->find();
				$comment[$key]['car'] = $this->get_car_info($order['brand_id'],$order['series_id'],$order['model_id'],$type='1');
				$comment[$key]['brand_id'] = $order['brand_id'];
				$comment[$key]['series_id'] = $order['series_id'];
				$comment[$key]['model_id'] = $order['model_id'];
				if (!$val['comment']) {
					$comment[$key]['comment'] = "此用户没有填写评价内容";
				}

				$map_cr['comment_id'] = $val['id'];
				if (!$val['user_name']){
				   $memberinfo = $model_member->find($val['uid']);
				   $comment[$key]['user_name'] = substr($memberinfo['mobile'],0,5).'******';
				}
				$commentreply = $model_commentreply->where($map_cr)->order("create_time DESC")->select();
				$comment[$key]['commentreply'] = $commentreply;

				//服务项目
				$order_info = $model_order->find($val['order_id']);
				$service_map = array();
				$service_map['id'] = array('in',$order_info['service_ids']);
				$serviceitem = $model_serviceitem->where($service_map)->select();
				$comment[$key]['serviceitem'] = $serviceitem;
				
				//服务顾问
				if($val['servicemember_id'] != 0) {
					$servicemember = $this->Servicemembermodel->find($val['servicemember_id']);
					$comment[$key]['servicemember_name'] = $servicemember['name'];
				}
			}

			$map_c['comment_type'] = 1;
			$good = $model_comment->where($map_c)->count();
			$map_c['comment_type'] = 2;
			$normal = $model_comment->where($map_c)->count();
			$map_c['comment_type'] = 3;
			$bad = $model_comment->where($map_c)->count();
			
			$good = round($good/$count*100);
			//$normal = round($normal/$count*100);
			$normal = floor($normal/$count*100);
			
			//$bad = $bad/$count*100;
			/*ZZC要这么搞不知道逻辑*/
			$bad = 100-$good-$normal;
			$comment_rate['good'] = round($good);
			$comment_rate['normal'] = round($normal);
			$comment_rate['bad'] = round($bad);
			$this->assign("comment_rate",$comment_rate);

			$list['comment_rate'] = $comment_rate['good'] ;//修改新的好评率计算方式
		}
		if(is_array($comment)){
			$this->assign('comment',$comment);
			$this->assign('page', $page);
			
		}

		$comment1 = $this->poscomment($_REQUEST['shop_id'],'1');
		$this->assign('comment1',$comment1);
		$comment2 = $this->poscomment($_REQUEST['shop_id'],'2');
		$this->assign('comment2',$comment2);
		$comment3 = $this->poscomment($_REQUEST['shop_id'],'3');
		$this->assign('comment3',$comment3);
		
		//店铺品牌
		$brands = $this->getshopbrand($_REQUEST ['shop_id']);
		$this->assign('brands',$brands);
		if($brands) {
			foreach($brands as $key=>$val) {
				$brand_name .= $val['brand_name']." ";
				$brand_ids .= $val['brand_id'].",";
			}
			$brand_name = trim($brand_name);
		}

		//店铺配套设施 1 wifi  2 餐厅 3 台球室 4 儿童休息室.
		$Servicefun = $this->Servicefunmodel->where(array('shop_id'=>$_REQUEST ['shop_id']))->order('service_id ASC')->select();
		$this->assign('Servicefun',$Servicefun);
		
		//店铺配套设施图片
		$Servicefunimg = $this->Servicefunimgmodel->where(array('shop_id'=>$_REQUEST ['shop_id'],'status'=>1))->select();

		for($i=0; $i< 8; $i++) {
			if($Servicefunimg[$i]['pic'] == "" ) {
				$Servicefunimg[$i]['pic'] = "none";
			}
		}
		$this->assign('Servicefunimg',$Servicefunimg);

		if(TOP_CSS == "pa") {
			$title =$list['shop_name']."汽车保养维修_平安好车-携车网";
		}else {
			$title =$list['shop_name']."汽车保养维修_携车网";
		}
		$meta_keyword = $list['shop_name'].",".$brand_name."汽车保养,".$this->city_name.$brand_name."4S店汽车保养维修,工时费".$salesversion['workhours_sale'];

		$description = "携车网为您提供".$list['shop_name']."汽车保养维修服务,".$brand_name."保养多少钱, 在线预约,50元养车券免费领,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822";

		$this->assign('list',$list);
		$this->assign('title',$title);
		$this->assign('meta_keyword',$meta_keyword);
		$this->assign('description',$description);
		$this->assign('workhours_sale',round($last_version['workhours_sale']*10,1));

		//公告
		$model_notice = D('Notice');
		$map_n['shop_id'] = $_REQUEST ['shop_id'];
		$notice = $model_notice->where($map_n)->select();
		$this->assign('notice',$notice);
		
		//$art_limit = 21-count($notice);
		
		//咨询
		$model_article = D('Article');
		//$map_a['shop_id'] = $_REQUEST ['shop_id'];
		$map_a['shop_id'] = array(array('eq',$_REQUEST ['shop_id']),array('eq',0),'or');
		$map_a['city_name'] = array(array('eq',$this->city_name),array('eq','全部'),'or');
		$map_a['status'] = 1;

		$article = $model_article->where($map_a)->limit(3)->order('shop_id desc,id desc')->select();

		$this->assign('article',$article);
		//服务顾问
		$Servicemember = $this->Servicemembermodel->where(array('shop_id'=>$_REQUEST['shop_id'],'state'=>'1'))->order('id DESC')->limit('0,3')->select();
		if($Servicemember){
			foreach($Servicemember as $k=>$v){
				//店铺评价(服务顾问评价)
				$map_type['shop_id'] = $_REQUEST ['shop_id'];
				$map_type['type'] = '2';
				$map_type['servicemember_id'] = $v['id'];
				$Servicemember[$k]['commenttype'] = $model_comment->where($map_type)->order("create_time DESC")->select();
				if ($Servicemember[$k]['commenttype']){
					foreach ($Servicemember[$k]['commenttype'] as $key=>$val){
						$map_crtype['comment_id'] = $val['id'];
						$map_crtype['servicemember_id'] = $val['servicemember_id'];
						if (!$val['user_name']){
						   $memberinfo = $model_member->find($val['uid']);
						   $Servicemember[$k]['commenttype'][$key]['user_name'] = substr($memberinfo['mobile'],0,5).'******';
						}
						$commentreplytype = $model_commentreply->where($map_crtype)->order("create_time DESC")->select();
						$Servicemember[$k]['commenttype'][$key]['commentreply'] = $commentreplytype;
						$Servicemember[$k]['commenttype'][$key]['commenttype_count'] = count($Servicemember[$k]['commenttype']);
					}
				}
			}
			$this->assign('Servicemember',$Servicemember);
		}
		//现金券coupon_type=1
		$model_coupon = D('Coupon');
		$map_coupon1['is_delete'] = 0;
		$map_coupon1['start_time'] = array('lt',time());
		$map_coupon1['end_time'] = array('gt',time());
		$map_coupon1['coupon_type'] = 1;
		$map_coupon1['shop_id'] = $_REQUEST ['shop_id'];
		$coupon1 = $model_coupon->where($map_coupon1)->order("id DESC")->limit(5)->select();
		//echo '<pre>';print_r($coupon1);exit;
		$this->assign('coupon1',$coupon1);
		
		//团购券coupon_type=2
		$map_coupon2['is_delete'] = 0;
		$map_coupon2['start_time'] = array('lt',time());
		$map_coupon2['end_time'] = array('gt',time());
		$map_coupon2['coupon_type'] = 2;
		$map_coupon2['shop_id'] = $_REQUEST ['shop_id'];
		$coupon2 = $model_coupon->where($map_coupon2)->order("id DESC")->limit(5)->select();
		//echo '<pre>';print_r($coupon2);exit;
		$this->assign('coupon2',$coupon2);


		$brand_ids = mb_substr($brand_ids,0,-1);

		$this->get_tuijian_coupon();
		
		/*
		if($fsids_id_arr) {
			foreach($fsids_id_arr as $key=>$val) {
				$like_fsid .= "%".$val;
			}
			$like_fsid = $like_fsid."%";
		}*/

		$map_coupon1 = array();
		$map_coupon1['is_delete'] = 0;
		$map_coupon1['start_time'] = array('lt',time());
		$map_coupon1['end_time'] = array('gt',time());
		$map_coupon1['shop_id'] = array('neq',$_REQUEST['shop_id']);
		$map_coupon1['coupon_type'] = 1;
		if(!$coupon1) {
			//$map_coupon1['fsid'] = array('like',$like_fsid);
			$map_coupon1['fsid'] = array('in',$fsids_id_arr);
		}else {
			$fs_tuijian1 = "neq_shop";
		}
		$brand_coupon1 = $model_coupon->where($map_coupon1)->order("id DESC")->limit(5)->select();
		if($brand_coupon1) {
			if($fs_tuijian1 != "neq_shop") {
				$fs_tuijian1 = "brand1";
			}
			$this->assign('tuijian_coupon1',$brand_coupon1);
		}else {
			$fs_tuijian1 = "tuijian";
		}
		$this->assign('tuijian1',$fs_tuijian1);

		
		$map_coupon2 = array();
		$map_coupon2['is_delete'] = 0;
		$map_coupon2['start_time'] = array('lt',time());
		$map_coupon2['end_time'] = array('gt',time());
		$map_coupon2['shop_id'] = array('neq',$_REQUEST['shop_id']);
		$map_coupon2['coupon_type'] = 2;
		if(!$coupon2) {
			//$map_coupon2['fsid'] = array('like',$like_fsid);
			$map_coupon2['fsid'] = array('in',$fsids_id_arr);
		}else {
			$fs_tuijian2 = "neq_shop";
		}
		$brand_coupon2 = $model_coupon->where($map_coupon2)->order("id DESC")->limit(5)->select();
		if($brand_coupon2) {
			if($fs_tuijian2!="neq_shop") {
				$fs_tuijian2 = "brand2";
			}
			$this->assign('tuijian_coupon2',$brand_coupon2);
		}else {
			$fs_tuijian2 = "tuijian";
		}
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('tuijian2',$fs_tuijian2);
		$all_count = $model_comment->where(array('shop_id'=>$_REQUEST ['shop_id']))->count();	
		$good_count = $model_comment->where(array('shop_id'=>$_REQUEST ['shop_id'],"comment_type"=>1))->count();
		$medium_count = $model_comment->where(array('shop_id'=>$_REQUEST ['shop_id'],"comment_type"=>2))->count();
		$bad_count = $model_comment->where(array('shop_id'=>$_REQUEST ['shop_id'],"comment_type"=>3))->count();
		$this->assign('all_count',$all_count);
		$this->assign('good_count',$good_count);
		$this->assign('medium_count',$medium_count);
		$this->assign('bad_count',$bad_count);
		
		$this->display("index4s");
	}

    public function getfsid($shop_id){
        $model_shop_fs_relation = D('Shop_fs_relation');
        $map_shopfs['shopid'] = $shop_id;
        $shop_fs_relation = $model_shop_fs_relation->where($map_shopfs)->select();
        if ($shop_fs_relation){
            $model_carseries = D('Carseries');
            foreach ($shop_fs_relation as $k=>$v){
                if ($v['fsid']){
                    return $v['fsid'];
                }
            }
        }
    }

	/*
		@author:chf
		@function:(新)4S优惠信息页
		@time:2013-04-22
	*/
	function favtype(){
		$member_id = $_REQUEST['member_id'];
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$data['ShopList'] = $this->ShopModel->where($map)->join('xc_shopdetail ON xc_shop.id = xc_shopdetail.shop_id')->find();
	    if ($list['logo'] and file_exists("UPLOADS/Shop/Logo/".$list['logo'])){
            $list['have_logo'] = 1;
        }else {
            $list['have_logo'] = 0;
        }
		$NoticeId = $_REQUEST['id'];
		$data['notice_top'] = $this->NoticeModel->where("id=$NoticeId")->find();
		//公告

		$shop_model = D('Shop');
		$map_shop['id'] = $_REQUEST['shop_id'];
		$map_shop['status'] = 1;
		$data['shop'] = $this->ShopModel->where($map_shop)->find();
		
		$model_timesale = D('Timesale');
		$model_timesaleversion = D('Timesaleversion');
		$tuijian_shop = C('TUIJIAN_SHOP');
		$tuijian_shop = array();
		$model_shop_fs_relation = D('Shop_fs_relation');
		if ($data['shop']){
			//店铺品牌
		   $data['shop']['brands'] = $this->getshopbrand($data['shop']['id']);
			if($this->city_name=='上海'){
				 if ($data['shop']['shop_class'] == 1){
					$data['shop']['shop_pic'] = "/UPLOADS/Shop/280/".$data['shop']['id'].".jpg";
				}else{
					$fsid = $this->getfsid($data['shop']['id']);
					$data['shop']['shop_pic'] = "/UPLOADS/Brand/280/".$fsid.".jpg";
				}
			}else{
				if (file_exists("UPLOADS/Shop/130/".$data['shop']['id'].".jpg")){
					$data['shop']['shop_pic'] = "/UPLOADS/Shop/280/".$data['shop']['id'].".jpg";
				}else {
					$shop_id = $data['shop']['id'];
					$map_sfr['shopid'] = $shop_id;
					$shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
					$data['shop']['shop_pic'] = "/UPLOADS/Brand/280/".$shop_fs_relation['fsid'].".jpg";
				}
			
			}
			$map_t['xc_timesale.shop_id'] = $data['shop']['id'];
			$map_t['xc_timesale.status'] = 1;
			$map_t['xc_timesaleversion.status'] = 1;
			$list_timesale = $model_timesale->where($map_t)->join("xc_timesaleversion ON xc_timesale.id = xc_timesaleversion.timesale_id")->find();
			$timesale['oldworkhours_sale'] = $list_timesale['workhours_sale'];
			if (($list_timesale['e_time']<time()+3600*48 and time()>strtotime(date("Y-m-d")." 16:00:00")) || ($list_timesale['e_time']<time()+3600*24) || $list_timesale['s_time']>(time()+24*3600*15) ){
				//continue;
			}
			$timesale_arr = $list_timesale;
			if ($list_timesale['oldworkhours_sale']>0){
				$timesale_arr['oldworkhours_sale_str'] = round($list_timesale['oldworkhours_sale']*10,1)."折";
			}else {
				$timesale_arr['oldworkhours_sale_str'] = "无折扣";
			}
			if ($list_timesale['workhours_sale']>0){
				$timesale_arr['workhours_sale_str'] = round($list_timesale['workhours_sale']*10,1)."折";
				$timesale_arr['share_workhours_sale_str'] = "工时费：".round($timesale['workhours_sale']*10,1)."折";
			}else {
				if ($timesale_arr['workhours_sale'] == '-1'){
					$timesale_arr['workhours_sale_str'] = "全免";
					$timesale_arr['share_workhours_sale_str'] = "工时费：全免";
				}else{
					$timesale_arr['workhours_sale_str'] = "无折扣";
					$timesale_arr['share_workhours_sale_str'] = "";
				}
			}
			if ($list_timesale['product_sale']>0){
				$timesale_arr['product_sale_str'] = round($list_timesale['product_sale']*10,1)."折";
				$timesale_arr['share_product_sale_str'] = "，零件费：".round($list_timesale['product_sale']*10,1)."折";
			}else {
				$timesale_arr['product_sale_str'] = "无折扣";
				$timesale_arr['share_product_sale_str'] = "";
			}
			$timesale_arr['week_name'] = explode(',',$list_timesale['week']);
			foreach($timesale_arr['week_name'] AS $kk=>$vv){
				if (trim($vv)=='0'){
					$timesale_arr['week_name'][$kk] = '日';
				}
				$timesale_arr['week_name_s'] .= '周'.$timesale_arr['week_name'][$kk].',';
			}
			$timesale_arr['week_name_s'] = substr($timesale_arr['week_name_s'],0,-1);
			$data['shop']['timesale'] = $timesale_arr;
		}

		$this->assign('FavType',$_REQUEST['FavType']);


		$city_name = $this->city_name;
		
		//4s店优惠信息
		$NoticeMap['status'] = 1;
		$NoticeMap['city_name'] = $this->city_name;
		$NoticeMap['shop_id'] = array('neq',$data['shop']['id']);
		$data['NoticeList'] = $this->NoticeModel->where($NoticeMap)->limit(10)->order("update_time DESC")->select();

		
		//4s店售后信息
		$ArticleMap['shop_id'] = array(array('eq',$data['shop']['id']),array('eq',0),'or');
		$ArticleMap['city_name'] = array(array('eq',$this->city_name),array('eq','全部'),'or');
		$ArticleMap['status'] = 1;
		$data['ArticleList'] = $this->ArticleModel->where($ArticleMap)->limit(10)->order('shop_id desc,id desc')->select();

        //推荐优惠券
        $this->get_tuijian_coupon();
        
        //现金券coupon_type=1
		$model_coupon = D('Coupon');
		$map_coupon1['is_delete'] = 0;
		$map_coupon1['start_time'] = array('lt',time());
		$map_coupon1['end_time'] = array('gt',time());
		$map_coupon1['coupon_type'] = 1;
		$map_coupon1['shop_id'] = $_REQUEST ['shop_id'];
		$coupon1 = $model_coupon->where($map_coupon1)->order("id DESC")->select();
		
		$this->assign('tuijian_coupon1',$coupon1);
		
		//团购券coupon_type=2
		$map_coupon2['is_delete'] = 0;
		$map_coupon2['start_time'] = array('lt',time());
		$map_coupon2['end_time'] = array('gt',time());
		$map_coupon2['coupon_type'] = 2;
		$map_coupon2['shop_id'] = $_REQUEST ['shop_id'];
		$coupon2 = $model_coupon->where($map_coupon2)->order("id DESC")->select();
		
		$this->assign('tuijian_coupon2',$coupon2);
		
		//店铺品牌
		$brands = $this->getshopbrand($data['shop']['id']);
		$this->assign('brands',$brands);
		if($brands) {
			foreach($brands as $key=>$val) {
				$brand_name .= $val['brand_name']." ";
				$brand_ids .= $val['brand_id'].",";
			}
			$brand_name = trim($brand_name);
		}


		$description = $data['notice_top']['noticedescription'];
		$description = str_replace("\n", " ", $description);

		if( $data['notice_top']['seo_title'] ) {
			$title = $data['notice_top']['seo_title'];
		}else {
			$title = $data['notice_top']['noticetitle']."-优惠速递-携车网";
		}
		
		if( $data['notice_top']['keyword'] ) {
			$meta_keyword = $data['notice_top']['keyword'];
		}else {
			$meta_keyword = $brand_name."汽车保养,".$brand_name."保养多少钱,".$this->city_name.$brand_name."事故车维修,".$brand_name."汽车保养团购";
		}

		if( $data['notice_top']['summary'] ) {
			$description = $data['notice_top']['summary'];
		}else {
			$description = "携车网为您提供".$data['shop']['shop_name']."预约保养维修服务,".$brand_name."在线预约,50元养车券免费领,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822";
		}

		$this->assign('data',$data);
		$this->assign('title',$title);
		$this->assign('meta_keyword',$meta_keyword);
		$this->assign('description',$description);


		if(!$coupon1 && !$coupon2) {
			 //推荐优惠券
			$this->get_tuijian_coupon();
			$this->assign('tuijian',"tuijian");
		}
		
		
		$this->display("favourable_new");	
		
	}

    public function getshopbrand($shop_id){
        $model_shop_fs_relation = D('Shop_fs_relation');
		
        $map_shopfs['shopid'] = $shop_id;
        $shop_fs_relation = $model_shop_fs_relation->where($map_shopfs)->select();
		//echo $model_shop_fs_relation->getlastSql();
        $brand_id_arr = array();
        if ($shop_fs_relation){
            $model_carseries = D('Carseries');
            foreach ($shop_fs_relation as $k=>$v){
                $map_s['fsid'] = $v['fsid'];
                $carseries = $model_carseries->where($map_s)->select();
                if ($carseries){
                    foreach ($carseries as $_k=>$_v){
                        $brand_id_arr[$_v['brand_id']] = $_v['brand_id'];
                    }
                }
            }
        }
        $model_brand = D('Carbrand');
        $map_b['brand_id'] = array('in',implode(',',$brand_id_arr));
        $brand = $model_brand->where($map_b)->select();
        return $brand;
    }

	public function shoplist(){
		Header( "HTTP/1.1 301 Moved Permanently" ) ;
		header("Location:http://www.xieche.com.cn/order");
		exit;
	   $city_name = $_SESSION['area_info']['0'];
	    $model_membercar = D('Membercar');
		//得到区域ID(CityId)(方法寄存在CommonAction里)
		$this->GetArea();
		//得到区域ID(CityId)
	    if($uid = $this->GetUserId()){
		    //查询用户所有自定义车型
    		$list_membercar = $model_membercar->where("uid=$uid AND status=1")->select();
    		//用户所有自定义车型初始化
    		$list_membercar = $model_membercar->Membercar_format_by_arr($list_membercar);
		    $this->assign('uid',$uid);
		    $this->assign('list_membercar',$list_membercar);
		}
	    $map_s['status'] = 1;
		if ($_REQUEST['shop_area']){
		    $map_s['shop_area'] = $_REQUEST['shop_area'];
			$this->assign('shop_area',$_REQUEST['shop_area']);
		}
	    if ($_REQUEST['u_c_id'] || $_REQUEST['model_id'] || $_REQUEST['fsid']){
	        $model_series = D('Carseries');
    		if($_REQUEST['u_c_id']){
    	        $map_m['u_c_id'] = $_REQUEST['u_c_id'];
    		    $membercar = $model_membercar->where($map_m)->find();
    		    $map_se['series_id'] = $membercar['series_id'];
	            $this->assign('brand_id',$membercar['brand_id']);
	            $this->assign('series_id',$membercar['series_id']);
	            $this->assign('model_id',$membercar['model_id']);
	            $this->assign('u_c_id',$membercar['u_c_id']);
	            $this->assign('other_car',0);
    		}elseif ($_REQUEST['series_id'] and $_REQUEST['model_id']){
	            $map_se['series_id'] = $_REQUEST['series_id'];
	            $this->assign('brand_id',$_REQUEST['brand_id']);
	            $this->assign('series_id',$_REQUEST['series_id']);
	            $this->assign('model_id',$_REQUEST['model_id']);
	            $this->assign('other_car',1);
	        }elseif ($_REQUEST['fsid']){
	            $map_se['fsid'] = $_REQUEST['fsid'];
	        }
	        $series = $model_series->where($map_se)->select();
	        $fsid_arr = array();
	        if ($series){
	            foreach ($series as $_k=>$_v){
	                $fsid_arr[$_v['fsid']] = $_v['brand_id'];
	            }
	        }
	        $shopid_arr = array();
	        if ($fsid_arr){
	            $model_shop_fs_relation = D('Shop_fs_relation');
	            foreach ($fsid_arr as $kk=>$vv){
	                $map_fs['fsid'] = $kk;
	                $shop_fs_relation = $model_shop_fs_relation->where($map_fs)->select();
	                if ($shop_fs_relation){
	                    foreach ($shop_fs_relation as $_kk=>$_vv){
	                        $shopid_arr[$_vv['shopid']] = $_vv['fsid'];
	                    }
	                }
	            }
	        }
	        if ($shopid_arr){
		        $shop_id_arr = array_keys($shopid_arr);
		        $shop_id_str = implode(',',$shop_id_arr);
	        }
	        $map_s['id'] = array('in',$shop_id_str); 
		}else{
		    $this->assign('default_check',1);
		}
        $shop_model = D('Shop');
        $count = $shop_model->where($map_s)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 5);
		$shops = $shop_model->where($map_s)->order("shop_class ASC,comment_rate DESC")->limit($p->firstRow.','.$p->listRows)->select();
	    foreach ($_REQUEST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
        // 分页显示输出
        $page = $p->show();
		$model_timesale = D('Timesale');
		$model_timesaleversion = D('Timesaleversion');
		$tuijian_shop = C('TUIJIAN_SHOP');
		$tuijian_shop = array();
		if ($shops){
		    foreach ($shops as $k=>$v){
		        //店铺品牌
                $shops[$k]['brands'] = $this->getshopbrand($v ['id']);
                if ($v['shop_class'] == 1){
                    $shops[$k]['shop_pic'] = "/UPLOADS/Shop/200/".$v ['id'].".jpg";
                }else{
                    $fsid = $this->getfsid($v ['id']);
                    $shops[$k]['shop_pic'] = "/UPLOADS/Brand/200/".$fsid.".jpg";
                }
		        $map_t['xc_timesale.shop_id'] = $v['id'];
		        $map_t['xc_timesale.status'] = 1;
		        $map_t['xc_timesaleversion.status'] = 1;
		        $list_timesale = $model_timesale->where($map_t)->join("xc_timesaleversion ON xc_timesale.id = xc_timesaleversion.timesale_id")->select();
		        $timesale_arr = array();
		        foreach ($list_timesale as $_key=>$timesale){
	                $timesale['oldworkhours_sale'] = $timesale['workhours_sale'];
	                if (($timesale['e_time']<time()+3600*48 and time()>strtotime(date("Y-m-d")." 16:00:00")) || ($timesale['e_time']<time()+3600*24) || $timesale['s_time']>(time()+24*3600*15) ){
	                    continue;
	                }
	                $timesale_arr[$timesale['timesale_id']] = $timesale;
	                if ($timesale['oldworkhours_sale']>0){
        		        $timesale_arr[$timesale['timesale_id']]['oldworkhours_sale_str'] = round($timesale['oldworkhours_sale']*10,1)."折";
        		    }else {
        		        $timesale_arr[$timesale['timesale_id']]['oldworkhours_sale_str'] = "无折扣";
        		    }
        		    if ($timesale['workhours_sale']>0){
        		        $timesale_arr[$timesale['timesale_id']]['workhours_sale_str'] = round($timesale['workhours_sale']*10,1)."折";
        		        $timesale_arr[$timesale['timesale_id']]['share_workhours_sale_str'] = "工时费：".round($timesale['workhours_sale']*10,1)."折";
        		    }else {
        		        if ($timesale['workhours_sale'] == '-1'){
        		            $timesale_arr[$timesale['timesale_id']]['workhours_sale_str'] = "全免";
            		        $timesale_arr[$timesale['timesale_id']]['share_workhours_sale_str'] = "工时费：全免";
        		        }else{
            		        $timesale_arr[$timesale['timesale_id']]['workhours_sale_str'] = "无折扣";
            		        $timesale_arr[$timesale['timesale_id']]['share_workhours_sale_str'] = "";
        		        }
        		    }
        		    if ($timesale['product_sale']>0){
        		        $timesale_arr[$timesale['timesale_id']]['product_sale_str'] = round($timesale['product_sale']*10,1)."折";
        		        $timesale_arr[$timesale['timesale_id']]['share_product_sale_str'] = "，零件费：".round($timesale['product_sale']*10,1)."折";
        		    }else {
        		        $timesale_arr[$timesale['timesale_id']]['product_sale_str'] = "无折扣";
        		        $timesale_arr[$timesale['timesale_id']]['share_product_sale_str'] = "";
        		    }
        			$timesale_arr[$timesale['timesale_id']]['week_name'] = explode(',',$timesale['week']);
        			foreach($timesale_arr[$timesale['timesale_id']]['week_name'] AS $kk=>$vv){
        			    if (trim($vv)=='0'){
        			        $timesale_arr[$timesale['timesale_id']]['week_name'][$kk] = '日';
        			    }
        			    $timesale_arr[$timesale['timesale_id']]['week_name_s'] .= '周'.$timesale_arr[$timesale['timesale_id']]['week_name'][$kk].',';
        			}
        			$timesale_arr[$timesale['timesale_id']]['week_name_s'] = substr($timesale_arr[$timesale['timesale_id']]['week_name_s'],0,-1);
	                //$timesale_arr[] = $timesale;
		        }
		        $shops[$k]['timesale'] = $timesale_arr;
		        if (in_array($v['id'],$tuijian_shop)){
		            $tuijian_shop_array[] = $shops[$k];
		            unset($shops[$k]);
		        }
		    }
		}
		$this->get_ordermenu();
		$this->get_expert_tuijian();
		//echo '<pre>';print_r($shops);exit;
		$this->assign('page',$page);
		$this->assign('tuijian_shop',$tuijian_shop_array);
		$this->assign('shops',$shops);
		$this->assign('shop_area',$_REQUEST['shop_area']);
		$this->assign('fsid',$_REQUEST['fsid']);
		$this->assign('title',"4S店预约保养_保养价格_事故理赔_工时费折扣优惠_携车网");
	    Cookie::set('_currentUrl_', __URL__);
		$this->display('orderindex');
	}

	public function getshoplist(){
	    $cid = $_POST['cid'];
	    if ($cid){
	        $cid_arr = explode(' ',$cid);
	        $cid_str = implode(',',$cid_arr);
	    }
	    $model_category = D('Category');
	    $map['id'] = array('in',$cid_str);
	    $category = $model_category->where($map)->select();
	    $series_ids = array();
	    if ($category){
	        foreach ($category as $k=>$v){
	            $series_ids[] = $v['series_id'];
	        }
	    }
	    

	    $series_ids_str = implode(',',$series_ids);
	    $model_carseries = D('Carseries');
	    $map_s['series_id'] = array('in',$series_ids_str);
	    $series = $model_carseries->where($map_s)->select();
	    $fs_arr = array();
	    if ($series){
	        foreach ($series as $kk=>$vv){
	            $fs_arr[$vv['fsid']] = $vv['fsid'];
	        }
	    }
	    if ($fs_arr) {
	    	$fs_str = implode(',',$fs_arr);
	    	$model_shop_fs_relation = D('Shop_fs_relation');
	    	$map_sh['fsid'] = array('in',$fs_str);
	    	$shop_fs_relation = $model_shop_fs_relation->where($map_sh)->select();
	    	$shopids_arr = array();
	    	if ($shop_fs_relation){
	    	    foreach ($shop_fs_relation as $_k=>$_v){
	    	        $shopids_arr[$_v['shopid']] = $_v['shopid'];
	    	    }
	    	}
	    }
	    if ($shopids_arr){
	        $shop_ids_str = implode(',',$shopids_arr);
	        $model_shop = D('Shop');
	        $map_shop['id'] = array('in',$shop_ids_str);
	        $shops = $model_shop->field('id,shop_name')->where($map_shop)->select();
	    }
	    if ($shops){
	        echo json_encode($shops);
	    }else{
	        echo 0;
	    }
	    exit;
	}
	
	
	
	
	
	
	
	
	
	
	
}