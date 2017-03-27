<?php
//用车心得
class XindeAction extends CommonAction {
	
	/*
		@author:chf
		@function:显示用车心得(首页)
		@time:2014-04-02
	*/
    function index(){
		$this->assign('current','articlelist');
		$city_name = $_SESSION['area_info']['0'];
        $model_article = D('Article');
        if ($_REQUEST['fsid']){
            $map_a['fsid'] = $_REQUEST['fsid'];
        }
		if($_REQUEST['keyword']) {
			$map_a['title'] = array('like',"%{$_REQUEST[keyword]}%");
			$fuhao = "|";
			$str = $_REQUEST['keyword'];
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
		$this->assign('keyword',$_REQUEST['keyword']);		
		$this->assign('meta_keyword',"汽车保养,汽车售后,汽车保养维修,汽车维修,事故车维修");
		$this->assign('description',"携车网,汽车售后保养维修资讯平台,为汽车用户提供汽车保养维修攻略,同时也提供汽车保养维修,事故车维修在线预约服务,预约享受分时折扣,海量团购套餐任您选4006602822");
        //推荐顾问
        $this->get_expert_tuijian();
        //相关文章
        $this->get_relation_article();
        //推荐优惠券
        $this->get_tuijian_coupon();
		$this->assign('title',$title_seo);
        $this->display();
    }


		/*
		@author:chf
		@function:显示文章详细页 (新 )
		@time:2013-9-5
	*/
	function detail(){
		$this->assign('current','articlelist');
		$city_name = $this->city_name;
        $aid = $_REQUEST['id'];
        if ($aid){
            $model_article = D('Article');
            $map_a['id'] = $aid;
            $articleinfo = $model_article->where($map_a)->find($aid);
			if(!$articleinfo){
				header("location:http://www.xieche.com.cn/Article-articlelist");	
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
        }
		
		if( $fs_name[fsname] ) {
			$fs_name[fsname] = $fs_name[fsname]."-";
		}
		if( $brand_name ) {
			$brand_name = $brand_name."-";
		}

		if ($articleinfo['seo_title']) {
			$title = $articleinfo['seo_title'];
		}else{
			$title = "{$articleinfo[title]} 携车网,汽车保养维修预约,4S店折扣优惠,事故车维修预约";
		}
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
        $this->get_relation_article($fsid);
        //推荐优惠券
        $this->get_tuijian_coupon();
        $this->display();
    }

}