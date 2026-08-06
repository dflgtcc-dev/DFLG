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

        if (!empty($filtros['dataFim'])) {
            $sql .= " AND data_transacao <= :dataFim";
            $params[':dataFim'] = $filtros['dataFim'];
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

    /**
     * Soma as despesas dia a dia de um mês inteiro — é o que alimenta o
     * Mapa de Calor do Dashboard com dados reais. Formato: [dia => total].
     */
    public function getGastosDiariosDoMes(?int $usuarioId, int $ano, int $mes): array
    {
        $inicio = sprintf('%04d-%02d-01', $ano, $mes);
        $fim = date('Y-m-t', strtotime($inicio));

        if ($usuarioId !== null) {
            $sql = "SELECT DAY(data_transacao) AS dia, SUM(valor) AS total
                    FROM transacoes
                    WHERE tipo = 'despesa' AND data_transacao BETWEEN :inicio AND :fim
                      AND (usuario_id = :usuario_id OR usuario_id IS NULL)
                    GROUP BY DAY(data_transacao)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        } else {
            $sql = "SELECT DAY(data_transacao) AS dia, SUM(valor) AS total
                    FROM transacoes
                    WHERE tipo = 'despesa' AND data_transacao BETWEEN :inicio AND :fim
                      AND usuario_id IS NULL
                    GROUP BY DAY(data_transacao)";
            $stmt = $this->connection->prepare($sql);
        }

        $stmt->bindValue(':inicio', $inicio);
        $stmt->bindValue(':fim', $fim);
        $stmt->execute();

        $resultado = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $resultado[(int) $linha['dia']] = (float) $linha['total'];
        }

        return $resultado;
    }

    /** Últimas transações registradas por um usuário — usado nas "Atividades Recentes" do perfil. */
    public function getRecentesPorUsuario(int $usuarioId, int $limite = 5): array
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM transacoes WHERE usuario_id = :usuario_id ORDER BY criado_em DESC, id DESC LIMIT :limite"
        );
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Soma (e conta) as despesas já registradas, agrupadas por categoria —
     * é o que alimenta a tela de Categorias com o "gasto" de cada uma.
     * Formato: ['NomeCategoria' => ['total' => float, 'qtd' => int]]
     */
    public function getGastosPorCategoria(?int $usuarioId): array
    {
        if ($usuarioId !== null) {
            $sql = "SELECT categoria, SUM(valor) AS total, COUNT(*) AS qtd
                    FROM transacoes
                    WHERE tipo = 'despesa' AND (usuario_id = :usuario_id OR usuario_id IS NULL)
                    GROUP BY categoria";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        } else {
            $sql = "SELECT categoria, SUM(valor) AS total, COUNT(*) AS qtd
                    FROM transacoes
                    WHERE tipo = 'despesa' AND usuario_id IS NULL
                    GROUP BY categoria";
            $stmt = $this->connection->prepare($sql);
        }

        $stmt->execute();

        $gastos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $gastos[$linha['categoria']] = ['total' => (float) $linha['total'], 'qtd' => (int) $linha['qtd']];
        }

        return $gastos;
    }
}
