<?php
class SmsAction extends CommonAction {
    function __construct() {
		parent::__construct();
		
	}
    public function index(){
        if (isset($_REQUEST['phones']) and !empty($_REQUEST['phones'])){
            $condition['phones'] = $_REQUEST['phones'];
            $this->assign('phones',$_REQUEST['phones']);
        }
        $model_sms = D(GROUP_NAME.'/Sms');
        // 计算总数
        $count = $model_sms->where($condition)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        $map['phones'] = $_REQUEST['phones'];
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}

		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_sms->where($condition)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();
    }

	function make_semiangle($str){
		$arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
		'５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
		'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
		'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
		'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
		'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
		'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
		'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
		'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
		'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
		'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
		'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
		'ｙ' => 'y', 'ｚ' => 'z',
		'（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
		'】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
		'‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',     '》' => '>',
		'％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
		'：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
		'；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
		'”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
		'　' => ' ');
		return strtr($str, $arr);
	}
    
    public function send_sms(){
        $id = isset($_POST['id'])?$_POST['id']:'0';
        $model_sms = D(GROUP_NAME.'/Sms');
	
        if ($id){
            $smsinfo = $model_sms->find($id);
			$smsinfo['content'] = $this->make_semiangle($smsinfo['content']);
            $send_add_order_data = array(
				'phones'=>$smsinfo['phones'],
				//'phones'=>'13681971367',
				'content'=>$smsinfo['content'],
					
			);
			//echo '<pre>';print_r($send_add_order_data);exit;
    		$return_data = $this->curl_sms($send_add_order_data);
					
			$now = time();
			$send_add_order_data['sendtime'] = $now;
			$model_sms->add($send_add_order_data);

    		//$return_data = $this->curl_sms_other($send_add_order_data);
    		//print_r($return_data);exit;
    		echo 1;exit;
        }
        echo 0;exit;
    }

    /*
        @author:chf
        @function:发送短信(新老通道)type=1老 type=2新
        @time:2014-01-20
    */
    public function send_smstype(){
        $id = isset($_POST['id'])?$_POST['id']:'0';
        $model_sms = D(GROUP_NAME.'/Sms');
        $type = $_POST['type'];
        if ($id){
            $smsinfo = $model_sms->find($id);
            $smsinfo['content'] = $this->make_semiangle($smsinfo['content']);
            $send_add_order_data = array(
                'phones'=>$smsinfo['phones'],
                'content'=>$smsinfo['content'],
            );
          
            $return_data = $this->curl_smstype($send_add_order_data,$type);
                    
            $now = time();
            $send_add_order_data['sendtime'] = $now;
            $model_sms->add($send_add_order_data);
            echo 1;exit;
        }
        echo 0;exit;
    }




    
    public function send_sms_more(){exit;
        $id = 3065;
        $noin = '3611,3602,3468,3440,3315,3208,3200,3171,3105,3103,3102';
        //$id = 33 	;
        //$noin = array(34,35);
        $model_sms = D(GROUP_NAME.'/Sms');
        $model_smslog = D(GROUP_NAME.'/Smslog');
        if ($id){
            $map['id'] = array('gt',$id);
            //$smsinfos = $model_sms->where($map)->select();
            $smsinfos = $model_sms->where($map)->select();
            //echo $model_sms->getLastSql();exit;
            if ($smsinfos){
                foreach ($smsinfos as $_k=>$_v){
                    if (!in_array($_v['id'],$noin)){
                        $send_add_order_data = array(
            				'phones'=>$_v['phones'],
            				'content'=>$_v['content'],
            			);
            			$return_data = $this->curl_sms($send_add_order_data);
            			$data['mobile'] = $_v['phones'];
            			$data['return_str'] = $return_data;
            			$model_smslog->add($data);
            			//echo $model_smslog->getLastSql();exit;
                    }
                    
                }
            }
			//echo '<pre>';print_r($send_add_order_data);exit;
    		//$return_data = $this->curl_sms_other($send_add_order_data);
    		//print_r($return_data);exit;
    		echo 'OK';exit;
        }
    }
}
?>