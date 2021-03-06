<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210202001750 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE price (id INT AUTO_INCREMENT NOT NULL, amount INT NOT NULL, currency VARCHAR(3) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, price_id INT NOT NULL, style_number VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, images LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_D34A04ADD614C7E7 (price_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADD614C7E7 FOREIGN KEY (price_id) REFERENCES price (id)');
        $this->addSql('CREATE TABLE catalog (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE catalog ADD state VARCHAR(255)');
        $this->addSql('ALTER TABLE catalog CHANGE state state VARCHAR(255) DEFAULT \'submitted\' NOT NULL');
        $this->addSql('CREATE TABLE catalog_products (catalog_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_816D8444CC3C66FC (catalog_id), UNIQUE INDEX UNIQ_816D84444584665A (product_id), PRIMARY KEY(catalog_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE catalog_products ADD CONSTRAINT FK_816D8444CC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id)');
        $this->addSql('ALTER TABLE catalog_products ADD CONSTRAINT FK_816D84444584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADD614C7E7');
        $this->addSql('DROP TABLE price');
        $this->addSql('DROP TABLE product');
    }
}
