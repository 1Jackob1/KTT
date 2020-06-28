<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version0002 extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return 'Add task table and relation between user and task.';
    }

    /**
     * @inheritDoc
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE tasks (id INT AUTO_INCREMENT NOT NULL, title LONGTEXT NOT NULL, description LONGTEXT NOT NULL, priority INT DEFAULT 1 NOT NULL, estimate INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    /**
     * @inheritDoc
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE tasks');
    }
}
