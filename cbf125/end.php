<?php


$t->set("config",$config);

$t->set("sess",$sess);

$t->set("is_login",$is_login,true);
$t->set("qg_start",$qg_start,true);
$t->set("server_time",date("Y-m-d H:i:s",$now));

$t->output();
