<?php
//记账维修模型
class NotemaintainModel extends NoteModel {	
	public function data_format_insert($insert_id){
			$data = $this->create();			
			if($data === false){
				js_back($this->getError());
			}
			$data['detail'] = $this->MianTain_detail_distribute($_POST);
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
			$data['detail'] = $this->MianTain_detail_distribute($_POST);
			return $data;
		}
	public function MianTain_detail_distribute($data){
		if(is_array($data)){
			if($data['TXT_BigClass']){
				foreach($data['TXT_BigClass'] AS $key=>$val){
					$detail_maintain[]=array(		
						'Big' => $data['TXT_BigClass'][$key],
						'Midl' => $data['TXT_MidlClass'][$key],
						'Small' => $data['TXT_SmallClass'][$key],
						'Pric' => $data['TXT_Pric'][$key],
						'Quantity' => $data['TXT_Quantity'][$key],
						'SubCost' => $data['TXT_SubCost'][$key],
					);
				}
				return serialize($detail_maintain);
			}else{
				return '';
			}	
		}
	}
	public function bbb(){
			return 1111111111111111111111111111111;
	}
}

?>