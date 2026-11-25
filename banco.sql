-- banco.sql
-- Importe no phpMyAdmin (aba Importar/SQL) DENTRO do banco já criado.
--
-- Local (opcional, se o banco ainda não existe):
--   CREATE DATABASE IF NOT EXISTS ABNQRCF CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
--   USE ABNQRCF;
-- Em hospedagem, o banco é criado pelo painel: selecione-o e importe este arquivo.

CREATE TABLE IF NOT EXISTS usuario (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    senha_hash  VARCHAR(255) NOT NULL,
    criado_em   DATETIME     NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuario_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tarefa (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    descricao   VARCHAR(200) NOT NULL,
    concluida   TINYINT(1)   NOT NULL DEFAULT 0,
    usuario_id  INT UNSIGNED NOT NULL,
    criada_em   DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_tarefa_usuario (usuario_id),
    CONSTRAINT fk_tarefa_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuario (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
