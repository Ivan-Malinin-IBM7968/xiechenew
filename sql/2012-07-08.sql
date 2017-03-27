ALTER TABLE `xc_comment` CHANGE `user_name` `user_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名';

ALTER TABLE `xc_noteoil` ADD `isfull` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '是否加满：1加满；0没有';

ALTER TABLE `xc_order` ADD `iscomment` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '是否评价:0未评价；1已评价';

CREATE TABLE IF NOT EXISTS `xc_oilwear` (
  `oid` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `u_c_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL COMMENT '品牌ID',
  `series_id` int(11) NOT NULL COMMENT '车系ID',
  `model_id` int(11) NOT NULL COMMENT '车型ID',
  `oilwear` float(3,2) NOT NULL COMMENT '平均油耗（单位：升/100公里）',
  PRIMARY KEY (`oid`),
  KEY `u_c_id` (`u_c_id`),
  KEY `brand_id` (`brand_id`),
  KEY `series_id` (`series_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='平均油耗表' AUTO_INCREMENT=4 ;