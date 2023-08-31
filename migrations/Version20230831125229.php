<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230831125229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, invoice_number VARCHAR(255) NOT NULL, invoice_date DATETIME NOT NULL, amount NUMERIC(10, 2) NOT NULL, INDEX IDX_90651744A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD id INT AUTO_INCREMENT NOT NULL, ADD email VARCHAR(180) NOT NULL, ADD roles JSON NOT NULL, ADD password VARCHAR(255) NOT NULL, ADD full_name VARCHAR(100) NOT NULL, ADD phone_number VARCHAR(15) NOT NULL, ADD adress VARCHAR(100) NOT NULL, ADD city VARCHAR(50) NOT NULL, ADD postal_code VARCHAR(6) NOT NULL, ADD total_space INT NOT NULL, ADD is_verified TINYINT(1) NOT NULL, ADD verif_token VARCHAR(255) DEFAULT NULL, CHANGE idazazazaz used_space INT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744A76ED395');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('ALTER TABLE users MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
        $this->addSql('DROP INDEX `primary` ON users');
        $this->addSql('ALTER TABLE users ADD idazazazaz INT NOT NULL, DROP id, DROP email, DROP roles, DROP password, DROP full_name, DROP phone_number, DROP adress, DROP city, DROP postal_code, DROP used_space, DROP total_space, DROP is_verified, DROP verif_token');
    }
}
