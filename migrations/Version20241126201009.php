<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241126201009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, text VARCHAR(255) NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE follower (id INT AUTO_INCREMENT NOT NULL, follower_id INT NOT NULL, following_id INT NOT NULL, INDEX IDX_B9D60946AC24F853 (follower_id), INDEX IDX_B9D609461816E3A3 (following_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, photo VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT \' \', INDEX IDX_5A8A6C8DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_postgres (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F5E76375F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_follower (user_postgres_source INT NOT NULL, user_postgres_target INT NOT NULL, INDEX IDX_595BED46BDC5E7D2 (user_postgres_source), INDEX IDX_595BED46A420B75D (user_postgres_target), PRIMARY KEY(user_postgres_source, user_postgres_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE likes (user_postgres_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_49CA4E7D44C520C1 (user_postgres_id), INDEX IDX_49CA4E7D4B89032C (post_id), PRIMARY KEY(user_postgres_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user_postgres (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D60946AC24F853 FOREIGN KEY (follower_id) REFERENCES user_postgres (id)');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D609461816E3A3 FOREIGN KEY (following_id) REFERENCES user_postgres (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user_postgres (id)');
        $this->addSql('ALTER TABLE user_follower ADD CONSTRAINT FK_595BED46BDC5E7D2 FOREIGN KEY (user_postgres_source) REFERENCES user_postgres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_follower ADD CONSTRAINT FK_595BED46A420B75D FOREIGN KEY (user_postgres_target) REFERENCES user_postgres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D44C520C1 FOREIGN KEY (user_postgres_id) REFERENCES user_postgres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE follower DROP FOREIGN KEY FK_B9D60946AC24F853');
        $this->addSql('ALTER TABLE follower DROP FOREIGN KEY FK_B9D609461816E3A3');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE user_follower DROP FOREIGN KEY FK_595BED46BDC5E7D2');
        $this->addSql('ALTER TABLE user_follower DROP FOREIGN KEY FK_595BED46A420B75D');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D44C520C1');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D4B89032C');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE follower');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE user_postgres');
        $this->addSql('DROP TABLE user_follower');
        $this->addSql('DROP TABLE likes');
    }
}
