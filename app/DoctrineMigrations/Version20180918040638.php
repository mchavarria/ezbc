<?php

namespace sfEzbc\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180918040638 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_wallet ADD bc_id INT NOT NULL, DROP bc_type');
        $this->addSql('ALTER TABLE user_wallet ADD CONSTRAINT FK_193A8922954397FF FOREIGN KEY (bc_id) REFERENCES block_chain (id)');
        $this->addSql('CREATE INDEX IDX_193A8922954397FF ON user_wallet (bc_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_wallet DROP FOREIGN KEY FK_193A8922954397FF');
        $this->addSql('DROP INDEX IDX_193A8922954397FF ON user_wallet');
        $this->addSql('ALTER TABLE user_wallet ADD bc_type VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci, DROP bc_id');
    }
}
