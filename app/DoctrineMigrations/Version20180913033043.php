<?php

namespace sfEzbc\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180913033043 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8df675f31b');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526cf675f31b');
        $this->addSql('ALTER TABLE post_tag DROP CONSTRAINT fk_5ace3af0bad26311');
        $this->addSql('ALTER TABLE post_tag DROP CONSTRAINT fk_5ace3af04b89032c');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526c4b89032c');
        $this->addSql('ALTER TABLE bc_transaction DROP CONSTRAINT fk_586ef59372170f4e');
        $this->addSql('DROP SEQUENCE post_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE comment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_public_key_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_wallet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_wallet (id INT NOT NULL, user_id INT NOT NULL, wallet_key VARCHAR(100) NOT NULL, bc_type VARCHAR(30) NOT NULL, created_by VARCHAR(50) DEFAULT NULL, updated_by VARCHAR(50) DEFAULT NULL, deleted_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_193A8922A76ED395 ON user_wallet (user_id)');
        $this->addSql('ALTER TABLE user_wallet ADD CONSTRAINT FK_193A8922A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE post_tag');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE user_public_key');
        $this->addSql('DROP INDEX idx_586ef59372170f4e');
        $this->addSql('ALTER TABLE bc_transaction RENAME COLUMN bc_public_key_id TO bc_wallet_key_id');
        $this->addSql('ALTER TABLE bc_transaction ADD CONSTRAINT FK_586EF593C2463FB5 FOREIGN KEY (bc_wallet_key_id) REFERENCES user_wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_586EF593C2463FB5 ON bc_transaction (bc_wallet_key_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bc_transaction DROP CONSTRAINT FK_586EF593C2463FB5');
        $this->addSql('DROP SEQUENCE user_wallet_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE post_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_public_key_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, firstname VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(64) NOT NULL, roles JSON NOT NULL, lastname VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649f85e0677 ON "user" (username)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_389b7835e237e06 ON tag (name)');
        $this->addSql('CREATE TABLE post_tag (post_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(post_id, tag_id))');
        $this->addSql('CREATE INDEX idx_5ace3af04b89032c ON post_tag (post_id)');
        $this->addSql('CREATE INDEX idx_5ace3af0bad26311 ON post_tag (tag_id)');
        $this->addSql('CREATE TABLE post (id INT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, content TEXT NOT NULL, publishedat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_5a8a6c8df675f31b ON post (author_id)');
        $this->addSql('CREATE TABLE comment (id INT NOT NULL, post_id INT NOT NULL, author_id INT NOT NULL, content TEXT NOT NULL, publishedat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9474526c4b89032c ON comment (post_id)');
        $this->addSql('CREATE INDEX idx_9474526cf675f31b ON comment (author_id)');
        $this->addSql('CREATE TABLE user_public_key (id INT NOT NULL, user_id INT NOT NULL, bc_public_key VARCHAR(100) NOT NULL, bc_type VARCHAR(30) NOT NULL, created_by VARCHAR(50) DEFAULT NULL, updated_by VARCHAR(50) DEFAULT NULL, deleted_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_c19e128fa76ed395 ON user_public_key (user_id)');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT fk_5ace3af04b89032c FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT fk_5ace3af0bad26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8df675f31b FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526c4b89032c FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526cf675f31b FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_public_key ADD CONSTRAINT fk_c19e128fa76ed395 FOREIGN KEY (user_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE user_wallet');
        $this->addSql('DROP INDEX IDX_586EF593C2463FB5');
        $this->addSql('ALTER TABLE bc_transaction RENAME COLUMN bc_wallet_key_id TO bc_public_key_id');
        $this->addSql('ALTER TABLE bc_transaction ADD CONSTRAINT fk_586ef59372170f4e FOREIGN KEY (bc_public_key_id) REFERENCES user_public_key (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_586ef59372170f4e ON bc_transaction (bc_public_key_id)');
    }
}
