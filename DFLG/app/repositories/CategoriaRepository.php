<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use PDO;

class CategoriaRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    /**
     * Retorna os orçamentos definidos, no formato ['NomeCategoria' => valor].
     * Se o usuário estiver logado e já tiver personalizado o orçamento de
     * uma categoria, esse valor tem prioridade sobre o padrão (usuario_id
     * NULL) — mesma lógica de "dado público de demonstração" usada em
     * transações e parcelamentos.
     */
    public function getOrcamentos(?int $usuarioId): array
    {
        if ($usuarioId !== null) {
            $sql = "SELECT nome, orcamento_mensal, usuario_id FROM categorias
                    WHERE usuario_id = :usuario_id OR usuario_id IS NULL
                    ORDER BY (usuario_id IS NULL) ASC";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        } else {
            $sql = "SELECT nome, orcamento_mensal, usuario_id FROM categorias WHERE usuario_id IS NULL";
            $stmt = $this->connection->prepare($sql);
        }

        $stmt->execute();

        $orcamentos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            // Como a query já traz o registro do usuário antes do padrão,
            // só guardamos a primeira ocorrência de cada nome.
            if (!isset($orcamentos[$linha['nome']])) {
                $orcamentos[$linha['nome']] = (float) $linha['orcamento_mensal'];
            }
        }

        return $orcamentos;
    }

    /** Cria ou atualiza o orçamento de uma categoria para o usuário (ou o padrão público, se não logado). */
    public function upsertOrcamento(?int $usuarioId, string $nome, float $valor): bool
    {
        $sqlBusca = $usuarioId !== null
            ? "SELECT id FROM categorias WHERE usuario_id = :usuario_id AND nome = :nome"
            : "SELECT id FROM categorias WHERE usuario_id IS NULL AND nome = :nome";

        $busca = $this->connection->prepare($sqlBusca);
        if ($usuarioId !== null) {
            $busca->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        }
        $busca->bindValue(':nome', $nome);
        $busca->execute();
        $existente = $busca->fetch(PDO::FETCH_ASSOC);

        if ($existente) {
            $update = $this->connection->prepare("UPDATE categorias SET orcamento_mensal = :valor WHERE id = :id");
            $update->bindValue(':valor', $valor);
            $update->bindValue(':id', $existente['id'], PDO::PARAM_INT);
            return $update->execute();
        }

        $insert = $this->connection->prepare(
            "INSERT INTO categorias (usuario_id, nome, orcamento_mensal) VALUES (:usuario_id, :nome, :valor)"
        );
        $insert->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $insert->bindValue(':nome', $nome);
        $insert->bindValue(':valor', $valor);
        return $insert->execute();
    }
}
