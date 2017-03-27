<?php
class EmptyAction extends CommonAction{
	function _empty(){
		header("HTTP/1.0 404 Not Found");
		$this->display('Empty:index');
	}
	// 404
	function index() {
		header("HTTP/1.0 404 Not Found");
		$this->display('Empty:index');
	}
}