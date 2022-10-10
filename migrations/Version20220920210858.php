<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220920210858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
        '
        CREATE TABLE vk_tokens (
		    id int NOT NULL AUTO_INCREMENT,
                    token VARCHAR(2048) DEFAULT NULL,
                    datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
        )');

        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE vk_tokens');
        // this down() migration is auto-generated, please modify it to your needs

    }
}
