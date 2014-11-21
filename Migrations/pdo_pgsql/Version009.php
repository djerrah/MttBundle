<?php

namespace CanalTP\MttBundle\Migrations\pdo_pgsql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version009 extends AbstractMigration
{
    const VERSION = '0.0.9';

    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE mtt.layout_config ADD notes_type text not null default \'exponent\';');
        $this->addSql('ALTER TABLE mtt.layout_config ADD notes_colors text;');
    }

    public function down(Schema $schema)
    {
    }

    public function getName()
    {
        return self::VERSION;
    }
}
