<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Parcela;
use PDO;

class ParcelaRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    /**
     * Lista os parcelamentos. Quando $usuarioId é informado, traz também
     * os registros "públicos" (usuario_id NULL) usados como dado de
     * demonstração antes de existir um usuário logado.
     */
    public function getAll(?int $usuarioId = null): array
    {
        if ($usuarioId !== null) {
            $sql = "SELECT * FROM parcelas WHERE usuario_id = :usuario_id OR usuario_id IS NULL ORDER BY data_primeira_parcela DESC";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM parcelas WHERE usuario_id IS NULL ORDER BY data_primeira_parcela DESC";
            $stmt = $this->connection->prepare($sql);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(Parcela $parcela): bool
    {
        $sql = "INSERT INTO parcelas (usuario_id, descricao, categoria, valor_total, numero_parcelas, valor_parcela, data_primeira_parcela)
                VALUES (:usuario_id, :descricao, :categoria, :valor_total, :numero_parcelas, :valor_parcela, :data_primeira_parcela)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuario_id', $parcela->getUsuarioId(), PDO::PARAM_INT);
        $stmt->bindValue(':descricao', $parcela->getDescricao());
        $stmt->bindValue(':categoria', $parcela->getCategoria());
        $stmt->bindValue(':valor_total', $parcela->getValorTotal());
        $stmt->bindValue(':numero_parcelas', $parcela->getNumeroParcelas(), PDO::PARAM_INT);
        $stmt->bindValue(':valor_parcela', $parcela->getValorParcela());
        $stmt->bindValue(':data_primeira_parcela', $parcela->getDataPrimeiraParcela());

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM parcelas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
