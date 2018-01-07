<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180107233658 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE zshwag_user_mini_program (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, open_id VARCHAR(255) NOT NULL, js_code VARCHAR(255) NOT NULL, session_key VARCHAR(255) NOT NULL, union_id VARCHAR(255) DEFAULT NULL, last_login INT NOT NULL, UNIQUE INDEX UNIQ_DFED1638F89B8A9C (open_id), UNIQUE INDEX UNIQ_DFED1638A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE zshwag_user_mini_program ADD CONSTRAINT FK_DFED1638A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_shop_user (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE zshwag_user_mini_program');
    }
}
