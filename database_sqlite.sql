-- ============================================
-- Script de Criação do Banco de Dados SQLite
-- Sistema de Gestão Psicológica
-- ============================================
-- Habilitar chaves estrangeiras
PRAGMA foreign_keys = ON;
-- ============================================
-- Tabela: usuarios
-- ============================================
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    cpf TEXT NOT NULL UNIQUE,
    telefone TEXT,
    senha TEXT NOT NULL,
    role TEXT CHECK(role IN ('admin', 'user')) DEFAULT 'user',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_usuarios_cpf ON usuarios(cpf);
-- Trigger para atualizar atualizado_em automaticamente
CREATE TRIGGER usuarios_update_timestamp
AFTER
UPDATE ON usuarios BEGIN
UPDATE usuarios
SET atualizado_em = CURRENT_TIMESTAMP
WHERE id = NEW.id;
END;
-- ============================================
-- Tabela: password_resets
-- ============================================
DROP TABLE IF EXISTS password_resets;
CREATE TABLE password_resets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario_id INTEGER NOT NULL,
    token_hash TEXT NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
CREATE INDEX idx_password_resets_usuario ON password_resets(usuario_id);
CREATE INDEX idx_password_resets_token ON password_resets(token_hash);
-- ============================================
-- Tabela: pessoas (pacientes)
-- ============================================
DROP TABLE IF EXISTS pessoas;
CREATE TABLE pessoas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    genero TEXT CHECK(genero IN ('Masculino', 'Feminino', 'Outro')) DEFAULT 'Outro',
    idade INTEGER,
    peso REAL,
    altura REAL,
    etnia TEXT,
    rg TEXT,
    cpf TEXT UNIQUE,
    cnh TEXT,
    data_nasc DATE,
    email TEXT,
    telefone TEXT,
    foto TEXT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_pessoas_nome ON pessoas(nome);
CREATE INDEX idx_pessoas_cpf ON pessoas(cpf);
CREATE INDEX idx_pessoas_email ON pessoas(email);
CREATE TRIGGER pessoas_update_timestamp
AFTER
UPDATE ON pessoas BEGIN
UPDATE pessoas
SET atualizado_em = CURRENT_TIMESTAMP
WHERE id = NEW.id;
END;
-- ============================================
-- Tabela: agendamentos
-- ============================================
DROP TABLE IF EXISTS agendamentos;
CREATE TABLE agendamentos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome_paciente TEXT NOT NULL,
    data_agendamento DATE NOT NULL,
    hora_agendamento TIME NOT NULL,
    observacoes TEXT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_agendamentos_data ON agendamentos(data_agendamento);
CREATE INDEX idx_agendamentos_nome ON agendamentos(nome_paciente);
CREATE TRIGGER agendamentos_update_timestamp
AFTER
UPDATE ON agendamentos BEGIN
UPDATE agendamentos
SET atualizado_em = CURRENT_TIMESTAMP
WHERE id = NEW.id;
END;
-- ============================================
-- Tabela: categorias
-- ============================================
DROP TABLE IF EXISTS categorias;
CREATE TABLE categorias (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    tipo TEXT CHECK(tipo IN ('Receita', 'Despesa')) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(nome, tipo)
);
CREATE INDEX idx_categorias_tipo ON categorias(tipo);
CREATE TRIGGER categorias_update_timestamp
AFTER
UPDATE ON categorias BEGIN
UPDATE categorias
SET atualizado_em = CURRENT_TIMESTAMP
WHERE id = NEW.id;
END;
-- ============================================
-- Tabela: contas
-- ============================================
DROP TABLE IF EXISTS contas;
CREATE TABLE contas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL UNIQUE,
    saldo_inicial REAL DEFAULT 0.00,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_contas_nome ON contas(nome);
