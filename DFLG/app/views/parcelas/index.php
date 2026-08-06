<?php
if (!function_exists('dflg_money')) {
    function dflg_money(float $valor): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
}
if (!function_exists('dflg_data_br')) {
    function dflg_data_br(?string $data): string
    {
        if (!$data) return '—';
        $dt = \DateTime::createFromFormat('Y-m-d', $data);
        return $dt ? $dt->format('d/m/Y') : $data;
    }
}

$iconesPorCategoria = [
    'Trabalho' => 'bi-briefcase',
    'Extra' => 'bi-cash-stack',
    'Moradia' => 'bi-house',
    'Alimentação' => 'bi-basket',
    'Contas' => 'bi-receipt',
    'Saúde' => 'bi-heart-pulse',
    'Lazer' => 'bi-controller',
    'Transporte' => 'bi-car-front',
    'Educação' => 'bi-mortarboard',
    'Tecnologia' => 'bi-laptop',
    'Outros' => 'bi-three-dots',
];

function dflg_parcela_qs(array $atuais, array $overrides = []): string
{
    return '?' . http_build_query(array_merge($atuais, $overrides));
}

$filtrosAtuais = [
    'busca' => $busca,
    'categoriaFiltro' => $categoriaFiltro,
    'status' => $statusFiltro,
    'pagina' => $pagina,
];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcelamentos • DFLG Investiments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/dflg.css" rel="stylesheet">
</head>

