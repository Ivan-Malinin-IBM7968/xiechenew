<?php
/*
 * 分时折扣
 */
class TimesaleversionAction extends CommonAction {
	
	public function _before_insert(){
		if($this->isPost()){
			$_POST['s_time'] = strtotime($_POST['s_time']. '00:00:00');
			$_POST['e_time'] = strtotime($_POST['e_time']. '00:00:00');
			if ($_POST['s_time']<time()){
			    $this->error("开始日期最晚从明天开始！");
			}
			if ($_POST['s_time']>$_POST['e_time']){
			    $this->error("开始日期不能大于结束日期！");
			}
			$_POST['submit_time']=time();
			$_POST['update_time']=time();
		}
	}
	public function _before_index(){
	    $model_timesale = D(GROUP_NAME."/Timesale");
	    $map['id'] = $_GET['timesale_id'];
	    $timesale = $model_timesale->where($map)->find();
	    $this->assign("timesale",$timesale);
	    Cookie::set('_currentUrl_', __SELF__);
	}
    public function _before_add(){
	    $model_coupon = D(GROUP_NAME."/Coupon");
	    $map['is_delete'] = 0;
	    $coupon = $model_coupon->where($map)->select();
	    $this->assign("coupon",$coupon);
	}
	public function _get_order_sort(){
	    $arr['order'] = 's_time';
	    $arr['sort'] = 'ASC';
	    return $arr;
	}
	public function _trans_data($list){
	    if (!empty($list)){
	        $model_coupon = D('Coupon');
	        foreach ($list as $k=>$v){
	            if ($v['coupon_id']){
	                $coupon = $model_coupon->find($v['coupon_id']);
	                $list[$k]['coupon_name'] = $coupon['coupon_name'];
	            }
	        }
	    }
	    return $list;
	}
    public function insert(){
	    $timesale_id = $_POST['timesale_id'];
	    $model_timesaleversion = D('Timesaleversion');
	    $condition['timesale_id'] = $timesale_id;
	    $condition['status'] = 1;
	    $oneday_time = 24*3600-1;
        $_POST['e_time'] = $_POST['e_time']+$oneday_time;
        if($model_timesaleversion->add($_POST)){
            if ($_POST['coupon_id'] and $_POST['coupon_id']!=$_POST['old_coupon_id']){
                $model_timesale = D('Timesale');
                $map['id'] = $timesale_id;
                $timesale = $model_timesale->where($map)->find();
                $shop_id = $timesale['shop_id'];
                $model_coupon = D('Coupon');
                $coupon_map['id'] = $_POST['coupon_id'];
                $coupon = $model_coupon->find($_POST['coupon_id']);
                if ($coupon['shop_ids']){
                    $shop_ids_str = $coupon['shop_ids'].','.$shop_id;
                }else{
                    $shop_ids_str = $shop_id;
                }
                $shop_ids_arr = explode(',',$shop_ids_str);
                $data['shop_ids'] = implode(',',array_unique($shop_ids_arr));
                $model_coupon->where($coupon_map)->save($data);
            }
            $this->success("提交成功！","index/timesale_id/".$timesale_id);
        }else{
            $this->success("提交失败！","index/timesale_id/".$timesale_id);
        }
	}
	/*public function insert(){
	    $timesale_id = $_POST['timesale_id'];
	    $model_timesaleversion = D('Timesaleversion');
	    $condition['timesale_id'] = $timesale_id;
	    $condition['status'] = 1;
	    $oneday_time = 24*3600-1;
	    if($timesaleversion = $model_timesaleversion->where($condition)->select()){
	        if (!empty($timesaleversion)){
	            $update_arr = array();
	            $add_arr = array();
	            foreach ($timesaleversion as $k=>$v){
	                if ($v['s_time']<$_POST['s_time'] and $v['e_time']>=$_POST['s_time']+$oneday_time){
	                    $updata['id'] = $v['id'];
                        $updata['s_time'] = $v['s_time'];
                        $updata['e_time'] = $_POST['s_time']-1;
                        $updata['update_time'] = $_POST['update_time'];
                        $update_arr[] = $updata;
                        unset($updata);
	                    if ($_POST['e_time']+$oneday_time<$v['e_time']){
	                        $adddata_1['timesale_id'] = $timesale_id;
	                        $adddata_1['product_sale'] = $_POST['product_sale'];;
	                        $adddata_1['workhours_sale'] = $_POST['workhours_sale'];;
	                        $adddata_1['s_time'] = $_POST['s_time'];
	                        $adddata_1['e_time'] = $_POST['e_time']+$oneday_time;
	                        $adddata_1['submit_time'] = $_POST['submit_time'];
	                        $adddata_1['update_time'] = $_POST['update_time'];
	                        $adddata_1['memo'] = $_POST['memo'];
	                        $add_arr[] = $adddata_1;
	                        $adddata_2['timesale_id'] = $timesale_id;
	                        $adddata_2['product_sale'] = $v['product_sale'];;
	                        $adddata_2['workhours_sale'] = $v['workhours_sale'];;
	                        $adddata_2['s_time'] = $_POST['e_time']+$oneday_time+1;
	                        $adddata_2['e_time'] = $v['e_time'];
	                        $adddata_2['submit_time'] = $_POST['submit_time'];
	                        $adddata_2['update_time'] = $_POST['update_time'];
	                        $add_arr[] = $adddata_2;
	                    }else {
	                        $adddata['timesale_id'] = $timesale_id;
	                        $adddata['product_sale'] = $_POST['product_sale'];;
	                        $adddata['workhours_sale'] = $_POST['workhours_sale'];;
	                        $adddata['s_time'] = $_POST['s_time'];
	                        $adddata['e_time'] = $_POST['e_time']+$oneday_time;
	                        $adddata['memo'] = $_POST['memo'];
	                        $adddata['submit_time'] = $_POST['submit_time'];
	                        $adddata['update_time'] = $_POST['update_time'];
	                        $add_arr[] = $adddata;
	                    }
	                }else if ($v['s_time']<$_POST['e_time'] and $v['e_time']>$_POST['e_time']+$oneday_time){
	                    $adddata['timesale_id'] = $timesale_id;
                        $adddata['product_sale'] = $_POST['product_sale'];;
                        $adddata['workhours_sale'] = $_POST['workhours_sale'];;
                        $adddata['s_time'] = $_POST['s_time'];
                        $adddata['e_time'] = $_POST['e_time']+$oneday_time;
                        $adddata['memo'] = $_POST['memo'];
                        $adddata['submit_time'] = $_POST['submit_time'];
                        $adddata['update_time'] = $_POST['update_time'];
                        $add_arr[] = $adddata;
                        $updata['id'] = $v['id'];
                        $updata['s_time'] = $_POST['e_time']+$oneday_time+1;
                        $updata['e_time'] = $v['e_time'];
                        $updata['update_time'] = $_POST['update_time'];
                        $update_arr[] = $updata;
                        unset($updata);
	                }else if ($v['s_time']>$_POST['s_time'] and $v['e_time']<$_POST['e_time']+$oneday_time){
	                    $adddata['timesale_id'] = $timesale_id;
                        $adddata['product_sale'] = $_POST['product_sale'];;
                        $adddata['workhours_sale'] = $_POST['workhours_sale'];;
                        $adddata['s_time'] = $_POST['s_time'];
                        $adddata['e_time'] = $_POST['e_time']+$oneday_time;
                        $adddata['memo'] = $_POST['memo'];
                        $adddata['submit_time'] = $_POST['submit_time'];
                        $adddata['update_time'] = $_POST['update_time'];
                        $add_arr[] = $adddata;
                        $updata['id'] = $v['id'];
                        $updata['status'] = 0;
                        $updata['update_time'] = $_POST['update_time'];
                        $update_arr[] = $updata;
                        unset($updata);
	                }else {
	                    $adddata['timesale_id'] = $timesale_id;
                        $adddata['product_sale'] = $_POST['product_sale'];;
                        $adddata['workhours_sale'] = $_POST['workhours_sale'];;
                        $adddata['s_time'] = $_POST['s_time'];
                        $adddata['e_time'] = $_POST['e_time']+$oneday_time;
                        $adddata['memo'] = $_POST['memo'];
                        $adddata['submit_time'] = $_POST['submit_time'];
                        $adddata['update_time'] = $_POST['update_time'];
                        $add_arr[] = $adddata;
	                }
	            }
	            //$add_arr = array_unique($add_arr);
	            //$update_arr = array_unique($update_arr);
	            //去掉相同的数据
	            if (!empty($add_arr)){
	                $arr_each = array();
	                foreach ($add_arr as $kk=>$vv){
	                    $arr_each[$kk] = implode('|',$vv);
	                }
	                $arr_each=array_unique($arr_each);
    	            foreach($arr_each as $key=>$val){
                    	$arr[$key]=$add_arr[$key];
                    }
	                foreach ($arr as $_k=>$_v){
	                    $model_timesaleversion->add($_v);
	                }
	            }
	            if (!empty($update_arr)){
	                foreach ($update_arr as $_kk=>$_vv){
	                    $condition['id'] = $_vv['id'];
	                    $model_timesaleversion->where($condition)->save($_vv);
	                }
	            }
	            $this->success("提交成功！","index/timesale_id/".$timesale_id);
	        }
	    }else {
	        $_POST['e_time'] = $_POST['e_time']+$oneday_time;
	        $model_timesaleversion->add($_POST);
	        $this->success("提交成功！","index/timesale_id/".$timesale_id);
	    }
	}*/
	
