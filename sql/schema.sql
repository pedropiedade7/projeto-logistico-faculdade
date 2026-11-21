-- ============================================================
-- Gerenciador Logístico - Schema do Banco de Dados
-- Disciplina: Fundamentos de Programação para Internet
-- ============================================================

CREATE DATABASE IF NOT EXISTS gerenciador_logistico
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gerenciador_logistico;

-- -----------------------------------------------------------
-- Tabela: produtos
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS produtos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(150)   NOT NULL,
    descricao   TEXT,
    peso_kg     DECIMAL(10,3)  NOT NULL DEFAULT 0.000,
    volume_m3   DECIMAL(10,4)  NOT NULL DEFAULT 0.0000,
    valor_reais DECIMAL(12,2)  NOT NULL DEFAULT 0.00,
    created_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Tabela: enderecos
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS enderecos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo        ENUM('origem','destino') NOT NULL DEFAULT 'origem',
    cep         CHAR(9)        NOT NULL,
    logradouro  VARCHAR(200)   NOT NULL,
    numero      VARCHAR(20)    NOT NULL,
    complemento VARCHAR(100),
    bairro      VARCHAR(100)   NOT NULL,
    cidade      VARCHAR(100)   NOT NULL,
    uf          CHAR(2)        NOT NULL,
    created_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Tabela: transportadores
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS transportadores (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(150)   NOT NULL,
    cpf_cnpj    VARCHAR(18)    NOT NULL,
    telefone    VARCHAR(20),
    email       VARCHAR(150),
    veiculo     VARCHAR(100),
    placa       VARCHAR(10),
    created_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Tabela: fretes
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS fretes (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transportador_id     INT UNSIGNED NOT NULL,
    endereco_origem_id   INT UNSIGNED NOT NULL,
    endereco_destino_id  INT UNSIGNED NOT NULL,
    data_frete           DATE         NOT NULL,
    observacoes          TEXT,
    created_at           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_frete_transportador FOREIGN KEY (transportador_id)    REFERENCES transportadores(id) ON DELETE RESTRICT,
    CONSTRAINT fk_frete_origem        FOREIGN KEY (endereco_origem_id)  REFERENCES enderecos(id)       ON DELETE RESTRICT,
    CONSTRAINT fk_frete_destino       FOREIGN KEY (endereco_destino_id) REFERENCES enderecos(id)       ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Tabela: frete_produtos  (relação 1 frete -> N produtos)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS frete_produtos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    frete_id    INT UNSIGNED NOT NULL,
    produto_id  INT UNSIGNED NOT NULL,
    quantidade  INT UNSIGNED NOT NULL DEFAULT 1,
    CONSTRAINT fk_fp_frete   FOREIGN KEY (frete_id)   REFERENCES fretes(id)   ON DELETE CASCADE,
    CONSTRAINT fk_fp_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
