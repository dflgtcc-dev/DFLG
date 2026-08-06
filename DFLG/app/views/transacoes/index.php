<?php
if (!function_exists('dflg_money')) {
    function dflg_money(float $valor): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
}

if (!function_exists('dflg_data_br')) {
    function dflg_data_br(string $data): string
    {
        $dt = \DateTime::createFromFormat('Y-m-d', $data);
        return $dt ? $dt->format('d/m/Y') : $data;
    }
}

/**
 * Monta a query string atual da página aplicando overrides — usado nos
 * links de filtro/paginação para preservar os demais parâmetros.
 * Recebe os filtros atuais explicitamente pois uma função PHP não tem
 * acesso às variáveis locais do escopo onde a view foi incluída.
 */
function dflg_qs(array $filtrosAtuais, array $overrides = []): string
{
    return '?' . http_build_query(array_merge($filtrosAtuais, $overrides));
}

$filtrosAtuais = [
    'tipo' => $tipo,
    'categoria' => $categoria,
    'ordenar' => $ordenar,
    'busca' => $busca,
    'dataInicio' => $dataInicio,
    'dataFim' => $dataFim,
    'pagina' => $pagina,
    'tamanho' => $tamanhoPagina,
];

$exibindoDe = $totalTransacoes === 0 ? 0 : ($pagina - 1) * $tamanhoPagina + 1;
$exibindoAte = min($pagina * $tamanhoPagina, $totalTransacoes);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transações • DFLG Investiments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/dflg.css" rel="stylesheet">
</head>

