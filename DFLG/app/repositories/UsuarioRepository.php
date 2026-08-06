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
        $sql = "INSERT INTO usuarios (nome_usuario, sobrenome, nickname, email, senha, perfil, cpf_cnpj, data_nascimento)
                VALUES (:nome, :sobrenome, :nickname, :email, :senha, :perfil, :cpfCnpj, :dataNascimento)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $usuario->getNomeUsuario());
        $stmt->bindValue(':sobrenome', $usuario->getSobrenome());
        $stmt->bindValue(':nickname', $usuario->getNickname());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':senha', password_hash($usuario->getSenha(), PASSWORD_BCRYPT));
        $stmt->bindValue(':perfil', $usuario->getPerfil());
        $stmt->bindValue(':cpfCnpj', $usuario->getCpfCnpj());
        $stmt->bindValue(':dataNascimento', $usuario->getDataNascimento());
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

    /** Usado na validação de unicidade do nickname no cadastro (RN02). */
    public function getUsuarioByNickname(string $nickname)
    {
        $sql = "SELECT * FROM usuarios WHERE nickname = :nickname";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nickname', $nickname);
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
    public function atualizarDadosPessoais(
        int $id,
        string $nome,
        ?string $sobrenome,
        ?string $nickname,
        ?string $telefone,
        ?string $localizacao,
        ?string $dataNascimento
    ): bool {
        // Propositalmente sem cpf_cnpj: assim como email/senha, não é alterável por aqui.
        $sql = "UPDATE usuarios SET nome_usuario = :nome, sobrenome = :sobrenome, nickname = :nickname,
                    telefone = :telefone, localizacao = :localizacao, data_nascimento = :dataNascimento
                WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':sobrenome', $sobrenome);
        $stmt->bindValue(':nickname', $nickname);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':localizacao', $localizacao);
        $stmt->bindValue(':dataNascimento', $dataNascimento);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** Salva o nome do arquivo da foto de perfil já enviada para public/assets/img/perfil. */
    public function atualizarFoto(int $id, string $nomeArquivo): bool
    {
        $stmt = $this->connection->prepare("UPDATE usuarios SET foto = :foto WHERE id = :id");
        $stmt->bindValue(':foto', $nomeArquivo);
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
     * RN07: a pontuação de XP só é contabilizada no primeiro registro
     * financeiro (ou parcelamento) feito no dia — evita que o usuário
     * "force" a subida de nível criando vários lançamentos falsos no
     * mesmo dia. Retorna true se os pontos foram somados (1º do dia) e
     * false se o usuário já havia ganhado XP hoje (pontos não somados).
     */
    public function adicionarPontosSeElegivel(int $id, int $pontos): bool
    {
        $stmt = $this->connection->prepare("SELECT data_ultimo_xp FROM usuarios WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $atual = $stmt->fetch();

        $hoje = (new \DateTime('today'))->format('Y-m-d');
        $dataUltimoXp = $atual['data_ultimo_xp'] ?? null;

        if ($dataUltimoXp === $hoje) {
            // Já ganhou XP hoje — não soma de novo.
            return false;
        }

        $update = $this->connection->prepare(
            "UPDATE usuarios SET pontos_totais = pontos_totais + :pontos, data_ultimo_xp = :hoje WHERE id = :id"
        );
        $update->bindValue(':pontos', $pontos, PDO::PARAM_INT);
        $update->bindValue(':hoje', $hoje);
        $update->bindValue(':id', $id, PDO::PARAM_INT);
        return $update->execute();
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