CREATE TRIGGER contas_update_timestamp
AFTER
UPDATE ON contas BEGIN
UPDATE contas
SET atualizado_em = CURRENT_TIMESTAMP
WHERE id = NEW.id;
END;
-- ============================================
-- Tabela: transacoes
-- ============================================
DROP TABLE IF EXISTS transacoes;
CREATE TABLE transacoes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tipo TEXT CHECK(
        tipo IN ('Receita', 'Despesa', 'receita', 'despesa')
    ) NOT NULL,
    nome TEXT NOT NULL,
    descricao TEXT,
    valor REAL NOT NULL,
    data_vencimento DATE NOT NULL,
    situacao TEXT CHECK(situacao IN ('Pago', 'A pagar')) DEFAULT 'A pagar',
    categoria_id INTEGER,
    conta_id INTEGER,
    parcelado INTEGER DEFAULT 0,
    qtd_parcelas INTEGER DEFAULT 1,
    data_inclusao DATETIME DEFAULT CURRENT_TIMESTAMP,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE
    SET NULL,
        FOREIGN KEY (conta_id) REFERENCES contas(id) ON DELETE
    SET NULL
);
CREATE INDEX idx_transacoes_tipo ON transacoes(tipo);
CREATE INDEX idx_transacoes_data_vencimento ON transacoes(data_vencimento);
CREATE INDEX idx_transacoes_situacao ON transacoes(situacao);
CREATE INDEX idx_transacoes_categoria ON transacoes(categoria_id);
CREATE INDEX idx_transacoes_conta ON transacoes(conta_id);
CREATE TRIGGER transacoes_update_timestamp
AFTER
UPDATE ON transacoes BEGIN
UPDATE transacoes
SET atualizado_em = CURRENT_TIMESTAMP
WHERE id = NEW.id;
END;
-- ============================================
-- Dados Iniciais
-- ============================================
-- Inserir usuário admin padrão (senha: 0389)
INSERT INTO usuarios (nome, email, cpf, telefone, senha, role)
VALUES (
        'Admin',
        'admin@email.com',
        '000.000.000-00',
        '(00) 00000-0000',
        '$2y$10$JRyiMvC9ZmXJfLGwq881puEFLGPGxGXmfnKgJTo6PFkqkVHSh2IYq',
        'admin'
    );
-- Inserir categorias padrão
INSERT INTO categorias (nome, tipo)
VALUES ('Consulta', 'Receita'),
    ('Terapia', 'Receita'),
    ('Avaliação', 'Receita'),
    ('Aluguel', 'Despesa'),
    ('Material de Escritório', 'Despesa'),
    ('Energia Elétrica', 'Despesa'),
    ('Internet', 'Despesa'),
    ('Telefone', 'Despesa'),
    ('Marketing', 'Despesa'),
    ('Impostos', 'Despesa');
-- Inserir contas padrão
INSERT INTO contas (nome, saldo_inicial)
VALUES ('Conta Corrente', 0.00),
    ('Caixa', 0.00),
    ('Poupança', 0.00);
-- ============================================
-- Views úteis
-- ============================================
-- View para saldo de contas
DROP VIEW IF EXISTS vw_saldo_contas;
CREATE VIEW vw_saldo_contas AS
SELECT c.id,
    c.nome,
    c.saldo_inicial,
    COALESCE(
        SUM(
            CASE
                WHEN t.tipo IN ('Receita', 'receita')
                AND t.situacao = 'Pago' THEN t.valor
                ELSE 0
            END
        ),
        0
    ) AS total_receitas,
    COALESCE(
        SUM(
            CASE
                WHEN t.tipo IN ('Despesa', 'despesa')
                AND t.situacao = 'Pago' THEN t.valor
                ELSE 0
            END
        ),
        0
    ) AS total_despesas,
    (
        c.saldo_inicial + COALESCE(
            SUM(
                CASE
                    WHEN t.tipo IN ('Receita', 'receita')
                    AND t.situacao = 'Pago' THEN t.valor
                    ELSE 0
                END
            ),
            0
        ) - COALESCE(
            SUM(
                CASE
                    WHEN t.tipo IN ('Despesa', 'despesa')
                    AND t.situacao = 'Pago' THEN t.valor
                    ELSE 0
                END
            ),
            0
        )
    ) AS saldo_atual
FROM contas c
    LEFT JOIN transacoes t ON c.id = t.conta_id
GROUP BY c.id,
    c.nome,
    c.saldo_inicial;
-- View para resumo financeiro
DROP VIEW IF EXISTS vw_resumo_financeiro;
CREATE VIEW vw_resumo_financeiro AS
SELECT strftime('%Y-%m', data_vencimento) AS mes_ano,
    tipo,
    situacao,
    COUNT(*) AS quantidade,
    SUM(valor) AS total
FROM transacoes
GROUP BY strftime('%Y-%m', data_vencimento),
    tipo,
    situacao
ORDER BY mes_ano DESC,
    tipo;
-- ============================================
-- Fim do Script
-- ============================================