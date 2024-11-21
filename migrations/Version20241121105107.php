<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121105107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_postgres_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE comment (id INT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('CREATE INDEX IDX_9474526C4B89032C ON comment (post_id)');
        $this->addSql('CREATE TABLE follower (id INT NOT NULL, follower_id INT NOT NULL, following_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B9D60946AC24F853 ON follower (follower_id)');
        $this->addSql('CREATE INDEX IDX_B9D609461816E3A3 ON follower (following_id)');
        $this->addSql('CREATE TABLE post (id INT NOT NULL, user_id INT NOT NULL, photo VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT \' \', PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('CREATE TABLE user_postgres (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F5E76375F85E0677 ON user_postgres (username)');
        $this->addSql('CREATE TABLE user_follower (user_postgres_source INT NOT NULL, user_postgres_target INT NOT NULL, PRIMARY KEY(user_postgres_source, user_postgres_target))');
        $this->addSql('CREATE INDEX IDX_595BED46BDC5E7D2 ON user_follower (user_postgres_source)');
        $this->addSql('CREATE INDEX IDX_595BED46A420B75D ON user_follower (user_postgres_target)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user_postgres (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D60946AC24F853 FOREIGN KEY (follower_id) REFERENCES user_postgres (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D609461816E3A3 FOREIGN KEY (following_id) REFERENCES user_postgres (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user_postgres (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_follower ADD CONSTRAINT FK_595BED46BDC5E7D2 FOREIGN KEY (user_postgres_source) REFERENCES user_postgres (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_follower ADD CONSTRAINT FK_595BED46A420B75D FOREIGN KEY (user_postgres_target) REFERENCES user_postgres (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_postgres_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE follower DROP CONSTRAINT FK_B9D60946AC24F853');
        $this->addSql('ALTER TABLE follower DROP CONSTRAINT FK_B9D609461816E3A3');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE user_follower DROP CONSTRAINT FK_595BED46BDC5E7D2');
        $this->addSql('ALTER TABLE user_follower DROP CONSTRAINT FK_595BED46A420B75D');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE follower');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE user_postgres');
        $this->addSql('DROP TABLE user_follower');
    }
}
