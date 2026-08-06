<?php

namespace app\services;

use app\models\Transacao;
use app\repositories\TransacaoRepository;

class TransacaoService
{
    /** Lista de categorias disponíveis no sistema (mesma lista do Figma). */
    public const CATEGORIAS = [
        'Trabalho', 'Extra', 'Moradia', 'Alimentação', 'Contas',
        'Saúde', 'Lazer', 'Transporte', 'Educação', 'Tecnologia', 'Outros',
    ];

    private TransacaoRepository $repository;

    public function __construct()
    {
        $this->repository = new TransacaoRepository();
    }

    /** Lista transações já filtradas/ordenadas de acordo com o array de filtros. */
    public function listar(array $filtros = []): array
    {
        return $this->repository->getAll($filtros);
    }

    public function buscar(int $id): ?array
    {
        return $this->repository->getById($id);
    }

    public function criar(Transacao $transacao): bool
    {
        return $this->repository->create($transacao);
    }

    public function remover(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /** Últimas transações de um usuário (usado nas "Atividades Recentes" do perfil). */
    public function recentesPorUsuario(int $usuarioId, int $limite = 5): array
    {
        return $this->repository->getRecentesPorUsuario($usuarioId, $limite);
    }

    /** Gastos (soma + contagem) por categoria — usado na tela de Categorias. */
    public function gastosPorCategoria(?int $usuarioId): array
    {
        return $this->repository->getGastosPorCategoria($usuarioId);
    }

    /** Gastos dia a dia de um mês — alimenta o Mapa de Calor do Dashboard com dados reais. */
    public function gastosDiariosDoMes(?int $usuarioId, int $ano, int $mes): array
    {
        return $this->repository->getGastosDiariosDoMes($usuarioId, $ano, $mes);
    }

    /** Converte o filtro de período ('week', 'month', '3months', '6months', 'all') em uma data mínima. */
    public function dataInicioPeriodo(string $periodo): ?string
    {
        $hoje = new \DateTime();

        return match ($periodo) {
            'week' => (clone $hoje)->modify('-7 days')->format('Y-m-d'),
            'month' => (clone $hoje)->modify('-1 month')->format('Y-m-d'),
            '3months' => (clone $hoje)->modify('-3 months')->format('Y-m-d'),
            '6months' => (clone $hoje)->modify('-6 months')->format('Y-m-d'),
            default => null, // 'all'
        };
    }

    /** Calcula os totais (receitas, despesas, saldo) de uma lista de transações já filtrada. */
    public function resumo(array $transacoes): array
    {
        $totalReceitas = 0.0;
        $totalDespesas = 0.0;

        foreach ($transacoes as $t) {
            if ($t['tipo'] === 'receita') {
                $totalReceitas += (float) $t['valor'];
            } else {
                $totalDespesas += (float) $t['valor'];
            }
        }

        return [
            'totalReceitas' => $totalReceitas,
            'totalDespesas' => $totalDespesas,
            'saldo' => $totalReceitas - $totalDespesas,
            'total' => count($transacoes),
        ];
    }
}
