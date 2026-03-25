<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260325083643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, create_at DATETIME NOT NULL, update_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, prix DOUBLE PRECISION NOT NULL, photo VARCHAR(255) NOT NULL, categorie VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE food_category (id INT AUTO_INCREMENT NOT NULL, food_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_2E013E83BA8E87C4 (food_id), INDEX IDX_2E013E8312469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, price SMALLINT NOT NULL, created_at DATETIME NOT NULL, update_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE menu_category (id INT AUTO_INCREMENT NOT NULL, menu_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_2A1D5C57CCD7E912 (menu_id), INDEX IDX_2A1D5C5712469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, create_at DATETIME NOT NULL, update_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, nb_couvert SMALLINT NOT NULL, date DATE NOT NULL, heure DATETIME NOT NULL, allergies VARCHAR(255) DEFAULT NULL, allergies_active TINYINT NOT NULL, status VARCHAR(20) NOT NULL, created_at VARCHAR(255) NOT NULL, updated_at VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(64) NOT NULL, prenom VARCHAR(32) NOT NULL, email VARCHAR(64) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, guest_number INT DEFAULT NULL, allergy VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E83BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id)');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E8312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE menu_category ADD CONSTRAINT FK_2A1D5C57CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE menu_category ADD CONSTRAINT FK_2A1D5C5712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E83BA8E87C4');
        $this->addSql('ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E8312469DE2');
        $this->addSql('ALTER TABLE menu_category DROP FOREIGN KEY FK_2A1D5C57CCD7E912');
        $this->addSql('ALTER TABLE menu_category DROP FOREIGN KEY FK_2A1D5C5712469DE2');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE food_category');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_category');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE utilisateur');
    }
}
