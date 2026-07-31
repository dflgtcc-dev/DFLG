CREATE DATABASE IF NOT EXISTS dflg_investments;
USE dflg_investments;
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
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
