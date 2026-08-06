<?php
if (!function_exists('dflg_money')) {
    function dflg_money(float $valor): string
    {
        $sinal = $valor < 0 ? '−' : '';
        return $sinal . 'R$ ' . number_format(abs($valor), 2, ',', '.');
    }
}

// ---- Mapa de calor: monta a grade de semanas do mês ----
$primeiroDiaSemana = (int) date('w', mktime(0, 0, 0, $heatMapMes, 1, $heatMapAno)); // 0=Dom
$diasNoMes = (int) date('t', mktime(0, 0, 0, $heatMapMes, 1, $heatMapAno));

$celulas = array_fill(0, $primeiroDiaSemana, null);
for ($d = 1; $d <= $diasNoMes; $d++) {
    $celulas[] = $d;
}
while (count($celulas) % 7 !== 0) {
    $celulas[] = null;
}
$semanas = array_chunk($celulas, 7);

$gastoTotalAteHoje = 0;
$maiorGasto = ['dia' => 0, 'valor' => 0];
foreach ($gastosDiarios as $dia => $valor) {
    if ($dia <= $heatMapHoje) {
        $gastoTotalAteHoje += $valor;
        if ($valor > $maiorGasto['valor']) {
            $maiorGasto = ['dia' => $dia, 'valor' => $valor];
        }
    }
}
$mediaDiaria = $gastoTotalAteHoje / max($heatMapHoje, 1);
$maiorGastoDoMes = !empty($gastosDiarios) ? max($gastosDiarios) : 1;

function dflg_heat_class(int $valor, int $max): string
{
    if ($valor === 0) return '';
    $ratio = $valor / $max;
    if ($ratio < 0.15) return 'dflg-heat-l1';
    if ($ratio < 0.3) return 'dflg-heat-l2';
    if ($ratio < 0.5) return 'dflg-heat-l3';
    if ($ratio < 0.75) return 'dflg-heat-l4';
    return 'dflg-heat-l5';
}

// ---- Transações recentes agrupadas por data ----
$gruposTransacoes = [];
foreach ($transacoesRecentes as $t) {
    $gruposTransacoes[$t['data']][] = $t;
}
krsort($gruposTransacoes);

