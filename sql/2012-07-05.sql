ALTER TABLE `xc_membercar` ADD `car_number` VARCHAR( 20 ) NOT NULL COMMENT '车牌号码' AFTER `car_name` ;
ALTER TABLE `xc_membercar` ADD `car_identification_code` VARCHAR( 50 ) NOT NULL COMMENT '车辆识别代码' AFTER `car_number` ;