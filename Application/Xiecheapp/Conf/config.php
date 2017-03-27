<?php
$config = array (
		'tuijian_coupon' => array (
				21,
				28,
				39,
				47,
				53,
				52,
				55,
				64 
		),
		'tuijian_money_coupon' => array (
			339,
			396,
			46,
			397,
			231,
			396,
			229,
			395
		),

		'UPLOAD_ROOT' => 'http://www.xieche.com.cn/UPLOADS',
		'UPLOAD_ROOT2' => 'UPLOADS',
		'WEB_ROOT' => 'http://www.xieche.com.cn',
		'WEEK_NUM' => array (
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '0' 
		),
		// 记账标题
		'NOTE_TYPE_TITLE' => array (
				'1' => 'Noteoil',
				'2' => 'Notebeautify',
				'3' => 'Notemaintain',
				'4' => 'Noteinsurance',
				'5' => 'Noteparking',
				'6' => 'Notepass',
				'7' => 'Noteforfeit',
				'8' => 'Notepurchase',
				'9' => 'Noterule',
				// '10'=>'Noteearning',
				'11' => 'Noteother' 
		),
		'NOTE_TYPE_NAME' => array (
				'1' => '加油',
				'2' => '美容',
				'3' => '维修',
				'4' => '保险',
				'5' => '停车',
				'6' => '通行',
				'7' => '罚款',
				'8' => '购置',
				'9' => '规费',
				// '10'=>'收入',
				'11' => '其他' 
		),
		// 油品
		'OIL_TYPE' => array (
				'1' => '90#',
				'2' => '93#',
				'3' => '97#',
				'4' => '98#',
				'5' => '0#',
				'6' => 'CNG',
				'7' => 'LPG' 
		),
		// 付款方式
		'PAY_TYPE' => array (
				'1' => '现金',
				'2' => '刷卡',
				'3' => ' 现金卡' 
		),
		
		'INSURANCE_TYPE' => array (
				'1' => '交强险',
				'2' => '车辆损失险',
				'3' => '第三者责任险',
				'4' => '盗抢险',
				'5' => '车上座位责任险',
				'6' => '玻璃单独破碎险',
				'7' => '自燃险',
				'8' => '划痕险',
				'9' => '不计免赔险',
				'10' => '其他保险' 
		),
		'PURCHASE_TYPE' => array (
				'2' => '车辆价格',
				'3' => '购置税',
				'1' => '其他购车费用' 
		),
		'RULE_TYPE' => array (
				'1' => '道路年费',
				'2' => '养路费',
				'3' => '车船税',
				'4' => '其他规费' 
		),
		'AVGOIL_TYPE' => array (
				'1' => '前次油量/行驶里程 =平均油耗',
				'2' => '本次油量/行驶里程=平均油耗' 
		),
		'MAINTAIN_BIGCLASS' => array (
				'190' => '发动机',
				'191' => '传动系配件',
				'192' => '转向系配件',
				'193' => '制动系配件',
				'194' => '行走系配件',
				'195' => '电器仪表件',
				'196' => '车身及附件',
				'197' => '横向件及其他',
				'200' => '保养用品及设备',
				'201' => '工具',
				'202' => '汽车影音娱乐',
				'203' => '汽车内外饰用品',
				'204' => '汽车改装',
				'205' => '汽车安全用品',
				'515' => '工时费',
				'206' => '其他' 
		),
		'ORDER_STATE' => array (
				'0' => '等待处理',
				'1' => '预约已确认',
				'2' => '预约已完成',
				'-1' => '作废预约',
				'6' => '推后订单' 
		),
		'PAY_STATE' => array (
				'0' => '未支付',
				'1' => '已支付',
				'2' => '申请退款',
				'3' => '退款成功' 
		),
		'COMPLAIN_STATE' => array (
				'0' => '无',
				'1' => '投诉中',
				'2' => '结束投诉' 
		),
		'ORDER_HOURS' => array (
				'1' => '8',
				'2' => '9',
				'3' => '10',
				'4' => '11',
				'5' => '12',
				'6' => '13',
				'7' => '14',
				'8' => '15',
				'9' => '16',
				'10' => '17',
				'11' => '18',
				'12' => '19',
				'13' => '20',
				'14' => '21',
				'15' => '22',
				'16' => '23',
				'17' => '24' 
		),
		'ORDER_MINUTE' => array (
				'1' => '00',
				'2' => '10',
				'3' => '20',
				'4' => '30',
				'5' => '40',
				'6' => '50' 
		),
		'ORDER_MENU_TYPE' => array (
				'1' => '<a href="' . __APP__ . '/myhome/index/order_state/0/type/1">等待处理</a>',
				'2' => '<a href="' . __APP__ . '/myhome/index/order_state/1/type/2">预约确认</a>',
				'3' => '<a href="' . __APP__ . '/myhome/index/order_state/2/type/3">预约完成</a>',
				'4' => '<a href="' . __APP__ . '/myhome/index/order_state/-1/type/4">作废预约</a>',
				'5' => '<a href="' . __APP__ . '/myhome/index/order_state/2/complain_state/1/type/5">投诉</a>',
				'6' => '<a href="' . __APP__ . '/myhome/index/order_state/2/complain_state/2/type/6">结束投诉</a>' 
		),
		'SALE_VALUE' => array (
				'1' => '0.00',
				'2' => '0.05',
				'3' => '0.10',
				'4' => '0.15',
				'5' => '0.20',
				'6' => '0.25',
				'7' => '0.30',
				'8' => '0.35',
				'9' => '0.40',
				'10' => '0.45',
				'11' => '0.50',
				'12' => '0.55',
				'13' => '0.60',
				'14' => '0.65',
				'15' => '0.68',
				'16' => '0.70',
				'17' => '0.75',
				'18' => '0.76',
				'19' => '0.80',
				'20' => '0.85',
				'21' => '0.88',
				'22' => '0.90',
				'23' => '0.98',
				'24' => '0.95' 
		)
		,
		'WEEK' => array (
				'1' => '星期一',
				'2' => '星期二',
				'3' => '星期三',
				'4' => '星期四',
				'5' => '星期五',
				'6' => '星期六',
				'7' => '星期天' 
		),
		'WEEK_NUM' => array (
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '0' 
		),
		// ORDER中只能单选的项目
		'CHECKBOX_ITEM' => array (
				'1' => '9',
				'2' => '10' 
		),
		'SHOP_CLASS' => array (
				'1' => '4S店--签约',
				'2' => '4S店--未签约',
				'3' => '快修店--签约',
				'4' => '快修店--未签约' 
		)
		,
		'MEMBER_FORM' => array (
				'1' => '百度/谷歌',
				'2' => '论坛/微博',
				'3' => '朋友介绍',
				'4' => 'APP',
				'5' => '客服电话',
				'6' => '老用户',
				'7' => '宣传单',
				'8' => '其他',
				'9' => '短信推广',
				'10' => 'vw',
				'11' => 'svw',
				'12' => '别克',
				'13' => '雪弗莱',
				'14' => '斯柯达',
				'15' => '一汽丰田',
				'16' => '东风日产',
				'17' => '起亚',
				'18' => '优惠',
				'19' => 'th',
				'20' => 'sh',
				'21' => '都市港湾小区',
				'22' => 'lmm',
				'23' => '微博',
				'24' => '彩生活',
				'25' => '出租车广告',
				'26' => '传单',
				'27' => 'ch' 
		),
		
		'LOTTERY_FROM' => array (
				'10' => 'vw',
				'11' => 'svw',
				'12' => 'bk',
				'13' => 'xfl',
				'14' => 'skd',
				'15' => 'ft',
				'16' => 'rc',
				'17' => 'kia',
				'18' => 'yh',
				'19' => 'th',
				'20' => 'sh' 
		),
		
		'CANCEL_STATE' => array (
				'1' => '路太远',
				'2' => '没时间',
				'3' => '4S店不配合',
				'4' => '重复订单',
				'5' => '测试',
				'6' => '其他',
				'7' => '换4S店' 
		),
		
		'CAR_SHIFT' => array (
				'1' => 'AT',
				'2' => 'MT' 
		),
		'CALL_400' => '4006602822',
		'SHORT_PROVINCIAL_CAPITAL' => array (
				'沪',
				'京',
				'港',
				'吉',
				'鲁',
				'冀',
				'湘',
				'青',
				'苏',
				'浙',
				'粤',
				'台',
				'甘',
				'川',
				'黑',
				'蒙',
				'新',
				'津',
				'渝',
				'澳',
				'辽',
				'豫',
				'鄂',
				'晋',
				'皖',
				'赣',
				'闽',
				'琼',
				'陕',
				'云',
				'贵',
				'藏',
				'宁',
				'桂' 
		),
		'FRIENDLYLINK' => array (
				array (
						'link' => 'http://vt.vancl.com',
						'title' => 'T恤衫',
						'description' => 'T恤衫' 
				),
				array (
						'link' => 'http://www.hiputian.com/qiche',
						'title' => '莆田汽车',
						'description' => '莆田汽车' 
				),
				array (
						'link' => 'http://nc.xgo.com.cn',
						'title' => '南昌车市',
						'description' => '南昌车市' 
				),
				array (
						'link' => 'http://js.52car.net/',
						'title' => '南京汽车网',
						'description' => '南京汽车网' 
				),
				array (
						'link' => 'http://zuche.zyue.com',
						'title' => '众悦汽车租赁',
						'description' => '众悦汽车租赁' 
				),
				array (
						'link' => 'http://www.yicheshi.com/price/a/',
						'title' => '紧凑型车销量',
						'description' => '紧凑型车销量' 
				),
				array (
						'link' => 'http://am.cn2che.com/',
						'title' => '澳门二手车',
						'description' => '澳门二手车' 
				),
				array (
						'link' => 'http://jinzhou.100che.cn/',
						'title' => '锦州汽车团购',
						'description' => '锦州汽车团购' 
				),
				
				array (
						'link' => 'http://www.queenlook.com/',
						'title' => '女王派',
						'description' => '女王派/全球大牌资讯情报站' 
				),
				array (
						'link' => 'http://www.jiangduoduo.com/',
						'title' => '奖多多彩票网',
						'description' => '奖多多彩票网' 
				),
				array (
						'link' => 'http://www.aicarzu.com/',
						'title' => '爱车族',
						'description' => '爱车族' 
				),
				
				array (
						'link' => 'http://www.s.cn/p-0-131-67.html',
						'title' => '单肩包品牌',
						'description' => '单肩包品牌' 
				),
				array (
						'link' => 'http://www.tzma.net',
						'title' => '中国涂装机械网',
						'description' => '中国涂装机械网' 
				),
				array (
						'link' => 'http://premiumcare-rc.com',
						'title' => '芙优润',
						'description' => '芙优润' 
				),
				array (
						'link' => 'http://jinjiangzhixing.jiudian.tieyou.com/',
						'title' => ' 锦江之星酒店',
						'description' => ' 锦江之星酒店' 
				),
				array (
						'link' => 'http://www.uccall.net/',
						'title' => '语音群呼',
						'description' => '语音群呼' 
				),
				array (
						'link' => 'http://top.78.cn/keji/',
						'title' => '科技加盟',
						'description' => '科技加盟' 
				),
				array (
						'link' => 'http://yangzhou.xueda.com',
						'title' => '扬州教育',
						'description' => '扬州教育' 
				),
				array (
						'link' => 'http://www.wobei.org',
						'title' => 'TXT电子书下载',
						'description' => 'TXT电子书下载' 
				),
				array (
						'link' => 'http://www.hicac.cn/',
						'title' => '自驾游俱乐部',
						'description' => '自驾游俱乐部' 
				),
				array (
						'link' => 'http://www.sfbest.com',
						'title' => '顺丰优选',
						'description' => '顺丰优选' 
				),
				array (
						'link' => 'http://cc.jiajiao400.com/',
						'title' => '长春家教网',
						'description' => '长春家教网' 
				),
				array (
						'link' => 'http://www.szooo.com/selftrip/',
						'title' => '出境游',
						'description' => '出境游' 
				),
				array (
						'link' => 'http://www.aimylife.com',
						'title' => '爱我生活',
						'description' => '爱我生活' 
				),
				array (
						'link' => 'http://www.9191cy.com/',
						'title' => '经典创业网',
						'description' => '经典创业网' 
				),
				array (
						'link' => 'http://www.changchunw.com.cn',
						'title' => '长春新闻',
						'description' => '长春新闻' 
				),
				array (
						'link' => 'http://chaohu.kuyiso.com/',
						'title' => '巢湖分类信息',
						'description' => '巢湖分类信息' 
				),
				array (
						'link' => 'http://www.17zwd.com/',
						'title' => '搜款网',
						'description' => '搜款网' 
				),
				array (
						'link' => 'http://www.nnwch.com/',
						'title' => '南宁租车',
						'description' => '南宁租车' 
				),
				array (
						'link' => 'http://www.ty21.com/',
						'title' => '古筝厂',
						'description' => '古筝厂' 
				),
				array (
						'link' => 'http://www.gaosubao.com',
						'title' => '高速宝',
						'description' => '高速宝' 
				),
				array (
						'link' => 'http://www.yaoba18.net/',
						'title' => '药吧易购',
						'description' => '药吧易购' 
				),
				array (
						'link' => 'http://www.daoduoduo.com/',
						'title' => '岛多多',
						'description' => '岛多多' 
				),
				array (
						'link' => 'http://www.55usedcar.com/',
						'title' => '二手车寄卖',
						'description' => '二手车寄卖' 
				) 
		)
		,
		'BRAND_LOGO' => array (
				array (
						'pic' => 'aodi.jpg',
						'name' => '奥迪' 
				),
				array (
						'pic' => 'baoma.jpg',
						'name' => '宝马' 
				),
				array (
						'pic' => 'benchi.jpg',
						'name' => '奔驰' 
				),
				array (
						'pic' => 'bentian.jpg',
						'name' => '东风本田',
						'name2' => '广汽本田' 
				),
				array (
						'pic' => 'biaozhi.jpg',
						'name' => '东风标致',
						'name2' => '进口标致' 
				),
				array (
						'pic' => 'bieke.jpg',
						'name' => '别克' 
				),
				
				array (
						'pic' => 'dazhong.jpg',
						'name' => '一汽大众',
						'name2' => '上海大众' 
				),
				array (
						'pic' => 'dihao.jpg',
						'name' => '帝豪' 
				),
				array (
						'pic' => 'dongfengfengxing.jpg',
						'name' => '东风风行' 
				),
				array (
						'pic' => 'dongnan.jpg',
						'name' => '东南汽车' 
				),
				array (
						'pic' => 'fengtian.jpg',
						'name' => '一汽丰田',
						'name2' => '广汽丰田' 
				),
				array (
						'pic' => 'fute.jpg',
						'name' => '长安福特' 
				),
				
				array (
						'pic' => 'guangqichuanqi.jpg',
						'name' => '广汽传祺' 
				),
				array (
						'pic' => 'haima.jpg',
						'name' => '海马汽车' 
				),
				array (
						'pic' => 'jianghuai.jpg',
						'name' => '江淮汽车' 
				),
				array (
						'pic' => 'leikesasi.jpg',
						'name' => '雷克萨斯' 
				),
				array (
						'pic' => 'leinuo.jpg',
						'name' => '雷诺' 
				),
				array (
						'pic' => 'lufeng.jpg',
						'name' => '陆风汽车' 
				),
				
				array (
						'pic' => 'mazida.jpg',
						'name' => '一汽马自达',
						'name2' => '长安马自达' 
				),
				array (
						'pic' => 'oubao.jpg',
						'name' => '欧宝' 
				),
				array (
						'pic' => 'qirui.jpg',
						'name' => '奇瑞' 
				),
				array (
						'pic' => 'qiya.jpg',
						'name' => '东风悦达起亚',
						'name2' => '进口起亚' 
				),
				array (
						'pic' => 'quanqiuying.jpg',
						'name' => '吉利全球鹰' 
				),
				array (
						'pic' => 'richan.jpg',
						'name' => '东风日产' 
				),
				
				array (
						'pic' => 'rongwei.jpg',
						'name' => '荣威' 
				),
				array (
						'pic' => 'sanling.jpg',
						'name' => '三菱' 
				),
				array (
						'pic' => 'sikeda.jpg',
						'name' => '斯柯达' 
				),
				array (
						'pic' => 'xiandai.jpg',
						'name' => '北京现代',
						'name2' => '进口现代' 
				),
				array (
						'pic' => 'xuefulan.jpg',
						'name' => '雪佛兰' 
				),
				array (
						'pic' => 'xuetielong.jpg',
						'name' => '雪铁龙' 
				),
				
				array (
						'pic' => 'yinglun.jpg',
						'name' => '英伦' 
				),
				array (
						'pic' => 'changcheng.jpg',
						'name' => '长城汽车' 
				),
				array (
						'pic' => 'zhonghua.jpg',
						'name' => '华晨中华' 
				),
				array (
						'pic' => 'biyadi.jpg',
						'name' => '比亚迪' 
				),
				array (
						'pic' => 'jinbei.jpg',
						'name' => '金杯' 
				),
				array (
						'pic' => 'lingmu.jpg',
						'name' => '长安铃木' 
				) 
		)
		,
		'MAPS_DIFF' => array (
				0.00665000,
				0.006799056 
		),
		'TUIJIAN_SHOP' => array (
				'24' 
		),
		'AREAS' => array (
				'sh' => array (
						'729' => '徐汇区',
						'730' => '长宁区',
						'732' => '普陀区',
						'733' => '闸北区',
						'734' => '虹口区',
						'735' => '杨浦区',
						'736' => '闵行区',
						'737' => '宝山区',
						'739' => '浦东新区',
						'742' => '青浦区',
						'738' => '嘉定区' 
				),
				'bj' => array (
						'8' => '海淀区',
						'5' => '朝阳区',
						'2' => '西城区',
						'1' => '东城区',
						'6' => '丰台区',
						'12' => '顺义区',
						'11' => '通州区' 
				),
				'gz' => array (
						'729' => '测试1区',
						'1' => '测试23区',
						'2' => '测试4区',
						'3' => '测试5区' 
				) 
		),
		'BRANDS' => array (
				'18' => '上海大众',
				'61' => '斯柯达',
				'67' => '雪佛兰',
				'12' => '别克',
				'19' => '一汽大众',
				'1' => '奥迪',
				'26' => '一汽丰田',
				'54' => '东风悦达起亚',
				'11' => '东风标致',
				'8' => '东风本田' 
		),
		'AREAFLAG' => array (
				'sh' => '上海',
				'c' => '上海',
				'www' => '上海',
				'bj' => '北京',
				'gz' => '广州' 
		),
		'CITYS' => array (
				'' => '全部',
				'3306' => '上海',
				'2912' => '北京',
				'2918' => '广州' 
		),
		'UPLOAD_FILE_EXT' => 'jpg,gif,png,jpeg',
		'YEAR' => date ( "Y" ),
		
		// 结算的百分比 下定时插入到membercoupon表
		'COUPON_RATIO' => array (
				'coupon_type1' => 3, // 现金券佣金百分比
				'coupon_type2' => 3  //套餐券佣金百分比
		),
		'COUPON_JIESUAN_STATUS' => array (
				'0' => '未结算',
				'1' => '申请结算',
				'2' => '商家确认,待打款',
				'3' => '已结算,已打款' 
		),
		'TUIJIAN_COUPON1' => array (
				61,
				59,
				75,
				46,
				24,
				23 
		),
		'TUIJIAN_COUPON2' => array (
				73,
				70,
				57,
				56,
				132,
				52 
		),
		
		//竞价状态
		'insurance_status'=> array (
				'1' => '竞价中',
				'2' => '竞价结束',
				'3' => '竞价确认',
				'4' => '竞价完成' 
		),
		'BIDORDER_STATE' => array (
				'0' => '已预约',
				'1' => '已确认',
				'2' => '维修中',
				'3' => '已取消',
				'4' => '已完成' 
		),
		
		'SERVICE_FUN' => array (
				'1' => 'wifi(无线网络)',
				'2' => '免费午餐',
				'3' => '桌球',
				'4' => '儿童游玩区',
				'5' => '店内提供电脑',
				'6' => '茶水饮料',
				'7' => '视听室',
				'8' => '按摩椅' 
		),
		
		'STATUS' => array (
				'0' => '等待处理',
				'1' => '预约确认',
				'2' => '已分配技师',
				'9' => '服务已完成' 
		),
        'NEW_FILTER_TIME' => '2015-9-29 19:30:00',  //新三滤表上线时间
        
);

if (substr ( $_SERVER ['REQUEST_URI'], 1, 3 ) != 'App' && substr ( $_SERVER ['REQUEST_URI'], 1, 10 ) != 'Appandroid' && substr ( $_SERVER ['REQUEST_URI'], 1, 10 ) != 'appandroid' && substr ( $_SERVER ['REQUEST_URI'], 1, 3 ) != 'app' && substr ( $_SERVER ['REQUEST_URI'], 1, 13 ) != 'index.php/app' && substr ( $_SERVER ['REQUEST_URI'], 1, 13 ) != 'index.php/App' && substr ( $_SERVER ['REQUEST_URI'], 1, 13 ) != 'index.php/appandroid') {
	// $config['URL_PATHINFO_DEPR'] = '-';
	//$config['URL_HTML_SUFFIX'] = '.html';
}
return $config;