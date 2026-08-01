<?php

namespace app\services;

use app\repositories\CategoriaRepository;

class CategoriaService
{
    /**
     * Categorias de despesa disponíveis para orçamento, com ícone e cor —
     * o mesmo conjunto de nomes usado em Transações/Parcelamentos (menos
     * "Trabalho" e "Extra", que são categorias de receita, não de gasto).
     */
    public const CATALOGO = [
        'Moradia'     => ['icone' => 'bi-house',         'cor' => '#10B981'],
        'Alimentação' => ['icone' => 'bi-cup-hot',        'cor' => '#22C55E'],
        'Contas'      => ['icone' => 'bi-receipt',        'cor' => '#3B82F6'],
        'Saúde'       => ['icone' => 'bi-heart-pulse',    'cor' => '#6EE7B7'],
        'Lazer'       => ['icone' => 'bi-controller',     'cor' => '#A7F3D0'],
        'Transporte'  => ['icone' => 'bi-car-front',      'cor' => '#34D399'],
        'Educação'    => ['icone' => 'bi-mortarboard',    'cor' => '#F59E0B'],
        'Tecnologia'  => ['icone' => 'bi-phone',          'cor' => '#8B5CF6'],
        'Outros'      => ['icone' => 'bi-three-dots',     'cor' => '#D1FAE5'],
    ];

    private CategoriaRepository $repository;
    private TransacaoService $transacaoService;

    public function __construct()
    {
        $this->repository = new CategoriaRepository();
        $this->transacaoService = new TransacaoService();
    }

    public function atualizarOrcamento(?int $usuarioId, string $nome, float $valor): bool
    {
        if (!array_key_exists($nome, self::CATALOGO)) {
            return false;
        }

        return $this->repository->upsertOrcamento($usuarioId, $nome, $valor);
    }

    /**
     * Junta o catálogo de categorias com o orçamento definido (categorias)
     * e o gasto real já registrado (transacoes) — essa é a "cola" que faz
     * a tela funcionar em conjunto com o resto do sistema.
     */
    public function listarComGasto(?int $usuarioId): array
    {
        $orcamentos = $this->repository->getOrcamentos($usuarioId);
        $gastos = $this->transacaoService->gastosPorCategoria($usuarioId);

        $categorias = [];
        foreach (self::CATALOGO as $nome => $visual) {
            $gasto = $gastos[$nome]['total'] ?? 0.0;
            $qtdTransacoes = $gastos[$nome]['qtd'] ?? 0;
            $orcamento = $orcamentos[$nome] ?? 0.0;

            $percentual = $orcamento > 0 ? ($gasto / $orcamento) * 100 : 0;
            $percentualExibido = min(100, $percentual);
            $pertoDoLimite = $orcamento > 0 && $percentual > 80;

            $categorias[] = [
                'nome' => $nome,
                'icone' => $visual['icone'],
                'cor' => $visual['cor'],
                'gasto' => $gasto,
                'orcamento' => $orcamento,
                'transacoes' => $qtdTransacoes,
                'percentual' => $percentual,
                'percentualExibido' => (int) round($percentualExibido),
                'pertoDoLimite' => $pertoDoLimite,
                'restante' => max(0, $orcamento - $gasto),
            ];
        }

        return $categorias;
    }

    /** Totais para os cards do topo. */
    public function resumo(array $categorias): array
    {
        $totalGasto = array_sum(array_column($categorias, 'gasto'));
        $totalOrcamento = array_sum(array_column($categorias, 'orcamento'));
        $pertoDoLimite = count(array_filter($categorias, fn($c) => $c['pertoDoLimite']));

        return [
            'totalGasto' => $totalGasto,
            'totalOrcamento' => $totalOrcamento,
            'categoriasAtivas' => count($categorias),
            'pertoDoLimite' => $pertoDoLimite,
        ];
    }
}
