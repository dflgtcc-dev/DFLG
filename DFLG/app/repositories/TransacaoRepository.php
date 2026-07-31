<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Transacao;
use PDO;

class TransacaoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    /**
     * Lista transações aplicando os filtros do front (período, tipo,
     * categoria, busca) e a ordenação escolhida.
     *
     * Filtros aceitos:
     *   'dataInicio' => 'Y-m-d' | null
     *   'tipo'       => 'receita' | 'despesa' | 'all'
     *   'categoria'  => string | 'all'
     *   'busca'      => string
     *   'ordenar'    => 'newest' | 'oldest' | 'highest' | 'lowest'
     */
    public function getAll(array $filtros = []): array
    {
        $sql = "SELECT * FROM transacoes WHERE 1 = 1";
        $params = [];

        if (!empty($filtros['dataInicio'])) {
            $sql .= " AND data_transacao >= :dataInicio";
            $params[':dataInicio'] = $filtros['dataInicio'];
        }

        if (!empty($filtros['tipo']) && $filtros['tipo'] !== 'all') {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

        if (!empty($filtros['categoria']) && $filtros['categoria'] !== 'all') {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }

        if (!empty($filtros['busca'])) {
            $sql .= " AND (descricao LIKE :busca OR categoria LIKE :busca2)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
            $params[':busca2'] = '%' . $filtros['busca'] . '%';
        }

        $ordenar = $filtros['ordenar'] ?? 'newest';
        $sql .= match ($ordenar) {
            'oldest' => " ORDER BY data_transacao ASC, id ASC",
            'highest' => " ORDER BY valor DESC",
            'lowest' => " ORDER BY valor ASC",
            default => " ORDER BY data_transacao DESC, id DESC",
        };

        $stm = $this->connection->prepare($sql);
        $stm->execute($params);

        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stm = $this->connection->prepare("SELECT * FROM transacoes WHERE id = :id");
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();

        $transacao = $stm->fetch(PDO::FETCH_ASSOC);
        return $transacao ?: null;
    }

    public function create(Transacao $transacao): bool
    {
        $sql = "INSERT INTO transacoes (usuario_id, descricao, valor, categoria, tipo, data_transacao, moeda)
                VALUES (:usuario_id, :descricao, :valor, :categoria, :tipo, :data_transacao, :moeda)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuario_id', $transacao->getUsuarioId(), PDO::PARAM_INT);
        $stmt->bindValue(':descricao', $transacao->getDescricao());
        $stmt->bindValue(':valor', $transacao->getValor());
        $stmt->bindValue(':categoria', $transacao->getCategoria());
        $stmt->bindValue(':tipo', $transacao->getTipo());
        $stmt->bindValue(':data_transacao', $transacao->getData());
        $stmt->bindValue(':moeda', $transacao->getMoeda());

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM transacoes WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
