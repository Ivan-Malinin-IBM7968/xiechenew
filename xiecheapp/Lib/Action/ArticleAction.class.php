<?php
//文章
class ArticleAction extends CommonAction {
    
    function detail(){
		$city_name = $this->city_name;
        $aid = $_REQUEST['aid'];
        if ($aid){
            $model_article = D('Article');
            $map_a['id'] = $aid;
            $articleinfo = $model_article->where($map_a)->find($aid);
            $fsid = $articleinfo['fsid'];

			$model_fs = D("Fs");
			$fs_name = $model_fs->getByFsid($fsid);
			$next_article = $model_article->where("status=1 AND (city_name='$city_name' OR city_name='全部') AND id>{$aid}")->order("id")->limit(1)->find();
			if(!$next_article) {
				$next_article = $model_article->where("status=1 AND (city_name='$city_name' OR city_name='全部') ")->order("id")->limit(1)->find();
			}
			$prev_article = $model_article->where("status=1 AND (city_name='$city_name' OR city_name='全部') AND id<{$aid}")->order("id desc")->limit(1)->find();
			if(!$prev_article) {
				$prev_article = $model_article->where("status=1 AND (city_name='$city_name' OR city_name='全部') ")->order("id desc")->limit(1)->find();
			}
			
			//$articleinfo['content'] = strip_tags($articleinfo['content']);
			//关键字链接
			if($articleinfo['keyword']) {
				$keyword = explode(",",$articleinfo['keyword']);
				$this->assign("keyword",$keyword);
			}

			//修改 article content 格式
			if($articleinfo['id'] >= 11712) {
				//var_dump($articleinfo['content']); 
				
				//$articleinfo['content'] = str_replace("<br /><br />","</p>",$articleinfo['content']);
				$articleinfo['content'] = str_replace("<br />","</p><p>",$articleinfo['content']);
				$articleinfo['content'] = str_replace("<p>","<p style='text-indent:2em;'>",$articleinfo['content']);

				$articleinfo['content'] = "<p style='text-indent:2em;'>".$articleinfo['content'];
			}

			$model_shop = D("Shop");
			$shop_info = $model_shop->find($articleinfo['shop_id']);
			
			$brands = R("Shop/getshopbrand",array($articleinfo['shop_id']));
			if($brands) {
				foreach($brands as $key=>$val) {
					$brand_name .= $val['brand_name']." ";
				}
				$brand_name = mb_substr($brand_name,0,-1);
			}
			$this->assign('brands',$brands);
			
			//文章关键字相关
			$map_key['status'] = 1;
			if($keyword) {
				foreach($keyword as $key=>$val) {
					if($val) {
						$like = array("like","%,{$val},%");
						$result[] = $like;
					}
				}
			}
			$result[] = 'ThinkPHP';
			$result[] = 'or';
			$map_key['keyword'] = $result;
			$map_key['city_name'] = array(array('eq',$city_name),array('eq','全部'),'or');

			$article_key_relation = $model_article->where($map_key)->limit(10)->select();
			$this->assign("article_key_relation",$article_key_relation);

			//文章厂商相关
			$map_s_r['shop_id'] = $articleinfo['shop_id'];
			$map_s_r['status'] = 1;
			$article_shop_relation = $model_article->where($map_s_r)->limit(10)->select();
			$this->assign("article_shop_relation",$article_shop_relation);
			$this->assign("shop_info",$shop_info);
			$this->assign("next_article",$next_article);
			$this->assign("prev_article",$prev_article);
        }
		
		if( $fs_name[fsname] ) {
			$fs_name[fsname] = $fs_name[fsname]."-";
		}
		if( $brand_name ) {
			$brand_name = $brand_name."-";
		}

		$title = "{$articleinfo[title]}-{$shop_info[shop_name]}价格,预约,优惠,折扣,点评,电话查询-{$fs_name[fsname]}{$brand_name}携车网";
		$this->assign('title',$title);
		if($keyword) {
			$meta_keyword = implode("," , $keyword);
			$this->assign('meta_keyword',$meta_keyword);
		}
		$description = $articleinfo[summary];
		$this->assign('description',$description);
        $this->assign('articleinfo',$articleinfo);
        
        
        //推荐顾问
        $this->get_expert_tuijian();
        
        //相关文章
        //$this->get_relation_article($fsid);
        
        //推荐优惠券
        $this->get_tuijian_coupon();
        
        $this->display();
    }

