ALTER DATABASE mqueue COLLATE = 'utf8mb4_unicode_520_ci';
DROP TABLE IF EXISTS setting;

ALTER TABLE movie
    CHANGE dateUpdate date_update DATETIME NOT NULL,
    CHANGE searchCount search_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
    CHANGE id id INT AUTO_INCREMENT NOT NULL,
    CHANGE title title VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
    CHANGE source source VARCHAR(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
    ADD start_year SMALLINT UNSIGNED DEFAULT NULL AFTER title,
    CHANGE dateSearch date_search DATETIME DEFAULT NULL,
    COLLATE = 'utf8mb4_unicode_520_ci';

UPDATE movie
SET start_year = YEAR(dateRelease);
ALTER TABLE movie
    DROP dateRelease;

ALTER TABLE status
    DROP FOREIGN KEY `status_ibfk_1`,
    DROP FOREIGN KEY `status_ibfk_2`;

DROP INDEX idMovie ON status;
DROP INDEX idUser ON status;

ALTER TABLE status
    CHANGE dateUpdate date_update DATETIME NOT NULL,
    CHANGE isLatest is_latest TINYINT DEFAULT 0 NOT NULL,
    CHANGE idMovie movie_id INT NOT NULL,
    CHANGE idUser user_id INT NOT NULL,
    CHANGE rating rating INT UNSIGNED DEFAULT 0 NOT NULL,
    ADD CONSTRAINT FK_7B00651C8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE,
    ADD CONSTRAINT FK_7B00651CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE,
    COLLATE = 'utf8mb4_unicode_520_ci';


CREATE INDEX IDX_7B00651C8F93B6FC ON status (movie_id);
CREATE INDEX IDX_7B00651CA76ED395 ON status (user_id);
ALTER TABLE user
    CHANGE dateUpdate date_update DATETIME NOT NULL,
    CHANGE nickname nickname VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
    CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
    CHANGE password password VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
    RENAME INDEX nickname TO UNIQ_8D93D649A188FE64, RENAME INDEX email TO UNIQ_8D93D649E7927C74,
    COLLATE = 'utf8mb4_unicode_520_ci';


CREATE TABLE `doctrine_migration_versions`
(
    `version`        VARCHAR(255) NOT NULL,
    `executed_at`    DATETIME DEFAULT NULL,
    `execution_time` INT(11)  DEFAULT NULL,
    PRIMARY KEY (`version`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;

INSERT INTO doctrine_migration_versions (version, executed_at, execution_time)
VALUES ('Application\\Migration\\Version20260629062614', NOW(), 0);
