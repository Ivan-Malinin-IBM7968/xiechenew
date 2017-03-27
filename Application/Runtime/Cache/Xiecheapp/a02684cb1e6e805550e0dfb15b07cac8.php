<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?php if(!empty($title)): echo ($title); ?>
            <?php else: ?>
            携车网,汽车保养维修预约,4S店折扣优惠,事故车维修预约<?php endif; ?>
    </title>
    <?php if(empty($meta_keyword)): ?><meta content="汽车保养,汽车维修,携车网,4S店预约保养,事故车维修" name="keywords">
        <?php else: ?>
        <meta content="<?php echo ($meta_keyword); ?>" name="keywords"><?php endif; ?>
    <?php if(empty($description)): ?><meta content="汽车保养维修,事故车维修-首选携车网,全国唯一4S店汽车保养维修折扣的网站,最低5折起,还有更多团购套餐任您选,事故车维修预约,不花钱,还有返利拿4006602822"
              name="description">
        <?php else: ?>
        <meta content="<?php echo ($description); ?>" name="description"><?php endif; ?>

    <link type="text/css" href="/Public_new/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="/Public_new/css/common.css" rel="stylesheet">
    <link type="text/css" href="/Public_new/css/header_new.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="/Public_new/jquery/jquery.min.js"></script>
    <script src="/Public_new/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<div class="header_new">
    <div class="head_content">
        <div class="logo fl"><a href="/"><img src="/Public_new/images/index_new/logo2.png"></a></div>
        <div class="city2 fl">
            <p><?php echo ($new_city_name); ?></p>
            <p><a href="/Public/city">[切换城市]</a></p>
        </div>
        <div class="model fr">
            <div class="binds"> <?php echo ($carName); ?>&nbsp;&nbsp;
                <a href="javascript:;" class="car-select">
                    <?php if(empty($choseCar)): ?>修改车型
                        <?php else: ?>
                        车型绑定<?php endif; ?>
                </a>
                &nbsp;|&nbsp;
                <?php if(empty($username)): ?><a href="/Public/login">登录 / 注册</a>
                    <?php else: ?>
                    <a href="/myhome"><?php echo ($username); ?></a> <a href="/public/logout">退出</a><?php endif; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <b>400-660-2822</b></div>
            <div class="select">
                <div class="fl" id="menu_f">
                    <ul>
                        <li id="#item1" class="fl li_style"><a href="/">首页</a></li>
                        <li  class="fl bg">|</li>
                        <li id="#item2" class="fl left2 li_style line2"><a href="/carservice" onclick="checkCar('/carservice');return false;">养车</a></li>
                        <li class="fl bg">|</li>
                        <?php if(($new_city_name) == "上海"): ?><li id="#item3" class="fl left2 li_style"><a href="/shiguche">修车</a></li>
                            <li class="fl bg">|</li><?php endif; ?>

                        <li id="#item4" class="fl left2 li_style"><a href="/ask">提问</a></li>
                        <li class="clearf"></li>
                    </ul>
                </div>
                <?php if(($new_city_name) == "上海"): ?><div class="fr">
                        <div class="search">
                            <form action="/shopservice/index" method="get">
                                <input type="text" name="k" class="search_input" placeholder="输入4S店铺">
                                <input type="submit" value="" class="search_sub" class="fl">
                            </form>
                        </div>
                    </div><?php endif; ?>
                <div class="clearf"></div>
            </div>
        </div>
        <div class="clearf"></div>
    </div>
</div>
<div class="kong"></div>

