<?php
//预约服务项目
class ServiceitemAction extends CommonAction {	
	
	public function index() {
			echo '显示所有的服务项目分类';	
			$this->display();
	}
	public function insertservice(){
		$this->insert();
	}
	public function addserviceitem(){
		$model = D('Serviceitem');
		$list = $model->where("si_level=0")->select();
		$this->assign('list',$list);
		$this->display();
	}
	public function insertserviceitem(){
		$this->insert();
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