$hojeISO = '2026-06-18';
$ontemISO = '2026-06-17';
function dflg_data_label(string $data, string $hoje, string $ontem): string
{
    if ($data === $hoje) return 'HOJE';
    if ($data === $ontem) return 'ONTEM';
    $meses = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
    $ts = strtotime($data);
    return str_pad((string) date('d', $ts), 2, '0', STR_PAD_LEFT) . ' ' . strtoupper($meses[(int) date('n', $ts) - 1]);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visão Geral • DFLG Investiments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/dflg.css" rel="stylesheet">
</head>

<body class="dflg-app">

    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container-xxl px-4 py-5">

        <?php if ($modoDemo): ?>
            <div class="dflg-demo-banner mb-4">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-eye"></i>
                    <div>
                        <p class="mb-0 text-white fw-medium">Modo demonstração</p>
                        <p class="mb-0 text-dflg-muted small">Os números aqui são fictícios. Crie sua conta pra começar a controlar suas finanças de verdade.</p>
                    </div>
                </div>
                <a href="<?= URL_BASE ?>/login?aba=cadastro" class="dflg-btn-solid-green">Criar minha conta</a>
            </div>
        <?php endif; ?>

        <div class="mb-5">
            <div class="dflg-page-title">
                <span class="bar"></span>
                <h1>Visão Geral</h1>
            </div>
            <p class="dflg-page-subtitle mt-2">Panorama completo das suas finanças pessoais</p>
        </div>

        <!-- ===================== Cards resumo ===================== -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="dflg-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <span class="dflg-card-icon"><i class="bi bi-currency-dollar"></i></span>
                        <i class="bi bi-graph-up-arrow text-success"></i>
                    </div>
                    <p class="dflg-eyebrow mb-2">Receitas do Mês</p>
                    <p class="dflg-metric"><?= dflg_money($totalReceitas) ?></p>
                    <p class="text-dflg-muted small mb-0">Junho 2026</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="dflg-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <span class="dflg-card-icon is-danger"><i class="bi bi-credit-card"></i></span>
                        <i class="bi bi-graph-down-arrow" style="color: var(--dflg-red-400);"></i>
                    </div>
                    <p class="dflg-eyebrow mb-2">Despesas do Mês</p>
                    <p class="dflg-metric"><?= dflg_money($totalDespesas) ?></p>
                    <p class="text-dflg-muted small mb-0">Inclui <?= dflg_money($parcelamentosDoMes) ?> em parcelas</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="dflg-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <span class="dflg-card-icon"><i class="bi bi-bar-chart-line"></i></span>
                        <i class="bi bi-graph-up-arrow text-success"></i>
                    </div>
                    <p class="dflg-eyebrow mb-2">Saldo</p>
                    <p class="dflg-metric <?= $saldo >= 0 ? 'is-green' : 'is-red' ?>"><?= dflg_money($saldo) ?></p>
                    <p class="text-dflg-muted small mb-0"><?= $saldo >= 0 ? 'Superávit este mês' : 'Deficitário este mês' ?></p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="dflg-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <span class="dflg-card-icon"><i class="bi bi-bullseye"></i></span>
                        <i class="bi bi-graph-up-arrow text-success"></i>
                    </div>
                    <p class="dflg-eyebrow mb-2">Meta Atual</p>
                    <p class="dflg-metric"><?= $metaProgresso ?>%</p>
                    <p class="text-dflg-muted small mb-0">
                        R$ <?= number_format($metaAtual, 0, ',', '.') ?> de R$ <?= number_format($metaValor, 0, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- ===================== Gráficos ===================== -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="dflg-panel">
                    <h2>Receitas vs Despesas</h2>
                    <p class="dflg-panel-sub">Últimos 7 meses</p>
                    <p class="dflg-panel-note"><span class="dot"></span> Despesas incluem parcelamentos do mês</p>
                    <canvas id="chartReceitasDespesas" height="260"></canvas>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="dflg-panel">
                    <h2>Despesas por Categoria</h2>
                    <p class="dflg-panel-sub">Distribuição mensal</p>
                    <p class="dflg-panel-note"><span class="dot"></span> Inclui parcelamentos ativos</p>
                    <canvas id="chartCategorias" height="260"></canvas>
                </div>
            </div>
        </div>

        <!-- ===================== Mapa de calor + Transações recentes ===================== -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="dflg-panel">
                    <p class="dflg-eyebrow mb-0">Mapa de Calor</p>
                    <p class="dflg-metric mt-1"><?= dflg_money($gastoTotalAteHoje) ?></p>
                    <p class="text-dflg-muted small mb-4">Média diária: <span class="text-light"><?= dflg_money($mediaDiaria) ?></span></p>

                    <div class="d-flex gap-1">
                        <div class="d-flex flex-column gap-1 pe-1">
                            <?php foreach (['D', 'S', 'T', 'Q', 'Q', 'S', 'S'] as $label): ?>
                                <div style="height:28px;" class="d-flex align-items-center">
                                    <span class="text-dflg-muted" style="font-size:10px; width:12px; text-align:center;"><?= $label ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php foreach ($semanas as $semana): ?>
                            <div class="d-flex flex-column gap-1 flex-fill">
                                <?php foreach ($semana as $dia): ?>
                                    <?php if ($dia === null): ?>
                                        <div style="height:28px;"></div>
                                    <?php else:
                                        $valor = $gastosDiarios[$dia] ?? 0;
                                        $isFuture = $dia > $heatMapHoje;
                                        $isToday = $dia === $heatMapHoje;
                                        $classes = 'dflg-heat-cell';
                                        if ($isFuture) {
                                            $classes .= ' is-future';
                                        } else {
                                            $classes .= ' ' . dflg_heat_class((int) $valor, (int) $maiorGastoDoMes);
                                            if ($valor > 0) $classes .= ' has-value';
                                        }
                                        if ($isToday) $classes .= ' is-today';
                                        $dataCompleta = sprintf('%04d-%02d-%02d', $heatMapAno, $heatMapMes, $dia);
                                        $tooltipTexto = $valor > 0 ? 'Dia ' . $dia . ': ' . dflg_money($valor) : 'Dia ' . $dia;
                                        ?>
                                        <?php if ($isFuture): ?>
                                            <div class="<?= $classes ?>" data-tooltip="<?= $tooltipTexto ?>"><?= $dia ?></div>
                                        <?php else: ?>
                                            <a href="<?= URL_BASE ?>/transacoes?dataInicio=<?= $dataCompleta ?>&dataFim=<?= $dataCompleta ?>" class="<?= $classes ?>" data-tooltip="<?= $tooltipTexto ?>"><?= $dia ?></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="dflgHeatTooltip" class="dflg-heat-tooltip"></div>

                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <span class="text-dflg-muted" style="font-size:11px;">Menos</span>
                        <div class="d-flex gap-2">
                            <span class="dflg-heat-legend-swatch" style="background: rgba(255,255,255,.05);"></span>
                            <span class="dflg-heat-legend-swatch dflg-heat-l1"></span>
                            <span class="dflg-heat-legend-swatch dflg-heat-l3"></span>
                            <span class="dflg-heat-legend-swatch dflg-heat-l4"></span>
                            <span class="dflg-heat-legend-swatch dflg-heat-l5"></span>
                        </div>
                        <span class="text-dflg-muted" style="font-size:11px;">Mais</span>
                    </div>

                    <?php if ($maiorGasto['dia'] > 0): ?>
                        <div class="d-flex justify-content-between mt-3 pt-3 border-top border-secondary-subtle">
                            <span class="text-dflg-muted small">Maior gasto</span>
                            <span class="small fw-semibold" style="color: var(--dflg-green-400);">
                                <?= dflg_money($maiorGasto['valor']) ?> — dia <?= $maiorGasto['dia'] ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="dflg-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="dflg-eyebrow mb-0">Transações Recentes</p>
                        <a href="<?= URL_BASE ?>/transacoes" class="dflg-chip-btn border-0">
                            ver todas <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <div class="dflg-transacoes-scroll">
                        <?php foreach ($gruposTransacoes as $data => $itens): ?>
                            <p class="dflg-tx-group-label"><?= dflg_data_label($data, $hojeISO, $ontemISO) ?></p>
                            <?php foreach ($itens as $t): ?>
                                <div class="dflg-tx-item">
                                    <span class="dflg-tx-icon <?= $t['tipo'] === 'despesa' ? 'is-expense' : '' ?>">
                                        <i class="bi <?= $t['tipo'] === 'receita' ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' ?>"></i>
                                    </span>
                                    <span class="dflg-tx-desc text-truncate"><?= htmlspecialchars($t['descricao']) ?></span>
                                    <span class="dflg-tx-category d-none d-sm-inline-block"><?= htmlspecialchars($t['categoria']) ?></span>
                                    <span class="dflg-tx-value <?= $t['tipo'] === 'receita' ? 'is-income' : 'is-expense' ?>">
                                        <?= $t['tipo'] === 'receita' ? '+' : '−' ?><?= dflg_money($t['valor']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== Metas + Parcelamentos ===================== -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="dflg-panel">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h2>Minhas Metas</h2>
                            <p class="dflg-panel-sub mb-0">Até 3 fixadas / mais próximas de bater 100%</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button
                                type="button"
                                class="dflg-chip-btn border-0"
                                onclick="window.location.href='<?= URL_BASE ?>/metas'">
                                ver todas <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <?php if (empty($metas)): ?>
                        <div class="text-center py-4 text-dflg-muted">
                            <i class="bi bi-bullseye d-block mb-3" style="font-size: 2rem; opacity: .3;"></i>
                            <p class="mb-2">Você ainda não tem metas ativas.</p>
                            <a href="<?= URL_BASE ?>/metas" class="dflg-btn-outline-green dflg-btn-sm">Criar minha primeira meta</a>
                        </div>
                    <?php else: ?>
                        
                        <?php foreach ($metas as $meta): ?>
                            <div class="dflg-goal">
                                <div class="dflg-goal-top">
                                    <span class="name">
                                        <?= htmlspecialchars($meta['nome_meta']) ?>
                                        <?php if ($meta['fixada']): ?>
                                            <i class="bi bi-pin-angle-fill text-success ms-1" title="Fixada" style="font-size: 0.78rem;"></i>
                                        <?php endif; ?>
                                    </span>
                                    <span class="pct"><?= $meta['percentual'] ?>%</span>
                                </div>
                                <div class="dflg-progress">
                                    <div class="dflg-progress-bar" style="width: <?= $meta['percentual'] ?>%;"></div>
                                </div>
                                <div class="dflg-goal-bottom">
                                    <span class="current"><?= dflg_money((float) $meta['valor_atual']) ?></span>
                                    <span class="target">de <?= dflg_money((float) $meta['valor_meta']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="dflg-panel">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h2>Próximos Parcelamentos</h2>
                            <p class="dflg-panel-sub mb-0">Vencimentos do mês</p>
                        </div>
                        <a href="<?= URL_BASE ?>/parcelamentos" class="dflg-chip-btn border-0">ver todos <i class="bi bi-arrow-right"></i></a>
                    </div>

                    <div class="dflg-installments-total">
                        <div>
                            <p class="dflg-eyebrow mb-1">Comprometido este mês</p>
                            <p class="dflg-metric mb-0"><?= dflg_money($totalParcelasMes) ?></p>
                        </div>
                        <span class="dflg-card-icon" style="background: rgba(249,115,22,.1); border-color: rgba(249,115,22,.2); color: var(--dflg-orange-500); width:56px; height:56px; font-size:1.4rem;">
                            <i class="bi bi-credit-card-2-front"></i>
                        </span>
                    </div>

                    <?php foreach ($proximasParcelas as $p): ?>
                        <div class="dflg-installment-item">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="text-white fw-medium"><?= htmlspecialchars($p['descricao']) ?></span>
                                    <span class="dflg-badge-soft"><?= htmlspecialchars($p['categoria']) ?></span>
                                </div>
                                <p class="text-dflg-muted small mb-0">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Vence em <?= date('d/m/Y', strtotime($p['vencimento'])) ?>
                                </p>
                            </div>
                            <span class="fw-bold" style="color: var(--dflg-orange-500);"><?= dflg_money($p['valor']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const moneyFmt = (v) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);

        // ---- Mapa de Calor: tooltip customizado (mesma linguagem visual dos gráficos) ----
        (function () {
            const tooltip = document.getElementById('dflgHeatTooltip');
            if (!tooltip) return;

            document.querySelectorAll('.dflg-heat-cell[data-tooltip]').forEach((cell) => {
                cell.addEventListener('mouseenter', () => {
                    tooltip.textContent = cell.dataset.tooltip;
                    tooltip.classList.add('is-visible');
                });
                cell.addEventListener('mousemove', (e) => {
                    tooltip.style.left = e.clientX + 'px';
                    tooltip.style.top = (e.clientY - 34) + 'px';
                });
                cell.addEventListener('mouseleave', () => {
                    tooltip.classList.remove('is-visible');
                });
            });
        })();

        // ---- Receitas vs Despesas ----
        new Chart(document.getElementById('chartReceitasDespesas'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($graficoMensal, 'mes')) ?>,
                datasets: [
                    {
                        label: 'Receitas',
                        data: <?= json_encode(array_column($graficoMensal, 'receitas')) ?>,
                        backgroundColor: '#10B981',
                        borderRadius: 6,
                        maxBarThickness: 28,
                    },
                    {
                        label: 'Despesas Totais',
                        data: <?= json_encode(array_map(fn($m) => $m['despesas'] + $m['parcelas'], $graficoMensal)) ?>,
                        backgroundColor: '#EF4444',
                        borderRadius: 6,
                        maxBarThickness: 28,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#9CA3AF', font: { size: 12 } } },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        borderColor: 'rgba(16,185,129,0.3)',
                        borderWidth: 1,
                        callbacks: { label: (ctx) => `${ctx.dataset.label}: ${moneyFmt(ctx.parsed.y)}` }
                    }
                },
                scales: {
                    x: { ticks: { color: '#6B7280' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                    y: { ticks: { color: '#6B7280' }, grid: { color: 'rgba(255,255,255,0.05)' } }
                }
            }
        });

        // ---- Despesas por Categoria ----
        new Chart(document.getElementById('chartCategorias'), {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_column($despesasPorCategoria, 'nome')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($despesasPorCategoria, 'valor')) ?>,
                    backgroundColor: <?= json_encode(array_column($despesasPorCategoria, 'cor')) ?>,
                    borderColor: '#000',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#9CA3AF', font: { size: 12 }, usePointStyle: true } },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        borderColor: 'rgba(16,185,129,0.3)',
                        borderWidth: 1,
                        callbacks: { label: (ctx) => `${ctx.label}: ${moneyFmt(ctx.parsed)}` }
                    }
                }
            }
        });
    </script>

 
</body>

</html>
