<?php

declare(strict_types=1);

namespace Application\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20260412140844 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE setting');
        $this->addSql('ALTER TABLE movie CHANGE dateUpdate date_update DATETIME NOT NULL, CHANGE searchCount search_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE title title VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE source source VARCHAR(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL, ADD start_year SMALLINT UNSIGNED DEFAULT NULL, CHANGE dateSearch date_search DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE status DROP FOREIGN KEY `status_ibfk_1`');
        $this->addSql('UPDATE movie SET start_year = YEAR(dateRelease);');
        $this->addSql('ALTER TABLE movie DROP dateRelease;');
        $this->addSql('ALTER TABLE status DROP FOREIGN KEY `status_ibfk_2`');
        $this->addSql('DROP INDEX idMovie ON status');
        $this->addSql('DROP INDEX idUser ON status');
        $this->addSql('ALTER TABLE status CHANGE dateUpdate date_update DATETIME NOT NULL, CHANGE isLatest is_latest TINYINT DEFAULT 0 NOT NULL, CHANGE idMovie movie_id INT NOT NULL, CHANGE idUser user_id INT NOT NULL,  CHANGE rating rating INT UNSIGNED DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE status ADD CONSTRAINT FK_7B00651C8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE status ADD CONSTRAINT FK_7B00651CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_7B00651C8F93B6FC ON status (movie_id)');
        $this->addSql('CREATE INDEX IDX_7B00651CA76ED395 ON status (user_id)');
        $this->addSql('ALTER TABLE user CHANGE dateUpdate date_update DATETIME NOT NULL, CHANGE nickname nickname VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE password password VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL');
        $this->addSql('ALTER TABLE user RENAME INDEX nickname TO UNIQ_8D93D649A188FE64');
        $this->addSql('ALTER TABLE user RENAME INDEX email TO UNIQ_8D93D649E7927C74');
    }
}