<link type="text/css" href="/Public_new/css/order.css" rel="stylesheet">
<script src="/Public_new/bootstrap/js/datetimepicker.min.js"></script>
<script src="/Public_new/bootstrap/js/datetimepicker.cn.js"></script>
<link type="text/css" href="/Public_new/bootstrap/css/datetimepicker.css" rel="stylesheet">
    <div class="ordernav">
      <div class="onavtip">
        <p><span class="current">&nbsp;</span></p>
        <p><span>&nbsp;</span></p>
      </div>
      <div class="onavdes">
        <p>填写订单</p>
        <p>订单支付</p>
      </div>
    </div>

    <div class="ordercon">
      <div class="orderchange fl">

        <div class="bsby">
          <div class="smallby"><label class="checkbox-inline"><input type="checkbox" checked="checked" id="xby" disabled="disabled"/> 小保养（机油、机滤）</label></div>
          <div class="bigby"><label class="checkbox-inline"><input type="checkbox" id="dby"> 大保养（机油、三滤）</label></div>
        </div>
        
        <div class="xieshifu">
          <ul class="nav xieshifunav" role="tablist"><li><a href="#xsf" role="tab" data-toggle="tab"  data-id="<?php echo ($recommend_high["id"]); ?>">谢师傅推荐款&nbsp;&nbsp;<span>￥<span id="highprice"><?php echo ($recommend_high["price"]); ?></span></span></a></li><li class="active"><a href="#jiben" role="tab" data-toggle="tab"  data-id="<?php echo ($recommend_low["id"]); ?>">基本款&nbsp;&nbsp;<span>￥<span id="lowprice"><?php echo ($recommend_low["price"]); ?></span></span></a></li></ul>
          <!-- Tab panes -->
          <div class="tab-content xieshifucon">
            <div class="tab-pane" id="xsf">
              <div class="oil">
                <span>机油</span>
                <span><?php echo ($recommend_high["name"]); ?></span>
                <span><?php echo ($recommend_high["type"]); ?></span>
              </div>
              <div class="oildes" style="height:150px; overflow:hidden;">
                <p class="pbt">什么是全合成油？</p>
                <p>全合成的优点是：对应矿物油、半合成机油，全合成可以在更低温度时就产生油膜形成保护，同时可以比半合成使用时间保持更长，油质表现的更稳定，在极端车况下也能提供很好的保护。</p>
                <p class="pbt">什么是半合成油？</p>
                <p>半合成的优点是：低温时比矿物油流动性更好，保证一定的润滑度；高温时比矿物油有更好的粘性度，给车件所需的润滑；同时相比较矿物油不容易产生油泥，对发动机更好的保护。</p>
                <p class="pbt">什么是矿物油？</p>
                <p>矿物油的优点：价格比较低；缺点是低温时对车辆的润滑度降低，高温时粘度会降低不能够达到车件所需的润滑，沉淀物较多容易产生油泥等。</p>
              </div>
            </div>
            <div class="tab-pane active" id="jiben">
              <div class="oil">
                <span>机油</span>
                <span><?php echo ($recommend_low["name"]); ?></span>
                <span><?php echo ($recommend_low["type"]); ?></span>
              </div>
              <div class="oildes" style="height:150px; overflow:hidden;">
                <p class="pbt">什么是全合成油？</p>
                <p>全合成的优点是：对应矿物油、半合成机油，全合成可以在更低温度时就产生油膜形成保护，同时可以比半合成使用时间保持更长，油质表现的更稳定，在极端车况下也能提供很好的保护。</p>
                <p class="pbt">什么是半合成油？</p>
                <p>半合成的优点是：低温时比矿物油流动性更好，保证一定的润滑度；高温时比矿物油有更好的粘性度，给车件所需的润滑；同时相比较矿物油不容易产生油泥，对发动机更好的保护。</p>
                <p class="pbt">什么是矿物油？</p>
                <p>矿物油的优点：价格比较低；缺点是低温时对车辆的润滑度降低，高温时粘度会降低不能够达到车件所需的润滑，沉淀物较多容易产生油泥等。</p>
              </div>
            </div>
            <p><span id="showoildes" style="background:url(/Public_new/images/order/updown.png) no-repeat 0 -23px;display:block;">&nbsp;</span><span id="hideoildes" style="display:none;background:url(/Public_new/images/order/updown.png) no-repeat 0 0px;">&nbsp;</span></p>
          </div>
        </div>
        
        <div class="changeitem">
          <div class="col-head">选购您所需的项目</div>
          <div class="item item1">
            <div class="checkbox fl">
              <?php if(empty($$item_sets[0]['price'])): ?><input type="checkbox" checked="checked" id="check1" class="cb" data-price="0" disabled="disabled"/>
              <?php else: ?>
                <input type="checkbox" checked="checked" id="check1" class="cb" data-price="<?php echo ($item_sets[0]['price']); ?>" disabled="disabled"/><?php endif; ?>
            </div>
            <div class="changecon fl">
              <div class="changename fl">机油</div>
              <div class="changediv fr">
                <?php if(empty($item_sets)): ?><input data-oil-1-id="0" data-oil-2-id="0" data-oil-1-num="0" data-oil-2-num="0" name="item_0" type="text" id="item_0" class="item_i oil-input" placeholder="请选择机油" data_id="-1" value="自备配件（￥0）" data-price="0" />
                <?php else: ?>
                  <input data-oil-1-id="<?php echo ($item_sets[0]['oil_1']); ?>" data-oil-2-id="<?php echo ($item_sets[0]['oil_2']); ?>" data-oil-1-num="<?php echo ($item_sets[0]['oil_1_num']); ?>" data-oil-2-num="<?php echo ($item_sets[0]['oil_2_num']); ?>" name="item_0" type="text" id="item_0" data-price="<?php echo ($item_sets[0]['price']); ?>" class="item_i oil-input" placeholder="请选择机油" data_id="<?php echo ($item_sets[0]['id']); ?>" value="<?php echo ($item_sets[0]['name']); ?>&nbsp;<?php echo ($car_style['norms']); ?>（￥<?php echo ($item_sets[0]['price']); ?>）" /><?php endif; ?>
                <ul style="display:none;">
                  <p>全合成油</p>
                  <?php if(is_array($item_sets)): foreach($item_sets as $k=>$set1): if(($set1["type"]) == "3"): ?><li data-type="<?php echo ($set1['type']); ?>" data-oil-1-id="<?php echo ($set1['oil_1']); ?>" data-oil-2-id="<?php echo ($set1['oil_2']); ?>" data-oil-1-num="<?php echo ($set1['oil_1_num']); ?>" data-oil-2-num="<?php echo ($set1['oil_2_num']); ?>" width="309" height="45" class="CheckboxGroup" data-tag="check1" id="CheckboxGroup0_<?php echo ($set1['id']); ?>" data-id="<?php echo ($set1['id']); ?>" data-price="<?php echo ($set1['price']); ?>" style="padding-left:20px;"><?php echo ($set1["name"]); ?>&nbsp;<?php echo ($car_style['norms']); ?><strong>（￥<?php echo ($set1["price"]); ?>）</strong></li><?php endif; endforeach; endif; ?>
                  <p>半合成油</p>
                  <?php if(is_array($item_sets)): foreach($item_sets as $k=>$set1): if(($set1["type"]) == "2"): ?><li data-type="<?php echo ($set1['type']); ?>" data-oil-1-id="<?php echo ($set1['oil_1']); ?>" data-oil-2-id="<?php echo ($set1['oil_2']); ?>" data-oil-1-num="<?php echo ($set1['oil_1_num']); ?>" data-oil-2-num="<?php echo ($set1['oil_2_num']); ?>" width="309" height="45" class="CheckboxGroup" data-tag="check1" id="CheckboxGroup0_<?php echo ($set1['id']); ?>" data-id="<?php echo ($set1['id']); ?>" data-price="<?php echo ($set1['price']); ?>" style="padding-left:20px;"><?php echo ($set1["name"]); ?>&nbsp;<?php echo ($car_style['norms']); ?><strong>（￥<?php echo ($set1["price"]); ?>）</strong></li><?php endif; endforeach; endif; ?>
                  <p><b>矿物油</b></p>
                  <?php if(is_array($item_sets)): foreach($item_sets as $k=>$set1): if(($set1["type"]) == "1"): ?><li data-type="<?php echo ($set1['type']); ?>" data-oil-1-id="<?php echo ($set1['oil_1']); ?>" data-oil-2-id="<?php echo ($set1['oil_2']); ?>" data-oil-1-num="<?php echo ($set1['oil_1_num']); ?>" data-oil-2-num="<?php echo ($set1['oil_2_num']); ?>" width="309" height="45" class="CheckboxGroup" data-tag="check1" id="CheckboxGroup0_<?php echo ($set1['id']); ?>" data-id="<?php echo ($set1['id']); ?>" data-price="<?php echo ($set1['price']); ?>" style="padding-left:20px;"><?php echo ($set1["name"]); ?>&nbsp;<?php echo ($car_style['norms']); ?><strong>（￥<?php echo ($set1["price"]); ?>）</strong></li><?php endif; endforeach; endif; ?>
                  <p><b>其他</b></p>
                  <li data-oil-1-id="0" data-oil-2-id="0" data-oil-1-num="0" data-oil-2-num="0" width="309" height="45" class="CheckboxGroup" data-tag="check1" id="CheckboxGroup0_0" data-id="-1" data-price="0" style="padding-left:20px;">自备配件<strong>（￥0）</strong></li>
                </ul>
                <span id="item_price_0" class="item_price"><?php echo ($item_sets['0']['price']); ?></span>
              </div>
            </div>
          </div>
          <div class="item item2">
            <div class="checkbox fl">
              <?php if(empty($$item_set[1][0]['price'])): ?><input type="checkbox" checked="checked" id="check2"  data-price="0" class="cb"  disabled="disabled" data-a="1"/>
              <?php else: ?>
                <input type="checkbox" checked="checked" id="check2"  data-price="<?php echo ($item_set[1][0]['price']); ?>" class="cb"  disabled="disabled" data-a="2"/><?php endif; ?>
            </div>
            <div class="changecon fl">
              <div class="changename fl">机滤</div>
              <div class="changediv fr">
                <?php if(empty($item_set[1])): ?><input name="item_1" type="text" id="item_1" class="item_i" placeholder="请选择机油滤清器" data_id="-1" data-price="0" value="自备配件（￥0）" />
                <?php else: ?>
                  <input name="item_1" type="text" id="item_1" data-price="<?php echo ($item_set['1']['0']['price']); ?>" class="item_i" placeholder="请选择机油滤清器" data_id="<?php echo ($item_set['1']['0']['id']); ?>" value="<?php echo ($item_set['1']['0']['name']); ?>（￥<?php echo ($item_set['1']['0']['price']); ?>）" /><?php endif; ?>
                <ul style="display:none;">
                  <?php if(is_array($item_set[1])): foreach($item_set[1] as $key=>$value): ?><li width="309" height="45" class="CheckboxGroup" data-tag="check2" id="CheckboxGroup1_<?php echo ($value['id']); ?>" data-id="<?php echo ($value['id']); ?>" data-price="<?php echo ($value['price']); ?>" style="padding-left:20px;"><?php echo ($value['name']); ?><strong>（￥<?php echo ($value['price']); ?>）</strong></li><?php endforeach; endif; ?>
                    <li width="309" height="45" class="CheckboxGroup" data-tag="check2" id="CheckboxGroup1_0" data-id="-1" data-price="0" style="padding-left:20px;">自备配件<strong>（￥0）</strong></li>
                </ul>
                <span id="item_price_1" class="item_price"><?php echo ($item_set['1']['0']['price']); ?></span>
              </div>
            </div>
          </div>
          <div class="item item3">
            <div class="checkbox fl"><input type="checkbox" id="check3" data-price="<?php echo ($item_set[2][0]['price']); ?>" class="cb" /></div>
            <div class="changecon fl">
              <div class="changename fl">空气滤</div>
              <div class="changediv fr">
                <?php if(empty($item_set[2])): ?><input name="item_2" type="text" id="item_2" class="item_i" placeholder="请选择空气滤清器" data_id="-1" data-price="0" value="自备配件（￥0）" />
                <?php else: ?>
                  <input name="item_2" type="text" id="item_2" data-price="<?php echo ($item_set['2']['0']['price']); ?>" class="item_i" placeholder="请选择空气滤清器" data_id="<?php echo ($item_set['2']['0']['id']); ?>" value="<?php echo ($item_set['2']['0']['name']); ?>（￥<?php echo ($item_set['2']['0']['price']); ?>）" /><?php endif; ?>  
                <ul style="display:none;">
                  <?php if(is_array($item_set[2])): foreach($item_set[2] as $key=>$value): ?><li width="309" height="45" class="CheckboxGroup" data-tag="check3" id="CheckboxGroup2_<?php echo ($value['id']); ?>" data-id="<?php echo ($value['id']); ?>" data-price="<?php echo ($value['price']); ?>" style="padding-left:20px;"><?php echo ($value['name']); ?><strong>（￥<?php echo ($value['price']); ?>）</strong></li><?php endforeach; endif; ?>
                     <li width="309" height="45" class="CheckboxGroup" data-tag="check3" id="CheckboxGroup2_0" data-id="-1" data-price="0" style="padding-left:20px;">自备配件<strong>（￥0）</strong></li>
                </ul>
                <span id="item_price_2" class="item_price"><?php echo ($item_set['2']['0']['price']); ?></span>
              </div>
            </div>
          </div>
          <div class="item item4">
            <div class="checkbox fl"><input type="checkbox" id="check4" data-price="<?php echo ($item_set[3][0]['price']); ?>" class="cb" /></div>
            <div class="changecon fl">
              <div class="changename fl">空调滤</div>
              <div class="changediv fr">
                <?php if(empty($item_set[3])): ?><input name="item_3" type="text" id="item_3" class="item_i" placeholder="请选择空调滤清器" data_id="-1" data-price="0" value="自备配件（￥0）" />
                <?php else: ?>
                <input name="item_3" type="text" id="item_3" data-price="<?php echo ($item_set['3']['0']['price']); ?>" class="item_i" placeholder="请选择空调滤清器" data_id="<?php echo ($item_set['3']['0']['id']); ?>" value="<?php echo ($item_set['3']['0']['name']); ?>（￥<?php echo ($item_set['3']['0']['price']); ?>）" /><?php endif; ?>
                <ul style="display:none;">
                  <?php if(is_array($item_set[3])): foreach($item_set[3] as $key=>$value): ?><li width="309" height="45" class="CheckboxGroup" data-tag="check4" id="CheckboxGroup3_<?php echo ($value['id']); ?>" data-id="<?php echo ($value['id']); ?>" data-price="<?php echo ($value['price']); ?>" style="padding-left:20px;"><?php echo ($value['name']); ?><strong>（￥<?php echo ($value['price']); ?>）</strong></li><?php endforeach; endif; ?>
                    <li width="309" height="45" class="CheckboxGroup" data-tag="check4" id="CheckboxGroup3_0" data-id="-1" data-price="0" style="padding-left:20px;">自备配件<strong>（￥0）</strong></li>
                </ul>
                <span id="item_price_3" class="item_price"><?php echo ($item_set['3']['0']['price']); ?></span>
              </div>
            </div>
          </div>
          <div class="item item5">
            <div class="checkbox fl"><input type="checkbox"  id="no-service" /></div>
            <div class="changeonly fl">
              已有配件，仅购买上门服务
            </div>
          </div>
          <div class="item item6">
            <div class="checkbox fl"><input id="service_cost" type="checkbox" disabled="true" checked="checked" value="1" name="service_cost"></div>
            <div class="changeonly fl">
              服务费 ￥99
            </div>
          </div>
        </div>

      </div>
      <div class="orderform fr">
        <form method="post" name="subForm" id="subForm" action="/carservice/create_order">
        <div class="col-head">请正确填写您的订单，提交订单后，有客服跟您联系</div>
        <div class="forminfo">
          <table width="100%">
            <tr>
              <td width="75"><label for="uname">您的姓名：</label></td>
              <td colspan="2"><input type="text" class="form-control" id="truename" name="truename" placeholder="您的姓名" value="<?php if(!empty($order_info["truename"])): echo ($order_info["truename"]); endif; ?>"/></td>
            </tr>
            
            <tr id="area">
                <td><label for="uadr">城市区域：</label></td>
                <td colspan="2">                    
                      <select name="area_id" id="area_id" class="form-control" style="width:100%; height:34px;">
                      </select>
                </td>
			</tr>

            <tr>
              <td><label for="uadr">预约地点：</label></td>
              <td colspan="2"><input type="text" class="form-control" id="address" name="address" value="<?php if(!empty($order_info["address"])): echo ($order_info["address"]); else: echo ($new_city_name); ?>市<?php endif; ?>" /></td>
            </tr>

            <tr>
              <td><label for="utel">手机号码：</label></td>
              <td colspan="2"><input type="text" class="form-control" id="mobile" name="mobile" placeholder="手机号码" value="<?php if(!empty($order_info["mobile"])): echo ($order_info["mobile"]); endif; ?>" pattern="[0-9]" maxlength="12"/></td>
            </tr>

            <tr>
              <td><label for="utime">预约时间：</label></td>
              <td><input type="text" class="form-control" id="order_time" name="order_time" placeholder="预约日期" /></td>
              <td>
                <select id="order_time2" name="order_time2" class="form-control"  onchange="prevent()">
					<option value="">预约时间</option>
					<option value="8:00">8:00-8:59</option>
					<option value="9:00">9:00-9:59</option>
					<option value="10:00">10:00-10:59</option>
					<option value="11:00">11:00-11:59</option>
					<!--<option value="12:00">12:00-12:59</option>-->
					<!--<option value="13:00">13:00-13:59</option>-->
					<option value="14:00">14:00-14:59</option>
					<option value="15:00">15:00-15:59</option>
					<option value="16:00">16:00-16:59</option>
					<option value="17:00">17:00-17:59</option>
					<option value="18:00">18:00-18:59</option>
					<option value="19:00">19:00-19:59</option>
					<option value="20:00">20:00-20:59</option>
					<option value="21:00">21:00-21:59</option>
                </select>
              </td>
            </tr>
            <!--update-->
            <tr>
              <td  width="75"><label for="carnum">车牌号：</label></td>
              <td colspan="2">
                <div class="row">
                  <div class="col-md-3 class-w" >
                    <select name="licenseplate_type" id="licenseplate_type" class="form-control">
                      <?php if(!empty($order_info["license_plate"])): ?><option value="<?php echo ($order_info["license_plate"]); ?>"><?php echo ($order_info["license_plate"]); ?></option><?php endif; ?>
                      <?php if(!empty($city_abbreviation)): ?><option value="<?php echo ($city_abbreviation); ?>"><?php echo ($city_abbreviation); ?></option><?php endif; ?>


                      <?php if(is_array(C("SHORT_PROVINCIAL_CAPITAL"))): $i = 0; $__LIST__ = C("SHORT_PROVINCIAL_CAPITAL");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo_os): $mod = ($i % 2 );++$i; if($city_abbreviation == '<?php echo ($vo_os); ?>'): else: ?>  <option value="<?php echo ($vo_os); ?>"><?php echo ($vo_os); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                     <!-- <?php if(is_array(C("SHORT_PROVINCIAL_CAPITAL"))): $i = 0; $__LIST__ = C("SHORT_PROVINCIAL_CAPITAL");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo_os): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo_os); ?>"><?php echo ($vo_os); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>-->
                    </select>
                  </div>
                  <div class="col-md-9 class-w2"><input type="text" class="form-control" name="licenseplate" id="licenseplate" placeholder="车牌号" maxlength="6" value="<?php if(!empty($order_info["license_plate_num"])): echo ($order_info["license_plate_num"]); endif; ?>"/></div>
                </div>
              </td>
            </tr>
          </table>
        </div>

        <div class="col-head moreinfo"><span class="fromsh fr" style="background:url(/Public_new/images/order/updown.png) no-repeat 0 -12px;">&nbsp;</span>填写更多信息，以便为您配备更精准的配件</div>
        <div class="forminfo disno">
          <table width="100%">
            <tr>
              <td width="75"><label for="cartime">车辆注册时间：</label></td>
              <td><input type="text" class="form-control" id="car_reg_time" name="car_reg_time" placeholder="车辆注册时间" /></td>
            </tr>

            <tr>
              <td><label for="engine">发动机号码：</label></td>
              <td><input type="text" class="form-control" id="engine_num" name="engine_num" placeholder="发动机号码" /></td>
            </tr>

            <tr>
              <td><label for="carvin">VIN：</label></td>
              <td><input type="text" class="form-control" id="vin_num" name="vin_num" placeholder="VIN" value="<?php if(!empty($order_info["vin_num"])): echo ($order_info["vin_num"]); endif; ?>" /></td>
            </tr>
          </table>
        </div>

        <div class="col-head">支付方式</div>
        <div class="forminfo">
          <table width="100%">
            <tr>
              <td width="75"><label for="payment">支付方式：</label></td>
              <td style="position:relative;">
