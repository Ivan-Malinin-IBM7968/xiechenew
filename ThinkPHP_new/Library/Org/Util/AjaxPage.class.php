<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Page.class.php 2712 2012-02-06 10:12:49Z liu21st $

class AjaxPage {
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    protected $config  = array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>'<li style="width: auto; padding: 0px 5px;"> %totalRow% %header% %nowPage%/%totalPage% 页</li> %upPage% %first%  %prePage%  %linkPage% %downPage% %nextPage% %end%');

	protected $configapp  =	array('prev'=>'上一页','header'=>'条记录','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>'%upPage%  <div id=totle-page> %nowPage%/%totalPage% 页</div> %downPage%  %first%  %prePage%  %linkPage%  %nextPage% %end%');  
    // 默认分页变量名
    protected $varPage;


    public function __construct($totalRows,$listRows='',$ajax_func,$parameter='') {
        $this->totalRows = $totalRows;
        $this->ajax_func = $ajax_func;
        $this->parameter = $parameter;
        $this->varPage = C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

	/*
	@author:chf
	@function:网页版AJAX分页
	@time:2014-01-24
	*/
	public function show() {
		if(0 == $this->totalRows) return '';
        $p = $this->varPage;
        $nowCoolPage = ceil($this->nowPage/$this->rollPage);
		

		$url_p = explode("-p-",$_SERVER['REQUEST_URI']);
		if(substr($url_p[0],-1) == "/" ) {
			$url_p[0] = substr($url_p[0] , 0 , -1);
		}

		$url_array = explode("/",$_SERVER['REQUEST_URI']);
		if($url_array[1] == "order" || $url_array[1] == "coupon") {
			if($url_array[2] == "") {
				$is_method = "/index";	
			}
		}
		
		//伪静态.html去掉后加p 再加.html
		
		if(substr($url_p[0],-5) == ".html" ) {
			$url_p[0] = substr($url_p[0] , 0 , -5);
			$IS_HTML_SUFFIX = ".html";
		}
		if(substr($url_p[1],-5) == ".html") {
			$IS_HTML_SUFFIX = ".html";
		}
        $url  =  $url_p[0].$is_method."-".$this->parameter;
		//$url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'/')?'':"/").$this->parameter;
		$parse = parse_url($url);

		$parse['path'] =  substr($parse['path'],0,-5);

         if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="<li><a class='page-trigger' href='javascript:".$this->ajax_func."(".$upRow.")'>".$this->config['prev']."</a></li>";
        }else{
            $upPage="";
        }

		if ($downRow <= $this->totalPages){
            $downPage="<li><a class='page-trigger' href='javascript:".$this->ajax_func."(".$downRow.")'>".$this->config['next']."</a></li>";
        }else{
            $downPage="";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "<li><a class='page-trigger' href='javascript:".$this->ajax_func."(".$preRow.")'>上".$this->rollPage."页</a></li>";
            $theFirst = "<li><a class='page-trigger' href='javascript:".$this->ajax_func."(1)' >".$this->config['first']."</a></li>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<li><a class='page-trigger' href='javascript:".$this->ajax_func."(".$nextRow.")' >下".$this->rollPage."页</a></li>";
            $theEnd = "<li><a class='page-trigger' href='javascript:".$this->ajax_func."(".$theEndRow.")' >".$this->config['last']."</a></li>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                   $linkPage .= "&nbsp;<li><a href='javascript:".$this->ajax_func."(".$page.")'>&nbsp;".$page."&nbsp;</a></li>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= "&nbsp;<li><a href='javascript: void(0);' class='current'>".$page."</a></li>";
                }
            }
        }
        $pageStr  =  str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%first%','%prePage%','%linkPage%','%downPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$theFirst,$prePage,$linkPage,$downPage,$nextPage,$theEnd),$this->config['theme']);
        return $pageStr;
    }

}

 php?>