	/*
		@author:chf
		@function:显示文章详细页 (新 )
		@time:2013-9-5
	*/
	function d(){
		
		$this->assign('current','articlelist');

		$city_name = $this->city_name;
        $aid = $_REQUEST['id'];
        if ($aid){
            $model_article = D('Article');
            $map_a['id'] = $aid;
			$map_a['status'] = 1;
            $articleinfo = $model_article->where($map_a)->find($aid);
			if(!$articleinfo){
				$this->_empty();
				exit();
			}else{
				$fsid = $articleinfo['fsid'];

				$model_fs = D("Fs");
				$fs_name = $model_fs->getByFsid($fsid);
				
				$next_article = $model_article->where("status=1 AND (city_name='$city_name' OR city_name='全部') AND id>{$aid}")->order("id")->limit(1)->find();
				if(!$next_article) {
					$next_article = $model_article->where("status=1 AND (city_name='$city_name' OR city_name='全部') ")->order("id")->limit(1)->find();
				}
				$prev_article = $model_article->where("status=1 AND (city_name='$city_name' OR city_name='全部') AND id<{$aid}")->order("id desc")->limit(1)->find();
				if(!$prev_article) {
					$prev_article = $model_article->where("status=1 AND (city_name='$city_name' OR city_name='全部') ")->order("id desc")->limit(1)->find();
				}
				
				
				//关键字链接
				if($articleinfo['keyword']) {
					$keyword = explode(",",$articleinfo['keyword']);
					$this->assign("keyword",$keyword);
				}

				//修改 article content 格式
				//if($articleinfo['id'] >= 11712) {
					$articleinfo['content'] = str_replace("<br />","</p><p>",$articleinfo['content']);
					$articleinfo['content'] = str_replace("<p>","<p style='text-indent:2em;'>",$articleinfo['content']);
					$articleinfo['content'] = "<p style='text-indent:2em;'>".$articleinfo['content'];
					//$articleinfo['content'] = strip_tags($articleinfo['content']);
				//}
				$model_shop = D("Shop");
				$shop_info = $model_shop->find($articleinfo['shop_id']);
				
				$brands = R("Shop/getshopbrand",array($articleinfo['shop_id']));
				if($brands) {
					foreach($brands as $key=>$val) {
						$brand_name .= $val['brand_name']." ";
					}
					$brand_name = mb_substr($brand_name,0,-1);
				}
				$this->assign('brands',$brands);
				
				/*文章关键字相关
				$map_key['status'] = 1;
				if($keyword) {
					foreach($keyword as $key=>$val) {
						if($val) {
							$like = array("like","%,{$val},%");
							$result[] = $like;
						}
					}
				}
				$result[] = 'ThinkPHP';
				$result[] = 'or';
				$map_key['keyword'] = $result;
				$map_key['city_name'] = array(array('eq',$city_name),array('eq','全部'),'or');

				$article_key_relation = $model_article->where($map_key)->limit(10)->select();
				$this->assign("article_key_relation",$article_key_relation);
				*/

				//文章厂商相关
				$map_s_r['shop_id'] = $articleinfo['shop_id'];
				$map_s_r['status'] = 1;
				$article_shop_relation = $model_article->where($map_s_r)->limit(10)->order("rand()")->select();
				$this->assign("article_shop_relation",$article_shop_relation);

				//优惠速递
				$notice_model = D("Notice");
				$NoticeMap['status'] = 1;
				$NoticeMap['city_name'] = $city_name;
				$noticeList = $notice_model->where($NoticeMap)->limit(10)->order("rand()")->select();
				$this->assign("noticeList",$noticeList);

				$this->assign("shop_info",$shop_info);
				$this->assign("next_article",$next_article);
				$this->assign("prev_article",$prev_article);
			}
        }
		
		if( $fs_name[fsname] ) {
			$fs_name[fsname] = $fs_name[fsname]."-";
		}
		if( $brand_name ) {
			$brand_name = $brand_name."-";
		}

		//if ($articleinfo['seo_title']) {
		//	$title = $articleinfo['seo_title'];
		//}else{
			$title = "{$articleinfo[title]}-用车心得-携车网";
		//}
		$this->assign('title',$title);
		if($articleinfo['keyword']) {
			$meta_keyword = $articleinfo['keyword'];
			$this->assign('meta_keyword',$meta_keyword);
		}
		$description = $articleinfo[summary];
		$this->assign('description',$description);
        $this->assign('articleinfo',$articleinfo);
        //推荐顾问
        $this->get_expert_tuijian();
        //相关文章
        //$this->get_relation_article($fsid);
        //推荐优惠券
        $this->get_tuijian_coupon();
        $this->display('news_detail');
    }