<!--                <input type="text" class="form-control" id="payment" value="现场支付"/>
                <ul class="paymentul">
				     <option value="1">现场支付</option>
                      <option value="1">现场支付</option>
                      <option value="3">建行支付</option>

                </ul>
                <input type="hidden" name="pay_type" id="pay_type" value="1">-->
                <select name="pay_type" class="form-control" style="width:100%; text-indent:12px;">
                      <script type="text/javascript">
                        if(navigator.userAgent.toLowerCase().match(/MicroMessenger/i)=="micromessenger"){
                          document.write('<option value="2">微信支付</option>');
                        }
                      </script>
                      <option value="1">现场支付</option>
                      <option value="3">建行支付</option>
                </select>
                
                
                
              </td>
            </tr>
          </table>
        </div>
        <div class="col-head moreinfo"><span class="fromsh fr" style="background:url(/Public_new/images/order/updown.png) no-repeat 0 -12px;">&nbsp;</span>使用优惠券兑换码</div>
        <div class="forminfo disno">

          <!-- <div class="countquan">您账户可用的优惠券<span>0</span>张，现金券<span>2</span>张，账户余额为<span>899</span>元</div> -->

          <!-- <div class="quan">有优惠券兑换码？<span>[点击输入兑换码]</span></div> -->
          <table width="100%">
            <tr>
              <td width="75"><label for="quanma">输入券码：</label></td>
              <td>
                <input type="text" class="form-control code-input" id="quanma" placeholder="" />
              </td>
              <td width="60" align="center"><u id="use-code"><a href="javascript:;">使用</a></u></td>
            </tr>
          </table>
          <!-- <div class="quan">有现金券兑换码？<span>[点击输入兑换码]</span></div>
          <table width="100%" style="display:none;">
            <tr>
              <td width="75"><label for="quanma">输入券码：</label></td>
              <td>
                <input type="text" class="form-control" id="quanma" placeholder="" />
              </td>
              <td width="60" align="center"><u id="shiyong">使用</u></td>
            </tr>
          </table>
          <div class="quan">有充值卡兑换码？<span>[点击输入兑换码]</span></div>
          <table width="100%" style="display:none;">
            <tr>
              <td width="75"><label for="quanma">输入券码：</label></td>
              <td>
                <input type="text" class="form-control" id="quanma" placeholder="" />
              </td>
              <td width="60" align="center"><u id="shiyong">使用</u></td>
            </tr>
          </table>


          <div class="countquan">您还未<a href="#"><span>登录</span></a>  查看不到优惠券信息</div>
          <div class="quan">有优惠券兑换码？<span>[点击输入兑换码]</span></div>
          <table width="100%" style="display:none;">
            <tr>
              <td width="75"><label for="quanma">输入券码：</label></td>
              <td>
                <input type="text" class="form-control" id="quanma" placeholder="" />
              </td>
              <td width="60" align="center"><u id="shiyong">使用</u></td>
            </tr>
          </table> -->

          
          <!-- <div class="syquan">共使用了<span>1</span>张抵用券，优惠了<span>20</span>元</div> -->

        </div>
        
        <div class="tijiao">
            <table width="100%">
              <tr>
                <td>实际付款</td>
                <td width="110"><span id="amount"></span><span>元</span>&nbsp;&nbsp;</td>
                <td width="200"><button href="javascript:;" id="subBtn">提交订单</button></td>
              </tr>
            </table>
        </div>
        <input type="hidden" name="city_id"  id="city_id"   value="<?php echo ($new_city_id); ?>" />
        <input type="hidden" name="oil_1_id" value="<?php echo ($item_sets['0']['oil_1']); ?>" />
        <input type="hidden" name="oil_1_num" value="<?php echo ($item_sets['0']['oil_1_num']); ?>" />
        <input type="hidden" name="oil_2_id" value="<?php echo ($item_sets['0']['oil_2']); ?>" />
        <input type="hidden" name="oil_2_num" value="<?php echo ($item_sets['0']['oil_2_num']); ?>" />
        <input type="hidden" name="CheckboxGroup0_res" id="CheckboxGroup0_res" value="<?php if(empty($item_sets['0']['id'])): ?>-1<?php else: echo ($item_sets['0']['id']); endif; ?>">
        <input type="hidden" name="CheckboxGroup1_res" id="CheckboxGroup1_res" value="<?php if(empty($item_set['1']['0']['id'])): ?>-1<?php else: echo ($item_set['1']['0']['id']); endif; ?>">
        <input type="hidden" name="CheckboxGroup2_res" id="CheckboxGroup2_res" value="<?php echo ($item_set['2']['0']['id']); ?>">
        <input type="hidden" name="CheckboxGroup3_res" id="CheckboxGroup3_res" value="<?php echo ($item_set['3']['0']['id']); ?>">
        </form>
      </div>
    </div>

