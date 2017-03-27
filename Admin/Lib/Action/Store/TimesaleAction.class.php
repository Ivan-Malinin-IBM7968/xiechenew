<?php
/*
 * 分时折扣
 */
class TimesaleAction extends CommonAction {
	public function _before_index(){
		$model_shop = D('Shop');
		$list = $model_shop->where("id=$_GET[shop_id] AND status=1")->select();
		$ShopName = $_REQUEST['ShopName'];
		$this->assign('ShopName',$ShopName);
		$this->assign('shop',$list);
		Cookie::set('_currentUrl_', __SELF__);
	}
	
	public function _before_insert(){
		if($this->isPost()){
			$_POST['begin_time']=$_POST['start_hours'].':'.$_POST['start_minute'];
			$_POST['end_time']=$_POST['end_hours'].':'.$_POST['end_minute'];
			$_POST['submit_time']=time();
		}

	}
	public function edit(){
		$model_shop = D('Shop');
		$list = $model_shop->select();
		$this->assign('shop',$list);
		$model_timesale = D('Timesale');
		$id = $_REQUEST['id'];
		$vo = $model_timesale->find($id);
		if ($vo['begin_time']){
		    $begin_time = explode(":",$vo['begin_time']);
		    $vo['begin_hour'] = $begin_time[0];
		    $vo['begin_minute'] = $begin_time[1];
		}
	    if ($vo['end_time']){
		    $end_time = explode(":",$vo['end_time']);
		    $vo['end_hour'] = $end_time[0];
		    $vo['end_minute'] = $end_time[1];
		}
		Cookie::set('_currentUrl_', __SELF__);
		$this->assign('vo', $vo);
        $this->display();
	}
	public function _before_update(){
		if($this->isPost()){
			$_POST['begin_time']=$_POST['start_hours'].':'.$_POST['start_minute'];
			$_POST['end_time']=$_POST['end_hours'].':'.$_POST['end_minute'];
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
        $model_timesale = D('timesale');
        $map['id'] = array('in',$id);
        if ($model_timesale->where($map)->save(array('status'=>0))){
            $this->success('删除成功！');
        }else {
            $this->error('删除失败');
        }
    }
}
