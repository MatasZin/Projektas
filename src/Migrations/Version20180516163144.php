<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180516163144 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_7332E169BF396750 ON services');
        $this->addSql('DROP INDEX UNIQ_8D93D649BF396750 ON user');
        $this->addSql('ALTER TABLE user ADD is_deleted TINYINT(1) NOT NULL, ADD confirmation_token VARCHAR(255) DEFAULT NULL, ADD forget_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_7332E169BF396750 ON services (id)');
        $this->addSql('ALTER TABLE `user` DROP is_deleted, DROP confirmation_token, DROP forget_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649BF396750 ON `user` (id)');
    }
}