<script type="text/javascript">
      $(function(){

        function stopPropagation(e) {
          if (e.stopPropagation)
            e.stopPropagation();
          else
            e.cancelBubble = true;
        }

        $(document).click(function(){
          $(".changediv ul,.paymentul").hide();
        });

        $('.changediv,.forminfo').click(function(e){
            stopPropagation(e);
        });

        $(".changediv>input").click(function(){
          $(".changediv ul").hide();
          $(this).next().slideDown();
        });

        $(".changediv ul li").click(function(){
          $(this).parent().hide();
        });

        $(".quan span").click(function(){
          $(this).parent().next().toggle();
        });

        $("#payment").click(function(){
          $(this).next().slideDown();
        });
        //机油介绍打开 收起
        $("#showoildes").click(function(){
          $(".xieshifucon .oildes").height('');
          $("#hideoildes").css("display","block");
          $("#showoildes").css("display","none");
        });

        $("#hideoildes").click(function(){
          $(".xieshifucon .oildes").animate({"height":'150px'});
          $("#hideoildes").css("display","none");
          $("#showoildes").css("display","block");
        });

        $(".moreinfo").click(function(){
          $(this).next('.forminfo').slideToggle();
          if($(this).find("span").css("background-position")=="0px 14px"){
            $(this).find("span").css("background-position","0px -12px");
          }else{
            $(this).find("span").css("background-position","0px 14px");
          }
        });


        $(".paymentul li").click(function(){
          $("#payment").val($(this).html());
          $(".paymentul").hide();
          $("#pay_type").val($(this).attr("data-val"));
        });

        if( $('#check1').prop('checked') == true ){
          var item_0_p = $("#item_0").attr('data-price');
          $('#check1').attr('data-price',Number(item_0_p));
          var dp1 = $('#check1').attr('data-price');

        }else{
          var dp1 = 0;
        }
        if( $('#check2').prop('checked') == true ){
          var item_1_p = $("#item_1").attr('data-price');
          $('#check2').attr('data-price',Number(item_1_p));
          var dp2 = $('#check2').attr('data-price');
        }else{
          var dp2 = 0;
        }
        if( $('#check3').prop('checked') == true ){
          var item_2_p = $("#item_2").attr('data-price');
          $('#check3').attr('data-price',Number(item_2_p));
          var dp3 = $('#check3').attr('data-price');
        }else{
          var dp3 = 0;
        }
        if( $('#check4').prop('checked') == true ){
          var item_3_p = $("#item_3").attr('data-price');
          $('#check4').attr('data-price',Number(item_3_p));
          var dp4 = $('#check4').attr('data-price');
        }else{
          var dp4 = 0;
        }

        var amount = (Number(dp1)+Number(dp2)+Number(dp3)+Number(dp4)+99);

        $("#amount").text(amount);

    var old_price = 0, price = 0,back_amount = amount;

    $(".CheckboxGroup").click(function(){
        id = $(this).attr("data-id");
        price = $(this).attr("data-price");
    
        //console.log(price);
        var tag = $(this).attr("id");
        tag = tag.split("_");
        if(tag[0] && tag[1]){
            $("#"+tag[0]+"_res").val(id);
        }else{
            return false;
        }


        $(this).parent().prev('input').attr('data_id',id);
        $(this).parent().prev('input').attr('data-price',price);

        old_price = $(this).parent().parent().find(".item_price").text();

        //console.log(old_price);
        var $top_input = $(this).parent().prev();
        $top_input.val( $(this).text() );
        if ( $top_input.hasClass('oil-input') ){
          var $data_oil_1_id = $(this).attr('data-oil-1-id');
          var $data_oil_1_num = $(this).attr('data-oil-1-num');
          var $data_oil_2_id = $(this).attr('data-oil-2-id');
          var $data_oil_2_num = $(this).attr('data-oil-2-num');
          $top_input.attr('data-oil-1-id',$data_oil_1_id).attr('data-oil-1-num',$data_oil_1_num).attr('data-oil-2-id',$data_oil_2_id).attr('data-oil-2-num',$data_oil_2_num);
        }
        $(this).parent().parent().find(".item_price").text(price);
        $(this).parent().parent().parent().parent().find(".cb").attr('data-price',price);
    
    //判断checkbox是否选中
    var $target =$(this).attr('data-tag');
    if ($('#'+$target).prop('checked') == true)
    {
      amount -= Number(old_price);
        if(amount < 0){
            amount = 99;
        }
      amount += Number(price);
      $("#amount").text(amount);  
    } 
        //console.log(amount);

    });

    // $("#service_cost").click(function(){
    //     if($('input[name="service_cost"]:checked').val() == "1"){
    //         amount += 99;
    //     }else{
    //         amount -= 99;
    //     }
    //     $("#amount").text(amount);
    // })

    $("#subBtn").click(function(){
        addValues();
        //检查选择时间段内订单过盛
        prevent() ;
        
        var truename = $("#truename").val();
        var address = $("#address").val();
        var mobile = $("#mobile").val();
        var order_time = $("#order_time").val();
        var order_time2 = $("#order_time2").val();
        var licenseplate = $("#licenseplate").val();
        var area_id = $("#area_id").val();
        var city_id = <?php echo ($city_id); ?>;
        
        if(!truename){
          alert('请填写姓名');
          $("#truename").focus();
          return false;
        }
        if(city_id==1&&!area_id){
          alert('请选择区域');
          $("#area_id").focus();
          return false;
        }
        if(!address){
          alert('请填写详细地址');
          $("#address").focus();
          return false;
        }
        if(!mobile){
          alert('请填写手机号码');
          $("#mobile").focus();
          return false;
        }
        var mobileRegExp = /^1[3|4|5|7|8][0-9]\d{4,8}$/;
        if(!mobileRegExp.test(mobile)){
          alert('请填写正确的手机号码');
          $("#mobile").focus();
          return false;
        }
        if(!order_time){
          alert('请选择预约日期');
          $("#order_time").focus();
          return false;
        }
        if(!order_time2){
          alert('请选择预约时间');
          $("#order_time2").focus();
          return false;
        }

        if(licenseplate){
          if(!/^[A-Za-z]{1}[A-Za-z_0-9]{5}$/.test(licenseplate)){
            alert('车牌号错误');
          $("#licenseplate").focus();
          return false;
          }
        }

        if($('#check1').prop('checked') == true||$('#check2').prop('checked') == true||$('#check3').prop('checked') == true||$('#check4').prop('checked') == true||$('#no-service').prop('checked') == true){
          //防止重复提交
          $("#subBtn").attr('disabled',"true");
          setTimeout(function () {$("#subBtn").attr('disabled',false);}, 30000);
          
         $.ajax({
             type: "POST",
             url: "/carservice/create_order",
             data: $("#subForm").serialize(),
             dataType: "json",
             success: function(data){
              if(data.status){
                // alert(data.info);
                if($("#pay_type").val()==1){
                  alert(data.info);
                }
                window.location.href= data.url;
              }else{
                alert(data.info);
              }
             }
         });

        }else {
          if(confirm("您没有勾选任何项目，是否已经自配配件？是否仅购买上门服务")){
            $('#no-service').click();
            $.ajax({
             type: "POST",
             url: "/carservice/create_order",
             data: $("#subForm").serialize(),
             dataType: "json",
             success: function(data){
              if(data.status){
                if($("#pay_type").val()==1){
                  alert(data.info);
                }
                window.location.href= data.url;
              }else{
                alert(data.info);
              }
             }
         });
          }else{
            return false;
          }
        }

    });

    $('#no-service').click(function(){
      if( $(this).prop('checked') == true ){
        $('.cb').prop('checked',false);
        $('.cb').prop('disabled',false);
        $("#amount").text('99');
        amount = 99;
        $('#CheckboxGroup0_res').val('');
        $('#CheckboxGroup1_res').val('');
        $('#CheckboxGroup2_res').val('');
        $('#CheckboxGroup3_res').val('');
      }else{
        $('.cb').prop('checked',true);
        
        $('.changeitem').find('.cb').each(function(){
          if( $(this).prop('checked') == true ){
            amount += Number( $(this).attr('data-price') );
          }
        })
        $("#amount").text(amount);
        addValues();
      }
    })

    function addValues(){
      CheckboxGroup0_res = $('input[name=item_0]').attr('data_id');
      CheckboxGroup1_res = $('input[name=item_1]').attr('data_id');
      CheckboxGroup2_res = $('input[name=item_2]').attr('data_id');
      CheckboxGroup3_res = $('input[name=item_3]').attr('data_id');
        //获得每样的数量
      var $oil_1_id = $('input[name=item_0]').attr('data-oil-1-id'); 
      var $oil_1_num = $('input[name=item_0]').attr('data-oil-1-num');
      var $oil_2_id = $('input[name=item_0]').attr('data-oil-2-id');
      var $oil_2_num = $('input[name=item_0]').attr('data-oil-2-num');
        if( $('#check1').prop('checked') == true ){
          $('input[name=oil_1_id]').val($oil_1_id);//放到要提交的数据框里面
          $('input[name=oil_2_id]').val($oil_2_id);
          $('input[name=oil_1_num]').val($oil_1_num);
          $('input[name=oil_2_num]').val($oil_2_num);
          $('#CheckboxGroup0_res').val(CheckboxGroup0_res);
        }else{
          $('#CheckboxGroup0_res').val('');
        }
        if( $('#check2').prop('checked') == true ){
          $('#CheckboxGroup1_res').val(CheckboxGroup1_res);
        }else{
          $('#CheckboxGroup1_res').val('');
        }
        if( $('#check3').prop('checked') == true ){
          $('#CheckboxGroup2_res').val(CheckboxGroup2_res);
        }else{
          $('#CheckboxGroup2_res').val('');
        }
        if( $('#check4').prop('checked') == true ){
          $('#CheckboxGroup3_res').val(CheckboxGroup3_res);
        }else{
          $('#CheckboxGroup3_res').val('');
        }
    }

    $('.cb').click(function (){

      $('#no-service').prop('checked',false);

      if( $(this).prop('checked') == true ){
        if($(this).attr("id")=='check1'){
          
          if($("#check2").prop("checked")==false){
            alert("提示：机油、机滤为必保养项目，可自备配件");
            // amount += Number($("#check2").attr('data-price'));
            $("#check2").click();
          }
          $(this).attr("disabled","disabled");
        }
        if($(this).attr("id")=='check2'){
          
          if($("#check1").prop("checked")==false){
            alert("提示：机油、机滤为必保养项目，可自备配件");
            // amount += Number($("#check1").attr('data-price'));
            $("#check1").click();
          }
          $(this).attr("disabled","disabled");
        }
        $price = $(this).attr('data-price');
        amount +=  Number($price);

        $("#amount").text(amount);

      }else{
        if($(this).attr("id")=='check1'){return false;}
        if($(this).attr("id")=='check2'){return false;}
        $price = $(this).attr('data-price');
        amount -= Number($price);
        if(amount<0){
          amount = 0;
        }
        //console.log(amount)
        $("#amount").text(amount);
      }

      if($("#check1").prop("checked") == true && $("#check2").prop("checked") == true && $("#check3").prop("checked") == false && $("#check4").prop("checked") == false){
        $("#xby").prop("disabled",true);
        $("#xby").prop("checked",true);
        $("#dby").prop("disabled",false);
        $("#dby").prop("checked",false);
      }else if($("#check1").prop("checked") == true && $("#check2").prop("checked") == true && $("#check3").prop("checked") == true && $("#check4").prop("checked") == true){
        $("#xby").prop("disabled",false);
        $("#xby").prop("checked",false);
        $("#dby").prop("disabled",true);
        $("#dby").prop("checked",true);
      }else{
        $("#xby").prop("disabled",false);
        $("#xby").prop("checked",false);
        $("#dby").prop("disabled",false);
        $("#dby").prop("checked",false);
      }

    });

    //大小保养
    $("#xby").click(function(){
      // $("#dby").prop("disabled",false);
      // $("#dby").prop("checked",false);
      // $(this).prop("disabled",true);
      if($("#check1").prop("checked") == false){
        $("#check1").click();
      }

      if($("#check2").prop("checked") == false){
        $("#check2").click();
      }

      if($("#check3").prop("checked") == true){
        $("#check3").click();
      }

      if($("#check4").prop("checked") == true){
        $("#check4").click();
      }
    });

    $("#dby").click(function(){
      // $("#xby").prop("disabled",false);
      // $("#xby").prop("checked",false);
      // $(this).prop("disabled",true);

      if($("#check1").prop("checked") == false){
        $("#check1").click();
      }

      if($("#check2").prop("checked") == false){
        $("#check2").click();
      }

      if($("#check3").prop("checked") == false){
        $("#check3").click();
      }

      if($("#check4").prop("checked") == false){
        $("#check4").click();
      }

    });

    //谢师傅
    $(".xieshifunav li").click(function(){      
      if($(this).index() == 0){
        $(".CheckboxGroup[data-id='<?php echo ($recommend_high["id"]); ?>']").click();
      }
      else if($(this).index() == 1){
        $(".CheckboxGroup[data-id='<?php echo ($recommend_low["id"]); ?>']").click();
      }
      if($("#check3").prop("checked") == true){
          $("#check3").click();
        }

        if($("#check4").prop("checked") == true){
          $("#check4").click();
        }
    });

    //兑换码
    $('#use-code').click(function(){
      var mobile = $("#mobile").val();
      var mobileRegExp = /^1[3|4|5|7|8][0-9]\d{4,8}$/;
      if(mobile){
        if(!(mobileRegExp.test(mobile))){
          alert("手机号码错误");
          $("#mobile").focus();
          return false;
        }
      }else{
        alert('请填写手机号码');
        $("#mobile").focus();
        return false;
      }
      var coupon_code = $('.code-input').val();
      if(!coupon_code){
        alert('请填写您的抵用码');
        return false;
      }
      $.post('/carservice/valid_coupon_code',{'coupon_code':coupon_code,'mobile':mobile},function(data){
        if(data.status){
          html = '成功使用一张价值'+data.info+'的抵用券';
          alert(html);
          var price = data.info+'.00';
          $('#replace-money').text(price);
          var old_amount = $('#amount').text();
          var new_amount = Number(old_amount)-Number(data.info);
          $('#amount').text(new_amount);
        }else{
          alert(data.info);
          return false;
        }
      },'json')
    });

    function getLastDay(year,month)        
        {        
         var new_year = year;    //取当前的年份        
         var new_month = month++;//取下一个月的第一天，方便计算（最后一天不固定）        
         if(month>12)            //如果当前大于12月，则年份转到下一年        
         {        
          new_month -=12;        //月份减        
          new_year++;            //年份增        
         }        
         var new_date = new Date(new_year,new_month,1);                //取当年当月中的第一天        
         return (new Date(new_date.getTime()-1000*60*60*24)).getDate();//获取当月最后一天日期        
        }

      //4-19 超过下午四点 5-20
       var now = new Date();
       year = now.getFullYear();
       hours = now.getHours();
       month = now.getMonth()+1;
       day = now.getDate();

       if(hours >=16){
        minDate = year+"-"+month+"-"+(day+2);
        maxDate = year+"-"+month+"-"+(day+16);
       }else{
        minDate = year+"-"+month+"-"+(day+1);
        maxDate = year+"-"+month+"-"+(day+15);
       }

       max2Date = year+"-"+month+"-"+getLastDay(year,month);

    $("#order_time").datetimepicker({
        language:'zh-CN',
        format: "yyyy-mm-dd",
        autoclose: true,
        startDate: minDate,
        endDate: maxDate,
        minView: 2,
        initialDate: minDate,
        pickerPosition: "bottom-left"
      }).on("outOfRange",function(){
        alert("日期不可选");
      });

      $("#car_reg_time").datetimepicker({
        language:'zh-CN',
        format: "yyyy-mm-dd",
        autoclose: true,
        minView: 2,
        endDate: max2Date,
        pickerPosition: "bottom-left"
      });


});



