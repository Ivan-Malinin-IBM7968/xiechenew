INSERT INTO `tp_xieche`.`xc_serviceitem` (`id`, `service_item_id`, `name`, `si_level`) VALUES (NULL, '1', '其他', '1');

DELETE FROM `tp_xieche`.`xc_serviceitem` WHERE `xc_serviceitem`.`id` = 13;

ALTER TABLE `xc_serviceitem` ADD `itemorder` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序：数字越大越靠前' AFTER `si_level` ,
ADD INDEX ( `itemorder` );