<body class="dflg-app">

    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container-xxl px-4 py-5">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-1">
            <div>
                <div class="dflg-page-title">
                    <span class="bar"></span>
                    <h1>Parcelamentos</h1>
                </div>
                <p class="dflg-page-subtitle mb-0">Acompanhe e gerencie suas compras parceladas</p>
            </div>
            <button type="button" class="dflg-btn-solid-green px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalNovoParcelamento">
                <i class="bi bi-plus-lg"></i> Novo Parcelamento
            </button>
        </div>
        <div class="mb-4"></div>

        <!-- ===================== Cards de resumo ===================== -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="dflg-eyebrow mb-0">Ativos</p>
                        <i class="bi bi-credit-card text-success"></i>
                    </div>
                    <p class="dflg-metric mb-0" style="font-size:1.8rem;"><?= $ativos ?></p>
                    <p class="text-dflg-muted small mb-0">parcelamentos</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="dflg-eyebrow mb-0">Total Mensal</p>
                        <i class="bi bi-graph-up-arrow" style="color:#3b82f6;"></i>
                    </div>
                    <p class="dflg-metric mb-0" style="font-size:1.5rem;"><?= dflg_money($totalMensal) ?></p>
                    <p class="text-dflg-muted small mb-0">comprometido/mês</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="dflg-eyebrow mb-0">Pendente</p>
                        <i class="bi bi-exclamation-circle" style="color: var(--dflg-orange-500);"></i>
                    </div>
                    <p class="mb-0" style="font-size:1.5rem; font-weight:700; color: var(--dflg-orange-500);"><?= dflg_money($totalPendente) ?></p>
                    <p class="text-dflg-muted small mb-0">a pagar</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="dflg-eyebrow mb-0">Total Pago</p>
                        <i class="bi bi-graph-up-arrow text-success"></i>
                    </div>
                    <p class="dflg-metric is-green mb-0" style="font-size:1.5rem;"><?= dflg_money($totalPago) ?></p>
                    <p class="text-dflg-muted small mb-0">acumulado</p>
                </div>
            </div>
        </div>

        <!-- ===================== Busca + filtros ===================== -->
        <form method="get" action="<?= URL_BASE ?>/parcelamentos" class="d-flex flex-wrap gap-3 mb-4 align-items-center">
            <input type="hidden" name="pagina" value="1">
            <div class="dflg-input-group flex-grow-1" style="min-width: 220px;">
                <i class="bi bi-search dflg-input-icon"></i>
                <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" class="dflg-input" placeholder="Buscar parcelamento...">
            </div>

            <select name="categoriaFiltro" class="dflg-input dflg-select-auto w-auto" onchange="this.form.submit()">
                <option value="all" <?= $categoriaFiltro === 'all' ? 'selected' : '' ?>>Todas as categorias</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>" <?= $categoriaFiltro === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="dflg-input dflg-select-auto w-auto" onchange="this.form.submit()">
                <option value="all" <?= $statusFiltro === 'all' ? 'selected' : '' ?>>Status: Todos</option>
                <option value="andamento" <?= $statusFiltro === 'andamento' ? 'selected' : '' ?>>Status: Em andamento</option>
                <option value="concluido" <?= $statusFiltro === 'concluido' ? 'selected' : '' ?>>Status: Concluído</option>
            </select>
        </form>

        <!-- ===================== Tabela de parcelamentos ===================== -->
        <?php if (empty($parcelas)): ?>
            <div class="dflg-panel text-center py-5 text-dflg-muted mb-4">
                <i class="bi bi-credit-card d-block mb-3" style="font-size: 2.5rem; opacity: .3;"></i>
                <p class="mb-0"><?= $totalFiltradas === 0 && $busca === '' && $categoriaFiltro === 'all' && $statusFiltro === 'all' ? 'Nenhum parcelamento ainda. Clique em "Novo Parcelamento" no topo da página para adicionar o primeiro.' : 'Nenhum parcelamento encontrado com esse filtro.' ?></p>
            </div>
        <?php else: ?>
            <div class="dflg-panel p-0 mb-4" style="overflow-x: auto;">
                <table class="dflg-table">
                    <thead>
                        <tr>
                            <th>Parcelamento</th>
                            <th>Início</th>
                            <th>Parcelas</th>
                            <th>Valor Total</th>
                            <th>Valor Mensal</th>
                            <th>Status</th>
                            <th>Próxima Parcela</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parcelas as $p): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="dflg-card-icon" style="width:38px; height:38px;">
                                            <i class="bi <?= $iconesPorCategoria[$p['categoria']] ?? 'bi-credit-card' ?>"></i>
                                        </span>
                                        <div>
                                            <p class="text-white fw-medium mb-0"><?= htmlspecialchars($p['descricao']) ?></p>
                                            <p class="text-dflg-muted small mb-0"><?= htmlspecialchars($p['categoria']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-dflg-muted"><?= dflg_data_br($p['data_primeira_parcela']) ?></td>
                                <td class="text-dflg-muted"><?= $p['parcelaAtual'] ?> de <?= $p['numero_parcelas'] ?></td>
                                <td class="text-white"><?= dflg_money((float) $p['valor_total']) ?></td>
                                <td class="text-white"><?= dflg_money((float) $p['valor_parcela']) ?></td>
                                <td>
                                    <?php if ($p['quitado']): ?>
                                        <span class="dflg-status-badge is-done">Concluído</span>
                                    <?php else: ?>
                                        <span class="dflg-status-badge is-progress">Em andamento</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-dflg-muted"><?= dflg_data_br($p['proximoVencimento']) ?></td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="dflg-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end dflg-nav-dropdown">
                                            <li>
                                                <button type="button" class="dropdown-item"
                                                    onclick="dflgAbrirEditarParcela(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['descricao'])) ?>', '<?= htmlspecialchars(addslashes($p['categoria'])) ?>', <?= $p['valor_total'] ?>, <?= $p['numero_parcelas'] ?>, '<?= $p['data_primeira_parcela'] ?>')">
                                                    <i class="bi bi-pencil me-2"></i>Editar
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item dflg-nav-dropdown-danger" onclick="dflgExcluirParcela(<?= $p['id'] ?>)">
                                                    <i class="bi bi-trash me-2"></i>Excluir
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ===================== Paginação ===================== -->
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                <span class="text-dflg-muted small">Mostrando <?= count($parcelas) ?> de <?= $totalFiltradas ?> parcelamentos</span>
                <div class="d-flex align-items-center gap-1">
                    <a class="dflg-page-btn <?= $pagina <= 1 ? 'disabled' : '' ?>" href="<?= dflg_parcela_qs($filtrosAtuais, ['pagina' => max(1, $pagina - 1)]) ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <?php for ($p2 = 1; $p2 <= $totalPaginas; $p2++): ?>
                        <a class="dflg-page-btn <?= $p2 === $pagina ? 'active' : '' ?>" href="<?= dflg_parcela_qs($filtrosAtuais, ['pagina' => $p2]) ?>"><?= $p2 ?></a>
                    <?php endfor; ?>
                    <a class="dflg-page-btn <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>" href="<?= dflg_parcela_qs($filtrosAtuais, ['pagina' => min($totalPaginas, $pagina + 1)]) ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- ===================== Modal Novo Parcelamento ===================== -->
    <div class="modal fade" id="modalNovoParcelamento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dflg-modal">
                <form method="post" action="<?= URL_BASE ?>/parcelamentos">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h2 class="modal-title">Novo Parcelamento</h2>
                            <p class="text-dflg-muted small mb-0 mt-1">O valor de cada parcela é calculado automaticamente.</p>
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
                            <label class="dflg-auth-label">Descrição da compra</label>
                            <input type="text" name="descricao" class="dflg-input" style="padding-left:1rem;" placeholder="Ex: Notebook Dell" value="<?= htmlspecialchars($formAntigo['descricao'] ?? '') ?>">
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
                                <label class="dflg-auth-label">Data da compra</label>
                                <input type="date" name="dataPrimeiraParcela" class="dflg-input" style="padding-left:1rem;" value="<?= htmlspecialchars($formAntigo['dataPrimeiraParcela'] ?? date('Y-m-d')) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-2">
                            <div class="col-6">
                                <label class="dflg-auth-label">Valor Total</label>
                                <div class="dflg-input-group">
                                    <i class="bi bi-cash-coin dflg-input-icon"></i>
                                    <input type="number" step="0.01" id="parcValorTotal" name="valorTotal" class="dflg-input" placeholder="0,00" value="<?= htmlspecialchars($formAntigo['valorTotal'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="dflg-auth-label">Nº de Parcelas</label>
                                <input type="number" id="parcNumeroParcelas" name="numeroParcelas" min="1" class="dflg-input" style="padding-left:1rem;" placeholder="12" value="<?= htmlspecialchars($formAntigo['numeroParcelas'] ?? '') ?>">
                            </div>
                        </div>

                        <p class="text-dflg-muted small mt-2 mb-0">
                            Valor da parcela: <span id="parcValorCalculado" class="text-success fw-semibold">R$ 0,00</span>
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="dflg-btn-cancel flex-fill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="dflg-auth-submit flex-fill">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===================== Modal Editar Parcelamento ===================== -->
    <div class="modal fade" id="modalEditarParcelamento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dflg-modal">
                <form method="post" id="formEditarParcela" action="<?= URL_BASE ?>/parcelamentos">
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title">Editar Parcelamento</h2>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="dflg-auth-label">Descrição da compra</label>
                            <input type="text" name="descricao" id="editarParcDescricao" class="dflg-input" style="padding-left:1rem;">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-7">
                                <label class="dflg-auth-label">Categoria</label>
                                <select name="categoria" id="editarParcCategoria" class="dflg-input" style="padding-left:1rem;">
                                    <?php foreach ($categorias as $c): ?>
                                        <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-5">
                                <label class="dflg-auth-label">Data da compra</label>
                                <input type="date" name="dataPrimeiraParcela" id="editarParcData" class="dflg-input" style="padding-left:1rem;">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="dflg-auth-label">Valor Total</label>
                                <div class="dflg-input-group">
                                    <i class="bi bi-cash-coin dflg-input-icon"></i>
                                    <input type="number" step="0.01" name="valorTotal" id="editarParcValorTotal" class="dflg-input">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="dflg-auth-label">Nº de Parcelas</label>
                                <input type="number" min="1" name="numeroParcelas" id="editarParcNumParcelas" class="dflg-input" style="padding-left:1rem;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="dflg-btn-cancel flex-fill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="dflg-auth-submit flex-fill">Salvar alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form escondido usado só para o Excluir (com confirm()) -->
    <form method="post" id="formExcluirParcela" action="<?= URL_BASE ?>/parcelamentos"></form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function dflgAtualizarValorParcela() {
            const total = parseFloat(document.getElementById('parcValorTotal').value) || 0;
            const numero = parseInt(document.getElementById('parcNumeroParcelas').value) || 0;
            const valorParcela = numero > 0 ? total / numero : 0;
            document.getElementById('parcValorCalculado').textContent = valorParcela.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }
        document.getElementById('parcValorTotal').addEventListener('input', dflgAtualizarValorParcela);
        document.getElementById('parcNumeroParcelas').addEventListener('input', dflgAtualizarValorParcela);

        function dflgAbrirEditarParcela(id, descricao, categoria, valorTotal, numeroParcelas, dataCompra) {
            document.getElementById('formEditarParcela').action = '<?= URL_BASE ?>/parcelamentos/' + id + '/atualizar';
            document.getElementById('editarParcDescricao').value = descricao;
            document.getElementById('editarParcCategoria').value = categoria;
            document.getElementById('editarParcValorTotal').value = valorTotal;
            document.getElementById('editarParcNumParcelas').value = numeroParcelas;
            document.getElementById('editarParcData').value = dataCompra;
            new bootstrap.Modal(document.getElementById('modalEditarParcelamento')).show();
        }

        function dflgExcluirParcela(id) {
            if (!confirm('Tem certeza que deseja excluir este parcelamento? Essa ação não pode ser desfeita.')) {
                return;
            }
            var form = document.getElementById('formExcluirParcela');
            form.action = '<?= URL_BASE ?>/parcelamentos/' + id + '/excluir';
            form.submit();
        }
    </script>
    <?php if (!empty($abrirModal)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('modalNovoParcelamento')).show();
            });
        </script>
    <?php endif; ?>
</body>

</html>