//阻止客服同一时间段下过盛的订单
function prevent(){
    var order_time = $("#order_time").val();
    var order_time2 = $("#order_time2").val();
    var city_id = $("#city_id").val();
    //alert(city_id);
    if(city_id==1){
        $.ajax({
          type:'POST',
          url:'/carservice/prevent',
          cache:false,
          dataType:'JSON',
          data:'order_time='+order_time+'&order_time2='+order_time2,
          success:function(data){
                  //alert(data);
                  if(data<12){
                        $("#subBtn").attr('style','');
                        return true;
                  }else{
                        $("#subBtn").attr('style','display:none;');
                        alert('这个时间段单量已经饱和');
                        return false;
                  }
          }
        });
    }
}


//页面加载完执行
jQuery(function(){
    city_ajax();
});

function  city_ajax(){
    //获取城市id
    var city_id = <?php echo ($city_id); ?>;
    if(city_id==1){
        jQuery("#area").show();
    }else{
        jQuery("#area").hide();
    }
    jQuery.ajax({
        type:'POST',
        url:'/carservice/ajax_area',
        cache:false,
        dataType:'JSON',
        data:'city_id='+city_id,
        success:function(data){
            if(data.errno == 0){
                var area_list_html = '<option value="">选择区域</option>';
                jQuery.each(data.result.area_list, function(k, v){
                area_list_html += '<option value="'+v['areaID']+'">'+v['area']+'</option>';
                });
                jQuery("#area_id").html(area_list_html);
            }else{
                return false;
            }
        }
    });
}   
    
    
</script>



