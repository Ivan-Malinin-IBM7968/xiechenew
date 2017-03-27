<?php
class CommonModel extends Model {
	//返回用户ID
		public function GetUserId(){
			return isset($_SESSION[C('USER_ID')])?$_SESSION[C('USER_ID')]:0;
		}
		
		
		//根据模块名查询对应的记账类型ID  
		//$type  获取 值的方法； 
		//  $val  如果type==value val为手动输入值  如果type==get val为get方式对应的字段
		public function GetNoteType($type='',$val=''){
			switch ($type) {
				case 'ModelName':
					$note_type = $this->getModelName();
				break;
				case 'get':
					$note_type = $_GET[$val];
				break;
				case 'value':
					$note_type = $val;
				break;
			}			
			if($note_type_id = array_search($note_type,C('NOTE_TYPE_TITLE'))){
				return $note_type_id;
			}else{
				return false;
			}
		}
}