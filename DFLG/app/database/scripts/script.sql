-- Script inicial do banco de dados do Projeto Integrador.
-- Executado automaticamente pelo DatabaseInitializer quando o banco
-- ainda não existe (ver app/database/ConnectionFactory.php).
--
-- As tabelas de categorias e parcelas ainda faltam ser adicionadas aqui
-- conforme os respectivos Controllers forem implementados.
--
-- IMPORTANTE: o DatabaseInitializer só roda este script quando o banco
-- ainda NÃO existe. Se vocês já tinham criado "db_projeto_integrador"
-- antes (ex: ao testar o login), apaguem o banco no phpMyAdmin/consola
-- e deixem o sistema recriar tudo, ou rodem manualmente o bloco da
-- tabela "transacoes" abaixo.

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_usuario VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil VARCHAR(20) NOT NULL DEFAULT 'usuario',
    telefone VARCHAR(30) NULL,
    localizacao VARCHAR(120) NULL,
    pontos_totais INT NOT NULL DEFAULT 0,
    sequencia_atual INT NOT NULL DEFAULT 0,
    maior_sequencia INT NOT NULL DEFAULT 0,
    ultimo_acesso DATE NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- MIGRAÇÃO: se o banco "db_projeto_integrador" já existia antes (ex: já
-- tinham testado login/transações), o CREATE TABLE acima é ignorado por
-- causa do IF NOT EXISTS. Rodem manualmente os comandos abaixo uma vez
-- no phpMyAdmin/console para adicionar as colunas novas de perfil e
-- gamificação (funciona em MySQL 8.0.29+ e MariaDB 10.0.2+):
--
-- ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS telefone VARCHAR(30) NULL;
-- ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS localizacao VARCHAR(120) NULL;
-- ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS pontos_totais INT NOT NULL DEFAULT 0;
-- ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS sequencia_atual INT NOT NULL DEFAULT 0;
-- ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS maior_sequencia INT NOT NULL DEFAULT 0;
-- ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS ultimo_acesso DATE NULL;
-- ---------------------------------------------------------------------

-- Conta padrão para facilitar testes (login: admin@dflg.com / senha: admin123).
-- A senha abaixo já está com hash bcrypt — NÃO é o texto puro salvo no banco.
-- INSERT IGNORE evita erro caso essa conta já exista (email é UNIQUE).
INSERT IGNORE INTO usuarios (nome_usuario, email, senha, perfil) VALUES
('Administrador', 'admin@dflg.com', '$2b$12$WFcgWxYfu40b.rZl/BYZNuwes7/6YoWM/1.IR2OjJU/VYTqGIKXjO', 'admin');

CREATE TABLE IF NOT EXISTS transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    descricao VARCHAR(255) NOT NULL,
    valor DECIMAL(12, 2) NOT NULL,
    categoria VARCHAR(60) NOT NULL,
    tipo ENUM('receita', 'despesa') NOT NULL,
    data_transacao DATE NOT NULL,
    moeda VARCHAR(3) NOT NULL DEFAULT 'BRL',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Dados de exemplo, iguais aos usados no protótipo do Figma, só para o
-- front não ficar vazio antes de existir um cadastro real de usuário.
INSERT INTO transacoes (descricao, valor, categoria, tipo, data_transacao) VALUES
('Salário',          7500.00, 'Trabalho',    'receita', '2026-06-01'),
('Freelance design',  1200.00, 'Extra',       'receita', '2026-06-04'),
('Aluguel',           1800.00, 'Moradia',     'despesa', '2026-06-05'),
('Supermercado',       430.00, 'Alimentação', 'despesa', '2026-06-06'),
('Energia elétrica',   220.00, 'Contas',      'despesa', '2026-06-07'),
('Internet',           120.00, 'Contas',      'despesa', '2026-06-07'),
('Academia',           180.00, 'Saúde',       'despesa', '2026-06-08'),
('Netflix',             55.00, 'Lazer',       'despesa', '2026-06-09'),
('Restaurante',        142.00, 'Alimentação', 'despesa', '2026-06-10'),
('Uber',                38.00, 'Transporte',  'despesa', '2026-06-11'),
('Dividendos',         650.00, 'Extra',       'receita', '2026-06-12'),
('Farmácia',            87.00, 'Saúde',       'despesa', '2026-06-13'),
('Curso online',       199.00, 'Educação',    'despesa', '2026-06-14'),
('Spotify',             22.00, 'Lazer',       'despesa', '2026-06-15'),
('Gasolina',           210.00, 'Transporte',  'despesa', '2026-06-16'),
('Venda equipamento',  800.00, 'Extra',       'receita', '2026-06-16'),
('Celular parcela',    155.00, 'Tecnologia',  'despesa', '2026-06-17'),
('Padaria',             48.00, 'Alimentação', 'despesa', '2026-06-17'),
('Salário',           7500.00, 'Trabalho',    'receita', '2026-05-01'),
('Aluguel',            1800.00, 'Moradia',     'despesa', '2026-05-05'),
('Supermercado',        510.00, 'Alimentação', 'despesa', '2026-05-08'),
('Freelance UX',        900.00, 'Extra',       'receita', '2026-05-15'),
('Energia elétrica',    198.00, 'Contas',      'despesa', '2026-05-07');

CREATE TABLE IF NOT EXISTS parcelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    descricao VARCHAR(255) NOT NULL,
    categoria VARCHAR(60) NOT NULL,
    valor_total DECIMAL(12, 2) NOT NULL,
    numero_parcelas INT NOT NULL,
    valor_parcela DECIMAL(12, 2) NOT NULL,
    data_primeira_parcela DATE NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Dados de exemplo (mesmos itens do protótipo do Figma). Repare que aqui
-- NÃO existe uma coluna "parcela atual": o ParcelaService calcula quantas
-- parcelas já venceram comparando "data_primeira_parcela" com a data de
-- hoje — por isso os valores abaixo avançam sozinhos com o tempo.
INSERT INTO parcelas (descricao, categoria, valor_total, numero_parcelas, valor_parcela, data_primeira_parcela) VALUES
('Notebook Dell',    'Tecnologia', 4500.00, 10, 450.00, '2026-05-01'),
('Sofá',              'Moradia',   2400.00, 12, 200.00, '2026-02-01'),
('Curso Online',      'Educação',  600.00,  6,  100.00, '2026-06-01'),
('Geladeira',         'Moradia',   3600.00, 12, 300.00, '2025-12-01'),
('Celular Samsung',   'Tecnologia',2700.00, 9,  300.00, '2026-03-01');

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    nome VARCHAR(60) NOT NULL,
    orcamento_mensal DECIMAL(12, 2) NOT NULL DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Orçamentos padrão (usuario_id NULL) para as categorias de despesa.
-- O "gasto" de cada uma NÃO vem daqui — é somado em tempo real a partir
-- da tabela "transacoes" (ver CategoriaService::listarComGasto).
INSERT INTO categorias (nome, orcamento_mensal) VALUES
('Moradia', 4000.00),
('Alimentação', 1300.00),
('Contas', 700.00),
('Saúde', 500.00),
('Lazer', 300.00),
('Transporte', 400.00),
('Educação', 300.00),
('Tecnologia', 500.00),
('Outros', 200.00);
