<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221004192909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
        '
        CREATE TABLE cities (
		    id int NOT NULL AUTO_INCREMENT,
            city_id int(11) NOT NULL,
            title VARCHAR(255),
            area VARCHAR(255),
            region VARCHAR(255),
            PRIMARY KEY (id)
        )');

        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE cities');
        // this down() migration is auto-generated, please modify it to your needs

    }
}
