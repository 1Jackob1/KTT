<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version0005 extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return 'Add timezone field to User.';
    }

    /**
     * @inheritDoc
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE users ADD timezone VARCHAR(255) DEFAULT \'Asia/Vladivostok\' NOT NULL');
    }

    /**
     * @inheritDoc
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE users DROP timezone');
    }
}