	public function edit(){
	    $model_coupon = D(GROUP_NAME."/Coupon");
	    $map_coupon['is_delete'] = 0;
	    $coupon = $model_coupon->where($map_coupon)->select();
	    $this->assign("coupon",$coupon);
	    $nowtime = time();
	    $this->assign('nowtime',$nowtime);
		$model_timesaleversion = D(GROUP_NAME."/Timesaleversion");
		if ($_GET['id']){
		    $map['id'] = $_GET['id'];
		    $map['e_time'] = array('gt',time());
		    if($timesaleversion = $model_timesaleversion->where($map)->find()){
		        $this->assign('vo', $timesaleversion);
                $this->display();
		    }else {
		        $this->error("结束时间小于当前时间不能修改折扣");
		    }
		}
	}
	public function _before_update(){
		if($this->isPost()){
		    $_POST['s_time'] = strtotime($_POST['s_time']. '00:00:00');
			$_POST['e_time'] = strtotime($_POST['e_time']. '23:59:59');
			if ($_POST['e_time']<time()){
			    $this->error("结束日期最晚从明天开始！");
			}
			if ($_POST['s_time']>$_POST['e_time']){
			    $this->error("开始日期不能大于结束日期！");
			}
			$_POST['update_time']=time();
		}
	}
    public function _product_img_update(){
        $model_product_img = D('Product_img');
        $condition['timesale_id'] = $_POST['id'];
        $data['update_time'] = time();
        $data['status'] = 1;
        $model_product_img->where($condition)->save($data);
    }
    public function foreverdelete(){
        $id = $_POST['id'];
        $model_timesale = D('Timesaleversion');
        $map['id'] = array('in',$id);
        if ($model_timesale->where($map)->save(array('status'=>0))){
            $this->success('删除成功！');
        }else {
            $this->error('删除失败');
        }
    }
    public function del(){
        $id = $_POST['id'];
        $model_timesaleversion = D('Timesaleversion');
        $map['id'] = $id;
        if ($model_timesaleversion->where($map)->save(array('status'=>0))){
            echo 1;
        }else {
            echo 0;
        }
        exit;
    }
}