<div class="footer2">
    <div id="go" style="display:none"></div>
    <div class="footer_contents">
        <div class="zhichi fl">
            <p class="p_bottom"><a href="/public/aboutus" target="_blank">关于我们</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="/shopservice/agreement" target="_blank">服务支持 </a>&nbsp;&nbsp; |&nbsp;&nbsp; <a href="/public/honour" target="_blank">媒体报道</a></p>
            <p>Copyright © 2015 WWW.XIECHE.COM.CN <a href="http://www.miibeian.gov.cn"><font color="#e47911">沪ICP备12017241号</font></a> 携车网 版权所有</p>
        </div>
        <div class="fr fenxiangicon">
            <p>扫二维码下载安卓app</p>
            <img src="/Public_new/images/index_new/xc-android-app.jpg" />
            <!-- <img src="/Public_new/images/index_new/fenxiang_01.jpg"/>
             <img src="/Public_new/images/index_new/fenxiang_02.jpg"/>
             <img src="/Public_new/images/index_new/fenxiang_03.png"/>-->
        </div>
        <div class="clearf"></div>
    </div>
</div>

<div class="changecarbox" id="changecarbox" style='<?php if(empty($carHistory)): ?>margin: -210px 0 0 -410px;<?php else: ?>margin: -280px 0 0 -410px;<?php endif; ?>'>
    <?php if(!empty($carHistory)): ?><div class="historycar">
            <div class="changecar-title">选择历史车型</div>
            <div class="historycarcon">
                <?php if(is_array($carHistory)): $i = 0; $__LIST__ = array_slice($carHistory,0,4,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc): $mod = ($i % 2 );++$i;?><div class="car-a" id="<?php echo ($i); ?>"><a href="javascript:;" brandId="<?php echo ($voc["brandId"]); ?>" seriesId="<?php echo ($voc["seriesId"]); ?>" modelId="<?php echo ($voc["modelId"]); ?>" onclick="carHistory(this)"><?php echo ($voc["carName"]); ?></a></div><?php endforeach; endif; else: echo "" ;endif; ?>
                <div style="clear: both;margin-bottom:0;"></div>
            </div>
        </div><?php endif; ?>
    <div class="changecar">
        <div class="changecar-title">选择新车型</div>
        <div class="changecarcon">
            <div class="mt-d-step">
                <div class="mitem mstep1 current">
                    <i>1</i>
                    选择品牌
                </div>
                <div class="mitem mstep2">
                    <i>2</i>
                    选择车系
                </div>
                <div class="mitem mstep3">
                    <i>3</i>
                    选择年款
                </div>
            </div>
            <div class="c-step-1">
                <div class="hot-brand-slect">
                    <div class="car-change-title">品牌首字母选择：</div>
                    <div class="car-change-kind">
                        <div class="car-kind current">热门</div>
                        <div class="car-kind">A</div>
                        <div class="car-kind">B</div>
                        <div class="car-kind">D</div>
                    </div>
                </div>
                <ul class="car-c-con">
                    <li>
                        <div class="cars-brand">
                            <i class="cars-icon" style="background-image: url(<?php echo C('UPLOAD_ROOT');?>/Brand/Logo/aodi.jpg)">&nbsp;</i>
                            <span>奥迪</span>
                        </div>
                    </li>
                    <li>
                        <div class="cars-brand">
                            <i class="cars-icon" style="background-image: url(<?php echo C('UPLOAD_ROOT');?>/Brand/Logo/aodi.jpg)">&nbsp;</i>
                            <span>大众</span>
                        </div>
                    </li>
                    <li>
                        <div class="cars-brand">
                            <i class="cars-icon" style="background-image: url(<?php echo C('UPLOAD_ROOT');?>/Brand/Logo/aodi.jpg)">&nbsp;</i>
                            <span>福特</span>
                        </div>
                    </li>
                    <li>
                        <div class="cars-brand">
                            <i class="cars-icon" style="background-image: url(<?php echo C('UPLOAD_ROOT');?>/Brand/Logo/aodi.jpg)">&nbsp;</i>
                            <span>别克</span>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="c-step-2" style="display:none;">
                <div class="has-select clearfix">
                    <div class="car-change-title"><b>已选车型:</b></div>
                    <div class="car-change-con clearfix">
                        <div class="cars">别克<b class="del" data="别克" step="1">×</b></div>
                    </div>
                </div>
                <div class="has-con-box">
                    <div class="has-select-con clearfix">
                        <h5>通用别克:</h5>
                        <div class="cars">君威</div>
                        <div class="cars">别克GL8</div>
                        <div class="cars">昂科拉ENCORE</div>
                        <div class="cars">林荫大道</div>
                        <div class="cars">君越</div>
                        <div class="cars">凯越</div>
                        <div class="cars">英朗</div>
                    </div>
                </div>
            </div>
            <div class="c-step-3" style="display:none;">
                <div class="has-select clearfix">
                    <div class="car-change-title"><b>已选车型:</b></div>
                    <div class="car-change-con clearfix">
                        <div class="cars">
                            别克
                            <b class="del" data="别克" step="1">×</b>
                        </div>
                        <div class="cars">
                            君威
                            <b class="del" data="别克" step="1">×</b>
                        </div>
                    </div>
                </div>
                <div class="has-con-box">
                    <div class="has-select-con clearfix">
                        <h5>通用别克:</h5>
                        <div class="cars">君威</div>
                        <div class="cars">别克GL8</div>
                        <div class="cars">昂科拉ENCORE</div>
                        <div class="cars">林荫大道</div>
                        <div class="cars">君越</div>
                        <div class="cars">凯越</div>
                        <div class="cars">英朗</div>
                    </div>
                </div>
            </div>
            <div class="c-step-4" style="display:none;">
                <p class="notice">
                    <i></i>
                    车型选择成功
                </p>
                <div class="carresult">
                    <strong>已选车型</strong>
                    <span class="r-pinpai">福特</span>
                    <span class="r-chexi">福克斯</span>
                    <span class="r-pailiang">福克斯 福克斯三厢 2012款 新一代1.6双离合 舒适型</span>
                </div>
                <div id="tofilldistance" class="carbtn">
                    <span id="mt_c_prev">返回重选</span>
                    <span id="mt_c_next">下一步</span>
                </div>
            </div>
        </div>
        <div class="scoll_speak">没找到我的车型？滚动查看更多</div>
    </div>
    <div id="changecarclose">关闭</div>

