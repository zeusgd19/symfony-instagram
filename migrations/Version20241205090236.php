<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205090236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('DROP SEQUENCE pgsodium.key_key_id_seq CASCADE');
        //$this->addSql('DROP SEQUENCE graphql.seq_schema_version CASCADE');
        $this->addSql('CREATE SEQUENCE story_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE story (id INT NOT NULL, user_story_id INT NOT NULL, image_story VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expire_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EB5604384AD0A436 ON story (user_story_id)');
        $this->addSql('COMMENT ON COLUMN story.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE story ADD CONSTRAINT FK_EB5604384AD0A436 FOREIGN KEY (user_story_id) REFERENCES user_postgres (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        //$this->addSql('DROP TABLE realtime.messages_2024_12_05');
        //$this->addSql('DROP TABLE realtime.subscription');
        //$this->addSql('DROP TABLE realtime.schema_migrations');
        //$this->addSql('DROP TABLE realtime.messages_2024_12_04');
        //$this->addSql('DROP TABLE realtime.messages_2024_12_03');
        //$this->addSql('DROP TABLE realtime.messages_2024_11_30');
        //$this->addSql('DROP TABLE realtime.messages_2024_12_02');
        //$this->addSql('DROP TABLE realtime.messages_2024_12_01');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA graphql');
        $this->addSql('CREATE SCHEMA graphql_public');
        $this->addSql('CREATE SCHEMA vault');
        $this->addSql('CREATE SCHEMA pgsodium_masks');
        $this->addSql('CREATE SCHEMA pgsodium');
        $this->addSql('CREATE SCHEMA auth');
        $this->addSql('CREATE SCHEMA storage');
        $this->addSql('CREATE SCHEMA realtime');
        $this->addSql('CREATE SCHEMA extensions');
        $this->addSql('CREATE SCHEMA pgbouncer');
        $this->addSql('DROP SEQUENCE story_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE pgsodium.key_key_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE graphql.seq_schema_version INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_05 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.subscription (id BIGINT NOT NULL, subscription_id UUID NOT NULL, entity VARCHAR(255) NOT NULL, filters VARCHAR(255) DEFAULT \'{}\' NOT NULL, claims JSONB NOT NULL, claims_role VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'utc\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX subscription_subscription_id_entity_filters_key ON realtime.subscription (subscription_id, entity, filters)');
        $this->addSql('CREATE INDEX ix_realtime_subscription_entity ON realtime.subscription (entity)');
        $this->addSql('CREATE TABLE realtime.schema_migrations (version BIGINT NOT NULL, inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(version))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_04 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_03 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_11_30 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_02 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_01 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('ALTER TABLE story DROP CONSTRAINT FK_EB5604384AD0A436');
        $this->addSql('DROP TABLE story');
    }
}
