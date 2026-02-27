<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260227153000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create customer_order table and ensure product catalog is stored in database';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on postgresql.');

        $this->addSql('CREATE TABLE IF NOT EXISTS product (id SERIAL NOT NULL, sku VARCHAR(80) NOT NULL, name VARCHAR(120) NOT NULL, description VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, active BOOLEAN DEFAULT TRUE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_D34A04AD40F6C20 ON product (sku)');

        $this->addSql('CREATE TABLE IF NOT EXISTS customer_order (id SERIAL NOT NULL, number VARCHAR(32) NOT NULL, status VARCHAR(40) NOT NULL, total_amount DOUBLE PRECISION NOT NULL, customer_email VARCHAR(180) NOT NULL, items JSON NOT NULL, history JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_6A55D6B8FC77F309 ON customer_order (number)');

        $this->addSql("INSERT INTO product (sku, name, description, price, active) VALUES
            ('haunted-castle-collector', 'Haunted Castle Collector Set', 'Collection premium château hanté', 120.00, true),
            ('ancient-totem-figurine', 'Ancient Totem Figurine', 'Figurine totem ancien', 85.00, true),
            ('dark-rituals-board-game', 'Dark Rituals Board Game', 'Jeu de plateau dark rituals', 45.00, true),
            ('ghostly-manifestation-art', 'Ghostly Manifestation Art', 'Affiche collection manifestation fantôme', 60.00, true)
            ON CONFLICT (sku) DO NOTHING");
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on postgresql.');

        $this->addSql('DROP TABLE IF EXISTS customer_order');
        $this->addSql('DROP TABLE IF EXISTS product');
    }
}
