<?php
/*CLASS:*/
class MembercouponAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->ShopModel = D('Shop');//商铺表
		$this->MemberModel = D('member');//用户表
		$this->MembercouponModel = D('membercoupon');//优惠卷信息
		$this->CouponModel = D('coupon');//优惠券信息表
		$this->coupon_logModel = D('coupon_log');//优惠券信息表
		$this->SmsModel = D('sms');//消息记录
	}


	/*
		@author:chf
		@function:客服代下优惠券选择页
		@time:2014-06-20
	*/
	function member_order(){
		$uid = $_REQUEST['uid'];
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $_REQUEST['coupon_type'];
		}
		if($_REQUEST['coupon_name']){
			$data['coupon_name'] = array('like',"%".$_REQUEST['coupon_name']."%");
		}
		$data['is_delete'] = 0;
		$count = $this->CouponModel->where($data)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		foreach ($_POST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
		$page = $p->show_admin();
		$coupon = $this->CouponModel->where($data)->limit($p->firstRow.','.$p->listRows)->select();
		
		$this->assign('coupon',$coupon);
		$this->assign('page',$page);
		$this->assign('uid',$uid);
		$this->assign('coupon_type',$_REQUEST['coupon_type']);
		$this->assign('coupon_name',$_REQUEST['coupon_name']);
		$this->display();
	}

	/*
		@author:chf
		@function:客服代下优惠券操作
		@time:2014-06-20
	*/
	function daimemberadd(){
		$data['uid'] = $_POST['add_uid'];
		$data['coupon_id'] = $_POST['coupon_id'];
		$data['mobile'] = $_POST['mobile'];
		$member = $this->MemberModel->where(array('uid'=>$data['uid']))->find();
		$coupon = $this->CouponModel->where(array('id'=>$data['coupon_id']))->find();
		
		foreach($_POST['num'] as $v){
			if($v){
				$num = $v;
			}
			
		}
		
		if($num != '0'){
			for($a=0;$a<$num;$a++){
				$data['coupon_name'] = $coupon['coupon_name'];
				$data['coupon_type'] = $coupon['coupon_type'];
				
				$data['mobile'] = $member['mobile'];
				$data['shop_ids'] = $coupon['shop_id'];
				$data['start_time'] = $coupon['start_time'];
				$data['end_time'] = $coupon['end_time'];
				$data['coupon_amount'] = $coupon['coupon_amount'];
				$data['create_time'] = time();
				$data['pa'] = '6';//客服代下单优惠券
				$this->MembercouponModel->add($data);
				//echo $this->MembercouponModel->getlastSql();
			}
				
			
		}else{
			echo "<script>alert('请填写优惠券数量~')</script>";
			
			exit;
		}
		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
		$this->success("代购成功!");
	}
	
	/*
		@author:chf
		@function:优惠卷用户信息管理
		@time:2013-04-12
	*/

	function index(){
		//print_r($_SESSION);
		$data['ShopList'] = $this->ShopModel->select();//得到所有商铺信息
		$data['membercoupon_id'] = $_REQUEST['membercoupon_id']; //优惠卷ID
		$data['mobile'] = $_REQUEST['mobile'];//手机号查询
		$data['is_pay'] = $_REQUEST['is_pay'];//订单状态 0未支付 1已支付 6全部
		$data['start_time'] = $_REQUEST['start_time'];//下订开始时间
		$data['end_time'] = $_REQUEST['end_time'];//下订结束时间
		$data['shop_id'] = $_REQUEST['shop_id'];//shop_id
		$data['is_use'] = $_REQUEST['is_use'];//使用状态
		$data['pay_start_time'] = $_REQUEST['pay_start_time'];//支付开始时间
		$data['pay_end_time'] = $_REQUEST['pay_end_time'];//支付结束时间
		$data['use_start_time'] = $_REQUEST['use_start_time'];//使用开始时间
		$data['use_end_time'] = $_REQUEST['use_end_time'];//使用结束时间
		$data['coupon_type'] = $_REQUEST['coupon_type'];//优惠券种类
		
		//排序字段 默认为主键名
        if (isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        } else {
            $order = "pay_time";
        }

        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST['_sort'])) {
            $sort = $_REQUEST['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = "desc";
        }

		if($_REQUEST['shopname']){
			$data['shopname'] = $_REQUEST['shopname'];//店铺名
		}
		if($data['membercoupon_id']){
			$map['membercoupon_id'] = $data['membercoupon_id'];
		}
		if($data['coupon_type']){
			$map['coupon_type'] = $data['coupon_type'];
		}
		if($data['mobile']){
			$map['mobile'] = $data['mobile'];
		}
		if($data['is_use']){
			$map['is_use'] = $data['is_use'];
		}elseif(isset($data['is_use']) and $data['is_use']==0){
			$map['is_use'] = '0';
		}
		if($data['is_pay'] && $data['is_pay'] != '6'){
			$map['is_pay'] = $data['is_pay'];
		}
		else if( $data['is_pay'] == '6'){
			$is_pay = '6';
			$this->assign('is_pay',$is_pay);
		}
		else if( $data['is_pay'] == '0'){
			$map['is_pay'] = $data['is_pay'] = '0';

		}else{
			$map['is_pay'] = $data['is_pay'] = '1';
		}
		
		if($data['shop_id']){
			$map['shop_id'] = $data['shop_id'];
		}
		if($data['start_time']){
			$map['create_time'] = array('gt',strtotime($data['start_time']));
		}
		if($data['end_time']){
			$map['create_time'] = array('lt',strtotime($data['end_time'].'23:59:59'));
		}
		if($data['start_time'] && $data['end_time']){
			$map['create_time'] = array(array('lt',strtotime($data['end_time'].'23:59:59')),array('gt',strtotime($data['start_time'])),'AND');
		}

		if($data['pay_start_time']){
			$map['pay_time'] = array('gt',strtotime($data['pay_start_time']));
		}
		if($data['pay_end_time']){
			$map['pay_time'] = array('lt',strtotime($data['pay_end_time'].'23:59:59'));
		}
		if($data['pay_start_time'] && $data['pay_end_time']){
			$map['pay_time'] = array(array('lt',strtotime($data['pay_end_time'].'23:59:59')),array('gt',strtotime($data['pay_start_time'])),'AND');
		}

		if($data['use_start_time']){
			$map['use_time'] = array('gt',strtotime($data['use_start_time']));
		}
		if($data['use_end_time']){
			$map['use_time'] = array('lt',strtotime($data['use_end_time'].'23:59:59'));
		}
	
		if($data['use_start_time'] && $data['use_end_time'] ){
			$map['use_time'] = array(array('lt',strtotime($data['use_end_time'].'23:59:59')),array('gt',strtotime($data['use_start_time'])),'AND');
		}
		$map['is_delete'] = 0;

		$count = $this->MembercouponModel->where($map)->count();
		
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		foreach ($_POST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
		$page = $p->show_admin();
		
		$list = $this->MembercouponModel->order($order . ' ' . $sort." , membercoupon_id DESC")->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		//echo $this->MembercouponModel->getLastsql();
		
		if($list){
			$cost_price =0;
			$coupon_amount =0;
			foreach($list as $k=>$v){
				$member = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
				if($v['shop_id']){
					$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				}else{
					$shop = $this->ShopModel->where(array('id'=>$v['shop_ids']))->find();
				}
				
				$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];

				if($v['is_refund'] != '1'){
					$cost_price += $coupon['cost_price'];
					$coupon_amount += $coupon['coupon_amount'];
				}
				$coupon_log = $this->coupon_logModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->find();
				$list[$k]['name'] = $coupon_log['name'];
				
				if($coupon['coupon_amount']<1){
					unset($list[$k]);
				}
			}
		}

		foreach($map as $k=>$v){
			$mad['xc_membercoupon.'.$k] = $v;
		}
		//$map['xc_membercoupon.coupon_type'] = 1;
		$all_couponamount = $this->MembercouponModel->join('xc_coupon on xc_coupon.id=xc_membercoupon.coupon_id')->where($mad)->sum('xc_coupon.coupon_amount');
		//echo $this->MembercouponModel->getLastsql();
		

		//列表排序显示
        $sortImg = $sort; //排序图标
        $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
        $sort = $sort == 'desc' ? 1 : 0; //排序方式
		$this->assign('sort', $sort);
        $this->assign('order', $order);
        $this->assign('sortImg', $sortImg);
        $this->assign('sortType', $sortAlt);
		
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->assign('cost_price',$cost_price);
		$this->assign('coupon_amount',$coupon_amount);
		$this->assign('all_couponamount',$all_couponamount);
		$this->assign('authid',$_SESSION['authId']);//判断权限
		$this->display();
	}
		
	/*
		@author:chf
		@function:后台确认支付
		@time:2013-07-01
	*/
	function pay_add(){
		$membercoupon_id = $_POST['membercoupon_id'];
		$membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$membercoupon_id))->find();
		$shop = $this->ShopModel->where(array('id'=>$membercoupon['shop_ids']))->find();
		if($membercoupon['coupon_type']==1){
			$coupon_type_str = "现金券编号:";
		}
		if($membercoupon['coupon_type']==2){
			$coupon_type_str = "套餐券编号:";
		}
		$coupon_code = $this->get_coupon_code();
		$start_time = date('Y-m-d',$membercoupon['start_time']);
		$end_time = date('Y-m-d',$membercoupon['end_time']);

		$cms_map['phones'] = $membercoupon['mobile'];
		$cms_map['content'] = "您的".$coupon_type_str.$membercoupon_id."(".$membercoupon['coupon_name'].")支付已成功，请凭消费码:".$coupon_code."至商户(".$shop['shop_name'].",".$shop['shop_address'].")处在有效期(".$start_time."至".$end_time.")消费";
	
		$send_time = time();
		$this->curl_sms($cms_map);

		$sms_map['phones'] = $membercoupon['mobile'];
		$sms_map['sendtime'] = time();
		$sms_map['content'] = $cms_map['content'];
		$this->SmsModel->add($sms_map);
		$this->MembercouponModel->where(array('membercoupon_id'=>$membercoupon_id))->save(array('coupon_code'=>$coupon_code,'is_pay'=>'1','pay_time'=>time(),'pay_type'=>4));
		echo '1';
	}
	

	/*
		得到验证码随机数
	*/
	function get_coupon_code(){
        $orderid = $this->randString(9,1);
        $sum = 0;
	    for($ii=0;$ii<strlen($orderid);$ii++){
	        $orderid = (string)$orderid;
            $sum += intval($orderid[$ii]);
        }
        $str = $sum%10;
		$result = $orderid.$str;
        return $result;
    }


	/*
		@author:chf
		@function:删除订单
		@time:2013-04-15
	*/
	  public function delete_order(){
        $id = isset($_POST['id'])?$_POST['id']:0;
        $model_order = D(GROUP_NAME.'/Order');
        $model_orderdelete = D(GROUP_NAME.'/Orderdelete');
        if ($id){
			$this->MembercouponModel->where(array('membercoupon_id'=>$id))->save(array('is_delete'=>1));
			echo 1;exit;
        }
    }

	/*
		@author:chf
		@function:得到店铺名
		@time:2013-5-6
	*/
	function GetShopname(){
		if($_REQUEST['shopname']){
			$map['shop_name'] = array('LIKE',"%".$_REQUEST['shopname']."%");
		}
		$Shop = $this->ShopModel->where($map)->select();
		if($Shop){
			echo json_encode($Shop);
		}else{
			echo 'none';
			
		}
	}
	
	/*
		@author:chf
		@function:确定是否有新的优惠卷信息数量
		@time:2013/9/12
	*/
	 public function get_new_order_coupon(){
        $map['is_use'] = 0;
		$map['is_pay'] = 0;
		$map['create_time'] = array('gt',strtotime(date('Y-m-d',time())));
        $newcoupon = $this->MembercouponModel->where($map)->count();

		$model_order = D(GROUP_NAME.'/Order');
        $map_o['postpone_time'] = array('eq','');
		$map_o['order_state'] = 0;
        $neworders = $model_order->where($map_o)->count();

		$model_insurance = D(GROUP_NAME.'/Insurance');
		$map_i['create_time'] = array('gt',time()-1200);
		$map_i['insurance_status'] = 0;
		$newinsurance = $model_insurance->where($map_i)->count();

		$model_reservation = D(GROUP_NAME.'/Reservation_order');
		$map_r['create_time'] = array('gt',time()-1200);
		$map_r['status'] = 0;
		$newcarserviceorder = $model_reservation->where($map_r)->count();

		$json = array($newcoupon,$neworders,$newinsurance,$newcarserviceorder);
        echo json_encode($json);
        exit;
    }

	/*
		@author:ysh
		@function:新增退款状态
		@time:2013/7/25
	*/
	function Ajax_update_refund() {
		$map['membercoupon_id'] = $_REQUEST['membercoupon_id'];
		$map['is_pay'] = 1;
		$map['is_use'] = 0;
		$refund = $this->MembercouponModel->where($map)->save(array('is_refund'=>1,'refund_time'=>time()));
		if($refund) {
			echo 1;
		}else {
			echo "false";
		}
		exit();
	}


	
		/**
	 +----------------------------------------------------------
	 * 产生随机字串，可用来自动生成密码
	 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
	 * @param string $len 长度
	 * @param string $type 字串类型
	 * 0 字母 1 数字 其它 混合
	 * @param string $addChars 额外字符
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function randString($len=6,$type='',$addChars='') {
		$str ='';
		switch($type) {
			case 0:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 1:
				$chars= str_repeat('0123456789',3);
				break;
			case 2:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
				break;
			case 3:
				$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 4:
				$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
				break;
			default :
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
				break;
		}
		if($len>10 ) {//位数过长重复字符串一定次数
			$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
		}
		if($type!=4) {
			$chars   =   str_shuffle($chars);
			$str     =   substr($chars,0,$len);
		}else{
			// 中文随机字
			for($i=0;$i<$len;$i++){
			  $str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
			}
		}
		return $str;
	}

}

?>