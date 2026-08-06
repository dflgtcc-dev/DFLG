<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Meta;
use PDO;

class MetaRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    /**
     * Lista as metas. Quando $usuarioId é informado, traz também os
     * registros "públicos" (usuario_id NULL) usados como demonstração
     * antes de existir um usuário logado — mesmo padrão de Parcela/Categoria.
     */
    public function getAll(?int $usuarioId = null): array
    {
        if ($usuarioId !== null) {
            $sql = "SELECT * FROM metas WHERE usuario_id = :usuario_id OR usuario_id IS NULL ORDER BY concluida ASC, data_limite ASC";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM metas WHERE usuario_id IS NULL ORDER BY concluida ASC, data_limite ASC";
            $stmt = $this->connection->prepare($sql);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?Meta
    {
        $stmt = $this->connection->prepare("SELECT * FROM metas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        return $linha ? Meta::arrayParaObjeto($linha) : null;
    }

    public function create(Meta $meta): bool
    {
        $sql = "INSERT INTO metas (usuario_id, nome_meta, tipo, valor_meta, valor_atual, data_limite)
                VALUES (:usuario_id, :nome_meta, :tipo, :valor_meta, :valor_atual, :data_limite)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuario_id', $meta->getUsuarioId(), PDO::PARAM_INT);
        $stmt->bindValue(':nome_meta', $meta->getNomeMeta());
        $stmt->bindValue(':tipo', $meta->getTipo());
        $stmt->bindValue(':valor_meta', $meta->getValorMeta());
        $stmt->bindValue(':valor_atual', $meta->getValorAtual());
        $stmt->bindValue(':data_limite', $meta->getDataLimite());

        return $stmt->execute();
    }

    /** Atualiza os dados cadastrais da meta (nome, tipo, valor-alvo, prazo). */
    public function update(Meta $meta): bool
    {
        $sql = "UPDATE metas SET nome_meta = :nome_meta, tipo = :tipo, valor_meta = :valor_meta, data_limite = :data_limite
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome_meta', $meta->getNomeMeta());
        $stmt->bindValue(':tipo', $meta->getTipo());
        $stmt->bindValue(':valor_meta', $meta->getValorMeta());
        $stmt->bindValue(':data_limite', $meta->getDataLimite());
        $stmt->bindValue(':id', $meta->getId(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    /** Atualiza o valor já guardado/investido e a flag de conclusão (RF19). */
    public function atualizarProgresso(int $id, float $novoValorAtual, bool $concluida): bool
    {
        $sql = "UPDATE metas SET valor_atual = :valor_atual, concluida = :concluida WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':valor_atual', $novoValorAtual);
        $stmt->bindValue(':concluida', $concluida ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** Fixa/desafixa a meta no Dashboard (aparece sempre entre as 3 exibidas por lá). */
    public function definirFixada(int $id, bool $fixada): bool
    {
        $stmt = $this->connection->prepare("UPDATE metas SET fixada = :fixada WHERE id = :id");
        $stmt->bindValue(':fixada', $fixada ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM metas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
