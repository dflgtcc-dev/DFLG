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
if (!function_exists('dflg_mes_ano_br')) {
    function dflg_mes_ano_br(string $data): string
    {
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $dt = \DateTime::createFromFormat('Y-m-d', $data);
        if (!$dt) return $data;
        return $meses[(int) $dt->format('n') - 1] . '/' . $dt->format('Y');
    }
}

$iconesPorTipo = [
    'economizar' => 'bi-piggy-bank',
    'comprar' => 'bi-bag-check',
    'investir' => 'bi-graph-up-arrow',
];

/** Query string atual com overrides, preservando os outros filtros. */
function dflg_meta_qs(array $atuais, array $overrides = []): string
{
    return '?' . http_build_query(array_merge($atuais, $overrides));
}

$filtrosAtuais = [
    'busca' => $busca,
    'tipoFiltro' => $tipoFiltro,
    'status' => $statusFiltro,
    'pagina' => $pagina,
];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metas • DFLG Investiments</title>

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
                    <h1>Metas</h1>
                </div>
                <p class="dflg-page-subtitle mb-0">Acompanhe seus objetivos financeiros</p>
            </div>
            <button type="button" class="dflg-btn-solid-green px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalNovaMeta">
                <i class="bi bi-plus-lg"></i> Nova Meta
            </button>
        </div>
        <div class="mb-4"></div>

        <!-- ===================== Cards de resumo ===================== -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="dflg-eyebrow mb-0">Metas Ativas</p>
                        <i class="bi bi-bullseye text-success"></i>
                    </div>
                    <p class="dflg-metric mb-0" style="font-size:1.7rem;"><?= $ativas ?></p>
                    <p class="text-dflg-muted small mb-0">objetivos em andamento</p>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="dflg-eyebrow mb-0">Valor Acumulado</p>
                        <i class="bi bi-wallet2 text-success"></i>
                    </div>
                    <p class="dflg-metric is-green mb-0" style="font-size:1.35rem;"><?= dflg_money($valorAcumulado) ?></p>
                    <p class="text-dflg-muted small mb-0">guardado no total</p>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="dflg-eyebrow mb-0">Valor Necessário</p>
                        <i class="bi bi-hourglass-split" style="color:#3b82f6;"></i>
                    </div>
                    <p class="dflg-metric mb-0" style="font-size:1.35rem; color:#3b82f6;"><?= dflg_money($valorNecessario) ?></p>
                    <p class="text-dflg-muted small mb-0">para todas as metas</p>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="dflg-eyebrow mb-0">Concluídas</p>
                        <i class="bi bi-check-circle" style="color:#eab308;"></i>
                    </div>
                    <p class="dflg-metric mb-0" style="font-size:1.7rem;"><?= $concluidas ?></p>
                    <p class="text-dflg-muted small mb-0">metas concluídas</p>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="dflg-card h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="dflg-eyebrow mb-0">Taxa de Sucesso</p>
                        <i class="bi bi-graph-up-arrow text-success"></i>
                    </div>
                    <p class="dflg-metric is-green mb-0" style="font-size:1.7rem;"><?= $taxaSucesso ?>%</p>
                    <p class="text-dflg-muted small mb-0">de metas concluídas</p>
                </div>
            </div>
        </div>

        <!-- ===================== Busca + filtros ===================== -->
        <form method="get" action="<?= URL_BASE ?>/metas" class="d-flex flex-wrap gap-3 mb-4 align-items-center">
            <input type="hidden" name="pagina" value="1">
            <div class="dflg-input-group flex-grow-1" style="min-width: 220px;">
                <i class="bi bi-search dflg-input-icon"></i>
                <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" class="dflg-input" placeholder="Buscar meta...">
            </div>

            <select name="tipoFiltro" class="dflg-input dflg-select-auto w-auto" onchange="this.form.submit()">
                <option value="all" <?= $tipoFiltro === 'all' ? 'selected' : '' ?>>Todos os tipos</option>
                <?php foreach ($tipos as $valorTipo => $label): ?>
                    <option value="<?= $valorTipo ?>" <?= $tipoFiltro === $valorTipo ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="dflg-input dflg-select-auto w-auto" onchange="this.form.submit()">
                <option value="all" <?= $statusFiltro === 'all' ? 'selected' : '' ?>>Status: Todas</option>
                <option value="ativas" <?= $statusFiltro === 'ativas' ? 'selected' : '' ?>>Status: Ativas</option>
                <option value="concluidas" <?= $statusFiltro === 'concluidas' ? 'selected' : '' ?>>Status: Concluídas</option>
            </select>
        </form>

        <!-- ===================== Grid de metas ===================== -->
        <?php if (empty($metas)): ?>
            <div class="dflg-panel text-center py-5 text-dflg-muted mb-4">
                <i class="bi bi-bullseye d-block mb-3" style="font-size: 2.5rem; opacity: .3;"></i>
                <p class="mb-0"><?= $totalFiltradas === 0 && $busca === '' && $tipoFiltro === 'all' && $statusFiltro === 'all' ? 'Nenhuma meta ainda. Clique em "Nova Meta" para criar a primeira.' : 'Nenhuma meta encontrada com esse filtro.' ?></p>
            </div>
        <?php else: ?>
            <div class="row g-3 mb-4">
                <?php foreach ($metas as $m):
                    $raio = 30;
                    $circunferencia = 2 * M_PI * $raio;
                    $offset = $circunferencia * (1 - $m['percentual'] / 100);
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="dflg-goal-card">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="dflg-card-icon">
                                        <i class="bi <?= $iconesPorTipo[$m['tipo']] ?? 'bi-bullseye' ?>"></i>
                                    </span>
                                    <div>
                                        <h3 class="dflg-parcela-title mb-0">
                                            <?= htmlspecialchars($m['nome_meta']) ?>
                                            <?php if ($m['fixada']): ?>
                                                <i class="bi bi-pin-angle-fill text-success ms-1" title="Fixada no Dashboard" style="font-size: 0.75rem;"></i>
                                            <?php endif; ?>
                                        </h3>
                                        <span class="text-dflg-muted small"><?= htmlspecialchars($tipos[$m['tipo']] ?? $m['tipo']) ?></span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="dflg-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end dflg-nav-dropdown">
                                        <li>
                                            <button type="button" class="dropdown-item" onclick="dflgFixarMeta(<?= $m['id'] ?>)">
                                                <i class="bi bi-pin-angle<?= $m['fixada'] ? '-fill' : '' ?> me-2"></i><?= $m['fixada'] ? 'Desafixar do Dashboard' : 'Fixar no Dashboard' ?>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item"
                                                onclick="dflgAbrirEditarMeta(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['nome_meta'])) ?>', '<?= $m['tipo'] ?>', <?= $m['valor_meta'] ?>, '<?= $m['data_limite'] ?>')">
                                                <i class="bi bi-pencil me-2"></i>Editar
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item dflg-nav-dropdown-danger" onclick="dflgExcluirMeta(<?= $m['id'] ?>)">
                                                <i class="bi bi-trash me-2"></i>Excluir
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3 mb-3">
                                <svg width="70" height="70" viewBox="0 0 70 70" class="flex-shrink-0">
                                    <circle cx="35" cy="35" r="<?= $raio ?>" class="dflg-ring-track"></circle>
                                    <circle cx="35" cy="35" r="<?= $raio ?>" class="dflg-ring-progress"
                                        style="stroke-dasharray: <?= round($circunferencia, 2) ?>; stroke-dashoffset: <?= round($offset, 2) ?>;"></circle>
                                    <text x="35" y="35" class="dflg-ring-text" text-anchor="middle" dominant-baseline="central"><?= $m['percentual'] ?>%</text>
                                </svg>
                                <div class="flex-grow-1">
                                    <p class="mb-1 small"><span class="text-dflg-muted">Meta:</span> <span class="text-white fw-medium"><?= dflg_money((float) $m['valor_meta']) ?></span></p>
                                    <p class="mb-0 small"><span class="text-dflg-muted">Guardado:</span> <span class="text-success fw-medium"><?= dflg_money((float) $m['valor_atual']) ?></span></p>
                                </div>
                            </div>

                            <div class="dflg-progress mb-3">
                                <div class="dflg-progress-bar" style="width: <?= $m['percentual'] ?>%"></div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <?php if ($m['concluida']): ?>
                                    <span class="dflg-status-badge is-done"><i class="bi bi-check-circle-fill"></i> Concluída</span>
                                    <span class="text-dflg-muted small d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-calendar3"></i> Concluída em <?= dflg_data_br($m['data_limite']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-dflg-muted small">Faltam <span class="text-white fw-medium"><?= dflg_money($m['faltam']) ?></span></span>
                                    <span class="text-dflg-muted small d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-calendar3"></i> Previsão: <?= dflg_mes_ano_br($m['data_limite']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!$m['concluida']): ?>
                                <button type="button" class="dflg-btn-outline-green dflg-btn-sm w-100 mt-3"
                                    onclick="dflgAbrirAportarMeta(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['nome_meta'])) ?>')">
                                    <i class="bi bi-plus-lg me-1"></i>Guardar valor
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ===================== Paginação ===================== -->
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                <span class="text-dflg-muted small">Mostrando <?= count($metas) ?> de <?= $totalFiltradas ?> metas</span>
                <div class="d-flex align-items-center gap-1">
                    <a class="dflg-page-btn <?= $pagina <= 1 ? 'disabled' : '' ?>" href="<?= dflg_meta_qs($filtrosAtuais, ['pagina' => max(1, $pagina - 1)]) ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                        <a class="dflg-page-btn <?= $p === $pagina ? 'active' : '' ?>" href="<?= dflg_meta_qs($filtrosAtuais, ['pagina' => $p]) ?>"><?= $p ?></a>
                    <?php endfor; ?>
                    <a class="dflg-page-btn <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>" href="<?= dflg_meta_qs($filtrosAtuais, ['pagina' => min($totalPaginas, $pagina + 1)]) ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- ===================== Modal Nova Meta ===================== -->
    <div class="modal fade" id="modalNovaMeta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dflg-modal">
                <form method="post" action="<?= URL_BASE ?>/metas">
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title">Nova Meta</h2>
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
                            <label class="dflg-auth-label">Nome da meta</label>
                            <input type="text" name="nomeMeta" class="dflg-input" style="padding-left:1rem;" placeholder="Ex: Reserva de emergência" value="<?= htmlspecialchars($formAntigo['nomeMeta'] ?? '') ?>">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-7">
                                <label class="dflg-auth-label">Tipo</label>
                                <select name="tipo" class="dflg-input" style="padding-left:1rem;">
                                    <?php foreach ($tipos as $valorTipo => $label): ?>
                                        <option value="<?= $valorTipo ?>" <?= ($formAntigo['tipo'] ?? '') === $valorTipo ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-5">
                                <label class="dflg-auth-label">Data-limite</label>
                                <input type="date" name="dataLimite" class="dflg-input" style="padding-left:1rem;" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($formAntigo['dataLimite'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-1">
                            <label class="dflg-auth-label">Valor-alvo</label>
                            <div class="dflg-input-group">
                                <i class="bi bi-cash-coin dflg-input-icon"></i>
                                <input type="number" step="0.01" name="valorMeta" class="dflg-input" placeholder="0,00" value="<?= htmlspecialchars($formAntigo['valorMeta'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="dflg-btn-cancel flex-fill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="dflg-auth-submit flex-fill">Criar meta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===================== Modal Editar Meta ===================== -->
    <div class="modal fade" id="modalEditarMeta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dflg-modal">
                <form method="post" id="formEditarMeta" action="<?= URL_BASE ?>/metas">
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title">Editar Meta</h2>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="dflg-auth-label">Nome da meta</label>
                            <input type="text" name="nomeMeta" id="editarMetaNome" class="dflg-input" style="padding-left:1rem;">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-7">
                                <label class="dflg-auth-label">Tipo</label>
                                <select name="tipo" id="editarMetaTipo" class="dflg-input" style="padding-left:1rem;">
                                    <?php foreach ($tipos as $valorTipo => $label): ?>
                                        <option value="<?= $valorTipo ?>"><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-5">
                                <label class="dflg-auth-label">Data-limite</label>
                                <input type="date" name="dataLimite" id="editarMetaData" class="dflg-input" style="padding-left:1rem;" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                            </div>
                        </div>
                        <div class="mb-1">
                            <label class="dflg-auth-label">Valor-alvo</label>
                            <div class="dflg-input-group">
                                <i class="bi bi-cash-coin dflg-input-icon"></i>
                                <input type="number" step="0.01" name="valorMeta" id="editarMetaValor" class="dflg-input">
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

    <!-- ===================== Modal Aportar (RF19 - Cumprir Meta) ===================== -->
    <div class="modal fade" id="modalAportarMeta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dflg-modal">
                <form method="post" id="formAportarMeta" action="<?= URL_BASE ?>/metas">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h2 class="modal-title">Guardar valor na meta</h2>
                            <p class="text-dflg-muted small mb-0 mt-1" id="aportarMetaNome"></p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <label class="dflg-auth-label">Quanto você guardou/investiu agora?</label>
                        <div class="dflg-input-group">
                            <i class="bi bi-cash-coin dflg-input-icon"></i>
                            <input type="number" step="0.01" min="0.01" name="valorAporte" class="dflg-input" placeholder="0,00" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="dflg-btn-cancel flex-fill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="dflg-auth-submit flex-fill">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Forms escondidos usados só para Excluir e Fixar/Desafixar -->
    <form method="post" id="formExcluirMeta" action="<?= URL_BASE ?>/metas"></form>
    <form method="post" id="formFixarMeta" action="<?= URL_BASE ?>/metas"></form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function dflgFixarMeta(id) {
            var form = document.getElementById('formFixarMeta');
            form.action = '<?= URL_BASE ?>/metas/' + id + '/fixar';
            form.submit();
        }

        function dflgAbrirEditarMeta(id, nome, tipo, valorMeta, dataLimite) {
            document.getElementById('formEditarMeta').action = '<?= URL_BASE ?>/metas/' + id + '/atualizar';
            document.getElementById('editarMetaNome').value = nome;
            document.getElementById('editarMetaTipo').value = tipo;
            document.getElementById('editarMetaValor').value = valorMeta;
            document.getElementById('editarMetaData').value = dataLimite;
            new bootstrap.Modal(document.getElementById('modalEditarMeta')).show();
        }

        function dflgAbrirAportarMeta(id, nome) {
            document.getElementById('formAportarMeta').action = '<?= URL_BASE ?>/metas/' + id + '/aportar';
            document.getElementById('aportarMetaNome').textContent = nome;
            new bootstrap.Modal(document.getElementById('modalAportarMeta')).show();
        }

        function dflgExcluirMeta(id) {
            if (!confirm('Tem certeza que deseja excluir esta meta? Essa ação não pode ser desfeita.')) {
                return;
            }
            var form = document.getElementById('formExcluirMeta');
            form.action = '<?= URL_BASE ?>/metas/' + id + '/excluir';
            form.submit();
        }
    </script>
    <?php if (!empty($abrirModal)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('modalNovaMeta')).show();
            });
        </script>
    <?php endif; ?>
</body>

</html>
