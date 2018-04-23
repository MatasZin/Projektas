<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180420082531 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, car_id INT NOT NULL, order_date DATETIME NOT NULL, order_end_date DATETIME DEFAULT NULL, completed TINYINT(1) NOT NULL, INDEX IDX_E52FFDEEC3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordered_services (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, worker_id INT NOT NULL, service_id INT NOT NULL, status VARCHAR(20) NOT NULL, last_change_date DATETIME NOT NULL, note VARCHAR(255) DEFAULT NULL, INDEX IDX_F96292168D9F6D38 (order_id), INDEX IDX_F96292166B20BA36 (worker_id), INDEX IDX_F9629216ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cars (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, license_plate VARCHAR(10) NOT NULL, INDEX IDX_95C71D147E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEC3C6F69F FOREIGN KEY (car_id) REFERENCES cars (id)');
        $this->addSql('ALTER TABLE ordered_services ADD CONSTRAINT FK_F96292168D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE ordered_services ADD CONSTRAINT FK_F96292166B20BA36 FOREIGN KEY (worker_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ordered_services ADD CONSTRAINT FK_F9629216ED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE cars ADD CONSTRAINT FK_95C71D147E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ordered_services DROP FOREIGN KEY FK_F96292168D9F6D38');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEC3C6F69F');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE ordered_services');
        $this->addSql('DROP TABLE cars');
    }
}
