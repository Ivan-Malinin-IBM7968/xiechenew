<?php
// 4s维修店预约
namespace Xiecheapp\Controller;

class ShopserviceController extends CommonController {
	function __construct() {
		parent::__construct ();
		$this->assign ( 'current', 'order' );
		$this->ShopModel = D ( 'shop' );
		$this->CouponModel = D ( 'coupon' );
		$this->MembersalecouponModel = D ( 'membersalecoupon' );
		$this->CarcodeModel = D ( 'carcode' );
		$this->MemberModel = D ( 'member' );
		$this->Salecouponmodel = D ( 'Salecoupon' );
		$this->MembersalecouponModel = D ( 'membersalecoupon' );
		$this->Smsmodel = D ( 'Sms' ); // 短信表
		$this->Servcemembermodel = D ( 'servicemember' ); // 服务顾问表
		$this->Commentmodel = D ( 'comment' ); // 评论表
		$this->Fsmodel = D ( 'fs' ); // 评论表
		$this->Regionmodel = D ( "Region" );
		
		$this->ArticleModel = D ( 'Article' ); // 咨询
		$this->NoticeModel = D ( 'Notice' ); // 优惠卷信息
		$this->Servicemembermodel = D ( 'servicemember' ); // 服务顾问表
		$this->Servicefunmodel = D ( 'servicefun' ); // 配套设施
		$this->Servicefunimgmodel = D ( 'servicefunimg' ); // 配套设施图片
		$this->Ordermodel = D ( 'order' ); // 订单表

		$this->assign('noshow',true);
		$this->assign('noclose',true);
	}
	
	/*
	 * 判断条件
	 */
	function _filter(&$map) {
		// 项目分类S1
		if (isset ( $_REQUEST ['s1'] ) && $_REQUEST ['s1'] != '') {
			$map ['xc_product.service_item_id'] = array (
					'eq',
					$_REQUEST ['s1'] 
			);
		}
		// 服务项目S2
		if (isset ( $_REQUEST ['s2'] ) && $_REQUEST ['s2'] != '') {
			$map ['xc_product.service_id'] = array (
					'eq',
					$_REQUEST ['s2'] 
			);
		}
		// 车型ID
		if (isset ( $_REQUEST ['model_id'] ) && $_REQUEST ['model_id'] != '') {
			$map ['xc_product.car_model_id'] = array (
					'eq',
					$_REQUEST ['model_id'] 
			);
		}
		// 店铺ID
		// if (isset ( $_REQUEST ['shop_id'] ) && $_REQUEST ['shop_id'] != '') {
		// $map['xc_product.shop_id'] = array('eq',$_REQUEST['shop_id']);
		// }
		// 订单状态
		if (isset ( $_REQUEST ['order_state'] ) && $_REQUEST ['order_state'] != '') {
			$map ['order_state'] = array (
					'eq',
					$_REQUEST ['order_state'] 
			);
		}
		// 订单投诉状态
		if (isset ( $_REQUEST ['complain_state'] ) && $_REQUEST ['complain_state'] != '') {
			$map ['complain_state'] = array (
					'eq',
					$_REQUEST ['complain_state'] 
			);
		}
		// 订单ID
		if (isset ( $_REQUEST ['order_id'] ) && $_REQUEST ['order_id'] != '') {
			$map ['order_id'] = array (
					'eq',
					$_REQUEST ['order_id'] 
			);
		}
	}
	