<body class="dflg-app">

    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container-xxl px-4 py-5">

        <div class="dflg-page-title">
            <span class="bar"></span>
            <h1>Transações</h1>
        </div>
        <p class="dflg-page-subtitle mb-4">Gerencie todas as suas movimentações financeiras</p>

        <!-- ===================== Filtros ===================== -->
        <form method="get" action="<?= URL_BASE ?>/transacoes" id="formFiltros" class="d-flex flex-wrap gap-3 mb-4 align-items-center">
            <input type="hidden" name="pagina" value="1">
            <input type="hidden" name="dataInicio" id="filtroDataInicio" value="<?= htmlspecialchars($dataInicio) ?>">
            <input type="hidden" name="dataFim" id="filtroDataFim" value="<?= htmlspecialchars($dataFim) ?>">

            <div class="dflg-dr" id="dflgDateRange">
                <button type="button" class="dflg-dr-trigger" id="dflgDrTrigger">
                    <i class="bi bi-calendar3"></i>
                    <span id="dflgDrLabel">Todo período</span>
                    <i class="bi bi-chevron-down small"></i>
                </button>

                <div class="dflg-dr-panel" id="dflgDrPanel">
                    <div class="dflg-dr-presets" id="dflgDrPresets">
                        <button type="button" data-preset="all">Todo período</button>
                        <button type="button" data-preset="30d">Últimos 30 dias</button>
                        <button type="button" data-preset="90d">Últimos 90 dias</button>
                        <button type="button" data-preset="month">Este mês</button>
                        <button type="button" data-preset="lastmonth">Mês passado</button>
                        <button type="button" data-preset="6m">Últimos 6 meses</button>
                        <button type="button" data-preset="year">Este ano</button>
                        <button type="button" data-preset="lastyear">Ano passado</button>
                    </div>

                    <div class="dflg-dr-cal">
                        <p class="dflg-dr-cal-caption">Selecione as datas</p>
                        <div class="dflg-dr-cal-header">
                            <button type="button" id="dflgDrPrev" class="dflg-dr-nav"><i class="bi bi-chevron-left"></i></button>
                            <div class="flex-grow-1"></div>
                            <button type="button" id="dflgDrNext" class="dflg-dr-nav"><i class="bi bi-chevron-right"></i></button>
                        </div>
                        <div class="dflg-dr-cal-grids" id="dflgDrGrids"></div>

                        <div class="dflg-dr-footer">
                            <span class="text-dflg-muted small" id="dflgDrSelectedLabel">Todo período</span>
                            <button type="button" class="dflg-btn-solid-green px-3 py-2" id="dflgDrApply">
                                <i class="bi bi-check-lg"></i> Aplicar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dflg-pill-group">
                <a href="<?= dflg_qs($filtrosAtuais, ['tipo' => 'all', 'pagina' => 1]) ?>" class="dflg-pill <?= $tipo === 'all' ? 'active' : '' ?>">Todas</a>
                <a href="<?= dflg_qs($filtrosAtuais, ['tipo' => 'receita', 'pagina' => 1]) ?>" class="dflg-pill is-income <?= $tipo === 'receita' ? 'active' : '' ?>">Entradas</a>
                <a href="<?= dflg_qs($filtrosAtuais, ['tipo' => 'despesa', 'pagina' => 1]) ?>" class="dflg-pill is-expense <?= $tipo === 'despesa' ? 'active' : '' ?>">Saídas</a>
            </div>

            <select name="categoria" class="dflg-input dflg-select-auto w-auto" onchange="this.form.submit()">
                <option value="all" <?= $categoria === 'all' ? 'selected' : '' ?>>Todas categorias</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>" <?= $categoria === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="ordenar" class="dflg-input dflg-select-auto w-auto" onchange="this.form.submit()">
                <option value="newest" <?= $ordenar === 'newest' ? 'selected' : '' ?>>Mais recentes</option>
                <option value="oldest" <?= $ordenar === 'oldest' ? 'selected' : '' ?>>Mais antigas</option>
                <option value="highest" <?= $ordenar === 'highest' ? 'selected' : '' ?>>Maior valor</option>
                <option value="lowest" <?= $ordenar === 'lowest' ? 'selected' : '' ?>>Menor valor</option>
            </select>

            <?php if ($busca !== ''): ?>
                <input type="hidden" name="busca" value="<?= htmlspecialchars($busca) ?>">
            <?php endif; ?>
            <input type="hidden" name="tamanho" value="<?= $tamanhoPagina ?>">

            <div class="flex-grow-1"></div>

            
        </form>

        <!-- ===================== Cards de resumo ===================== -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-wallet2 text-dflg-muted"></i>
                        <p class="dflg-eyebrow mb-0">Total de transações</p>
                    </div>
                    <p class="dflg-metric mb-0" style="font-size:1.6rem;"><?= $totalTransacoes ?></p>
                    <p class="text-dflg-muted small mb-0">no período</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-graph-up-arrow text-success"></i>
                        <p class="dflg-eyebrow mb-0">Receitas</p>
                    </div>
                    <p class="dflg-metric is-green mb-0" style="font-size:1.6rem;"><?= dflg_money($totalReceitas) ?></p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-graph-down-arrow" style="color: var(--dflg-red-400);"></i>
                        <p class="dflg-eyebrow mb-0">Despesas</p>
                    </div>
                    <p class="dflg-metric is-red mb-0" style="font-size:1.6rem;"><?= dflg_money($totalDespesas) ?></p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-currency-dollar" style="color: <?= $saldo >= 0 ? 'var(--dflg-green-500)' : 'var(--dflg-red-400)' ?>;"></i>
                        <p class="dflg-eyebrow mb-0">Saldo</p>
                    </div>
                    <p class="dflg-metric <?= $saldo >= 0 ? 'is-green' : 'is-red' ?> mb-0" style="font-size:1.6rem;"><?= dflg_money($saldo) ?></p>
                </div>
            </div>
        </div>

        <!-- ===================== Lista de transações ===================== -->
        <div class="dflg-panel">

            <form method="get"
                action="<?= URL_BASE ?>/transacoes"
                class="mb-4 d-flex align-items-center justify-content-between">

                <div class="dflg-input-group" style="max-width: 300px; width: 100%;">
                    <i class="bi bi-search dflg-input-icon"></i>
                    <input type="text"
                        name="busca"
                        value="<?= htmlspecialchars($busca) ?>"
                        class="dflg-input"
                        placeholder="Buscar transações...">
                </div>

                <button type="button"
                        class="dflg-btn-solid-green px-4 py-2 ms-3"
                        data-bs-toggle="modal"
                        data-bs-target="#modalNovaTransacao">
                    <i class="bi bi-plus-lg"></i> Nova Transação
                </button>

            </form>
            <?php if (empty($transacoes)): ?>
                <div class="text-center py-5 text-dflg-muted">
                    <i class="bi bi-search d-block mb-3" style="font-size: 2.5rem; opacity: .3;"></i>
                    <p class="mb-0">Nenhuma transação encontrada</p>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($transacoes as $t): ?>
                        <div class="dflg-tx-item">
                            <span class="dflg-tx-icon <?= $t['tipo'] === 'despesa' ? 'is-expense' : '' ?>">
                                <i class="bi <?= $t['tipo'] === 'receita' ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' ?>"></i>
                            </span>
                            <span class="dflg-tx-desc text-truncate"><?= htmlspecialchars($t['descricao']) ?></span>
                            <span class="dflg-tx-category d-none d-sm-inline-block"><?= htmlspecialchars($t['categoria']) ?></span>
                            <span class="text-dflg-muted small d-none d-md-inline-flex align-items-center gap-1" style="min-width: 90px;">
                                <i class="bi bi-calendar3"></i> <?= dflg_data_br($t['data_transacao']) ?>
                            </span>
                            <span class="dflg-tx-value <?= $t['tipo'] === 'receita' ? 'is-income' : 'is-expense' ?>">
                                <?= $t['tipo'] === 'receita' ? '+' : '−' ?> <?= dflg_money((float) $t['valor']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- ===================== Paginação ===================== -->
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 mt-4 pt-3 dflg-pagination-border">
                <div class="d-flex align-items-center gap-3 text-dflg-muted small">
                    <span>Exibindo <?= $exibindoDe ?>–<?= $exibindoAte ?> de <?= $totalTransacoes ?> transações</span>
                    <form method="get" action="<?= URL_BASE ?>/transacoes">
                        <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
                        <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria) ?>">
                        <input type="hidden" name="ordenar" value="<?= htmlspecialchars($ordenar) ?>">
                        <input type="hidden" name="busca" value="<?= htmlspecialchars($busca) ?>">
                        <input type="hidden" name="dataInicio" value="<?= htmlspecialchars($dataInicio) ?>">
                        <input type="hidden" name="dataFim" value="<?= htmlspecialchars($dataFim) ?>">
                        <input type="hidden" name="pagina" value="1">
                        <select name="tamanho" class="dflg-input dflg-select-sm" onchange="this.form.submit()">
                            <?php foreach ([5, 10, 25] as $n): ?>
                                <option value="<?= $n ?>" <?= $tamanhoPagina === $n ? 'selected' : '' ?>><?= $n ?> por página</option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <div class="d-flex align-items-center gap-1">
                    <a class="dflg-page-btn <?= $pagina <= 1 ? 'disabled' : '' ?>" href="<?= dflg_qs($filtrosAtuais, ['pagina' => max(1, $pagina - 1)]) ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                        <?php if ($p === 1 || $p === $totalPaginas || abs($p - $pagina) <= 1): ?>
                            <a class="dflg-page-btn <?= $p === $pagina ? 'active' : '' ?>" href="<?= dflg_qs($filtrosAtuais, ['pagina' => $p]) ?>"><?= $p ?></a>
                        <?php elseif ($p === 2 || $p === $totalPaginas - 1): ?>
                            <span class="text-dflg-muted px-1">…</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <a class="dflg-page-btn <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>" href="<?= dflg_qs($filtrosAtuais, ['pagina' => min($totalPaginas, $pagina + 1)]) ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- ===================== Modal Nova Transação ===================== -->
    <div class="modal fade" id="modalNovaTransacao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dflg-modal">
                <form method="post" action="<?= URL_BASE ?>/transacoes">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h2 class="modal-title">Nova Transação</h2>
                            <p class="text-dflg-muted small mb-0 mt-1">Adicione uma transação manualmente ao seu histórico financeiro.</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">

                        <?php if (!empty($erros)): ?>
                            <div class="dflg-auth-alert mb-3">
                                <?php foreach ($erros as $erro): ?>
                                    <div><?= htmlspecialchars($erro) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="dflg-auth-label">Valor</label>
                            <div class="dflg-input-group">
                                <i class="bi bi-cash-coin dflg-input-icon"></i>
                                <input type="number" step="0.01" name="valor" class="dflg-input" placeholder="0,00" value="<?= htmlspecialchars($formAntigo['valor'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="dflg-auth-label">Descrição</label>
                            <textarea name="descricao" rows="2" class="dflg-input" style="padding-left:1rem;" placeholder="Descrição"><?= htmlspecialchars($formAntigo['descricao'] ?? '') ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-7">
                                <label class="dflg-auth-label">Categoria</label>
                                <select name="categoria" class="dflg-input" style="padding-left:1rem;">
                                    <?php foreach ($categorias as $c): ?>
                                        <option value="<?= htmlspecialchars($c) ?>" <?= ($formAntigo['categoria'] ?? '') === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-5">
                                <label class="dflg-auth-label">Data</label>
                                <input type="date" name="data" class="dflg-input" style="padding-left:1rem;" value="<?= htmlspecialchars($formAntigo['data'] ?? date('Y-m-d')) ?>">
                            </div>
                        </div>

                        <label class="dflg-auth-label">Tipo</label>
                        <div class="dflg-type-toggle mb-1">
                            <?php $tipoSel = $formAntigo['tipo'] ?? 'despesa'; ?>
                            <label class="dflg-type-opt is-expense <?= $tipoSel === 'despesa' ? 'active' : '' ?>">
                                <input type="radio" name="tipo" value="despesa" <?= $tipoSel === 'despesa' ? 'checked' : '' ?>>
                                Saída (Débito)
                            </label>
                            <label class="dflg-type-opt is-income <?= $tipoSel === 'receita' ? 'active' : '' ?>">
                                <input type="radio" name="tipo" value="receita" <?= $tipoSel === 'receita' ? 'checked' : '' ?>>
                                Entrada (Crédito)
                            </label>
                        </div>
                        <input type="hidden" name="moeda" value="BRL">
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="dflg-btn-cancel flex-fill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="dflg-auth-submit flex-fill">Criar Transação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===================== Seletor de intervalo de datas =====================
        (function () {
            const trigger = document.getElementById('dflgDrTrigger');
            const panel = document.getElementById('dflgDrPanel');
            const label = document.getElementById('dflgDrLabel');
            const selectedLabel = document.getElementById('dflgDrSelectedLabel');
            const gridsEl = document.getElementById('dflgDrGrids');
            const inputInicio = document.getElementById('filtroDataInicio');
            const inputFim = document.getElementById('filtroDataFim');

            const MESES = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
            const DIAS_SEMANA = ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sab'];

            const pad = (n) => String(n).padStart(2, '0');
            const iso = (y, m, d) => `${y}-${pad(m + 1)}-${pad(d)}`;
            const hoje = new Date();
            const hojeIso = iso(hoje.getFullYear(), hoje.getMonth(), hoje.getDate());

            function fmtBR(dataIso) {
                if (!dataIso) return '';
                const [y, m, d] = dataIso.split('-');
                return `${d}/${m}/${y.slice(2)}`;
            }

            let rangeStart = <?= json_encode($dataInicio ?: null) ?>;
            let rangeEnd = <?= json_encode($dataFim ?: null) ?>;

            // Mês exibido na coluna da esquerda (a da direita é sempre o mês seguinte)
            let baseAno = hoje.getFullYear();
            let baseMes = hoje.getMonth();
            if (rangeStart) {
                const [y, m] = rangeStart.split('-');
                baseAno = parseInt(y, 10);
                baseMes = parseInt(m, 10) - 1;
            }

            function atualizarLabelGatilho() {
                if (!rangeStart && !rangeEnd) {
                    label.textContent = 'Todo período';
                } else if (rangeStart && rangeEnd) {
                    label.textContent = fmtBR(rangeStart) + ' - ' + fmtBR(rangeEnd);
                } else if (rangeStart) {
                    label.textContent = 'A partir de ' + fmtBR(rangeStart);
                } else {
                    label.textContent = 'Até ' + fmtBR(rangeEnd);
                }
                selectedLabel.textContent = label.textContent;
            }

            function construirGradeMes(ano, mes) {
                const primeiroDiaSemana = new Date(ano, mes, 1).getDay();
                const diasNoMes = new Date(ano, mes + 1, 0).getDate();
                const diasMesAnterior = new Date(ano, mes, 0).getDate();

                let celulasHtml = '';
                const totalCelulas = Math.ceil((primeiroDiaSemana + diasNoMes) / 7) * 7;

                for (let i = 0; i < totalCelulas; i++) {
                    const diaNoMes = i - primeiroDiaSemana + 1;

                    if (diaNoMes < 1) {
                        celulasHtml += `<span class="dflg-dr-day is-other">${diasMesAnterior + diaNoMes}</span>`;
                    } else if (diaNoMes > diasNoMes) {
                        celulasHtml += `<span class="dflg-dr-day is-other">${diaNoMes - diasNoMes}</span>`;
                    } else {
                        const dataIso = iso(ano, mes, diaNoMes);
                        let classes = 'dflg-dr-day';
                        if (dataIso === hojeIso) classes += ' is-today';
                        if (dataIso === rangeStart || dataIso === rangeEnd) classes += ' is-selected';
                        if (rangeStart && rangeEnd && dataIso > rangeStart && dataIso < rangeEnd) classes += ' is-in-range';
                        celulasHtml += `<button type="button" class="${classes}" data-date="${dataIso}">${diaNoMes}</button>`;
                    }
                }

                return `
                    <div class="dflg-dr-month">
                        <p class="dflg-dr-month-title">${MESES[mes]} ${ano}</p>
                        <div class="dflg-dr-weekdays">${DIAS_SEMANA.map(d => `<span>${d}</span>`).join('')}</div>
                        <div class="dflg-dr-days">${celulasHtml}</div>
                    </div>`;
            }

            function renderizar() {
                let mes2 = baseMes + 1, ano2 = baseAno;
                if (mes2 > 11) { mes2 = 0; ano2++; }

                gridsEl.innerHTML = construirGradeMes(baseAno, baseMes) + construirGradeMes(ano2, mes2);

                gridsEl.querySelectorAll('.dflg-dr-day[data-date]').forEach((el) => {
                    el.addEventListener('click', () => selecionarDia(el.dataset.date));
                });

                atualizarLabelGatilho();
            }

            function selecionarDia(dataIso) {
                if (!rangeStart || (rangeStart && rangeEnd)) {
                    rangeStart = dataIso;
                    rangeEnd = null;
                } else if (dataIso < rangeStart) {
                    rangeEnd = rangeStart;
                    rangeStart = dataIso;
                } else {
                    rangeEnd = dataIso;
                }
                renderizar();
            }

            function aplicarPreset(preset) {
                const y = hoje.getFullYear(), m = hoje.getMonth(), d = hoje.getDate();

                switch (preset) {
                    case 'all':
                        rangeStart = null; rangeEnd = null;
                        break;
                    case '30d':
                        rangeStart = iso(y, m, d - 30); rangeEnd = hojeIso;
                        break;
                    case '90d':
                        rangeStart = iso(y, m, d - 90); rangeEnd = hojeIso;
                        break;
                    case 'month':
                        rangeStart = iso(y, m, 1); rangeEnd = iso(y, m + 1, 0);
                        break;
                    case 'lastmonth':
                        rangeStart = iso(y, m - 1, 1); rangeEnd = iso(y, m, 0);
                        break;
                    case '6m':
                        rangeStart = iso(y, m - 6, d); rangeEnd = hojeIso;
                        break;
                    case 'year':
                        rangeStart = iso(y, 0, 1); rangeEnd = iso(y, 11, 31);
                        break;
                    case 'lastyear':
                        rangeStart = iso(y - 1, 0, 1); rangeEnd = iso(y - 1, 11, 31);
                        break;
                }

                const refDate = rangeStart ? new Date(rangeStart) : hoje;
                baseAno = refDate.getFullYear();
                baseMes = refDate.getMonth();
                renderizar();
            }

            document.getElementById('dflgDrPresets').addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-preset]');
                if (btn) aplicarPreset(btn.dataset.preset);
            });

            document.getElementById('dflgDrPrev').addEventListener('click', () => {
                baseMes--;
                if (baseMes < 0) { baseMes = 11; baseAno--; }
                renderizar();
            });
            document.getElementById('dflgDrNext').addEventListener('click', () => {
                baseMes++;
                if (baseMes > 11) { baseMes = 0; baseAno++; }
                renderizar();
            });

            document.getElementById('dflgDrApply').addEventListener('click', () => {
                inputInicio.value = rangeStart || '';
                inputFim.value = rangeEnd || '';
                document.getElementById('formFiltros').submit();
            });

            trigger.addEventListener('click', () => {
                panel.classList.toggle('is-open');
            });

            document.addEventListener('click', (e) => {
                if (!document.getElementById('dflgDateRange').contains(e.target)) {
                    panel.classList.remove('is-open');
                }
            });

            renderizar();
        })();

        document.querySelectorAll('.dflg-type-opt input').forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.dflg-type-opt').forEach(function (opt) { opt.classList.remove('active'); });
                radio.closest('.dflg-type-opt').classList.add('active');
            });
        });
    </script>
    <?php if (!empty($abrirModal)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('modalNovaTransacao')).show();
            });
        </script>
    <?php endif; ?>
</body>

</html>
