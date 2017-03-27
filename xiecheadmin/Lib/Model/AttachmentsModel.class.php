<?php
// 附件模型
class AttachmentsModel extends CommonModel {		
		//上传附件格式化数据
		
		public function data_format_insert($tmp_data) {
			if(is_array($tmp_data)){
				foreach($tmp_data AS $key=>$val){
				$data[$key]['attachments_name'] = $tmp_data[$key]['savename'];
				$data[$key]['attachments_ext'] = $tmp_data[$key]['extension'];
				$data[$key]['upload_time'] = time();
				$this->add($data[$key]);
				}
				return true;
			}else{
				return false;
			}
			
		}
	
	
}