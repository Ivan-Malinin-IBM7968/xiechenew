<?php

/*
 */

class NoticeAction extends CommonAction {
    function _filter(&$map) {
        $map['status'] = 1;
    }
    
    function delete_notice(){
        $model = D(GROUP_NAME . "/" . 'Notice');
        if ($_REQUEST['id']) {
            $map_n['id'] = $_REQUEST['id'];
            $data['status'] = 0;
            if ($model->where($map_n)->save($data)){
                $this->success("删除成功！");
            }else {
                $this->success("删除失败！");
            }
        }
    }
	
    function _before_add() {
		
        $citys = C('CITYS');
	    $this->assign('citys',$citys);
	    //品牌fs
	    $model_fs = D('Fs');
	    $fs = $model_fs->select();
	    $this->assign('fs',$fs);
		$this->assign('time',time());
        $this->prepare_data();
    }
	
	function prepare_data() {
		$province = R('Store/Region/getRegion');
		//dump($province);
		//exit;
        $this->assign('province',$province);
		$model = D(GROUP_NAME . "/" . 'Carbrand');
		$brand_arr = $model->select();
		$this->assign('brand_arr',$brand_arr);
		
		$model_fs = D(GROUP_NAME. "/" . 'fs');
		$fs_arr = $model_fs->select();
		$this->assign('fs_arr',$fs_arr);
    }

    function edit() {
        $model = D(GROUP_NAME . "/" . 'Notice');
		$list =    $model->find($_GET['id']);

		$shop_model = D(GROUP_NAME."/Shop");
		$shop_info = $shop_model->find($list['shop_id']);
		
		//店铺品牌
		$brands = $this->getshopbrand($shop_info['id']);
		if($brands) {
			foreach($brands as $key=>$val) {
				$brand_name .= $val['brand_name']." ";
			}
			$brand_name = trim($brand_name);
		}

		//工时折扣
		$model_timesale = D('Timesale');
		$model_timesaleversion = D('Timesaleversion');
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
		

		$title = $list['noticetitle']."-".$shop_info['shop_name']."_4S店预约保养_携车网";
		$keyword = $list['noticetitle'].",".$shop_info['shop_name'].",".$brand_name."保养多少钱,"."上海".$brand_name."4S店保养优惠,工时费".$timesale_arr['workhours_sale_str'];

		$summary = "携车网为您提供".$shop_info['shop_name']."预约保养维修服务,".$brand_name."保养多少钱,我们这里有最优质的服务,欲预约保养维修请来电,预约电话400-660-2822,惊喜等着你！";
		$this->assign('title',$title);
		$this->assign('keyword',$keyword);
		$this->assign('summary',$summary);

		$this->assign('list',$list);
		$this->display();
    }
    public function update() {
        $model = D(GROUP_NAME . "/" . 'Notice');
        $notice_arr = array();
		if ($_POST['id']){
		    $map_s['id'] = $_POST['id'];
		    $notice_arr['noticetitle'] = $_POST['noticetitle'];
			$notice_arr['seo_title'] = $_POST['seo_title'];
			$notice_arr['keyword'] = $_POST['keyword'];
			$notice_arr['summary'] = $_POST['summary'];
		    $notice_arr['noticedescription'] = $_POST['noticedescription'];
		    $notice_arr['update_time'] = time();
    		if($model->where($map_s)->save($notice_arr)){
    			$this->success(L('更新成功'));
    		}else{
    			 $this->error(L('更新失败'));
    		}
		}
    }

    function insert() {
        $model_notice = D(GROUP_NAME . "/" . 'Notice');

		$model_shop = D(GROUP_NAME . "/" . 'Shop');
		$shop_info = $model_shop->find($_POST['shop_id']);
		
		$model_region = D(GROUP_NAME . "/" . 'Region');
		$region_info = $model_region->find($shop_info['shop_city']);

        $notice_arr['shop_id'] = $_POST['shop_id'];
		$notice_arr['city_name'] = $region_info['region_name'];
	    $notice_arr['noticetitle'] = $_POST['noticetitle'];
//		$notice_arr['seo_title'] = $_POST['seo_title'];
//		$notice_arr['keyword'] = $_POST['keyword'];
//		$notice_arr['summary'] = $_POST['summary'];
	    $notice_arr['noticedescription'] = $_POST['noticedescription'];
	    $notice_arr['update_time'] = time();
		
		
		if ($lastInsId = $model_notice->add($notice_arr)) {
			
            $this->success(L('更新成功'));
        } else {
          $this->error(L('更新失败'));
        }
    }

	public function getshopbrand($shop_id){
        $model_shop_fs_relation = D(GROUP_NAME . "/" .'Shop_fs_relation');
		
        $map_shopfs['shopid'] = $shop_id;
        $shop_fs_relation = $model_shop_fs_relation->where($map_shopfs)->select();
		//echo $model_shop_fs_relation->getlastSql();
        $brand_id_arr = array();
        if ($shop_fs_relation){
            $model_carseries = D(GROUP_NAME . "/" .'Carseries');
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
        $model_brand = D(GROUP_NAME . "/" .'Carbrand');
        $map_b['brand_id'] = array('in',implode(',',$brand_id_arr));
        $brand = $model_brand->where($map_b)->select();
        return $brand;
    }


}