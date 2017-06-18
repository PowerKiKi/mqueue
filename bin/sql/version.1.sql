
-- Add timestamp fields
ALTER TABLE `status` ADD `dateUpdate` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `rating`;
UPDATE `status` SET `dateUpdate` =  '2010-01-01 00:00:00'  WHERE `dateUpdate` = '0000-00-00 00:00:00';

ALTER TABLE `user` ADD `dateUpdate` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `password`;
UPDATE `user` SET `dateUpdate` =  '2010-01-01 00:00:00'  WHERE `dateUpdate` = '0000-00-00 00:00:00';

ALTER TABLE `movie` CHANGE `dateUpdate` `dateUpdate` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;


-- Encrypt existing passwords
ALTER TABLE `user` CHANGE `password` `password` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
UPDATE `user` AS u1, `user` AS u2 SET u1.password = SHA1( u2.password ) WHERE u1.id = u2.id;
