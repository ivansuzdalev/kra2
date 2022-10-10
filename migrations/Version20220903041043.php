<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220903041043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
        '
        CREATE TABLE vk_users (
		    id int NOT NULL AUTO_INCREMENT,
                    user_id int(32) unique,
                    nickname VARCHAR(2048) DEFAULT NULL,
                    maiden_name VARCHAR(2048) DEFAULT NULL,
                    bdate DATE DEFAULT NULL,
                    city int DEFAULT NULL,
                    country int DEFAULT NULL,
                    photo_max_orig VARCHAR(2048) DEFAULT NULL,
                    has_photo BOOLEAN DEFAULT False,
                    has_mobile BOOLEAN DEFAULT False,
                    mobile_phone VARCHAR(255) DEFAULT NULL,
                    home_phone VARCHAR(255) DEFAULT NULL,
                    last_seen TIMESTAMP DEFAULT NULL,
                    screen_name VARCHAR(2048) DEFAULT NULL,
                    online BOOLEAN DEFAULT False,
                    first_name VARCHAR(255)DEFAULT NULL,
                    last_name VARCHAR(255)DEFAULT NULL,
                    skype  VARCHAR(255)DEFAULT NULL,
                    military  BOOLEAN DEFAULT False,
                    twitter  VARCHAR(255) DEFAULT NULL,
                    record_data json NOT NULL,
                    datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
        )');

        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE vk_users');
        // this down() migration is auto-generated, please modify it to your needs

    }
}
