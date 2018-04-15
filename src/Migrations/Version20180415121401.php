<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180415121401 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD is_active TINYINT(1) NOT NULL, ADD role VARCHAR(25) NOT NULL, DROP access, CHANGE name name VARCHAR(25) DEFAULT NULL, CHANGE second_name second_name VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE services CHANGE price price INT DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE services CHANGE price price INT NOT NULL, CHANGE description description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `user` ADD access INT DEFAULT 0 NOT NULL, DROP is_active, DROP role, CHANGE name name VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE second_name second_name VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
