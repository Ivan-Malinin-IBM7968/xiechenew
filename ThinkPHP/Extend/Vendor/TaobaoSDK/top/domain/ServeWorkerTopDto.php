<?php

/**
 * 服务人员基本信息
 * @author auto create
 */
class ServeWorkerTopDto
{
	
	/** 
	 * 用户头像，请BASE64传过来，这边也将以BASE64编码返回，且不做URL过滤.
	 **/
	public $avatarUrl;
	
	/** 
	 * 差评数,不可更改
	 **/
	public $badCount;
	
	/** 
	 * 籍贯
	 **/
	public $birthplace;
	
	/** 
	 * 市
	 **/
	public $cityCode;
	
	/** 
	 * 联系地址
	 **/
	public $contactAddress;
	
	/** 
	 * 联系电话
	 **/
	public $contactMobile;
	
	/** 
	 * 好评数,不可更改
	 **/
	public $goodCount;
	
	/** 
	 * 身份证号码
	 **/
	public $idCard;
	
	/** 
	 * 工作经验年限(单位为年)
	 **/
	public $jobExperience;
	
	/** 
	 * 工作状态
	 **/
	public $jobStatus;
	
	/** 
	 * 性别 男为true，女为false
	 **/
	public $male;
	
	/** 
	 * 服务者名称
	 **/
	public $name;
	
	/** 
	 * 省
	 **/
	public $provinceCode;
	
	/** 
	 * 县
	 **/
	public $regionCode;
	
	/** 
	 * 服务次数,不可更改
	 **/
	public $serviceCount;
	
	/** 
	 * 学历(0:未知,1:小学，2:初中，3:高中，4:中专，5:大专,6:本科)
	 **/
	public $workerEducation;
	
	/** 
	 * ID
	 **/
	public $workerId;	
}
?>