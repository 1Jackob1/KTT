<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version0004 extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return 'Fix relation between task and user.';
    }

    /**
     * @inheritDoc
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE users__tasks (user_id INT NOT NULL, task_id INT NOT NULL, INDEX IDX_D587079BA76ED395 (user_id), INDEX IDX_D587079B8DB60186 (task_id), PRIMARY KEY(user_id, task_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users__tasks ADD CONSTRAINT FK_D587079BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users__tasks ADD CONSTRAINT FK_D587079B8DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id)');
    }

    /**
     * @inheritDoc
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE task_user');
    }
}
