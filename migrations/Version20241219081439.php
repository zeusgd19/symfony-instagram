<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219081439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('DROP SEQUENCE auth.refresh_tokens_id_seq CASCADE');
        //$this->addSql('DROP SEQUENCE pgsodium.key_key_id_seq CASCADE');
        //$this->addSql('DROP SEQUENCE graphql.seq_schema_version CASCADE');
        $this->addSql('CREATE SEQUENCE notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE notification (id INT NOT NULL, notified_user_id INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF5476CAF5D8C9A7 ON notification (notified_user_id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF5D8C9A7 FOREIGN KEY (notified_user_id) REFERENCES user_postgres (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.mfa_challenges DROP CONSTRAINT mfa_challenges_auth_factor_id_fkey');
        $this->addSql('ALTER TABLE auth.refresh_tokens DROP CONSTRAINT refresh_tokens_session_id_fkey');
        $this->addSql('ALTER TABLE auth.mfa_factors DROP CONSTRAINT mfa_factors_user_id_fkey');
        $this->addSql('ALTER TABLE auth.one_time_tokens DROP CONSTRAINT one_time_tokens_user_id_fkey');
        $this->addSql('ALTER TABLE auth.identities DROP CONSTRAINT identities_user_id_fkey');
        $this->addSql('ALTER TABLE auth.saml_providers DROP CONSTRAINT saml_providers_sso_provider_id_fkey');
        $this->addSql('ALTER TABLE auth.mfa_amr_claims DROP CONSTRAINT mfa_amr_claims_session_id_fkey');
        $this->addSql('ALTER TABLE auth.sso_domains DROP CONSTRAINT sso_domains_sso_provider_id_fkey');
        $this->addSql('ALTER TABLE auth.sessions DROP CONSTRAINT sessions_user_id_fkey');
        $this->addSql('ALTER TABLE auth.saml_relay_states DROP CONSTRAINT saml_relay_states_sso_provider_id_fkey');
        $this->addSql('ALTER TABLE auth.saml_relay_states DROP CONSTRAINT saml_relay_states_flow_state_id_fkey');
        /*$this->addSql('DROP TABLE realtime.messages_2024_12_04');*/
        /*$this->addSql('DROP TABLE realtime.messages_2024_12_05');
        $this->addSql('DROP TABLE realtime.messages_2024_11_30');
        $this->addSql('DROP TABLE realtime.messages_2024_12_09');
        $this->addSql('DROP TABLE realtime.messages_2024_12_17');
        $this->addSql('DROP TABLE realtime.subscription');
        $this->addSql('DROP TABLE realtime.messages_2024_12_08');
        $this->addSql('DROP TABLE realtime.messages_2024_12_07');
        $this->addSql('DROP TABLE realtime.messages_2024_12_03');
        $this->addSql('DROP TABLE realtime.messages_2024_12_12');
        $this->addSql('DROP TABLE realtime.messages_2024_12_02');
        $this->addSql('DROP TABLE realtime.messages_2024_12_13');
        $this->addSql('DROP TABLE realtime.messages_2024_12_18');
        $this->addSql('DROP TABLE realtime.messages_2024_12_11');
        $this->addSql('DROP TABLE realtime.messages_2024_12_01');
        $this->addSql('DROP TABLE realtime.messages_2024_12_14');
        $this->addSql('DROP TABLE realtime.messages_2024_12_06');
        $this->addSql('DROP TABLE auth.mfa_challenges');
        $this->addSql('DROP TABLE auth.refresh_tokens');
        $this->addSql('DROP TABLE auth.mfa_factors');
        $this->addSql('DROP TABLE auth.one_time_tokens');
        $this->addSql('DROP TABLE auth.identities');
        $this->addSql('DROP TABLE auth.flow_state');
        $this->addSql('DROP TABLE auth.saml_providers');
        $this->addSql('DROP TABLE auth.mfa_amr_claims');
        $this->addSql('DROP TABLE auth.sso_domains');
        $this->addSql('DROP TABLE auth.schema_migrations');
        $this->addSql('DROP TABLE realtime.messages_2024_12_19');
        $this->addSql('DROP TABLE realtime.schema_migrations');
        $this->addSql('DROP TABLE auth.sso_providers');
        $this->addSql('DROP TABLE auth.sessions');
        $this->addSql('DROP TABLE auth.saml_relay_states');
        $this->addSql('DROP TABLE auth.audit_log_entries');
        $this->addSql('DROP TABLE auth.instances');
        $this->addSql('DROP TABLE auth.users');*/
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
        $this->addSql('DROP SEQUENCE notification_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE auth.refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pgsodium.key_key_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE graphql.seq_schema_version INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_04 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_05 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_11_30 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_09 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_17 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.subscription (id BIGINT NOT NULL, subscription_id UUID NOT NULL, entity VARCHAR(255) NOT NULL, filters VARCHAR(255) DEFAULT \'{}\' NOT NULL, claims JSONB NOT NULL, claims_role VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'utc\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX subscription_subscription_id_entity_filters_key ON realtime.subscription (subscription_id, entity, filters)');
        $this->addSql('CREATE INDEX ix_realtime_subscription_entity ON realtime.subscription (entity)');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_08 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_07 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_03 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_12 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_02 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_13 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_18 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_11 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_01 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_14 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_06 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE auth.mfa_challenges (id UUID NOT NULL, factor_id UUID NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, verified_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, ip_address VARCHAR(255) NOT NULL, otp_code TEXT DEFAULT NULL, web_authn_session_data JSONB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX mfa_challenge_created_at_idx ON auth.mfa_challenges (created_at)');
        $this->addSql('CREATE INDEX IDX_5A13C7D9BC88C1A3 ON auth.mfa_challenges (factor_id)');
        $this->addSql('CREATE TABLE auth.refresh_tokens (id BIGSERIAL NOT NULL, session_id UUID DEFAULT NULL, instance_id UUID DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, user_id VARCHAR(255) DEFAULT NULL, revoked BOOLEAN DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, parent VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX refresh_tokens_instance_id_user_id_idx ON auth.refresh_tokens (instance_id, user_id)');
        $this->addSql('CREATE INDEX refresh_tokens_updated_at_idx ON auth.refresh_tokens (updated_at)');
        $this->addSql('CREATE INDEX refresh_tokens_parent_idx ON auth.refresh_tokens (parent)');
        $this->addSql('CREATE INDEX refresh_tokens_instance_id_idx ON auth.refresh_tokens (instance_id)');
        $this->addSql('CREATE INDEX refresh_tokens_session_id_revoked_idx ON auth.refresh_tokens (session_id, revoked)');
        $this->addSql('CREATE UNIQUE INDEX refresh_tokens_token_unique ON auth.refresh_tokens (token)');
        $this->addSql('CREATE INDEX IDX_39BB651D613FECDF ON auth.refresh_tokens (session_id)');
        $this->addSql('CREATE TABLE auth.mfa_factors (id UUID NOT NULL, user_id UUID NOT NULL, friendly_name TEXT DEFAULT NULL, factor_type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, secret TEXT DEFAULT NULL, phone TEXT DEFAULT NULL, last_challenged_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, web_authn_credential JSONB DEFAULT NULL, web_authn_aaguid UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX factor_id_created_at_idx ON auth.mfa_factors (user_id, created_at)');
        $this->addSql('CREATE UNIQUE INDEX mfa_factors_last_challenged_at_key ON auth.mfa_factors (last_challenged_at)');
        $this->addSql('CREATE UNIQUE INDEX mfa_factors_user_friendly_name_unique ON auth.mfa_factors (friendly_name, user_id) WHERE (TRIM(BOTH FROM friendly_name) <> \'\'::text)');
        $this->addSql('CREATE UNIQUE INDEX unique_phone_factor_per_user ON auth.mfa_factors (user_id, phone)');
        $this->addSql('CREATE INDEX mfa_factors_user_id_idx ON auth.mfa_factors (user_id)');
        $this->addSql('CREATE TABLE auth.one_time_tokens (id UUID NOT NULL, user_id UUID NOT NULL, token_type DATE NOT NULL, token_hash TEXT NOT NULL, relates_to TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX one_time_tokens_token_hash_hash_idx ON auth.one_time_tokens (token_hash)');
        $this->addSql('CREATE INDEX one_time_tokens_relates_to_hash_idx ON auth.one_time_tokens (relates_to)');
        $this->addSql('CREATE UNIQUE INDEX one_time_tokens_user_id_token_type_key ON auth.one_time_tokens (user_id, token_type)');
        $this->addSql('CREATE INDEX IDX_F792B521A76ED395 ON auth.one_time_tokens (user_id)');
        $this->addSql('CREATE TABLE auth.identities (id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, user_id UUID NOT NULL, provider_id TEXT NOT NULL, identity_data JSONB NOT NULL, provider TEXT NOT NULL, last_sign_in_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, email TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX identities_user_id_idx ON auth.identities (user_id)');
        $this->addSql('CREATE UNIQUE INDEX identities_provider_id_provider_unique ON auth.identities (provider_id, provider)');
        $this->addSql('CREATE INDEX identities_email_idx ON auth.identities (email)');
        $this->addSql('COMMENT ON COLUMN auth.identities.email IS \'Auth: Email is a generated column that references the optional email property in the identity_data\'');
        $this->addSql('CREATE TABLE auth.flow_state (id UUID NOT NULL, user_id UUID DEFAULT NULL, auth_code TEXT NOT NULL, code_challenge_method VARCHAR(255) NOT NULL, code_challenge TEXT NOT NULL, provider_type TEXT NOT NULL, provider_access_token TEXT DEFAULT NULL, provider_refresh_token TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, authentication_method TEXT NOT NULL, auth_code_issued_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_user_id_auth_method ON auth.flow_state (user_id, authentication_method)');
        $this->addSql('CREATE INDEX flow_state_created_at_idx ON auth.flow_state (created_at)');
        $this->addSql('CREATE INDEX idx_auth_code ON auth.flow_state (auth_code)');
        $this->addSql('CREATE TABLE auth.saml_providers (id UUID NOT NULL, sso_provider_id UUID NOT NULL, entity_id TEXT NOT NULL, metadata_xml TEXT NOT NULL, metadata_url TEXT DEFAULT NULL, attribute_mapping JSONB DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, name_id_format TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX saml_providers_entity_id_key ON auth.saml_providers (entity_id)');
        $this->addSql('CREATE INDEX saml_providers_sso_provider_id_idx ON auth.saml_providers (sso_provider_id)');
        $this->addSql('CREATE TABLE auth.mfa_amr_claims (id UUID NOT NULL, session_id UUID NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, authentication_method TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX mfa_amr_claims_session_id_authentication_method_pkey ON auth.mfa_amr_claims (session_id, authentication_method)');
        $this->addSql('CREATE INDEX IDX_19ACBC5C613FECDF ON auth.mfa_amr_claims (session_id)');
        $this->addSql('CREATE TABLE auth.sso_domains (id UUID NOT NULL, sso_provider_id UUID NOT NULL, domain TEXT NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX sso_domains_sso_provider_id_idx ON auth.sso_domains (sso_provider_id)');
        $this->addSql('CREATE TABLE auth.schema_migrations (version VARCHAR(14) NOT NULL, PRIMARY KEY(version))');
        $this->addSql('CREATE UNIQUE INDEX schema_migrations_version_idx ON auth.schema_migrations (version)');
        $this->addSql('CREATE TABLE realtime.messages_2024_12_19 (inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, id UUID DEFAULT \'gen_random_uuid()\' NOT NULL, topic TEXT NOT NULL, extension TEXT NOT NULL, payload JSONB DEFAULT NULL, event TEXT DEFAULT NULL, private BOOLEAN DEFAULT false, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id, inserted_at))');
        $this->addSql('CREATE TABLE realtime.schema_migrations (version BIGINT NOT NULL, inserted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(version))');
        $this->addSql('CREATE TABLE auth.sso_providers (id UUID NOT NULL, resource_id TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN auth.sso_providers.resource_id IS \'Auth: Uniquely identifies a SSO provider according to a user-chosen resource ID (case insensitive), useful in infrastructure as code.\'');
        $this->addSql('CREATE TABLE auth.sessions (id UUID NOT NULL, user_id UUID NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, factor_id UUID DEFAULT NULL, aal VARCHAR(255) DEFAULT NULL, not_after TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, refreshed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, user_agent TEXT DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, tag TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX sessions_user_id_idx ON auth.sessions (user_id)');
        $this->addSql('CREATE INDEX user_id_created_at_idx ON auth.sessions (user_id, created_at)');
        $this->addSql('CREATE INDEX sessions_not_after_idx ON auth.sessions (not_after)');
        $this->addSql('COMMENT ON COLUMN auth.sessions.not_after IS \'Auth: Not after is a nullable column that contains a timestamp after which the session should be regarded as expired.\'');
        $this->addSql('CREATE TABLE auth.saml_relay_states (id UUID NOT NULL, sso_provider_id UUID NOT NULL, flow_state_id UUID DEFAULT NULL, request_id TEXT NOT NULL, for_email TEXT DEFAULT NULL, redirect_to TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX saml_relay_states_sso_provider_id_idx ON auth.saml_relay_states (sso_provider_id)');
        $this->addSql('CREATE INDEX saml_relay_states_for_email_idx ON auth.saml_relay_states (for_email)');
        $this->addSql('CREATE INDEX saml_relay_states_created_at_idx ON auth.saml_relay_states (created_at)');
        $this->addSql('CREATE INDEX IDX_E601D7E89692F398 ON auth.saml_relay_states (flow_state_id)');
        $this->addSql('CREATE TABLE auth.audit_log_entries (id UUID NOT NULL, instance_id UUID DEFAULT NULL, payload JSON DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, ip_address VARCHAR(64) DEFAULT \'\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX audit_logs_instance_id_idx ON auth.audit_log_entries (instance_id)');
        $this->addSql('CREATE TABLE auth.instances (id UUID NOT NULL, uuid UUID DEFAULT NULL, raw_base_config TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE auth.users (id UUID NOT NULL, instance_id UUID DEFAULT NULL, aud VARCHAR(255) DEFAULT NULL, role VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, encrypted_password VARCHAR(255) DEFAULT NULL, email_confirmed_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, invited_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, confirmation_sent_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, recovery_token VARCHAR(255) DEFAULT NULL, recovery_sent_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, email_change_token_new VARCHAR(255) DEFAULT NULL, email_change VARCHAR(255) DEFAULT NULL, email_change_sent_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, last_sign_in_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, raw_app_meta_data JSONB DEFAULT NULL, raw_user_meta_data JSONB DEFAULT NULL, is_super_admin BOOLEAN DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, phone TEXT DEFAULT NULL, phone_confirmed_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, phone_change TEXT DEFAULT \'\', phone_change_token VARCHAR(255) DEFAULT \'\', phone_change_sent_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, confirmed_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, email_change_token_current VARCHAR(255) DEFAULT \'\', email_change_confirm_status SMALLINT DEFAULT 0, banned_until TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, reauthentication_token VARCHAR(255) DEFAULT \'\', reauthentication_sent_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, is_sso_user BOOLEAN DEFAULT false NOT NULL, deleted_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, is_anonymous BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX users_instance_id_idx ON auth.users (instance_id)');
        $this->addSql('CREATE UNIQUE INDEX recovery_token_idx ON auth.users (recovery_token) WHERE ((recovery_token)::text !~ \'^[0-9 ]*$\'::text)');
        $this->addSql('CREATE UNIQUE INDEX reauthentication_token_idx ON auth.users (reauthentication_token) WHERE ((reauthentication_token)::text !~ \'^[0-9 ]*$\'::text)');
        $this->addSql('CREATE UNIQUE INDEX users_phone_key ON auth.users (phone)');
        $this->addSql('CREATE INDEX users_is_anonymous_idx ON auth.users (is_anonymous)');
        $this->addSql('CREATE UNIQUE INDEX confirmation_token_idx ON auth.users (confirmation_token) WHERE ((confirmation_token)::text !~ \'^[0-9 ]*$\'::text)');
        $this->addSql('CREATE INDEX users_instance_id_email_idx ON auth.users (instance_id)');
        $this->addSql('CREATE UNIQUE INDEX email_change_token_current_idx ON auth.users (email_change_token_current) WHERE ((email_change_token_current)::text !~ \'^[0-9 ]*$\'::text)');
        $this->addSql('CREATE UNIQUE INDEX email_change_token_new_idx ON auth.users (email_change_token_new) WHERE ((email_change_token_new)::text !~ \'^[0-9 ]*$\'::text)');
        $this->addSql('CREATE UNIQUE INDEX users_email_partial_key ON auth.users (email) WHERE (is_sso_user = false)');
        $this->addSql('COMMENT ON COLUMN auth.users.is_sso_user IS \'Auth: Set this column to true when the account comes from SSO. These accounts can have duplicate emails.\'');
        $this->addSql('ALTER TABLE auth.mfa_challenges ADD CONSTRAINT mfa_challenges_auth_factor_id_fkey FOREIGN KEY (factor_id) REFERENCES auth.mfa_factors (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.refresh_tokens ADD CONSTRAINT refresh_tokens_session_id_fkey FOREIGN KEY (session_id) REFERENCES auth.sessions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.mfa_factors ADD CONSTRAINT mfa_factors_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.one_time_tokens ADD CONSTRAINT one_time_tokens_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.identities ADD CONSTRAINT identities_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.saml_providers ADD CONSTRAINT saml_providers_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.mfa_amr_claims ADD CONSTRAINT mfa_amr_claims_session_id_fkey FOREIGN KEY (session_id) REFERENCES auth.sessions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.sso_domains ADD CONSTRAINT sso_domains_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.sessions ADD CONSTRAINT sessions_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.saml_relay_states ADD CONSTRAINT saml_relay_states_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.saml_relay_states ADD CONSTRAINT saml_relay_states_flow_state_id_fkey FOREIGN KEY (flow_state_id) REFERENCES auth.flow_state (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAF5D8C9A7');
        $this->addSql('DROP TABLE notification');
    }
}
