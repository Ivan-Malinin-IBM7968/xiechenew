<?php
//记账保险模型
	class NoteinsuranceModel extends NoteModel {
		protected $_auto = array ( 
			array('start_date','strtotime',1,'function'),
			array('end_date','strtotime',1,'function'),
		);
		
		//数据格式化插入
		public function data_format_insert($insert_id){
				$data = $this->create();
				if($data === false){
					js_back($this->getError());
				}
				$data['detail'] = $this-> Insurance_detail_distribute($_POST);
				return $data;
		}	
	    //数据格式化插入
		public function appdata_format_insert(){
			$data = $this->create();
			if($data === false){
				$xml_content .= "<status>1</status><desc>数据错误</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
			}
			$data['detail'] = $this-> Insurance_detail_distribute($_POST);
			return $data;
		}
		
		public function Insurance_detail_distribute($data){
			if(is_array($data)){
				if($data['InsurItem']){
					foreach($data['InsurItem'] AS $key=>$val){
						$detail_maintain[]=array(		
							'Item' => $data['InsurItem'][$key],
							'Val' => $data['InsurVal'][$key],
							'Pric' => $data['InsurPric'][$key],
							'Remark' => $data['InsurRemark'][$key],
						);
					}
					return serialize($detail_maintain);
				}else{
				return '';
				}	
			}
		}
		
		
	}
?>