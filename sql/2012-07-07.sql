CREATE TABLE IF NOT EXISTS `xc_registerrecommend` (
  `rid` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uid` int(11) NOT NULL COMMENT '注册人ID',
  `register_code` varchar(125) NOT NULL COMMENT '推荐代码',
  `ruid` int(11) NOT NULL COMMENT '推荐人uid',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='注册推荐表' AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `xc_point` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `operator_id` int(11) NOT NULL COMMENT '客服ID',
  `orderid` int(11) NOT NULL COMMENT '订单ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `point_number` int(11) NOT NULL DEFAULT '0' COMMENT '积分数',
  `point_memo` varchar(255) NOT NULL COMMENT '积分备注',
  PRIMARY KEY (`pid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

ALTER TABLE `xc_member` ADD `point_number` INT NOT NULL COMMENT '用户总积分' AFTER `reg_time` ;

ALTER TABLE `xc_comment` ADD `comment_type` TINYINT( 1 ) NOT NULL COMMENT '评价类型：1好评；2中评；3差评' AFTER `comment` ;