<?php
//文章
class YouhuiAction extends CommonAction {
	
	/*
		@author:chf
		@function:优惠速递现实页(新)
		@time:2013-09-04
	*/
    function index(){
		$this->assign('current','noticelist');
		$city_name = $this->city_name;
        $model_notice = D('Notice');
		if($_REQUEST['keyword']) {
			$map_a['noticetitle'] = array('like',"%{$_REQUEST[keyword]}%");
			$fuhao = "|";
			$str = $_REQUEST['keyword'];
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
		$this->assign('keyword',$_REQUEST['keyword']);
        
		$this->assign('meta_keyword',"汽车保养,汽车售后,汽车保养维修,汽车维修,事故车维修");
		$this->assign('description',"携车网,为用户提供汽车保养维修预约服务,享受分时折扣优惠,最低5折起,还有更多汽车保养维修团购套餐任您选,事故车维修预约,不花钱,还有返利拿4006602822");
        //推荐顾问
        $this->get_expert_tuijian();
        //相关文章
        $this->get_relation_article();
        //推荐优惠券
        $this->get_tuijian_coupon();
		$this->assign('title',$title_seo);
        $this->display();
    }

}