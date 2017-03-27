<?php
//   Note 主表模型
class NotemainModel extends NoteModel{
	//自动验证
		protected $_validate = array(
		array('u_c_id','require','名称必须'),
		array('happen_time','require','时间必须'),
		array('odometer','require','公里数必须'),
		array('total_cost','check_total_cost','总费用必须',1,'function'),
		//array('odometer','check_odometer','公里数必须或填写的数值错误',0,'callback'),
		);
	//自动完成
		protected $_auto = array ( 
			array('submit_time','time',1,'function'),
			array('happen_time','strtotime',1,'function'),
		);
	//关系模型
		protected $_link = array(
        	'Notemaintain'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Notemaintain',
                'class_name'  =>'Notemaintain',
				'foreign_key'=>'n_id',
				),
			'Notebeautify'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Notebeautify',
                'class_name'  =>'Notebeautify',
				'foreign_key'=>'n_id',
				),
			'Noteearning'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Noteearning',
                'class_name'  =>'Noteearning',
				'foreign_key'=>'n_id',
				),
			'Noteforfeit'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Noteforfeit',
                'class_name'  =>'Noteforfeit',
				'foreign_key'=>'n_id',
				),
			'Noteinsurance'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Noteinsurance',
                'class_name'  =>'Noteinsurance',
				'foreign_key'=>'n_id',
				),
			'Noteother'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Noteother',
                'class_name'  =>'Noteother',
				'foreign_key'=>'n_id',
				),
			'Noteparking'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Noteparking',
                'class_name'  =>'Noteparking',
				'foreign_key'=>'n_id',
				),
			'Notepass'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Notepass',
                'class_name'  =>'Notepass',
				'foreign_key'=>'n_id',
				),
			'Notepurchase'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Notepurchase',
                'class_name'  =>'Notepurchase',
				'foreign_key'=>'n_id',
				),
			'Noterule'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Noterule',
                'class_name'  =>'Noterule',
				'foreign_key'=>'n_id',
				),
			'Noteoil'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Noteoil',
                'class_name'  =>'Noteoil',
				'foreign_key'=>'n_id',
				),	
										
		);
		//记账主表数据格式化插入
		public function data_format_insert($note_type_name){
			$data = $this->create();
			if($data === false){
				js_back($this->getError());
			}
			$data['uid'] = $this->GetUserId();
			$data['cmonth'] = Date('Ym',strtotime($_REQUEST['happen_time']));
			$data['note_type'] = $this->GetNoteType('get','type');
			$model_Note_cate = D($note_type_name);
			$data[$note_type_name] = $model_Note_cate->data_format_insert();
			return $data;
		}
        //记账主表数据格式化插入
		public function appdata_format_insert($note_type_name){
			$data = $this->create();
			if($data === false){
				$xml_content .= "<status>1</status><desc>数据错误</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
			}
			$tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $membermap['tolken'] = $tolken;
	        $membermap['tolken_time'] = array('gt',time());
	        $member = $model_member->where($membermap)->find();
			$data['uid'] = $member['uid'];
			$data['cmonth'] = Date('Ym',strtotime($_REQUEST['happen_time']));
			$data['note_type'] = $this->GetNoteType('get','type');
			$model_Note_cate = D($note_type_name);
			$data[$note_type_name] = $model_Note_cate->appdata_format_insert();
			return $data;
		}
		
		//公里数检测
		public function check_odometer(){
			$post_odometer = isset($_POST['odometer'])?$_POST['odometer']:0;
			if($post_odometer){
				$happen_time = strtotime($_POST['happen_time']);
				$model_noteoil = D('Noteoil');
				$map1['u_c_id'] = array('eq',$_POST['u_c_id']);
				$map1['happen_time'] = array('elt',$happen_time);
				$maxoilinfo = $model_noteoil->field('happen_time, odometer')->where($map1)->order('odometer DESC')->find();
				if ($maxoilinfo['odometer']>=$post_odometer){
				    $error_str = "您".date('Y-m-d',$maxoilinfo['happen_time'])."记录的总里程表数大于你所填写日期(".$_POST['happen_time'].")的总里程表数，请重新填写！";
				    echo "<script>alert('".$error_str."');history.go('-1');</script>";
				    exit;
				}
				$map2['u_c_id'] = array('eq',$_POST['u_c_id']);
				$map2['happen_time'] = array('egt',$happen_time);
				$minoilinfo = $model_noteoil->field('happen_time, odometer')->where($map2)->order('odometer ASC')->find();
			    if (isset($minoilinfo['odometer']) and ($minoilinfo['happen_time']>$happen_time and $minoilinfo['odometer']<=$post_odometer) ){
				    $error_str = "您".date('Y-m-d',$minoilinfo['happen_time'])."记录的总里程表数小于你所填写日期(".$_POST['happen_time'].")的总里程表数，请重新填写！";
				    echo "<script>alert('".$error_str."');history.go('-1');</script>";
				    exit;
				}
			    if (isset($minoilinfo['odometer']) and ($minoilinfo['happen_time']==$happen_time and $minoilinfo['odometer']>$post_odometer) ){
				    $error_str = "您".date('Y-m-d',$minoilinfo['happen_time'])."记录的总里程表数大于你所填写日期(".$_POST['happen_time'].")的总里程表数，请重新填写！";
				    echo "<script>alert('".$error_str."');history.go('-1');</script>";
				    exit;
				}
				return true;
			}else{
				return false;
			}	
		}
		
		//公里数检测
		public function appcheck_odometer(){
		    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
			$post_odometer = isset($_POST['odometer'])?$_POST['odometer']:0;
			if($post_odometer){
				$happen_time = strtotime($_POST['happen_time']);
				$model_noteoil = D('Noteoil');
				$map1['u_c_id'] = array('eq',$_POST['u_c_id']);
				$map1['happen_time'] = array('elt',$happen_time);
				$maxoilinfo = $model_noteoil->field('happen_time, odometer')->where($map1)->order('odometer DESC')->find();
				if ($maxoilinfo['odometer']>=$post_odometer){
				    $error_str = "您".date('Y-m-d',$maxoilinfo['happen_time'])."记录的总里程表数大于你所填写日期(".$_POST['happen_time'].")的总里程表数，请重新填写！";
				    //echo "<script>alert('".$error_str."');history.go('-1');</script>";
				    $xml_content .= "<status>2</status><desc>".$error_str."</desc>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
				}
				$map2['u_c_id'] = array('eq',$_POST['u_c_id']);
				$map2['happen_time'] = array('egt',$happen_time);
				$minoilinfo = $model_noteoil->field('happen_time, odometer')->where($map2)->order('odometer ASC')->find();
			    if (isset($minoilinfo['odometer']) and ($minoilinfo['happen_time']>$happen_time and $minoilinfo['odometer']<=$post_odometer) ){
				    $error_str = "您".date('Y-m-d',$minoilinfo['happen_time'])."记录的总里程表数小于你所填写日期(".$_POST['happen_time'].")的总里程表数，请重新填写！";
				    //echo "<script>alert('".$error_str."');history.go('-1');</script>";
				    $xml_content .= "<status>2</status><desc>".$error_str."</desc>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
				}
			    if (isset($minoilinfo['odometer']) and ($minoilinfo['happen_time']==$happen_time and $minoilinfo['odometer']>$post_odometer) ){
				    $error_str = "您".date('Y-m-d',$minoilinfo['happen_time'])."记录的总里程表数大于你所填写日期(".$_POST['happen_time'].")的总里程表数，请重新填写！";
				    //echo "<script>alert('".$error_str."');history.go('-1');</script>";
				    $xml_content .= "<status>2</status><desc>".$error_str."</desc>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
				}
				return true;
			}else{
				return false;
			}	
		}
		public function aaa(){
			$model = D('Notemaintain');
			return $model->bbb();
			
		}
}



?>