<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241127123448 extends AbstractMigration
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
        $this->addSql('CREATE TABLE saved_posts (user_postgres_id INT NOT NULL, post_id INT NOT NULL, PRIMARY KEY(user_postgres_id, post_id))');
        $this->addSql('CREATE INDEX IDX_E58E61E344C520C1 ON saved_posts (user_postgres_id)');
        $this->addSql('CREATE INDEX IDX_E58E61E34B89032C ON saved_posts (post_id)');
        $this->addSql('ALTER TABLE saved_posts ADD CONSTRAINT FK_E58E61E344C520C1 FOREIGN KEY (user_postgres_id) REFERENCES user_postgres (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE saved_posts ADD CONSTRAINT FK_E58E61E34B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        //$this->addSql('DROP TABLE realtime.subscription');
        //$this->addSql('DROP TABLE realtime.schema_migrations');
        //$this->addSql('DROP TABLE auth.flow_state');
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
        $this->addSql('CREATE SEQUENCE pgsodium.key_key_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE graphql.seq_schema_version INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE realtime.subscription (id BIGINT NOT NULL, subscription_id UUID NOT NULL, entity VARCHAR(255) NOT NULL, filters VARCHAR(255) DEFAULT \'{}\' NOT NULL, claims JSONB NOT NULL, claims_role VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'utc\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ix_realtime_subscription_entity ON realtime.subscription (entity)');
        $this->addSql('CREATE UNIQUE INDEX subscription_subscription_id_entity_filters_key ON realtime.subscription (subscription_id, entity, filters)');
        $this->addSql('CREATE TABLE realtime.schema_migrations (version BIGINT NOT NULL, inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(version))');
        $this->addSql('CREATE TABLE auth.flow_state (id UUID NOT NULL, user_id UUID DEFAULT NULL, auth_code TEXT NOT NULL, code_challenge_method VARCHAR(255) NOT NULL, code_challenge TEXT NOT NULL, provider_type TEXT NOT NULL, provider_access_token TEXT DEFAULT NULL, provider_refresh_token TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, authentication_method TEXT NOT NULL, auth_code_issued_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX flow_state_created_at_idx ON auth.flow_state (created_at)');
        $this->addSql('CREATE INDEX idx_user_id_auth_method ON auth.flow_state (user_id, authentication_method)');
        $this->addSql('CREATE INDEX idx_auth_code ON auth.flow_state (auth_code)');
        $this->addSql('ALTER TABLE saved_posts DROP CONSTRAINT FK_E58E61E344C520C1');
        $this->addSql('ALTER TABLE saved_posts DROP CONSTRAINT FK_E58E61E34B89032C');
        $this->addSql('DROP TABLE saved_posts');
    }
}
