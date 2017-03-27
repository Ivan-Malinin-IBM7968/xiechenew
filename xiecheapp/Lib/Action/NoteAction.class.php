<?php
// 本类由系统自动生成，仅供测试用途
class NoteAction extends CommonAction {
	
	function __construct() {
		parent::__construct();
		if( true !== $this->login()){
			exit;
		}
	}
	
	//记账首页
    public function index(){
        header("Content-Type:text/html; charset=utf-8");
        Cookie::set('_currentUrl_', __SELF__);
		$this->display();

    }
    //记账菜单，根据get值获取相应的表单模板
	public function notemenu(){
		//获取记账类型
		if($_GET['type']){
			$check = array_search($_GET['type'], C('NOTE_TYPE_TITLE')); //检测记账类型
			if($check !== false){
				$model = D('Notemain'); 
				$uid = $model->GetUserId(); //获取用户名
				$model = D('Membercar');    
				$select_u_c_id = $model->where("uid=$uid and status='1'")->select(); //查询
				$this->assign('select_u_c_id',$select_u_c_id);
				$this->display($_GET['type']);
			}else{
				$this->error('非法操作！');
			}
		}else{
			$this->error('非法操作！');
		}
	}
	//插入数据
	public function add_note(){
		if(empty($_POST)){   //判断提交
			$this->error('非法操作！',__APP__);
		}
		$check = array_search($_GET['type'], C('NOTE_TYPE_TITLE')); //检测
		if($check !== false){
			$model_Notemain = D('Notemain'); 
			$model_Notemain->check_odometer();
			$data = $model_Notemain->data_format_insert($_GET['type']);  //格式化数据
			$result =  $model_Notemain->relation($_GET['type'])->add($data);  //关联插入
			if ($_GET['type']=='Noteoil'){
			    $this->get_per_oilwear($data['u_c_id']);
			}
			$this->action_tip($result);  //提示
		}else{
			redirect(__URL__);   //重定向
		}
	}
    //插入数据
	public function appadd_note(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
		if(empty($_POST)){   //判断提交
			//$this->error('非法操作！',__APP__);
			$xml_content .= "<status>1</status><desc>数据错误</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
		$check = array_search($_GET['type'], C('NOTE_TYPE_TITLE')); //检测
		if($check !== false){
			$model_Notemain = D('Notemain'); 
			$model_Notemain->appcheck_odometer();
			$data = $model_Notemain->appdata_format_insert($_REQUEST['type']);  //格式化数据
			$result =  $model_Notemain->relation($_REQUEST['type'])->add($data);  //关联插入
			//echo $model_Notemain->getLastSql();exit;
			if ($_GET['type']=='Noteoil'){
			    $this->get_per_oilwear($data['u_c_id']);
			}
			//$this->action_tip($result);  //提示
			$xml_content .= "<status>0</status><desc>保存成功</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}else{
			//redirect(__URL__);   //重定向
			$xml_content .= "<status>1</status><desc>数据错误</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
	}
	//平均油耗计算   
	public function get_per_oilwear($u_c_id){
	    $model_notmain = D('Notemain ');
	    $model_noteoil = D('Noteoil ');
	    $model_membercar = D('Membercar');
	    $model_oilwear = D('Oilwear');
	    $oilmap['xc_noteoil.u_c_id'] = $u_c_id;
	    $oilmap['xc_notemain.note_type'] = 1;
	    $oilmap['xc_notemain.main_del'] = 0;
	    $noteoil_arr = $model_noteoil->where($oilmap)->join("xc_notemain ON xc_notemain.note_id=xc_noteoil.n_id")->order("odometer ASC")->select();
	    $mainmap['note_type'] = 1;
	    
	    if (!empty($noteoil_arr) and count($noteoil_arr)>1){
	        $each_per_oilwear = array();
	        foreach ($noteoil_arr as $k=>$v){
	            
	            if (isset($noteoil_arr[$k-1]) and !empty($noteoil_arr[$k-1]) and ($v['odometer'] - $noteoil_arr[$k-1]['odometer'])>0 and $v['quantity']>0){
	                $per_oilwear = 100*$v['quantity']/($v['odometer'] - $noteoil_arr[$k-1]['odometer']);
	                $each_per_oilwear[$k]['per_oilwear'] = $per_oilwear;
	                $each_per_oilwear[$k]['per_oilprice'] = $v['unit_price'];
	                $each_per_oilwear[$k]['odometer'] = $v['odometer'] - $noteoil_arr[$k-1]['odometer'];
	                $each_per_oilwear[$k]['quantity'] = $v['quantity'];
	                if ($v['isfull'] == 1 and $noteoil_arr[$k-1]['isfull'] == 1){
	                    $condition['n_id'] = $v['n_id'];
	                    $save_data['last_per_oilwear'] = $per_oilwear;
	                    $model_noteoil->where($condition)->save($save_data);
	                }
	            }
	        }
	        $total_oilwear = 0;
	        $total_oilprice = 0;
	        $total_odometer = 0;
	        $total_quantity = 0;
	        if (!empty($each_per_oilwear)){
	            foreach ($each_per_oilwear as $_k=>$_v){
	                $total_oilwear += $_v['per_oilwear'];
	                $total_oilprice += $_v['per_oilprice'];
	                $total_odometer += $_v['odometer'];
	                $total_quantity += $_v['quantity'];
	            }
	        }
	        $total_per_oilwear = $total_oilwear/count($each_per_oilwear);
	        $total_per_oilprice = $total_oilprice/count($each_per_oilwear);
	        $membercarinfo = $model_membercar->find($u_c_id);
            $data['oilwear'] = $total_per_oilwear;
            $data['per_oilprice'] = $total_per_oilprice;
            $data['total_tripmile'] = $total_odometer;
            $data['total_quantity'] = $total_quantity;
            $data['u_c_id'] = $u_c_id;
            $data['brand_id'] = $membercarinfo['brand_id'];
            $data['series_id'] = $membercarinfo['series_id'];
            $data['model_id'] = $membercarinfo['model_id'];
            $map['u_c_id'] = $u_c_id;
            if ($model_oilwear->where($map)->find()){
                $model_oilwear->where($map)->save($data);
            }else{
                $model_oilwear->add($data);
            }
	    }
	}
	//计算平均油耗
    public function run_per_oilwear(){
	    $model_noteoil = D('Noteoil ');
	    $model_membercar = D('Membercar');
	    $model_oilwear = D('Oilwear');
	    $u_c_id_arr = $model_noteoil->field('distinct u_c_id')->select();
	    if (!empty($u_c_id_arr)){
	        foreach ($u_c_id_arr as $cid){
	            $u_c_id = $cid['u_c_id'];
	            $noteoil_per_car = $model_noteoil->where("u_c_id=$u_c_id")->select();
	            if ($noteoil_per_car){
	                $oil_all = 0;
	                $start_odometer = 0;
	                $end_odometer = 0;
	                $first_k = 0;
	                $last_k = 0;
	                foreach ($noteoil_per_car as $k=>$v){
	                    $oil_all +=$v['quantity'];
	                    if ($start_odometer==0){
	                        $first_k = $k;
	                        $start_odometer = $v['odometer'];
	                    }else{
    	                    if ($start_odometer>$v['odometer']){
    	                        $first_k = $k;
    	                        $start_odometer = $v['odometer'];
    	                    }
	                    }
	                     if ($end_odometer==0){
	                        $last_k = $k;
	                        $end_odometer = $v['odometer'];
	                    }else{
    	                    if ($end_odometer<$v['odometer']){
    	                        $last_k = $k;
    	                        $end_odometer = $v['odometer'];
    	                    }
	                    }
	                }
	                $oil_all = $oil_all - $noteoil_per_car[$last_k]['quantity'];
	                $odometer_diff = $end_odometer - $start_odometer;
                    if ($odometer_diff>0 ){
                        $data['oilwear'] = 100*(round($oil_all/$odometer_diff,3));
                        $data['u_c_id'] = $u_c_id;
                        $membercarinfo = $model_membercar->find($u_c_id);
                        $data['brand_id'] = $membercarinfo['brand_id'];
                        $data['series_id'] = $membercarinfo['series_id'];
                        $data['model_id'] = $membercarinfo['model_id'];
                        if ($model_oilwear->where("u_c_id=$u_c_id")->find()){
                            $model_oilwear->where("u_c_id=$u_c_id")->save($data);
                        }else{
                            $model_oilwear->add($data);
                        }
                    }
	            }
	        }
	    }
	}
	//删除记录
	public function Delnote(){
	    $note_id=$_POST['note_id']?intval($_POST['note_id']):'0';
	    if ($note_id) {
	    	$model_notemain = D('Notemain');
	    	$condition['note_id'] = $note_id;
	    	$data['main_del'] = 1;
	    	if($model_notemain->where($condition)->save($data)){
	    	    echo 1;
	    	}else {
	    	    echo -1;
	    	}
	    }
	    exit;
	}
	/*
	 * 显示记账内容修改的页面，并输出数据到模板
	 * 
	 */
	public function EditNote(){
		//传入参数检测
		$get_type=$_GET['type']?intval($_GET['type']):null;   
		$get_note_id=$_GET['note_id']?intval($_GET['note_id']):null;
		if($_GET['type'] && $_GET['note_id']){			
			$check = array_search($_GET['type'], C('NOTE_TYPE_TITLE'));  //检测
			$model_notemain = D('Notemain');
			//关连查询
			$list_note = $model_notemain->relation($_GET['type'])->where("uid=$_SESSION[uid] AND note_id=$get_note_id")->find();
			if(($check !== false) && $list_note){
				$model = D('Notemain');
				$uid = $model->GetUserId();
				$model = D('Membercar');
				$select_u_c_id = $model->where("uid=$uid")->find();
				$this->assign('select_u_c_id',$select_u_c_id);
				$this->assign('list_note',$list_note);
				//dump($select_u_c_id);
				
				//输出数据处理
				if($list_note[$_GET['type']]['detail']){
					$list_note[$_GET['type']]['detail'] = unserialize($list_note[$_GET['type']]['detail']);
					if($list_note[$_GET['type']]['detail'][0]['Midl']){
						foreach($list_note[$_GET['type']]['detail'] AS $k=>$v){
							if($list_note[$_GET['type']]['detail'][$k]['Midl']){
								$model = D('Maintainclass');
								$tmp_middle = $list_note[$_GET['type']]['detail'][$k]['Midl'];
								$tmp_Midl = $model->where("ItemID=$tmp_middle")->find();
								$list_note[$_GET['type']]['detail'][$k]['Midl_name'] = $tmp_Midl['ItemName'];
							}							
							if($list_note[$_GET['type']]['detail'][$k]['Small']){
								$model = D('Maintainclass');
								$tmp_small = $list_note[$_GET['type']]['detail'][$k]['Small'];
								$tmp_Small = $model->where("ItemID=$tmp_small")->find();								
								$list_note[$_GET['type']]['detail'][$k]['Small_name'] = $tmp_Small['ItemName'];
							}
							
						}
					}
					
					$this->assign('list_detail',$list_note[$_GET['type']]['detail']);
				}
				//dump($list_note);
				//dump($list_note[$_GET['type']]['detail']);
				$this->display($_GET['type'].'_e');
			}else{
				$this->error('非法操作！');
			}
		}else{
			$this->error('非法操作！');
		}
		
	}
	/*
	 * 提交记账内容修改
	 * 
	 */
	public function SaveNote() {
		$get_note_id=$_GET['note_id']?intval($_GET['note_id']):null;
			if(empty($_POST)){
			$this->error('非法操作！',__APP__);
		}
		$check = array_search($_GET['type'], C('NOTE_TYPE_TITLE'));
		if($check !== false){
			$model_Notemain = D('Notemain'); 
			$data = $model_Notemain -> data_format_insert($_GET['type']);
			$data['note_id']=$get_note_id;
			//dump($data);
			$result =  $model_Notemain->relation($_GET['type'])->save($data);
			//dump($result);
			$this->action_tip($result);
		}else{
			redirect(__URL__);
		}
	}
	
	
	
	

	
	
	
	
	
	//配件明细ajax输出
	public function maintaindetail(){
		//大分类生成中分类
		if ($_GET['select'] != null){
		  	$str=urldecode($_GET['select']); //解码 		  
			if ($_GET['select'] == 0){
			$MidClassSelect ='<select name="TXT_MidlClass[]" id="TXT_MidlClass" style="width:72px;">';
			$MidClassSelect.= "<option value='0'></option>";
			$MidClassSelect.= "</select>";
			echo $MidClassSelect;
			return;
			}
			$Maintainclass = M('Maintainclass');
			$rs = $Maintainclass ->where("PItemID ='$_GET[select]'")->select();	
			if ($rs){
				$MidClassSelect ="<select name=\"TXT_MidlClass[]\" id=\"TXT_MidlClass\" style=\"width:72px;\" onchange=\"RequestTwo(this, '{$_GET['smallClass']}')\"><option value='0'></option>";
				foreach ($rs AS $key=>$val){
					$MidClassSelect.= '<option value="'.$val['ItemID'].'">'.$val['ItemName'].'</option>';
				}
				$MidClassSelect.='</select>';
			}
			echo $MidClassSelect;
			return;
		}
		
		
		
	//根据中类生成小类
	if ($_GET['selectTwo'] != null)
	{
		if ($_GET['selectTwo'] == 0)
		{
			$smallClassSelect ='<select name="TXT_SmallClass[]" id="TXT_SmallClass" style="width:58px;">';
			$smallClassSelect.= "<option value='0'></option>";
			$smallClassSelect.= "</select>";
			echo $smallClassSelect;
			return;
		}
			$Maintainclass = M('Maintainclass');
			$rs = $Maintainclass ->where("PItemID ='$_GET[selectTwo]'")->select();
		if ($rs)
		{
			$smallClassSelect ='<select name="TXT_SmallClass[]" id="TXT_SmallClass" style="width:59px;"><option value='.'0'.'></option>';
				foreach ($rs AS $key=>$val){
					$smallClassSelect.= '<option value="'.$val['ItemID'].'">'.$val['ItemName'].'</option>';
				}
			$smallClassSelect.='</select>';
		}
		echo $smallClassSelect;
		return;
	}
	}
    
}



