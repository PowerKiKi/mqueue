-- Will manually update dateUpdate since now
ALTER TABLE `movie` CHANGE `dateUpdate` `dateUpdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Store movie release date
ALTER TABLE `movie` ADD `dateRelease` DATE NULL DEFAULT NULL AFTER `title`;
