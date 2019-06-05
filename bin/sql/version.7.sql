-- Support ID up to 8 chars
ALTER TABLE `movie` CHANGE `id` `id` VARCHAR(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
ALTER TABLE `status` CHANGE `idMovie` `idMovie` VARCHAR(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

-- Remove suffix
UPDATE `movie` SET title = REPLACE(title, ' - IMDb', '');
