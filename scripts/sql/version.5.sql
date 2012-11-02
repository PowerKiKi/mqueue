-- Allow to record the whole history of statuses, not only the last one
ALTER TABLE  `status` DROP INDEX  `unique_status`;
ALTER TABLE  `status` ADD  `isLatest` BOOLEAN NOT NULL AFTER  `rating`;
UPDATE `status` SET `isLatest` = 1, `dateUpdate` = `dateUpdate`;
