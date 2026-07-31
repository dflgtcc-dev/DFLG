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
    'periodo' => $periodo,
    'tipo' => $tipo,
    'categoria' => $categoria,
    'ordenar' => $ordenar,
    'busca' => $busca,
    'pagina' => $pagina,
    'tamanho' => $tamanhoPagina,
];

$periodLabels = [
    'week' => 'Última semana',
    'month' => 'Último mês',
    '3months' => 'Últimos 3 meses',
    '6months' => 'Últimos 6 meses',
    'all' => 'Todo período',
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

            <select name="periodo" class="dflg-input dflg-select-auto w-auto" onchange="this.form.submit()">
                <?php foreach ($periodLabels as $valor => $label): ?>
                    <option value="<?= $valor ?>" <?= $periodo === $valor ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>

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

            <button type="button" class="dflg-btn-solid-green px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalNovaTransacao">
                <i class="bi bi-plus-lg"></i> Nova Transação
            </button>
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

            <form method="get" action="<?= URL_BASE ?>/transacoes" class="mb-4">
                <input type="hidden" name="periodo" value="<?= htmlspecialchars($periodo) ?>">
                <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
                <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria) ?>">
                <input type="hidden" name="ordenar" value="<?= htmlspecialchars($ordenar) ?>">
                <input type="hidden" name="tamanho" value="<?= $tamanhoPagina ?>">
                <div class="dflg-input-group">
                    <i class="bi bi-search dflg-input-icon"></i>
                    <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" class="dflg-input" placeholder="Buscar transação por descrição ou categoria...">
                </div>
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
                        <input type="hidden" name="periodo" value="<?= htmlspecialchars($periodo) ?>">
                        <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
                        <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria) ?>">
                        <input type="hidden" name="ordenar" value="<?= htmlspecialchars($ordenar) ?>">
                        <input type="hidden" name="busca" value="<?= htmlspecialchars($busca) ?>">
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
