CREATE DATABASE IF NOT EXISTS dflg_investments;
USE dflg_investments;

-- Tabela de Usuários (Integra gamificação e controle de acesso)
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nickname VARCHAR(50) NOT NULL UNIQUE, -- RN 02 [1]
    email VARCHAR(100) NOT NULL UNIQUE,   -- RN 02 [1]
    senha VARCHAR(255) NOT NULL,
    xp_atual INT DEFAULT 0,               -- RF 06 [3]
    nivel INT DEFAULT 1,                  -- RF 06 [3]
    streak_dias INT DEFAULT 0,            -- RF 11 [3]
    papel_acesso ENUM('comum', 'admin') DEFAULT 'comum', -- RF 01 [3]
    data_nascimento DATE
);

-- Tabela de Categorias (Gerenciada pelo usuário com limites de gastos)
CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    limite_gastos_mensal DECIMAL(10, 2), -- RF 16 [3]
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- Tabela de Registros Financeiros (Receitas e Despesas)
CREATE TABLE registro_financeiro (
    id_registro INT AUTO_INCREMENT PRIMARY KEY,
    valor DECIMAL(10, 2) NOT NULL,
    data_registro DATE NOT NULL,
    id_usuario INT NOT NULL,
    id_categoria INT NOT NULL, -- Categoria como FK para definir o tipo [3]
    
    -- RN 05: Garante que o valor seja sempre maior que zero [1]
    CONSTRAINT chk_valor_positivo CHECK (valor > 0),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
);

-- Tabela de Metas Financeiras
CREATE TABLE meta_financeira (
    id_meta INT AUTO_INCREMENT PRIMARY KEY,
    tipo_meta ENUM('economizar', 'comprar', 'investir') NOT NULL, -- RF 08 [3]
    valor_objetivo DECIMAL(10, 2) NOT NULL,
    data_conclusao DATE NOT NULL,
    id_usuario INT NOT NULL,
    
    -- RN 08: Data de conclusão deve ser posterior à atual [1]
    -- Nota: A validação de data futura é geralmente feita via Trigger ou aplicação, 
    -- mas o campo está estruturado conforme o requisito.
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- Tabela de Parcelamentos
CREATE TABLE parcelamento (
    id_parcelamento INT AUTO_INCREMENT PRIMARY KEY,
    num_parcelas INT NOT NULL,
    valor_parcela DECIMAL(10, 2) NOT NULL,
    duracao_meses INT NOT NULL,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);