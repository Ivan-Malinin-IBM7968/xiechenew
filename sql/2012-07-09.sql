CREATE TABLE IF NOT EXISTS `xc_shop_fs_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `shopid` int(11) NOT NULL COMMENT '店铺ID',
  `fsid` int(11) NOT NULL COMMENT '关联ID',
  PRIMARY KEY (`id`),
  KEY `fsid` (`fsid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;