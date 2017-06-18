-- Change collation to have case insensitive search
ALTER TABLE  `movie` CHANGE  `title`  `title` VARCHAR( 512 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `user` CHANGE  `nickname`  `nickname` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `user` CHANGE  `email`  `email` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
