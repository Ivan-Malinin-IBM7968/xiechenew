<?php


$t->set("config",$config);

$t->set("sess",$sess);

$t->set("is_login",$is_login,true);
$t->set("qg_start",$qg_start,true);
$t->set("qg_end",$qg_end,true);
$t->set("the_s_time",$the_s_time);
$t->set("the_e_time",$the_e_time);
$t->set("next_s_time",$next_s_time);
$t->set("next_e_time",$next_e_time);
$t->set("ipvcode",$ipvcode);
$t->set("server_time",date("Y-m-d H:i:s",$now));

$t->output();