</div>
<div class="changecarboxbg"></div>

<script type="text/javascript">

    $(function(){
        $("#2,#4,#6,#8").css("margin-right","0");

        $(".scoll_speak").show();

        $(".changecarboxbg").height($(window).height());

        var brand_id,series_id,model_id,car_name,series_name;

        $(".car-select").click(function(){
            $.ajax({
                type: "GET",
                url: "/index/getCarBrand",
                cache: false,
                dataType:"json",

                success: function(data){

                    var html = '';

                    var lihtml = '';
                    $.each(data,function(i,v){

                        if(i!="A"){
                            html += '<div class="car-kind">'+i+'</div>';
                            lihtml += '<li style="display:none;">';
                        }else{
                            html += '<div class="car-kind current">'+i+'</div>';
                            lihtml += '<li>';
                        }
                        $.each(v,function(k,u){
                            lihtml += '<div class="cars-brand" brand-id="'+u.brand_id+'"><i class="cars-icon" style="background-image: url(<?php echo C('UPLOAD_ROOT');?>/Brand/Logo/'+u.brand_logo+')">&nbsp;</i><span>'+u.brand_name+'</span></div>';
                        })
                        lihtml +='</li>';
                    })
                    $(".car-change-kind").html(html);
                    $(".car-c-con").html(lihtml);
                }
            });
            $(".c-step-1").show().siblings().hide();
            $(".mt-d-step").show();
            $(".changecarboxbg").fadeIn();
            $("#changecarbox").slideDown();

        });




        $(document).on("click",".car-change-kind .car-kind",function(){
            $(this).addClass("current").siblings().removeClass("current");
            $(".car-c-con li").eq($(this).index()).show().siblings().hide();

        });

        $(document).on("click",".c-step-1 .cars-brand",function(){
            $(".c-step-2 .car-change-con .cars").eq(0).html($(this).find("span").text()+'<b class="del" data="'+$(this).find("span").text()+'" step="1">×</b>');
            $(".c-step-3 .car-change-con .cars").eq(0).html($(this).find("span").text()+'<b class="del" data="'+$(this).find("span").text()+'" step="1">×</b>');
            var html = '';
            brand_id = $(this).attr("brand-id");
            car_name = $(this).find("span").text();
            $.ajax({
                type: "POST",
                url: "/index/getCarSeries",
                cache: false,
                dataType:"json",
                data:{brand_id:brand_id},
                success: function(data){

                    html += '<div class="has-select-con clearfix">';
                    $.each(data,function(i,v){
                        html += '<div class="cars_series" series-id="'+v.series_id+'">'+v.series_name+'</div>';
                    })
                    html += '</div>';

                    $(".c-step-2 .has-con-box").html(html);
                }
            });
            $(".mt-d-step .mitem").eq(1).addClass("current").siblings().removeClass("current");
            $(".c-step-1").hide();
            $(".c-step-2").show();

        });

        $(document).on("click",".c-step-2 .has-select-con .cars_series",function(){
            $(".c-step-3 .car-change-con .cars").eq(1).html($(this).text()+'<b class="del" data="'+$(this).text()+'" step="2">×</b>');
            series_id = $(this).attr("series-id");
            series_name = $(this).text();
            var html = '';
            $.ajax({
                type: "POST",
                url: "/index/getCarModel",
                cache: false,
                dataType:"json",
                data:{series_id:series_id},
                success: function(data){

                    html += '<div class="has-select-con clearfix">';
                    if(data){
                       $.each(data,function(i,v){
                        html += '<div class="cars" model-id="'+v.model_id+'">'+v.model_name+'</div>';
                       }) 
                    }else{   //车型数据为空处理
                        html += '该车系下暂无录入车型';
                    }
                    
                    html += '</div>';

                    $(".c-step-3 .has-con-box").html(html);
                }
            });
            $(".mt-d-step .mitem").eq(2).addClass("current").siblings().removeClass("current");
            $(".c-step-2").hide();
            $(".c-step-3").show();

        });

        $(document).on("click",".c-step-3 .has-select-con .cars",function(){
            model_id = $(this).attr("model-id");
            $(".c-step-4 .r-pinpai").text(car_name);
            $(".c-step-4 .r-chexi").text(series_name);
            $(".c-step-4 .r-pailiang").text($(this).text());
            $(".c-step-3").hide();
            $(".c-step-4").show();
            $(".scoll_speak").hide();

            car_name = car_name+' -> '+series_name+' -> '+$(this).text();
        });

        $(document).on("click",".del",function(){
            $(".c-step-"+$(this).attr("step")).show().siblings().hide();
            $(".mt-d-step").show();
            $(".mt-d-step .mitem").eq($(this).attr("step")-1).addClass("current").siblings().removeClass("current");

        });

        $("#mt_c_next").click(function(){
            $.ajax({
                type: "POST",
                url: "/index/saveCarData",
                cache: false,
                dataType:"json",
                data:{series_id:series_id,brand_id:brand_id,model_id:model_id,car_name:car_name},
                success: function(data){
                    if(data.status){

                        $("#changecarbox").hide();
                        $(".changecarboxbg").hide();
                        var go = $('#go').text();
                        if(go){
                            document.location.href=go;
                        }else{
                            //window.location.reload();
                            window.location.href = '/carservice/order';
                        }
                    }else{
                        alert(data.msg);
                        $(".c-step-1").show().siblings().hide();
                        $(".mt-d-step").show();
                    }
                }
            });

        });

        $("#mt_c_prev").click(function(){
            $('.car-select').click();
        });

        $("#changecarclose").click(function(){
            $("#changecarbox").slideUp();
            $(".changecarboxbg").fadeOut();
        });
        //修改车型
        $('#snav u').click(function(){
            $('.car-select').click();
        })

    });


    function carHistory(e){
        $.ajax({
            type: "POST",
            url: "/index/saveCarData",
            cache: false,
            dataType:"json",
            data:{series_id:$(e).attr("seriesId"),brand_id:$(e).attr("brandId"),model_id:$(e).attr("modelId"),car_name:$(e).text()},
            success: function(data){
                if(data.status){
                    $("#changecarbox").hide();
                    $(".changecarboxbg").hide();
                    var go = $('#go').text();
                    if(go){
                        document.location.href=go;
                    }else{
                        window.location.reload();
                    }
                }else{
                    alert(data.msg);
                    $(".c-step-1").show().siblings().hide();
                    $(".mt-d-step").show();
                }
            }
        });
    }
</script>

<?php if(empty($carHistory)): ?><script type="text/javascript">
        //检测有无选过车型
        function checkCar(go){
            $('#go').text(go);
            //$("#changecarclose").hide();
            $('.car-select').click();
        }
    </script>
    <?php else: ?>
    <script type="text/javascript">
        //检测有无选过车型
        function checkCar(go){
            document.location.href = go;
        }
    </script><?php endif; ?>

<?php if(!empty($choseCar)): if(empty($noshow)): ?><script type="text/javascript">
            $(function(){
                $(".car-select").click();
            });
        </script><?php endif; ?>

    <?php if(empty($noclose)): ?><script type="text/javascript">
            $(function(){
                $("#changecarclose").hide();
            });
        </script><?php endif; endif; ?>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?60969e039f9a2a7252a22e6e27e9f16f";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</body>
</html>