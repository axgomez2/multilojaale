-- Script para criar as tabelas de wishlist e wantlist diretamente
-- Remover tabelas se existirem para evitar conflitos
DROP TABLE IF EXISTS wishlists;
DROP TABLE IF EXISTS wantlists;

-- Criar tabela de wishlist sem constraints
CREATE TABLE wishlists (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  vinyl_master_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY wishlist_unique (user_id, vinyl_master_id)
);

-- Criar tabela de wantlist sem constraints
CREATE TABLE wantlists (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  vinyl_master_id BIGINT UNSIGNED NOT NULL,
  notification_sent BOOLEAN DEFAULT 0,
  last_notification_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY wantlist_unique (user_id, vinyl_master_id)
);