	/*
		@author:chf
		@function:显示用车心得(新)
		@time:2013-09-04
	*/
	function index(){
		//$url = "http://www.xieche.com.cn/articlelist";
		$this->_empty();
		exit();
	}
    function articlelist(){
		$this->assign('current','articlelist');
		
		$city_name = $_SESSION['area_info']['0'];
        $model_article = D('Article');
        if ($_REQUEST['fsid']){
            $map_a['fsid'] = $_REQUEST['fsid'];
        }
		if($_REQUEST['keyword']) {
			$keyword = htmlspecialchars($_REQUEST['keyword']);
			$map_a['title'] = array('like',"%{$keyword}%");
			$fuhao = "|";
			$str = $keyword;
		}
			$title_seo = $str."汽车维修心得|".$str."汽车保养心得-携车网";
        $map_a['status'] = 1;
		$map_a['city_name'] = array(array('eq',$city_name),array('eq','全部'),'or');
        $count = $model_article->where($map_a)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
		
		 foreach ($_POST as $key => $val) {
            if (!is_array($val) && $val != "" && $key!='__hash__') {
                $p->parameter .= "$key-" . urlencode($val) . "-";
            }
        }

        $page = $p->show();
        $articleinfo = $model_article->where($map_a)->limit($p->firstRow.','.$p->listRows)->order("create_time DESC")->select();
        $this->assign('articleinfo',$articleinfo);
        $this->assign('page',$page);
		$this->assign('keyword',$keyword);		
		$this->assign('meta_keyword',"汽车保养,汽车售后,汽车保养维修,汽车维修,事故车维修");
		$this->assign('description',"携车网,汽车售后保养维修资讯平台,为汽车用户提供汽车保养维修攻略,同时也提供汽车保养维修,事故车维修在线预约服务,预约享受分时折扣,海量团购套餐任您选4006602822");
        //推荐顾问
        $this->get_expert_tuijian();
        //相关文章
        $this->get_relation_article();
        //推荐优惠券
        $this->get_tuijian_coupon();
		$this->assign('title',$title_seo);
        $this->display('newarticlelist');
    }


	/*
		@author:chf
		@function:优惠速递现实页(新)
		@time:2013-09-04
	*/
    function noticelist(){
		$this->assign('current','noticelist');
		$city_name = $this->city_name;
        $model_notice = D('Notice');
		if($_REQUEST['keyword']) {
			$keyword = htmlspecialchars($_REQUEST['keyword']);
			$map_a['noticetitle'] = array('like',"%{$keyword}%");
			$fuhao = "|";
			$str = $keyword;
		}
			$title_seo = $str."汽车维修优惠速递|".$str."汽车保养优惠速递-携车网";
        $map_a['status'] = 1;
		$map_a['city_name'] = array(array('eq',$city_name),array('eq','全部'),'or');
        $count = $model_notice->where($map_a)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
		foreach ($_POST as $key => $val) {
            if (!is_array($val) && $val != "" && $key!='__hash__') {
                $p->parameter .= "$key-" . urlencode($val) . "-";
            }
        }
        $page = $p->show();
        $noticeinfo = $model_notice->where($map_a)->limit($p->firstRow.','.$p->listRows)->order("id DESC")->select();
        $this->assign('noticeinfo',$noticeinfo);
        $this->assign('page',$page);
		$this->assign('keyword',$keyword);
        
		$this->assign('meta_keyword',"汽车保养,汽车售后,汽车保养维修,汽车维修,事故车维修");
		$this->assign('description',"携车网,为用户提供汽车保养维修预约服务,享受分时折扣优惠,最低5折起,还有更多汽车保养维修团购套餐任您选,事故车维修预约,不花钱,还有返利拿4006602822");
        //推荐顾问
        $this->get_expert_tuijian();
        //相关文章
        $this->get_relation_article();
        //推荐优惠券
        $this->get_tuijian_coupon();
		$this->assign('title',$title_seo);
        $this->display('newnoticelist');
    }

	function art1(){
	    $title = '"心悦"感恩季夏日清凉行广丰夏季服务月|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art2(){
	    $title = '东昌雪佛兰 清凉一下年中“零”度特卖|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art3(){
	    $title = '告别传统雨刷 法雷奥推新款喷水雨刷片|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art4(){
	    $title = '关于携车网|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art6(){
	    $title = '携车网积分规则|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art7(){
	    $title = '开门见“宝” “速”来抢购|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art8(){
	    $title = '全面贴心呵护  斯柯达护航儿童安全出行|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art9(){
	    $title = '如何通过携车网进行维修保养预约？|';
		
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art10(){
	    $title = '越野轮胎中的无冕之王：MT轮胎|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art11(){
	    $title = '奥迪夏季服务日活动 7月7日如约启动|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art12(){
	    $title = '东昌雪莱送你与曼联“零距离亲密接触”|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art13(){
	    $title = '嘉年华免费随心驾 每日时尚好礼送不停|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art14(){
	    $title = '上海晋熙服务站 邀您回家畅赢港澳游|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art15(){
	    $title = '上海中升之星奔驰——久光百货赏车会|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art17(){
	    $title = '缤纷好礼尽在夏季服务“悦”|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art18(){
	    $title = '车主如何自己拆卸与检查汽车火花塞|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art19(){
	    $title = '绿地东本换新喜悦精品折上折|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art20(){
	    $title = '选择开旧车，还是来原价置换新车！|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function brandlogo(){
	    $title = '携车网签约4S店品牌|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art21(){
	    $title = '秋意盎然 教师、中秋、国庆三重奏|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
    function art22(){
	    $title = '上海大众逸先店秋季服务专场|';
	    $this->assign('title',$title);
	    $this->display(); 
	}
	function ajax_checkpoint(){
	    echo -1;
	    exit;
	}
}