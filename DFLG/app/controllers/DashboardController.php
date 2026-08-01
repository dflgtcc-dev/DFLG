<?php

namespace app\controllers;

use app\core\Controller;
use app\services\ParcelaService;

class DashboardController extends Controller
{
    /**
     * Tela "Visão Geral".
     *
     * A maioria dos dados ainda é fixa (mock), só para montar o front-end.
     * Os parcelamentos já vêm de verdade do ParcelaService (ver abaixo).
     * Quando a camada Model/Repository/Service de Transacao e Categoria
     * também estiver 100% migrada, o restante deve seguir para um
     * DashboardService que busca os valores reais do usuário logado.
     */
    public function index()
    {
        // $this->autenticacaoRequired();

        $totalReceitas = 8500.00;
        $despesasRealizadas = 5200.00;

        // Parcelamentos: agora vêm de verdade da tela /parcelamentos (ParcelaService),
        // com o progresso calculado pela data — não é mais um valor fixo.
        $usuarioId = isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado']->getId() : null;
        $parcelaService = new ParcelaService();
        $parcelasAtivas = $parcelaService->listarComProgresso($usuarioId, true);
        $resumoParcelas = $parcelaService->resumo($parcelasAtivas);

        $parcelamentosDoMes = $resumoParcelas['totalMensal'];
        $totalDespesas = $despesasRealizadas + $parcelamentosDoMes;
        $saldo = $totalReceitas - $totalDespesas;

        $metaValor = 10000.00;
        $metaAtual = 6500.00;
        $metaProgresso = (int) round(($metaAtual / $metaValor) * 100);

        // Receitas x Despesas (últimos 7 meses)
        $graficoMensal = [
            ['mes' => 'Out', 'receitas' => 7800, 'despesas' => 5500, 'parcelas' => 1200],
            ['mes' => 'Nov', 'receitas' => 8200, 'despesas' => 5300, 'parcelas' => 1200],
            ['mes' => 'Dez', 'receitas' => 9500, 'despesas' => 6800, 'parcelas' => 1400],
            ['mes' => 'Jan', 'receitas' => 8000, 'despesas' => 5100, 'parcelas' => 1300],
            ['mes' => 'Fev', 'receitas' => 8300, 'despesas' => 5400, 'parcelas' => 1300],
            ['mes' => 'Mar', 'receitas' => 8100, 'despesas' => 4900, 'parcelas' => 1350],
            ['mes' => 'Abr', 'receitas' => 8500, 'despesas' => 5200, 'parcelas' => 1350],
        ];

        // Despesas por categoria (inclui parcelas ativas)
        $despesasPorCategoria = [
            ['nome' => 'Moradia',      'valor' => 2300, 'cor' => '#10B981'],
            ['nome' => 'Alimentação',  'valor' => 850,  'cor' => '#22C55E'],
            ['nome' => 'Tecnologia',   'valor' => 750,  'cor' => '#34D399'],
            ['nome' => 'Transporte',   'valor' => 650,  'cor' => '#6EE7B7'],
            ['nome' => 'Educação',     'valor' => 100,  'cor' => '#A7F3D0'],
            ['nome' => 'Lazer',        'valor' => 430,  'cor' => '#F59E0B'],
            ['nome' => 'Contas',       'valor' => 520,  'cor' => '#3B82F6'],
            ['nome' => 'Outros',       'valor' => 450,  'cor' => '#8B5CF6'],
        ];

        // Mapa de calor de gastos diários (ano, mês e dia "hoje" fixos para o mock)
        $heatMapAno = 2026;
        $heatMapMes = 6; // Junho
        $heatMapHoje = 18;
        $gastosDiarios = [
            3 => 120, 5 => 1800, 6 => 430, 7 => 340, 8 => 180,
            9 => 55, 10 => 142, 11 => 38, 12 => 87, 13 => 199,
            14 => 22, 15 => 210, 16 => 155, 17 => 48,
        ];

        // Transações recentes
        $transacoesRecentes = [
            ['descricao' => 'Padaria',           'categoria' => 'Alimentação', 'tipo' => 'despesa', 'valor' => 48,  'data' => '2026-06-17'],
            ['descricao' => 'Celular parcela',    'categoria' => 'Tecnologia',  'tipo' => 'despesa', 'valor' => 155, 'data' => '2026-06-16'],
            ['descricao' => 'Venda equipamento',  'categoria' => 'Extra',       'tipo' => 'receita', 'valor' => 800, 'data' => '2026-06-16'],
            ['descricao' => 'Gasolina',           'categoria' => 'Transporte',  'tipo' => 'despesa', 'valor' => 210, 'data' => '2026-06-15'],
            ['descricao' => 'Spotify',            'categoria' => 'Lazer',       'tipo' => 'despesa', 'valor' => 22,  'data' => '2026-06-14'],
            ['descricao' => 'Curso online',       'categoria' => 'Educação',    'tipo' => 'despesa', 'valor' => 199, 'data' => '2026-06-13'],
            ['descricao' => 'Farmácia',           'categoria' => 'Saúde',       'tipo' => 'despesa', 'valor' => 87,  'data' => '2026-06-12'],
        ];

        // Metas
        $metas = [
            ['nome' => 'Viagem',               'valorAtual' => 6500,  'valorAlvo' => 10000],
            ['nome' => 'Reserva de Emergência', 'valorAtual' => 12000, 'valorAlvo' => 15000],
            ['nome' => 'Carro Novo',            'valorAtual' => 18000, 'valorAlvo' => 50000],
        ];

        // Próximos parcelamentos (os 3 mais próximos de vencer, entre os ativos)
        $ordenadas = $parcelasAtivas;
        usort($ordenadas, fn($a, $b) => strcmp($a['proximoVencimento'] ?? '9999', $b['proximoVencimento'] ?? '9999'));
        $proximasParcelas = array_map(fn($p) => [
            'descricao' => $p['descricao'],
            'categoria' => $p['categoria'],
            'valor' => (float) $p['valor_parcela'],
            'vencimento' => $p['proximoVencimento'],
        ], array_slice($ordenadas, 0, 3));
        $totalParcelasMes = $parcelamentosDoMes;

        $this->view('dashboard/index', [
            'activePage' => 'overview',
            'totalReceitas' => $totalReceitas,
            'totalDespesas' => $totalDespesas,
            'parcelamentosDoMes' => $parcelamentosDoMes,
            'saldo' => $saldo,
            'metaValor' => $metaValor,
            'metaAtual' => $metaAtual,
            'metaProgresso' => $metaProgresso,
            'graficoMensal' => $graficoMensal,
            'despesasPorCategoria' => $despesasPorCategoria,
            'heatMapAno' => $heatMapAno,
            'heatMapMes' => $heatMapMes,
            'heatMapHoje' => $heatMapHoje,
            'gastosDiarios' => $gastosDiarios,
            'transacoesRecentes' => $transacoesRecentes,
            'metas' => $metas,
            'proximasParcelas' => $proximasParcelas,
            'totalParcelasMes' => $totalParcelasMes,
        ]);
    }
}