	/*
	 * @author:bright @function:4s店铺维修保养 @time:2014-11-02
	 */
	public function index() {
		/* 得到券 */
		$modelId = @$_SESSION ['modelId'];
		$this->get_tuijian_coupon ( '', $modelId, 4 ,true);
		
		$city_name = $_SESSION ['area_info'] ['0'];
		$model_membercar = D ( 'Membercar' );
		
		$this->GetArea (); // 得到区域ID(CityId)(方法寄存在CommonAction里)
		$map_s ['shop_city'] = $this->city_id;
		$map_s ['status'] = 1;
		
		if ($uid = $this->GetUserId ()) {
			// 查询用户所有自定义车型
			$list_membercar = $model_membercar->where ( "uid=$uid AND status=1" )->select ();
			$this->assign ( 'uid', $uid );
			$this->assign ( 'list_membercar', $list_membercar );
		}
		
		// 区域
		if ($this->request_int ( 'shop_area' )) {
			$map_s ['shop_area'] = $this->request_int ( 'shop_area' );
			$this->assign ( 'shop_area', $this->request_int ( 'shop_area' ) );
		}
		
		// 跟着车型走
		if ($_SESSION ['carName']) {
			$carName = trim(mb_substr ( $_SESSION ['carName'], 0, 3 ));
			$map_s ['shop_name'] = array (
					'like',
					"%{$carName}%" 
			);
		}
		
		// 搜索
		if (isset ( $_GET ['k'] ) && ! empty ( $_GET ['k'] )) {
			$map_s = array();
			$s = htmlspecialchars ( $_GET ['k'] );
			$map_s ['shop_name'] = array (
					'like',
					"%{$s}%"
			);
		}
		$shop_model = D ( 'Shop' );
		$count = $shop_model->where ( $map_s )->count ();
		$this->assign ( "count", $count );
		// 导入分页类
		import ( "Org.Util.Page" );
		// 实例化分页类
		$p = new \Page ( $count, 20 );
		$shops = $shop_model->where ( $map_s )->order ( "have_coupon DESC,shop_class ASC,comment_rate DESC" )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		// 分页显示输出
		foreach ( $_POST as $key => $val ) {
			if (! is_array ( $val ) && $val != "" && $key != '__hash__') {
				$p->parameter .= "$key/" . urlencode ( $val ) . "/";
			}
		}
		if ($_GET ['p'] == '' || $_GET ['p'] == '1') {
			$shop_p = 0;
		} else if ($_GET ['p'] == '2') {
			$shop_p = 1;
		} else {
			$shop_p = $_GET ['p'];
		}
		$shop_num = ($shop_p) * 20;
		$this->assign ( 'shop_num', $p->firstRow );
		$page = $p->show ();
		
		$model_timesale = D ( 'Timesale' );
		$model_timesaleversion = D ( 'Timesaleversion' );
		$tuijian_shop = C ( 'TUIJIAN_SHOP' );
		$tuijian_shop = array ();
		$model_shop_fs_relation = D ( 'Shop_fs_relation' );
		$model_coupon = D ( "Coupon" );
		if ($shops) {
			foreach ( $shops as $k => $v ) {
				// 查询服务顾问
				$shops [$k] ['Servcemember'] = $this->Servcemembermodel->where ( array (
						'shop_id' => $v ['id'],
						'state' => '1' 
				) )->select ();
				if ($shops [$k] ['Servcemember']) {
					foreach ( $shops [$k] ['Servcemember'] as $m_k => $m_v ) {
						// 总服务态度总条数
						$servicecount = $this->Commentmodel->where ( array (
								'shop_id' => $v ['id'],
								'servicemember_id' => $m_v ['id'],
								'type' => '2' 
						) )->count ();
						// 总服务态度评分
						$sumservice = $this->Commentmodel->where ( array (
								'shop_id' => $v ['id'],
								'servicemember_id' => $m_v ['id'],
								'type' => '2' 
						) )->sum ( 'service' );
						// 总服专业技能评分
						$sumprofession = $this->Commentmodel->where ( array (
								'shop_id' => $v ['id'],
								'servicemember_id' => $m_v ['id'],
								'type' => '2' 
						) )->sum ( 'profession' );
						// 总诚信态度评分
						$sumsincerity = $this->Commentmodel->where ( array (
								'shop_id' => $v ['id'],
								'servicemember_id' => $m_v ['id'],
								'type' => '2' 
						) )->sum ( 'sincerity' );
						$shops [$k] ['Servcemember'] [$m_k] ['service'] = number_format ( $sumservice / $servicecount, 1 );
						$shops [$k] ['Servcemember'] [$m_k] ['profession'] = number_format ( $sumprofession / $servicecount, 1 );
						$shops [$k] ['Servcemember'] [$m_k] ['sincerity'] = number_format ( $sumsincerity / $servicecount, 1 );
					}
				}
				
				// 店铺品牌
				$shops [$k] ['brands'] = $this->getshopbrand ( $v ['id'] );
				if ($this->city_name == '上海') {
					if ($v ['shop_class'] == 1) {
						$shops [$k] ['shop_pic'] = "/UPLOADS/Shop/200/" . $v ['id'] . ".jpg";
					} else {
						$fsid = $this->getfsid ( $v ['id'] );
						$shops [$k] ['shop_pic'] = "/UPLOADS/Brand/200/" . $fsid . ".jpg";
					}
				} else {
					if (file_exists ( "UPLOADS/Shop/200/" . $v ['id'] . ".jpg" )) {
						$shops [$k] ['shop_pic'] = "/UPLOADS/Shop/200/" . $v ['id'] . ".jpg";
					} else {
						$shop_id = $v ['id'];
						$map_sfr ['shopid'] = $shop_id;
						$shop_fs_relation = $model_shop_fs_relation->where ( $map_sfr )->find ();
						$shops [$k] ['shop_pic'] = "/UPLOADS/Brand/200/" . $shop_fs_relation ['fsid'] . ".jpg";
					}
				}
				$map_c ['shop_id'] = $v ['id'];
				$comment_count = $this->Commentmodel->where ( $map_c )->count ();
				$map_c ['comment_type'] = 1;
				$good = $this->Commentmodel->where ( $map_c )->count ();
				$map_c ['comment_type'] = 2;
				$normal = $this->Commentmodel->where ( $map_c )->count ();
				$map_c ['comment_type'] = 3;
				$bad = $this->Commentmodel->where ( $map_c )->count ();
				
				$good = round ( $good / $comment_count * 100 );
				$normal = floor ( $normal / $comment_count * 100 );
				
				$shops [$k] ['good'] = $good;
				$shops [$k] ['normal'] = $normal;
				$bad = 100 - $good - $normal;
				$shops [$k] ['comment_rate_new'] = $good;
				
				$map_t ['xc_timesale.shop_id'] = $v ['id'];
				$map_t ['xc_timesale.status'] = 1;
				$map_t ['xc_timesaleversion.status'] = 1;
				$map_t ['xc_timesaleversion.s_time'] = array (
						'lt',
						time () 
				);
				$map_t ['xc_timesaleversion.e_time'] = array (
						'gt',
						time () 
				);
				
				$list_timesale = $model_timesale->where ( $map_t )->join ( "xc_timesaleversion ON xc_timesale.id = xc_timesaleversion.timesale_id" )->select ();
				$timesale_arr = array ();
				foreach ( $list_timesale as $_key => $timesale ) {
					$timesale ['oldworkhours_sale'] = $timesale ['workhours_sale'];
					if (($timesale ['e_time'] < time () + 3600 * 48 and time () > strtotime ( date ( "Y-m-d" ) . " 16:00:00" )) || ($timesale ['e_time'] < time () + 3600 * 24) || $timesale ['s_time'] > (time () + 24 * 3600 * 15)) {
						continue;
					}
					$timesale_arr [$timesale ['timesale_id']] = $timesale;
					if ($timesale ['oldworkhours_sale'] > 0) {
						$timesale_arr [$timesale ['timesale_id']] ['oldworkhours_sale_str'] = round ( $timesale ['oldworkhours_sale'] * 10, 1 ) . "折";
					} else {
						$timesale_arr [$timesale ['timesale_id']] ['oldworkhours_sale_str'] = "无折扣";
					}
					if ($timesale ['workhours_sale'] > 0) {
						$timesale_arr [$timesale ['timesale_id']] ['workhours_sale_str'] = round ( $timesale ['workhours_sale'] * 10, 1 ) . "折";
						$timesale_arr [$timesale ['timesale_id']] ['share_workhours_sale_str'] = "工时费：" . round ( $timesale ['workhours_sale'] * 10, 1 ) . "折";
					} else {
						if ($timesale ['workhours_sale'] == '-1') {
							$timesale_arr [$timesale ['timesale_id']] ['workhours_sale_str'] = "全免";
							$timesale_arr [$timesale ['timesale_id']] ['share_workhours_sale_str'] = "工时费：全免";
						} else {
							$timesale_arr [$timesale ['timesale_id']] ['workhours_sale_str'] = "无折扣";
							$timesale_arr [$timesale ['timesale_id']] ['share_workhours_sale_str'] = "";
						}
					}
					if ($timesale ['product_sale'] > 0) {
						$timesale_arr [$timesale ['timesale_id']] ['product_sale_str'] = round ( $timesale ['product_sale'] * 10, 1 ) . "折";
						$timesale_arr [$timesale ['timesale_id']] ['share_product_sale_str'] = "，零件费：" . round ( $timesale ['product_sale'] * 10, 1 ) . "折";
					} else {
						$timesale_arr [$timesale ['timesale_id']] ['product_sale_str'] = "无折扣";
						$timesale_arr [$timesale ['timesale_id']] ['share_product_sale_str'] = "";
					}
					$timesale_arr [$timesale ['timesale_id']] ['week_name'] = explode ( ',', $timesale ['week'] );
					foreach ( $timesale_arr [$timesale ['timesale_id']] ['week_name'] as $kk => $vv ) {
						if (trim ( $vv ) == '0') {
							$timesale_arr [$timesale ['timesale_id']] ['week_name'] [$kk] = '日';
						}
						$timesale_arr [$timesale ['timesale_id']] ['week_name_s'] .= '周' . $timesale_arr [$timesale ['timesale_id']] ['week_name'] [$kk] . ',';
					}
					$timesale_arr [$timesale ['timesale_id']] ['week_name_s'] = substr ( $timesale_arr [$timesale ['timesale_id']] ['week_name_s'], 0, - 1 );
				}
				$shops [$k] ['timesale'] = $timesale_arr;
				if (in_array ( $v ['id'], $tuijian_shop )) {
					$tuijian_shop_array [] = $shops [$k];
					unset ( $shops [$k] );
				}
				
				$coupon_map ['shop_id'] = $v ['id'];
				$coupon_map ['is_delete'] = 0;
				$coupon_map ['start_time'] = array (
						'lt',
						time () 
				);
				$coupon_map ['end_time'] = array (
						'gt',
						time () 
				);
				$coupon_list = $model_coupon->where ( $coupon_map )->limit ( '5' )->select ();
				$shops [$k] ['coupon_list'] = $coupon_list;
				
				if ($v ['shop_maps']) {
					$shoplist_lng .= $v ['shop_maps'] . "|";
					$shoplist_name .= $v ['shop_name'] . "|";
				}
				
				$model_region = D ( "Region" );
				$region_info = $model_region->find ( $v ['shop_area'] );
				$shops [$k] ['shop_address'] = $region_info ['region_name'] . " " . $v ['shop_address'];
			}
		}
		
		$shoplist_lng = mb_substr ( $shoplist_lng, 0, - 1 );
		$shoplist_name = mb_substr ( $shoplist_name, 0, - 1 );
		$this->assign ( 'shoplist_lng', $shoplist_lng );
		$this->assign ( 'shoplist_name', $shoplist_name );
		$this->assign ( 'page', $page );
		$this->assign ( 'phone', C ( 'CALL_400' ) );
		$this->assign ( 'tuijian_shop', $tuijian_shop_array );
		$this->assign ( 'shops', $shops );
		
		if (TOP_CSS == "pa") {
			$this->assign ( 'title', "平安好车-携车网-汽车保养,事故车维修,全国唯一4S店售后折扣网站" );
		} else {
			$AREAS = C ( "AREAS" );
			$BRANDS = C ( "BRANDS" );
			if ($this->request_int ( 'shop_area' )) {
				$region_name = $AREAS [$this->request_int ( 'shop_area' )];
			}
			if ($this->request_int ( 'fsid' )) {
				$title_Fs_str = $BRANDS [$this->request_int ( 'fsid' )];
			}
			$last_regionname = $region_name . $title_Fs_str;
		}
		if (! $this->request_int ( 'shop_area' ) && ! $this->request_int ( 'fsid' )) {
			$this->assign ( 'title', "4S店售后预约-携车网" );
		} else {
			$this->assign ( 'title', $last_regionname . "汽车4s售后预约-携车网" );
		}
		
		$this->assign ( 'meta_keyword', "汽车保养,汽车维修,4S店预约保养,汽车保养预约,汽车维修预约" );
		$this->assign ( 'description', "汽车保养维修,事故车维修首选携车网,在线预约,50元养车券免费领,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822" );
		cookie ( '_currentUrl_', __URL__ );
		$this->assign ( 'shop_area', $this->request_int ( 'shop_area' ) );
		$this->assign ( 'uid', @$_SESSION ['uid'] );
		$this->display ();
	}
	public function getshopbrand($shop_id) {
		$model_shop_fs_relation = D ( 'Shop_fs_relation' );
		$map_shopfs ['shopid'] = $shop_id;
		$shop_fs_relation = $model_shop_fs_relation->where ( $map_shopfs )->select ();
		$brand_id_arr = array ();
		if ($shop_fs_relation) {
			$model_carseries = D ( 'Carseries' );
			foreach ( $shop_fs_relation as $k => $v ) {
				$map_s ['fsid'] = $v ['fsid'];
				$carseries = $model_carseries->where ( $map_s )->select ();
				if ($carseries) {
					foreach ( $carseries as $_k => $_v ) {
						$brand_id_arr [$_v ['brand_id']] = $_v ['brand_id'];
					}
				}
			}
		}
		$model_brand = D ( 'Carbrand' );
		$map_b ['brand_id'] = array (
				'in',
				implode ( ',', $brand_id_arr ) 
		);
		$brand = $model_brand->where ( $map_b )->select ();
		return $brand;
	}
	public function getfsid($shop_id) {
		$model_shop_fs_relation = D ( 'Shop_fs_relation' );
		$map_shopfs ['shopid'] = $shop_id;
		$shop_fs_relation = $model_shop_fs_relation->where ( $map_shopfs )->select ();
		if ($shop_fs_relation) {
			$model_carseries = D ( 'Carseries' );
			foreach ( $shop_fs_relation as $k => $v ) {
				if ($v ['fsid']) {
					return $v ['fsid'];
				}
			}
		}
	}
	private function poscomment($shop_id, $comment_type) {
		$model_comment = D ( 'Comment' );
		$model_commentreply = D ( 'Commentreply' );
		$model_member = D ( 'Member' );
		$model_order = D ( "Order" );
		$model_serviceitem = D ( "Serviceitem" );
		$map_c ['comment_type'] = $comment_type;
		$map_c ['shop_id'] = $shop_id;
		
		$comment = $model_comment->where ( $map_c )->order ( "create_time DESC" )->limit ( '0,5' )->select ();
		// 当前页数据查询
		if ($comment) {
			foreach ( $comment as $key => $val ) {
				// 查询对应改评论里的对应汽车类型 get_car_info
				$order = $this->Ordermodel->where ( array (
						'id' => $val ['order_id'] 
				) )->find ();
				$comment [$key] ['car'] = $this->get_car_info ( $order ['brand_id'], $order ['series_id'], $order ['model_id'] );
				if (! $val ['comment']) {
					$comment [$key] ['comment'] = "此用户没有填写评价内容";
				}
				
				$map_cr ['comment_id'] = $val ['id'];
				
				if (! $val ['user_name']) {
					$memberinfo = $model_member->find ( $val ['uid'] );
					$comment [$key] ['user_name'] = substr ( $memberinfo ['mobile'], 0, 5 ) . '******';
				}
				$commentreply = $model_commentreply->where ( $map_cr )->order ( "create_time DESC" )->select ();
				$comment [$key] ['commentreply'] = $commentreply;
				
				// 服务项目
				$order_info = $model_order->find ( $val ['order_id'] );
				$service_map = array ();
				$service_map ['id'] = array (
						'in',
						$order_info ['service_ids'] 
				);
				$serviceitem = $model_serviceitem->where ( $service_map )->select ();
				$comment [$key] ['serviceitem'] = $serviceitem;
				$ajaxservcename = "";
				if ($serviceitem) {
					foreach ( $serviceitem as $sk => $sv ) {
						$ajaxservcename .= "<li>" . $sv ['name'] . "</li>";
					}
				}
				// 服务顾问
				if ($val ['servicemember_id'] != 0) {
					$servicemember = $this->Servicemembermodel->find ( $val ['servicemember_id'] );
					$comment [$key] ['servicemember_name'] = $servicemember ['name'];
				}
			}
			return $comment;
		}
	}
	public function detail() {
		$map = array ();
		if (isset ( $_REQUEST ['shop_id'] ) && $_REQUEST ['shop_id'] != '') {
			$map ['xc_shop.id'] = array (
					'eq',
					$_REQUEST ['shop_id'] 
			);
		}
		
		$member_id = $_REQUEST ['member_id'];
		$get_model = $_REQUEST ['model_id']; // 从搜索页面接值ZZCXX搞的^-^你门懂的
		$this->assign ( 'get_model', $get_model );
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model_shopmain = D ( 'Shop' );
		$model_shop_fs_relation = D ( 'shop_fs_relation' );
		$FsModel = D ( 'fs' );
		$FsId = $model_shop_fs_relation->where ( array (
				'shopid' => $_REQUEST ['shop_id'] 
		) )->select ();
		
		if ($FsId) {
			foreach ( $FsId as $k => $v ) {
				$fsname = $FsModel->where ( array (
						'fsid' => $v ['fsid'] 
				) )->find ();
				$datafsname .= $fsname ['fsname'];
			}
		}
		
		$this->assign ( 'fsname', $datafsname );
		$list = $model_shopmain->where ( $map )->join ( 'xc_shopdetail ON xc_shop.id = xc_shopdetail.shop_id' )->find ();
		if (! $list) {
			echo '404';
			exit ();
		}
		
		if ($list ['logo'] and file_exists ( "UPLOADS/Shop/Logo/" . $list ['logo'] )) {
			$list ['have_logo'] = 1;
		} else {
			$list ['have_logo'] = 0;
		}
		
		$timesale_model = D ( 'Timesale' );
		$timesaleversion_model = D ( 'Timesaleversion' );
		$map_t ['shop_id'] = $_REQUEST ['shop_id'];
		$map_t ['status'] = 1;
		$list_timesale = $timesale_model->where ( $map_t )->select ();
		if ($list_timesale) {
			foreach ( $list_timesale as $_k => $_v ) {
				$query_array_timesale_id [] = $_v ['id'];
				$map_v ['timesale_id'] = $_v ['id'];
				$map_v ['status'] = 1;
				$map_v ['s_time'] = array (
						'lt',
						time () 
				);
				$map_v ['e_time'] = array (
						'gt',
						time () 
				);
				if ($_v ['week']) {
					$list_timesale [$_k] ['week_name'] = explode ( ',', $_v ['week'] );
					foreach ( $list_timesale [$_k] ['week_name'] as $kk => $vv ) {
						if (trim ( $vv ) == '0') {
							$list_timesale [$_k] ['week_name'] [$kk] = '日';
						}
						$list_timesale [$_k] ['week_name_s'] .= '周' . $list_timesale [$_k] ['week_name'] [$kk] . ',';
					}
				}
				if ($list_timesale [$_k] ['week_name_s']) {
					$list_timesale [$_k] ['week_name_s'] = substr ( $list_timesale [$_k] ['week_name_s'], 0, - 1 );
				}
				$list_timesale [$_k] ['timesaleversion'] = $timesaleversion_model->where ( $map_v )->select ();
				if ($list_timesale [$_k] ['timesaleversion']) {
					foreach ( $list_timesale [$_k] ['timesaleversion'] as $_kk => $_vv ) {
						if ($_vv ['workhours_sale'] > 0) {
							$list_timesale [$_k] ['timesaleversion'] [$_kk] ['workhours_sale_str'] = round ( $_vv ['workhours_sale'] * 10, 1 ) . "折";
						} else if ($_vv ['workhours_sale'] == '-1') {
							$list_timesale [$_k] ['timesaleversion'] [$_kk] ['workhours_sale_str'] = "全免";
						} else {
							$list_timesale [$_k] ['timesaleversion'] [$_kk] ['workhours_sale_str'] = "无折扣";
						}
						if ($_vv ['product_sale'] > 0) {
							$list_timesale [$_k] ['timesaleversion'] [$_kk] ['product_sale_str'] = round ( $_vv ['product_sale'] * 10, 1 ) . "折";
						} else if ($_vv ['product_sale'] == '-1') {
							$list_timesale [$_k] ['timesaleversion'] [$_kk] ['product_sale_str'] = "全免";
						} else {
							$list_timesale [$_k] ['timesaleversion'] [$_kk] ['product_sale_str'] = "无折扣";
						}
					}
				}
			}
		}
		// echo '<pre>';print_r($list_timesale);exit;
		$model_shop_fs_relation = D ( 'Shop_fs_relation' );
		$fs_arr = $model_shop_fs_relation->where ( "shopid=$_REQUEST[shop_id]" )->select ();
		
		$fsids_arr = array ();
		if (! empty ( $fs_arr )) {
			$model_fs = D ( 'Fs' );
			foreach ( $fs_arr as $v ) {
				$fs_info = $model_fs->find ( $v ['fsid'] );
				$fsids_arr [] = $fs_info ['fsname'];
				$fsids_id_arr [] = $v ['fsid'];
			}
		}
		
		if ($list ['shop_class'] == '2') {
			$list ['logo'] = "/UPLOADS/Brand/280/" . $fsids_id_arr [0] . ".jpg";
		}
		
		$timesaleversion_params ['timesale_id'] = array (
				'in',
				$query_array_timesale_id 
		);
		$timesaleversion_params ['status'] = 1;
		$timesaleversion_params ['s_time'] = array (
				'lt',
				time () 
		);
		$timesaleversion_params ['e_time'] = array (
				'gt',
				time () 
		);
		$last_version = $timesaleversion_model->where ( $timesaleversion_params )->order ( 'workhours_sale' )->limit ( 1 )->find ();
		
		if ($last_version ['workhours_sale'] > 0) {
			$salesversion ['workhours_sale'] = round ( $last_version ['workhours_sale'] * 10, 1 ) . "折";
		} else if ($last_version ['workhours_sale'] == '-1') {
			
			$salesversion ['workhours_sale'] = "全免";
		} else {
			
			$salesversion ['workhours_sale'] = "无折扣";
		}
		
		if ($last_version ['product_sale'] > 0) {
			$salesversion ['product_sale'] = round ( $last_version ['product_sale'] * 10, 1 ) . "折";
		} else {
			$salesversion ['product_sale'] = "无折扣";
		}
		$salesversion ['id'] = $last_version ['id'];
		
		if ($last_version ['workhours_sale'] == '0.00') {
			$last_version ['workhours_sale'] = 1;
		}
		if ($last_version ['product_sale'] == '0.00') {
			$last_version ['product_sale'] = 1;
		}
		
		if ($last_version ['workhours_sale'] == '-1') {
			$last_version ['workhours_sale'] = 0;
		}
		
		$model_product = D ( "Product" );
		$model_product_version = D ( "Productversion" );
		$model_serviceitem = D ( "Serviceitem" );
		
		$param ['service_id'] = 10; // 保养项目ID
		$param ['fsid'] = array (
				'in',
				$fsids_id_arr 
		);
		
		$service_name = $model_serviceitem->find ( $param ['service_id'] );
		$salesversion ['name'] = $service_name ['name'];
		
		$product_list = $model_product->where ( $param )->group ( "flag" )->select ();
		if ($product_list) {
			foreach ( $product_list as $kk => $vv ) {
				$product_detail = $model_product_version->where ( "id = {$vv[versionid]} AND status = 0 " )->field ( "product_detail" )->find ();
				$car_name = $vv ['flag'] . " " . $vv ['emission'] . " " . $vv ['shift'];
				$product_detail = unserialize ( $product_detail ['product_detail'] );
				// 工时费 零件费 门市价 折后价 节省
				if ($product_detail) {
					foreach ( $product_detail as $_v ) {
						$item_total = $_v ['quantity'] * $_v ['price'];
						if ($_v ['Midl_name'] == '工时费') {
							$workhour = $item_total;
							$work_sales = $item_total * $last_version ['workhours_sale'];
						} else {
							$product += $item_total;
							$product_sales += $item_total * $last_version ['product_sale'];
						}
					}
					
					if ($work_sales == 0 && $product_sales == 0) {
						$work_sales = $workhour;
						$product_sales = $product;
					}
					$result [] = array (
							'model_id' => $vv ['model_id'],
							'car_name' => $car_name,
							'workhour' => round ( $workhour, 2 ),
							'product' => round ( $product, 2 ),
							'total' => round ( $workhour + $product, 2 ),
							'sales_total' => round ( $work_sales + $product_sales, 2 ),
							'save_total' => round ( ($workhour + $product) - ($work_sales + $product_sales), 2 ) 
					);
					unset ( $product );
					unset ( $product_sales );
				}
			}
		}
		$_SESSION ['SEO'] = "result"; // SEO要记录的值在预约下单销毁
		$this->assign ( 'salesversion', $salesversion );
		$this->assign ( 'result', $result );
		$this->assign ( 'member_id', $member_id );
		$this->assign ( 'shop_id', $_REQUEST ['shop_id'] );
		$this->assign ( 'list_timesale', $list_timesale );
		
		// 店铺评价(店铺评价+服务顾问评价)
		$allcount = 0;
		$good = 0;
		$normal = 0;
		$bad = 0;
		
		$model_comment = D ( 'Comment' );
		$model_commentreply = D ( 'Commentreply' );
		$model_member = D ( 'Member' );
		$model_order = D ( "Order" );
		$model_serviceitem = D ( "Serviceitem" );
		
		$map_c ['shop_id'] = $_REQUEST ['shop_id'];
		// 计算总数
		$count = $model_comment->where ( $map_c )->count ();

		/*
		// 导入分页类
		import("Org.Util.Page");
		// 实例化分页类
		$p = new \Page($count, 5);
		print_r($p);
		*/

		import("Org.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类
		$limitRows = 5; // 设置每页记录数

		$p = new \AjaxPage($count, $limitRows,"get_comment"); //第三个参数是你需要调用换页的ajax函数名
		
		$limit_value = $p->firstRow . "," . $p->listRows;
		
		$page = $p->show (); 
		
		$comment = $model_comment->where ( $map_c )->order ( "create_time DESC" )->limit ( $limit_value )->select ();
		
		if ($comment) {
			foreach ( $comment as $key => $val ) {
				// 查询对应改评论里的对应汽车类型 get_car_info
				$order = $this->Ordermodel->where ( array (
						'id' => $val ['order_id'] 
				) )->find ();
				$comment [$key] ['car'] = $this->get_car_info ( $order ['brand_id'], $order ['series_id'], $order ['model_id'], $type = '1' );
				$comment [$key] ['brand_id'] = $order ['brand_id'];
				$comment [$key] ['series_id'] = $order ['series_id'];
				$comment [$key] ['model_id'] = $order ['model_id'];
				if (! $val ['comment']) {
					$comment [$key] ['comment'] = "此用户没有填写评价内容";
				}
				
				$map_cr ['comment_id'] = $val ['id'];
				if (! $val ['user_name']) {
					$memberinfo = $model_member->find ( $val ['uid'] );
					$comment [$key] ['user_name'] = substr ( $memberinfo ['mobile'], 0, 5 ) . '******';
				}
				$commentreply = $model_commentreply->where ( $map_cr )->order ( "create_time DESC" )->select ();
				$comment [$key] ['commentreply'] = $commentreply;
				
				// 服务项目
				$order_info = $model_order->find ( $val ['order_id'] );
				$service_map = array ();
				$service_map ['id'] = array (
						'in',
						$order_info ['service_ids'] 
				);
				$serviceitem = $model_serviceitem->where ( $service_map )->select ();
				$comment [$key] ['serviceitem'] = $serviceitem;
				
				// 服务顾问
				if ($val ['servicemember_id'] != 0) {
					$servicemember = $this->Servicemembermodel->find ( $val ['servicemember_id'] );
					$comment [$key] ['servicemember_name'] = $servicemember ['name'];
				}
			}
			
			$map_c ['comment_type'] = 1;
			$good = $model_comment->where ( $map_c )->count ();
			$map_c ['comment_type'] = 2;
			$normal = $model_comment->where ( $map_c )->count ();
			$map_c ['comment_type'] = 3;
			$bad = $model_comment->where ( $map_c )->count ();
			
			if ($bad) {
				$bad = $count - $good - $normal;
			}else{
				$bad = 0;
			}
			
			$good = round ( $good / $count * 100 );
			$normal = floor ( $normal / $count * 100 );
			
			$comment_rate ['good'] = round ( $good );
			$comment_rate ['normal'] = round ( $normal );
			$comment_rate ['bad'] = round ( $bad );
			$this->assign ( "comment_rate", $comment_rate );
			
			$list ['comment_rate'] = $comment_rate ['good']; // 修改新的好评率计算方式
		}
		if (is_array ( $comment )) {
			//echo "1111111111111";
			//print_r($page);
			$this->assign ( 'comment', $comment );
			$this->assign ( 'page', $page );
		}
		
		$comment1 = $this->poscomment ( $_REQUEST ['shop_id'], '1' );
		$this->assign ( 'comment1', $comment1 );
		$comment2 = $this->poscomment ( $_REQUEST ['shop_id'], '2' );
		$this->assign ( 'comment2', $comment2 );
		$comment3 = $this->poscomment ( $_REQUEST ['shop_id'], '3' );
		$this->assign ( 'comment3', $comment3 );
		
		// 店铺品牌
		$brands = $this->getshopbrand ( $_REQUEST ['shop_id'] );
		$this->assign ( 'brands', $brands );
		if ($brands) {
			foreach ( $brands as $key => $val ) {
				$brand_name .= $val ['brand_name'] . " ";
				$brand_ids .= $val ['brand_id'] . ",";
			}
			$brand_name = trim ( $brand_name );
		}
		
		// 店铺配套设施 1 wifi 2 餐厅 3 台球室 4 儿童休息室.
		$Servicefun = $this->Servicefunmodel->where ( array (
				'shop_id' => $_REQUEST ['shop_id'] 
		) )->order ( 'service_id ASC' )->select ();
		$this->assign ( 'Servicefun', $Servicefun );
		
		// 店铺配套设施图片
		$Servicefunimg = $this->Servicefunimgmodel->where ( array (
				'shop_id' => $_REQUEST ['shop_id'],
				'status' => 1 
		) )->select ();
		
		for($i = 0; $i < 8; $i ++) {
			if ($Servicefunimg [$i] ['pic'] == "") {
				$Servicefunimg [$i] ['pic'] = "none";
			}
		}
		$this->assign ( 'Servicefunimg', $Servicefunimg );
		
		if (TOP_CSS == "pa") {
			$title = $list ['shop_name'] . "汽车保养维修_平安好车-携车网";
		} else {
			$title = $list ['shop_name'] . "汽车保养维修_携车网";
		}
		$meta_keyword = $list ['shop_name'] . "," . $brand_name . "汽车保养," . $this->city_name . $brand_name . "4S店汽车保养维修,工时费" . $salesversion ['workhours_sale'];
		
		$description = "携车网为您提供" . $list ['shop_name'] . "汽车保养维修服务," . $brand_name . "保养多少钱, 在线预约,50元养车券免费领,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822";
		
		$this->assign ( 'list', $list );
		$this->assign ( 'title', $title );
		$this->assign ( 'meta_keyword', $meta_keyword );
		$this->assign ( 'description', $description );
		$this->assign ( 'workhours_sale', round ( $last_version ['workhours_sale'] * 10, 1 ) );
		
		// 公告
		$model_notice = D ( 'Notice' );
		$map_n ['shop_id'] = $_REQUEST ['shop_id'];
		$notice = $model_notice->where ( $map_n )->select ();
		$this->assign ( 'notice', $notice );
		
		// $art_limit = 21-count($notice);
		
		// 咨询
		$model_article = D ( 'Article' );
		// $map_a['shop_id'] = $_REQUEST ['shop_id'];
		$map_a ['shop_id'] = array (
				array (
						'eq',
						$_REQUEST ['shop_id'] 
				),
				array (
						'eq',
						0 
				),
				'or' 
		);
		$map_a ['city_name'] = array (
				array (
						'eq',
						$this->city_name 
				),
				array (
						'eq',
						'全部' 
				),
				'or' 
		);
		$map_a ['status'] = 1;
		
		$article = $model_article->where ( $map_a )->limit ( 3 )->order ( 'shop_id desc,id desc' )->select ();
		
		$this->assign ( 'article', $article );
		// 服务顾问
		$Servicemember = $this->Servicemembermodel->where ( array (
				'shop_id' => $_REQUEST ['shop_id'],
				'state' => '1' 
		) )->order ( 'id DESC' )->limit ( '0,3' )->select ();
		if ($Servicemember) {
			foreach ( $Servicemember as $k => $v ) {
				// 店铺评价(服务顾问评价)
				$map_type ['shop_id'] = $_REQUEST ['shop_id'];
				$map_type ['type'] = '2';
				$map_type ['servicemember_id'] = $v ['id'];
				$Servicemember [$k] ['commenttype'] = $model_comment->where ( $map_type )->order ( "create_time DESC" )->select ();
				if ($Servicemember [$k] ['commenttype']) {
					foreach ( $Servicemember [$k] ['commenttype'] as $key => $val ) {
						$map_crtype ['comment_id'] = $val ['id'];
						$map_crtype ['servicemember_id'] = $val ['servicemember_id'];
						if (! $val ['user_name']) {
							$memberinfo = $model_member->find ( $val ['uid'] );
							$Servicemember [$k] ['commenttype'] [$key] ['user_name'] = substr ( $memberinfo ['mobile'], 0, 5 ) . '******';
						}
						$commentreplytype = $model_commentreply->where ( $map_crtype )->order ( "create_time DESC" )->select ();
						$Servicemember [$k] ['commenttype'] [$key] ['commentreply'] = $commentreplytype;
						$Servicemember [$k] ['commenttype'] [$key] ['commenttype_count'] = count ( $Servicemember [$k] ['commenttype'] );
					}
				}
			}
			$this->assign ( 'Servicemember', $Servicemember );
		}
		// 现金券coupon_type=1
		$model_coupon = D ( 'Coupon' );
		$map_coupon1 ['is_delete'] = 0;
		$map_coupon1 ['show_s_time'] = array (
				'lt',
				time () 
		);
		$map_coupon1 ['show_e_time'] = array (
				'gt',
				time () 
		);
		$map_coupon1 ['coupon_type'] = 1;
		$map_coupon1 ['shop_id'] = $_REQUEST ['shop_id'];
		$coupon1 = $model_coupon->where ( $map_coupon1 )->order ( "id DESC" )->limit ( 5 )->select ();
		// echo '<pre>';print_r($coupon1);exit;
		$this->assign ( 'coupon1', $coupon1 );
		
		// 团购券coupon_type=2
		$map_coupon2 ['is_delete'] = 0;
		$map_coupon2 ['start_time'] = array (
				'lt',
				time () 
		);
		$map_coupon2 ['end_time'] = array (
				'gt',
				time () 
		);
		$map_coupon2 ['coupon_type'] = 2;
		$map_coupon2 ['shop_id'] = $_REQUEST ['shop_id'];
		$coupon2 = $model_coupon->where ( $map_coupon2 )->order ( "id DESC" )->limit ( 5 )->select ();
		// echo '<pre>';print_r($coupon2);exit;
		$this->assign ( 'coupon2', $coupon2 );
		
		$brand_ids = mb_substr ( $brand_ids, 0, - 1 );
		
		$this->get_tuijian_coupon ();
		
		$map_coupon1 = array ();
		$map_coupon1 ['is_delete'] = 0;
		$map_coupon1 ['start_time'] = array (
				'lt',
				time () 
		);
		$map_coupon1 ['end_time'] = array (
				'gt',
				time () 
		);
		$map_coupon1 ['shop_id'] = array (
				'neq',
				$_REQUEST ['shop_id'] 
		);
		$map_coupon1 ['coupon_type'] = 1;
		if (! $coupon1) {
			// $map_coupon1['fsid'] = array('like',$like_fsid);
			$map_coupon1 ['fsid'] = array (
					'in',
					$fsids_id_arr 
			);
		} else {
			$fs_tuijian1 = "neq_shop";
		}
		$brand_coupon1 = $model_coupon->where ( $map_coupon1 )->order ( "id DESC" )->limit ( 5 )->select ();
		if ($brand_coupon1) {
			if ($fs_tuijian1 != "neq_shop") {
				$fs_tuijian1 = "brand1";
			}
			$this->assign ( 'tuijian_coupon1', $brand_coupon1 );
		} else {
			$fs_tuijian1 = "tuijian";
		}
		$this->assign ( 'tuijian1', $fs_tuijian1 );
		
		$map_coupon2 = array ();
		$map_coupon2 ['is_delete'] = 0;
		$map_coupon2 ['start_time'] = array (
				'lt',
				time () 
		);
		$map_coupon2 ['end_time'] = array (
				'gt',
				time () 
		);
		$map_coupon2 ['shop_id'] = array (
				'neq',
				$_REQUEST ['shop_id'] 
		);
		$map_coupon2 ['coupon_type'] = 2;
		if (! $coupon2) {
			// $map_coupon2['fsid'] = array('like',$like_fsid);
			$map_coupon2 ['fsid'] = array (
					'in',
					$fsids_id_arr 
			);
		} else {
			$fs_tuijian2 = "neq_shop";
		}
		$brand_coupon2 = $model_coupon->where ( $map_coupon2 )->order ( "id DESC" )->limit ( 5 )->select ();
		if ($brand_coupon2) {
			if ($fs_tuijian2 != "neq_shop") {
				$fs_tuijian2 = "brand2";
			}
			$this->assign ( 'tuijian_coupon2', $brand_coupon2 );
		} else {
			$fs_tuijian2 = "tuijian";
		}
		$this->assign ( 'shop_id', $_REQUEST ['shop_id'] );
		$this->assign ( 'tuijian2', $fs_tuijian2 );
		$all_count = $model_comment->where ( array (
				'shop_id' => $_REQUEST ['shop_id'] 
		) )->count ();
		$good_count = $model_comment->where ( array (
				'shop_id' => $_REQUEST ['shop_id'],
				"comment_type" => 1 
		) )->count ();
		$medium_count = $model_comment->where ( array (
				'shop_id' => $_REQUEST ['shop_id'],
				"comment_type" => 2 
		) )->count ();
		$bad_count = $model_comment->where ( array (
				'shop_id' => $_REQUEST ['shop_id'],
				"comment_type" => 3 
		) )->count ();
		$this->assign ( 'all_count', $all_count );
		$this->assign ( 'good_count', $good_count );
		$this->assign ( 'medium_count', $medium_count );
		$this->assign ( 'bad_count', $bad_count );
		$this->display ();
	}
	
	/*
	 * @author:bright @function:预约保养页面 @time:2014-11-10
	 */
	public function order() {
// 		if( true !== $this->login()){
// 		exit;
// 		}
		$model_id = $_SESSION ['modelId'];
		$zhekou_work = $zhekou_product = 1;
		// 获取分时折扣id
		if (! empty ( $_REQUEST ['timesaleversion_id'] )) {
			$timesaleversion_id = $_REQUEST ['timesaleversion_id'];
			$model_timesale = D ( 'Timesale' ); // 载入模型
			$map_ts ['xc_timesaleversion.id'] = $timesaleversion_id;
			$list_timesale = $model_timesale->where ( $map_ts )->join ( "xc_timesaleversion ON xc_timesale.id=xc_timesaleversion.timesale_id" )->find (); // 根据id查询分时折扣信息
			$shop_id = $list_timesale ['shop_id'];
			$model_shop = D ( 'Shop' );
			$map_shop ['id'] = $shop_id;
			$shop = $model_shop->where ( $map_shop )->find ();
			$this->assign ( 'shop', $shop );
			$sale_check = $this->sale_check ( $list_timesale ['week'] ); // 根据分时折扣的星期数，处理无效日期
			$min_hours = explode ( ':', $list_timesale ['begin_time'] ); // 分时具体上下午时间输出到模板，做判断
			$max_hours = explode ( ':', $list_timesale ['end_time'] ); // 分时具体上下午时间输出到模板，做判断
			$now = time ();
			$fourhour = strtotime ( date ( 'Y-m-d' ) . ' 16:00:00' );
			if ($now < $fourhour) {
				$min = 1;
				$max = 15;
			} else {
				$min = 2;
				$max = 16;
			}
			if (($list_timesale ['s_time'] - strtotime ( date ( 'Y-m-d' ) )) > 0) {
				$s_day = floor ( ($list_timesale ['s_time'] - strtotime ( date ( 'Y-m-d' ) )) / 24 / 3600 );
				$min = max ( $s_day, $min );
			}
			if (($list_timesale ['e_time'] - strtotime ( date ( 'Y-m-d' ) )) > 0) {
				$e_day = floor ( ($list_timesale ['e_time'] - strtotime ( date ( 'Y-m-d' ) )) / 24 / 3600 );
				$max = min ( $e_day, $max );
			}
			
			if ($list_timesale ['workhours_sale'] > 0) {
				$zhekou_work = $list_timesale ['workhours_sale'];//工时折扣
				$salesversion ['workhours_sale'] = round ( $list_timesale ['workhours_sale'] * 10, 1 ) . "折";
			} else if ($list_timesale ['workhours_sale'] == '-1') {
				$salesversion ['workhours_sale'] = "全免";
			} else {
				$salesversion ['workhours_sale'] = "无折扣";
			}
				
			if ($list_timesale ['product_sale'] > 0) {
				$zhekou_product = $list_timesale ['product_sale'];//产品折扣
				$salesversion ['product_sale'] = round ( $list_timesale ['product_sale'] * 10, 1 ) . "折";
			} else {
				$salesversion ['product_sale'] = "无折扣";
			}
				
			$minday = "%y-%M-{%d+" . $min . "}";
			$maxday = "%y-%M-{%d+" . $max . "}";
			$this->assign ( "minday", $minday );
			$this->assign ( "maxday", $maxday );
		}
		$this->assign('salesversion',$salesversion);
		
		$model_serviceitem = D ( 'Serviceitem' );
		$list_si_level_0 = $model_serviceitem->where ( "si_level=0" )->select ();
		$list_si_level_1 = $model_serviceitem->where ( "si_level=1" )->order ( 'itemorder DESC' )->select ();
		
		$product_model = D ( 'Product' );
		foreach ( $list_si_level_1 as &$val ) {
			$where = array (
					'model_id' => $model_id,
					'service_id' => $val ['id'] 
			);
			$field = 'tp_xieche.xc_productversion.product_detail,tp_xieche.xc_product.id';
			$join = 'LEFT JOIN tp_xieche.xc_productversion ON tp_xieche.xc_product.versionid = tp_xieche.xc_productversion.id';
			$list = $product_model->field ( $field )->where ( $where )->join ( $join )->find ();
			if ($list) {
				$product_detail = unserialize ( $list ['product_detail'] );
				$val ['price'] = $product_detail [1] ['price'];
				$val ['midl_name'] = $product_detail [1] ['Midl_name'];
				$val ['detail1'] = $product_detail [0];	//工时费
				$total_price = $product_detail [0]['price'] * $product_detail [0]['quantity'] * $zhekou_work;//单价*数量*折扣
				unset($product_detail [0]);
				$val ['detail2'] = $product_detail; //价目费
				
				foreach ($product_detail as $product_price){
					$total_price +=$product_price['price']*$zhekou_product;
				}
				$val ['total_price'] = $total_price;
				
				if ($val['id'] == 10) {
					$defaultPrice = $total_price;
				}
			}
			unset ( $val );
		}
		
		$this->assign ( 'list_si_level_0', $list_si_level_0 );
		$this->assign ( 'list_si_level_1', $list_si_level_1 );
		
		
		/*
		 * $model_productversion = D('Productversion'); if ($list){ foreach ($list as $k=>$v){ //$map_pv['id'] = $v['versionid']; 刷数据好像有点问题 暂时用下面的查询 $map_pv['product_id'] = $v['id']; $map_pv['status'] = 0; $productversion = $model_productversion->where($map_pv)->find(); var_dump($productversion);exit; } }
		 */
		$this->assign('defaultPrice',$defaultPrice);
		$this->assign ( 'shop_area', $_REQUEST ['shop_area'] );
		$this->assign ( 'brand_id', $_REQUEST ['brand_id'] );
		// 添加得到区域ID(CityId)
		$this->GetArea ();
		// 得到区域ID(CityId)
		$uid = $this->GetUserId ();
		/* @author:chf @查询店铺名字 新添加 */
		$shop_info = $this->ShopModel->where ( array (
				'id' => $this->request_int ( 'shop_id' ) 
		) )->find ();
		// 优惠券下订
		if ($_REQUEST ['membercoupon_id']) {
			$model_membercoupon = D ( 'Membercoupon' );
			$map_mc ['membercoupon_id'] = $_REQUEST ['membercoupon_id'];
			$map_mc ['uid'] = $uid;
			$map_mc ['is_pay'] = 1;
			$membercoupon = $model_membercoupon->where ( $map_mc )->find ();
			if ($membercoupon) {
				if ($membercoupon ['is_use'] == 1) {
					$this->error ( '优惠券已使用！', '__APP__/coupon' );
				}
				if ($membercoupon ['end_time'] < time ()) {
					$this->error ( '优惠券已过期！', '__APP__/coupon' );
				}
				$model_coupon = D ( 'Coupon' );
				$map_c ['id'] = $membercoupon ['coupon_id'];
				$coupon = $model_coupon->where ( $map_c )->find ();
				$model_id = $coupon ['model_id'];
				$service_ids = $coupon ['service_ids'];
				$shop_id = $coupon ['shop_id'];
				$map_s ['id'] = array (
						'in',
						$service_ids 
				);
				$serviceitem = $model_serviceitem->where ( $map_s )->select ();
				
				$sale_check = $this->sale_check ( $coupon ['week'] ); // 根据分时折扣的星期数，处理无效日期
				$min_hours = explode ( ':', $coupon ['s_time'] ); // 分时具体上下午时间输出到模板，做判断
				$max_hours = explode ( ':', $coupon ['e_time'] ); // 分时具体上下午时间输出到模板，做判断
				$now = time ();
				$fourhour = strtotime ( date ( 'Y-m-d' ) . ' 16:00:00' );
				
				if ($now < $fourhour) {
					$min = 1;
					$max = 15;
				} else {
					$min = 2;
					$max = 16;
				}
				if (($coupon ['start_time'] - strtotime ( date ( 'Y-m-d' ) )) > 0) {
					$s_day = floor ( ($coupon ['start_time'] - strtotime ( date ( 'Y-m-d' ) )) / 24 / 3600 );
					$min = max ( $s_day, $min );
				}
				if (($coupon ['end_time'] - strtotime ( date ( 'Y-m-d' ) )) > 0) {
					$e_day = floor ( ($coupon ['end_time'] - strtotime ( date ( 'Y-m-d' ) )) / 24 / 3600 );
					$max = min ( $e_day, $max );
				}
				$minday = "%y-%M-{%d+" . $min . "}";
				$maxday = "%y-%M-{%d+" . $max . "}";
				$this->assign ( "minday", $minday );
				$this->assign ( "maxday", $maxday );
				$this->assign ( 'membercoupon_id', $_REQUEST ['membercoupon_id'] );
				$this->assign ( "serviceitem", $serviceitem );
				$this->assign ( "coupon", $coupon );
			} else {
				$this->error ( '优惠券ID无效！', '__APP__/coupon' );
			}
		}
		
		$doubleCalendar = $this->double_or_single_Calendar (); // 单双月显示判断
		
		if ($uid) {
			$model_member = D ( 'Member' );
			$map_m ['uid'] = $uid;
			$member = $model_member->where ( $map_m )->find ();
			$this->assign ( 'member', $member );
		}
		$this->get_ordermenu ();
		$this->get_expert_tuijian ();
		
		$model_carmodel = D ( 'Carmodel' );
		$model_carseries = D ( 'Carseries' );
		$model_carbrand = D ( 'Carbrand' );
		
		if ($model_id || $_REQUEST ['u_c_id']) {
			
			if ($_REQUEST ['u_c_id']) {
				$model_membercar = D ( "membercar" );
				$membercar = $model_membercar->find ( $_REQUEST ['u_c_id'] );
				$car_number_arr = explode ( '_', $membercar ['car_number'] );
				$membercar ['s_pro'] = $car_number_arr [0];
				$membercar ['licenseplate'] = $car_number_arr [1];
				$this->assign ( "membercar", $membercar );
				$model_id = $membercar ['model_id'];
			}
			$carname = array ();
			$map_cm ['model_id'] = $model_id;
			$carmodel = $model_carmodel->where ( $map_cm )->find ();
			$carname ['model_name'] = $carmodel ['model_name'];
			$series_id = $carmodel ['series_id'];
			
			$map_cs ['series_id'] = $series_id;
			$carseries = $model_carseries->where ( $map_cs )->find ();
			$carname ['series_name'] = $carseries ['series_name'];
			$brand_id = $carseries ['brand_id'];
			
			$map_cb ['brand_id'] = $brand_id;
			$carbrand = $model_carbrand->where ( $map_cb )->find ();
			$carname ['brand_name'] = $carbrand ['brand_name'];
			$this->assign ( 'carname', $carname );
		} else {
			$model_shop_fs_relation = D ( 'shop_fs_relation' );
			$fsid_info = $model_shop_fs_relation->where ( array (
					"shopid" => $_REQUEST ['shop_id'] 
			) )->select ();
			if ($fsid_info) {
				foreach ( $fsid_info as $key => $val ) {
					$fsid_arr [] = $val ['fsid'];
				}
			}
			$map_series ['fsid'] = array (
					'in',
					$fsid_arr 
			);
			$series_info = $model_carseries->where ( $map_series )->group ( 'brand_id' )->select ();
			if ($series_info) {
				foreach ( $series_info as $key => $val ) {
					$brand_ids_arr [] = $val ['brand_id'];
				}
			}
			$map_brand ['brand_id'] = array (
					'in',
					$brand_ids_arr 
			);
			$brand_info = $model_carbrand->where ( $map_brand )->select ();
			$this->assign ( 'brand_arr', $brand_info );
		}
		
		/* 取得拥有抵用券信息 */
		if ($_SESSION ['uid']) {
			// 普通抵用券
			$arr1 ['uid'] = $_SESSION ['uid'];
			$arr1 ['salecoupon_id'] = 1;
			$arr1 ['_string'] = "FIND_IN_SET('{$_REQUEST[shop_id]}', shop_ids)";
			
			$arr2 ['uid'] = $_SESSION ['uid'];
			$arr2 ['salecoupon_id'] = 2;
			$arr2 ['_string'] = "FIND_IN_SET('{$_REQUEST[shop_id]}', shop_ids)";
			
			$arr3 ['uid'] = $_SESSION ['uid'];
			$arr3 ['salecoupon_id'] = 3;
			$arr3 ['_string'] = "FIND_IN_SET('{$_REQUEST[shop_id]}', shop_ids)";
			
			$sale_count [1] = $this->MembersalecouponModel->where ( $arr1 )->count ();
			// 驴妈妈抵用券
			$sale_count [2] = $this->MembersalecouponModel->where ( $arr2 )->count ();
			$sale_count [3] = $this->MembersalecouponModel->where ( $arr3 )->count ();
			$this->MembersalecouponModel->where ( $arr3 )->count ();
			$salecoupon = $this->MembersalecouponModel->where ( array (
					'is_delete' => 0,
					'is_delete' => 0,
					'uid' => $_SESSION ['uid'] 
			) )->select ();
			
			$arr4 ['_string'] = "FIND_IN_SET('{$_REQUEST[shop_id]}', shop_ids)";
			$Salecoupon_count = $this->Salecouponmodel->where ( $arr4 )->count (); // 判断是否有车惠抵用券参加的店铺
			
			$this->assign ( 'sale_count', $sale_count );
			$this->assign ( 'Salecoupon_count', $Salecoupon_count );
			// $this->assign('salelmm_count',$salelmm_count);
			// $this->assign('salech_count',$salech_count);
			$this->assign ( 'salecoupon', $salecoupon );
		}
		
		/* 服务顾问查询 */
		$Servicemember = $this->Servcemembermodel->where ( array (
				'shop_id' => $_REQUEST ['shop_id'],
				'state' => '1' 
		) )->order ( 'id DESC' )->limit ( '0,3' )->select ();
		$this->assign ( 'Servicemember', $Servicemember );
		$this->assign ( 'sess_uid', $_SESSION ['uid'] );
		$this->assign ( 'timesaleversion_id', $timesaleversion_id );
		$this->assign ( 'model_id', $model_id );
		$this->assign ( 'u_c_id', $_REQUEST ['u_c_id'] );
		$this->assign ( 'shop_id', $shop_id );
		$this->assign ( 'min_hours', $min_hours [0] );
		$this->assign ( 'min_minute', $min_hours [1] );
		$this->assign ( 'max_hours', $max_hours [0] );
		$this->assign ( 'max_minute', $max_hours [1] );
		$this->assign ( 'sale_check', $sale_check );
		$this->assign ( 'doubleCalendar', $doubleCalendar );
		
		if (TOP_CSS == "pa") {
			$this->assign ( 'title', "平安好车-携车网-汽车保养,事故车维修,全国唯一4S店售后折扣网站" );
		} else {
			if ($carname) {
				$this->assign ( 'title', $shop ['shop_name'] . "-" . $carname ['brand_name'] . " " . $carname ['series_name'] . " " . $carname ['model_name'] . "-预约下单-4S店售后预约-携车网" );
				if (strpos ( $_SERVER ['REQUEST_URI'], "u_c_id-" )) {
					$canonical = WEB_ROOT."/order/yuyue-timesaleversion_id-{$timesaleversion_id}-shop_id-{$shop_id}-model_id-{$model_id}.html";
					$this->assign ( 'canonical', $canonical );
				}
			} else {
				$time_str = $min_hours [0] . ":" . $min_hours [1] . "-" . $max_hours [0] . ":" . $max_hours [1];
				$this->assign ( 'title', $shop ['shop_name'] . $time_str . "|工时折扣率" . $salesversion ['workhours_sale'] . "|零件折扣率" . $salesversion ['product_sale'] . "-预约下单-携车网" );
				if (strpos ( $_SERVER ['REQUEST_URI'], "model_id--u_c_id--" )) {
					$canonical = WEB_ROOT."/order/yuyue-timesaleversion_id-{$timesaleversion_id}-shop_id-{$shop_id}.html";
					$this->assign ( 'canonical', $canonical );
				}
			}
		}
		$this->assign ( 'meta_keyword', "汽车保养,汽车维修,4S店预约保养,汽车保养预约,汽车维修预约" );
		$this->assign ( 'description', "携车网,为用户提供汽车保养维修预约服务,享受分时折扣优惠,最低5折起,还有更多汽车保养维修团购套餐任您选,事故车维修预约,不花钱,还有返利拿4006602822" );
		$this->display ();
	}

	function shopserviceimg(){
		if(!$_POST){
			$this->error("参数错误");
		}
		$str = $_POST['content'];//提交过来的数据
		$strArr = explode('<p>',$str);//分割数据

		$length = count($strArr)-1;

		$im = imagecreatetruecolor(660, $length*20);
		$white = imagecolorallocate($im, 255, 255, 255);
		$grey = imagecolorallocate($im, 128, 128, 128);
		$black = imagecolorallocate($im, 0, 0, 0);

		imagefilledrectangle($im, 0, 0, 660, $length*20, $white);
		$font = 'msyhbd.ttf';
		for ($i=1; $i <= $length; $i++) {
			$str1 = strip_tags($strArr[$i]);
			$str2 = explode('|',$str1);
			for ($j=0; $j < count($str2); $j++) {
				imagettftext($im, 10, 0, $j*110, $i*20, $black, $font, $str2[$j]);				
			}
			
		}

		$filename = 'UPLOADS/shopservice/file.png';
		imagepng($im,$filename);
		imagedestroy($im);

		 if($im!== false){
		 	$this->success('<img src="/UPLOADS/shopservice/file.png?'.time().'" >');
		 }else{
		 	$this->error('返回错误');
		 }
    }

	function get_comment() {
		if (isset ( $_GET ['shop_id'] ) && ! empty ( $_GET ['shop_id'] )) {
			$model_comment = D ( 'Comment' );
			$map_c ['shop_id'] = $_GET ['shop_id'];
			if (! empty ( $_GET ['type'] )) {
				$map_c ['comment_type'] = $_GET ['type'];
			}
			$count = $model_comment->where ( $map_c )->count ();
			import ( "Org.Util.AjaxPage" ); // 导入分页类 注意导入的是自己写的AjaxPage类
			$limitRows = 5; // 设置每页记录数
			$p = new \AjaxPage ( $count, $limitRows, "get_comment" ); // 第三个参数是你需要调用换页的ajax函数名
			$limit_value = $p->firstRow . "," . $p->listRows;
			$page = $p->show (); // 产生分页信息，AJAX的连接在此处生成
			$comments = $model_comment->where ( $map_c )->order ( "create_time DESC" )->limit ( $limit_value )->select ();
			if ($comments) {
				$model_order = D ( "Order" );
				$model_serviceitem = D ( "Serviceitem" );
				$car_model_model = M ( 'tp_xieche.carmodel', 'xc_' );
				foreach ( $comments as &$val ) {
					// 服务项目
					$order_info = $model_order->field ( 'service_ids,model_id,brand_id,series_id' )->find ( $val ['order_id'] );
					$service_map = array ();
					$service_map ['id'] = array (
							'in',
							$order_info ['service_ids'] 
					);
					$serviceitems = $model_serviceitem->field ( 'name' )->where ( $service_map )->select ();
					$items = '';
					if ($serviceitems) {
						foreach ( $serviceitems as $serviceitem ) {
							$items .= $serviceitem ['name'];
						}
					}
					$val ['item'] = $items;
					// 评分
					if ($val ['comment_type'] == '1') {
						$val ['score'] = 10;
					} elseif ($val ['comment_type'] == '2') {
						$val ['score'] = 8;
					} else {
						$val ['score'] = 5;
					}
					$val ['model_id'] = $order_info ['model_id'];
					$val ['brand_id'] = $order_info ['brand_id'];
					$val ['series_id'] = $order_info ['series_id'];
					
					// 车型
					$car_model = $car_model_model->where ( array (
							'model_id' => $order_info ['model_id'] 
					) )->find ();
					$val ['car_name'] = $car_model ['model_name'];
					
					// 用户
					if (! $val ['user_name']) {
						$val ['user_name'] = '13*********';
					}
					unset ( $val );
				}
				echo json_encode ( array (
						'status' => 1,
						'data' => $comments,
						'page' => $page
				) );
			} else {
				echo json_encode ( array (
						'status' => 0,
						'data' => '暂无数据' 
				) );
			}
			;
		}
	}
	
	/*
	 * @author:bright @function:下订单 @time:2014-11-10
	 */
	public function create_order() {
		if (! $_SESSION ['modelId']) {
			$this->error ( '请先选择车型', '/carservice/order',true );
		}
		$this->assign ( 'jumpUrl', cookie ( '_currentUrl_' ) );
		if (empty ( $_POST ['xcagreement'] )) {
			$this->error ( '请勾选同意携车网维修保养预约协议！', '', true );
		}
		if (empty ( $_POST ['order_date'] )) {
			$this->error ( '请选择预约日期！', '', true );
		}
		
		if (empty ( $_POST ['truename'] )) {
			$this->error ( '姓名不能为空！', '', true );
		}
		if (empty ( $_POST ['mobile'] )) {
			$this->error ( '手机号不能为空！', '', true );
		}
		if (empty ( $_POST ['licenseplate'] )) {
			$this->error ( '车牌号不能为空！', '', true );
		}
		$CityId = $this->GetCityId ( $_SESSION ['area_info'] [0] ); // 得到城市ID
		                                                       // 根据提交过来的预约时间，做判断(暂时先注销)
		if ($_POST ['order_date']) {
			// 载入产品MODEL
			$model_product = D ( 'Product' );
			// $map['product_id'] = array('in',$_REQUEST['product_str']);
			if ($_REQUEST ['select_services']) {
				$select_services = implode ( ',', $_REQUEST ['select_services'] );
			} else {
				$select_services = '';
			}
			$uid = $this->GetUserId ();
			$order_time = $_POST ['order_date'].' '.$_POST ['order_hours'].':'.$_POST ['order_minute'] ;
			$order_time = strtotime ( $order_time );
			
			$now = time ();
			$fourhour = strtotime ( date ( 'Y-m-d' ) . ' 16:00:00' );
			if ($now < $fourhour) {
				$min = 1;
				$max = 15;
			} else {
				$min = 2;
				$max = 16;
			}
			if ($order_time > (time () + 86400 * $max)) {
				$this->error ( '最多预约15天以内,请重新选择！' );
			}
			if (! $u_c_id = $_POST ['u_c_id']) {
				$u_c_id = 0;
			}
			$save_discount = 0.00;
			$productversion_ids_str = '';
			if ($_REQUEST ['membercoupon_id']) {
				$membercoupon_id = $_REQUEST ['membercoupon_id'];
				$model_membercoupon = D ( 'Membercoupon' );
				$map_mc ['membercoupon_id'] = $_REQUEST ['membercoupon_id'];
				$membercoupon = $model_membercoupon->where ( $map_mc )->find ();
				$coupon_id = $membercoupon ['coupon_id'];
				$model_coupon = D ( 'Coupon' );
				$map_c ['id'] = $coupon_id;
				$coupon = $model_coupon->where ( $map_c )->find ();
				$model_id = $coupon ['model_id'];
				$select_services = $coupon ['service_ids'];
				$shop_id = $coupon ['shop_id'];
				$total_price = $coupon ['coupon_amount'];
				$cost_price = $coupon ['cost_price'];
				$jiesuan_money = $coupon ['jiesuan_money'];
				$save_price = $cost_price - $total_price;
				$order_type = $coupon ['coupon_type'];
				$data ['order_name'] = $coupon ['coupon_name'];
				$data ['order_des'] = $coupon ['coupon_summary'];
			} else {
				$order_type = 4;
				if ($select_services) {
					$map ['service_id'] = array (
							'in',
							$select_services 
					);
					$map ['model_id'] = array (
							'eq',
							$_REQUEST ['model_id'] 
					);
					$list_product = $model_product->where ( $map )->select ();
				}
				
				$timesale_model = D ( 'Timesale' );
				$map_tsv ['xc_timesaleversion.id'] = $_POST ['timesaleversion_id'];
				$sale_arr = $timesale_model->where ( $map_tsv )->join ( "xc_timesaleversion ON xc_timesale.id=xc_timesaleversion.timesale_id" )->find ();
				if ($order_time > $sale_arr ['s_time'] and $order_time < $sale_arr ['e_time']) {
					$order_week = date ( "w", $order_time );
					$normal_week = explode ( ',', $sale_arr ['week'] );
					
					if (! in_array ( $order_week, $normal_week )) {
						$this->error ( '预约时间错误,请重新选择！', '', true );
					}
					$order_hour = date ( "H:i", $order_time );
					
					if (strtotime ( date ( 'Y-m-d' ) . ' ' . $order_hour ) < strtotime ( date ( 'Y-m-d' ) . ' ' . $sale_arr ['begin_time'] ) || strtotime ( date ( 'Y-m-d' ) . ' ' . $order_hour ) > strtotime ( date ( 'Y-m-d' ) . ' ' . $sale_arr ['end_time'] )) {
						$this->error ( '服务范围为：' . $sale_arr ['begin_time'] . '到' . $sale_arr ['end_time'] . ',您预约时间不在服务时间范围内,请重新选择预约时间！', '', true );
					}
				} else {
					$this->error ( '预约时间错误,请重新选择！', '', true );
				}
				// echo '<pre>';print_r($sale_arr);exit;
				
				// 计算订单总价格
				$total_product_price = 0;
				$total_workhours_price = 0;
				$productversion_ids_arr = array ();
				if (! empty ( $list_product )) {
					foreach ( $list_product as $kk => $vv ) {
						$productversion_ids_arr [] = $vv ['versionid'];
						$list_product [$kk] ['list_detai'] = unserialize ( $vv ['product_detail'] );
						if (! empty ( $list_product [$kk] ['list_detai'] )) {
							foreach ( $list_product [$kk] ['list_detai'] as $key => $val ) {
								$list_product [$kk] ['list_detai'] [$key] ['total'] = $val ['price'] * $val ['quantity'];
								if ($val ['Midl_name'] == '工时费') {
									$total_workhours_price += $list_product [$kk] ['list_detai'] [$key] ['total'];
								} else {
									$total_product_price += $list_product [$kk] ['list_detai'] [$key] ['total'];
								}
							}
						}
					}
				}
				$cost_price = $total_workhours_price + $total_product_price;
				$jiesuan_money = 0;
				$productversion_ids_str = implode ( ",", $productversion_ids_arr );
				
				$total_price = 0;
				$save_price = 0;
				
				if (! empty ( $sale_arr )) {
					if ($sale_arr ['product_sale'] > 0) {
						$total_price += $total_product_price * $sale_arr ['product_sale'];
					} else {
						$total_price += $total_product_price;
					}
					/*
					 * if ($sale_arr['workhours_sale']=='0.00'){ $sale_arr['workhours_sale'] = 1; }
					 */
					$workhours_sale = $sale_arr ['workhours_sale'];
					if ($workhours_sale > 0) {
						$total_price += $total_workhours_price * $workhours_sale;
						$save_price = $total_workhours_price * ($sale_arr ['workhours_sale'] - $workhours_sale);
						$save_discount = $sale_arr ['workhours_sale'] - $workhours_sale;
					} else {
						$total_price += $total_workhours_price * 0;
						$save_price = $total_workhours_price;
						$save_discount = $sale_arr ['workhours_sale'];
					}
				} else {
					$total_price += $total_product_price + $total_workhours_price;
				}
				$membercoupon_id = 0;
				$coupon_id = 0;
			}
			if ($sale_arr ['product_sale']) {
				$product_sale = $sale_arr ['product_sale'];
			} else {
				$product_sale = 0;
			}
			if ($sale_arr ['workhours_sale']) {
				$workhours_sale = $sale_arr ['workhours_sale'];
			} else {
				$workhours_sale = 0;
			}
			if ($_REQUEST ['member_id']) {
				$data ['member_id'] = $_REQUEST ['member_id'];
			}
			$data ['u_c_id'] = $u_c_id;
			$data ['uid'] = $uid;
			$data ['shop_id'] = $_POST ['shop_id'];
			$data ['model_id'] = $_SESSION['modelId'];
			$data ['timesaleversion_id'] = $_POST ['timesaleversion_id'];
			$data ['service_ids'] = $select_services;
			$data ['product_sale'] = $product_sale;
			$data ['workhours_sale'] = $workhours_sale;
			$data ['truename'] = $_POST ['truename'];
			$data ['mobile'] = $_POST ['mobile'];
			$data ['licenseplate'] = trim ( $_POST ['cardqz'] . $_POST ['licenseplate'] );
			$data ['mileage'] = $_POST ['miles'];
			$data ['car_sn'] = $_POST ['car_sn'];
			$data ['remark'] = $_POST ['remark'];
			$data ['order_time'] = $order_time;
			$data ['create_time'] = time ();
			$data ['total_price'] = $total_price;
			$data ['cost_price'] = $cost_price;
			$data ['jiesuan_money'] = $jiesuan_money;
			$data ['productversion_ids'] = $productversion_ids_str;
			$data ['coupon_save_money'] = $save_price;
			$data ['coupon_save_discount'] = $save_discount;
			$data ['membercoupon_id'] = $membercoupon_id;
			$data ['coupon_id'] = $coupon_id;
			$data ['order_type'] = $order_type;
			
			
			if ($_REQUEST ['ra_servicemember_id']) {
				$data ['servicemember_id'] = $_REQUEST ['ra_servicemember_id'];
			}
			/*
			 * @得到是否是百度过来的用户
			 */
			if ($_COOKIE ["Baidu_id"] > 0) {
				$data ['baidu_id'] = $_COOKIE ["Baidu_id"];
				$data ['baidu_ip'] = $_SERVER ["REMOTE_ADDR"];
			}
			// @检查是否通过平安好车访问的用户
			if (TOP_CSS == 'pa') {
				$data ['is_pa'] = '1';
			}
			
			$model = D ( 'Order' );
			if ($uid) {
				$data ['city_id'] = $CityId;
				// 抵用券选择[1]50元抵用券 [2]车会抵用券
				$radio_sale = $_POST ['radio_sale'];
				$code = $_POST ['code'];
				if ($radio_sale || $code) {
					$membersalecoupon_id = $this->add_salemembercoupon ( $uid, $radio_sale, $code, $data ['shop_id'] );
					$data ['membersalecoupon_id'] = $membersalecoupon_id;
				}
				$model->add ( $data );
				
				if (false !== $model->add ( $data )) {
					$_POST ['order_id'] = $model->getLastInsID ();
					$this->MembersalecouponModel->where ( array (
							'membersalecoupon_id' => $membersalecoupon_id 
					) )->save ( array (
							'order_id' => $_POST ['order_id'] 
					) );
				}
				$model_member = D ( 'Member' );
				$get_user_name = $model_member->where ( "uid=$uid" )->find ();
				if ($list_product) {
					foreach ( $list_product as $k => $v ) {
						$sub_order [] = array (
								'order_id' => $_POST ['order_id'],
								'productversion_id' => $list_product [$k] ['versionid'],
								'service_id' => $list_product [$k] ['service_id'],
								'service_item_id' => $list_product [$k] ['service_item_id'],
								'uid' => $uid,
								'user_name' => $get_user_name ['username'],
								'create_time' => time (),
								'update_time' => time () 
						);
					}
					$model_suborder = D ( 'Suborder' );
					$list = $model_suborder->addAll ( $sub_order );
				}
			} else {
				$model = D ( 'Ordernologin' );
				if (false !== $model->add ( $data )) {
					$_POST ['order_id'] = $model->getLastInsID ();
				}
			}
			
			if (! empty ( $_POST ['order_id'] )) {
				if ($uid) {
					$this->success ( '预约提交成功！', __APP__ . '/myhome',true );
				} else {
					$this->success ( '预约提交成功！', __APP__ . '/index',true );
				}
			} else {
				$this->error ( '预约失败！', __APP__ . '/myhome',true );
			}
		}
	}
	
	/*
	 * @author:chf @function:添加进入抵用券表 @time:2013-11-4
	 */
	function add_salemembercoupon($uid, $radio_sale, $code, $shop_id) {
		/* 抵用券选择[1]50元抵用券 [2]驴妈妈50元抵用券 [3]车会抵用券 */
		$member = $this->MemberModel->where ( array (
				'uid' => $uid 
		) )->find ();
		if (! $member ['mobile']) {
			$this->error ( '请填写个人资料手机号！', __APP__ . '/myhome' );
			exit ();
		}
		$nowtime = time ();
		if ($radio_sale == 3) {
			$mobile = $member ['mobile'];
			$membersalecouponcount = $this->MembersalecouponModel->where ( array (
					'mobile' => $member ['mobile'],
					'is_use' => 0,
					'salecoupon_id' => '3' 
			) )->count ();
			if ($membersalecouponcount > 0) {
				$membersale = $this->MembersalecouponModel->where ( array (
						'mobile' => $member ['mobile'],
						'is_use' => 0,
						'salecoupon_id' => '1' 
				) )->find ();
				// $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersale['membersalecoupon_id']))->save(array('is_use'=>1,'use_time'=>$nowtime));
				$membersalecoupon_id = $membersale ['membersalecoupon_id'];
			}
		} else if ($radio_sale == 1) {
			$membersalecouponcount = $this->MembersalecouponModel->where ( array (
					'mobile' => $member ['mobile'],
					'is_use' => 0,
					'salecoupon_id' => '1' 
			) )->count ();
			if ($membersalecouponcount > 0) {
				$membersale = $this->MembersalecouponModel->where ( array (
						'mobile' => $member ['mobile'],
						'is_use' => 0,
						'salecoupon_id' => '1' 
				) )->find ();
				// $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersale['membersalecoupon_id']))->save(array('is_use'=>1,'use_time'=>$nowtime));
				$membersalecoupon_id = $membersale ['membersalecoupon_id'];
			}
		} else if ($radio_sale == 2) {
			$membersalecouponcount = $this->MembersalecouponModel->where ( array (
					'mobile' => $member ['mobile'],
					'is_use' => 0,
					'salecoupon_id' => '2' 
			) )->count ();
			if ($membersalecouponcount > 0) {
				$membersale = $this->MembersalecouponModel->where ( array (
						'mobile' => $member ['mobile'],
						'is_use' => 0,
						'salecoupon_id' => '2' 
				) )->find ();
				// $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersale['membersalecoupon_id']))->save(array('is_use'=>1,'use_time'=>$nowtime));
				$membersalecoupon_id = $membersale ['membersalecoupon_id'];
			}
		}
		return $membersalecoupon_id;
	}
	/*
	 *检查星期几是否有不包含在里面的星期
	*$week_str 表saletime取过来的对应数据
	*
	*/
	function sale_check($week_str){
	
		$week_arr = explode(',',$week_str);
		$week_num = C('WEEK_NUM');
	
		foreach($week_num AS $k=>$v){
			if(!in_array($v,$week_arr)){
				$tmp .= $v.',';
			}
		}
	
		$res = substr($tmp,0,-1);
		return $res;
	}
	/*
	 *用于日期的单双月判断
	*
	*/
	function double_or_single_Calendar(){
		$current_date=date('d',time());
		if(($current_date+16) >= 30){
			return 'true';
		}else{
			return 'false';
		}
	
	}
}