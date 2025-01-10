<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109213014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, password VARCHAR(200) NOT NULL, email VARCHAR(200) NOT NULL, roles VARCHAR(20) NOT NULL, date_create DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, brands_id INT NOT NULL, name VARCHAR(200) NOT NULL, price DOUBLE PRECISION NOT NULL, description VARCHAR(5000) NOT NULL, INDEX IDX_D34A04ADE9EEC0C7 (brands_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, username VARCHAR(200) NOT NULL, password VARCHAR(200) NOT NULL, email VARCHAR(200) NOT NULL, roles VARCHAR(20) NOT NULL, lastname VARCHAR(200) NOT NULL, firstname VARCHAR(200) NOT NULL, date_create DATE NOT NULL, INDEX IDX_1483A5E919EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE9EEC0C7 FOREIGN KEY (brands_id) REFERENCES brands (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E919EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE9EEC0C7');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E919EB6921');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE users');
    }
}
