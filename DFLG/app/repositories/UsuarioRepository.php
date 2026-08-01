<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Usuario;
use PDO;

class UsuarioRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getUsuarios(): array
    {
        $sql = "SELECT id, nome_usuario as nomeUsuario, email, perfil FROM usuarios";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function saveUsuario(Usuario $usuario): bool
    {
        $sql = "INSERT INTO usuarios (nome_usuario, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $usuario->getNomeUsuario());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':senha', password_hash($usuario->getSenha(), PASSWORD_BCRYPT));
        $stmt->bindValue(':perfil', $usuario->getPerfil());
        return $stmt->execute();
    }

    public function getUsuarioById(int $id)
    {
        $sql = "SELECT id, nome_usuario as nomeUsuario, email, perfil FROM usuarios WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /** Busca o usuário completo (todas as colunas, incluindo gamificação) já hidratado como objeto. */
    public function getUsuarioCompletoById(int $id): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $dados = $stmt->fetch();

        return $dados ? Usuario::arrayParaObjeto($dados) : null;
    }

    public function getUsuarioByEmail(string $email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch();

        if ($usuario == null) {
            return false;
        }

        return Usuario::arrayParaObjeto($usuario);
    }

    public function updateUsuario(Usuario $usuario): bool
    {
        $sql = "UPDATE usuarios SET nome_usuario = :nome, email = :email, perfil = :perfil WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $usuario->getNomeUsuario());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':perfil', $usuario->getPerfil());
        $stmt->bindValue(':id', $usuario->getId());
        return $stmt->execute();
    }

    /** Atualiza só os dados pessoais editáveis na tela de Perfil. */
    public function atualizarDadosPessoais(int $id, string $nome, ?string $telefone, ?string $localizacao): bool
    {
        $sql = "UPDATE usuarios SET nome_usuario = :nome, telefone = :telefone, localizacao = :localizacao WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':localizacao', $localizacao);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** Soma pontos de gamificação ao usuário (ex: ao cadastrar uma transação). */
    public function adicionarPontos(int $id, int $pontos): bool
    {
        $stmt = $this->connection->prepare("UPDATE usuarios SET pontos_totais = pontos_totais + :pontos WHERE id = :id");
        $stmt->bindValue(':pontos', $pontos, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Registra o acesso do dia e recalcula a sequência (streak):
     *  - Se o último acesso foi ontem, incrementa a sequência.
     *  - Se já foi hoje, mantém.
     *  - Se ficou mais de um dia sem acessar, reinicia em 1.
     * Retorna os valores já atualizados de sequência.
     */
    public function registrarAcesso(int $id): array
    {
        $stmt = $this->connection->prepare("SELECT sequencia_atual, maior_sequencia, ultimo_acesso FROM usuarios WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $atual = $stmt->fetch();

        $hoje = new \DateTime('today');
        $sequenciaAtual = (int) ($atual['sequencia_atual'] ?? 0);
        $maiorSequencia = (int) ($atual['maior_sequencia'] ?? 0);
        $ultimoAcesso = $atual['ultimo_acesso'] ?? null;

        if ($ultimoAcesso === $hoje->format('Y-m-d')) {
            // já registrou acesso hoje, não mexe na sequência
        } elseif ($ultimoAcesso !== null && (new \DateTime($ultimoAcesso))->diff($hoje)->days === 1) {
            $sequenciaAtual++;
        } else {
            $sequenciaAtual = 1;
        }

        $maiorSequencia = max($maiorSequencia, $sequenciaAtual);

        $update = $this->connection->prepare(
            "UPDATE usuarios SET sequencia_atual = :seq, maior_sequencia = :maior, ultimo_acesso = :hoje WHERE id = :id"
        );
        $update->bindValue(':seq', $sequenciaAtual, PDO::PARAM_INT);
        $update->bindValue(':maior', $maiorSequencia, PDO::PARAM_INT);
        $update->bindValue(':hoje', $hoje->format('Y-m-d'));
        $update->bindValue(':id', $id, PDO::PARAM_INT);
        $update->execute();

        return [
            'sequenciaAtual' => $sequenciaAtual,
            'maiorSequencia' => $maiorSequencia,
            'ultimoAcesso' => $hoje->format('Y-m-d'),
        ];
    }

    /** Ranking geral de usuários por pontos (usado para achar a posição do usuário logado). */
    public function getRankingPorPontos(): array
    {
        $sql = "SELECT id, nome_usuario as nomeUsuario, pontos_totais as pontosTotais FROM usuarios ORDER BY pontos_totais DESC, id ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function deleteUsuario(int $id): bool
    {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
