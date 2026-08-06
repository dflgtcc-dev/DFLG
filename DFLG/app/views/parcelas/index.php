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
        if (!$data) {
            return '—';
        }
        $dt = \DateTime::createFromFormat('Y-m-d', $data);
        return $dt ? $dt->format('d/m/Y') : $data;
    }
}
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

        <!-- ===================== Lista de parcelamentos ===================== -->
        <?php if (empty($parcelas)): ?>
            <div class="dflg-panel text-center py-5 text-dflg-muted">
                <i class="bi bi-credit-card d-block mb-3" style="font-size: 2.5rem; opacity: .3;"></i>
                <p class="mb-0">Nenhum parcelamento ativo. Clique em "Novo Parcelamento" no topo da página para adicionar o primeiro.</p>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($parcelas as $p): ?>
                    <?php $quaseQuitado = $p['percentual'] >= 80; ?>
                    <div class="col-12 col-xl-6">
                        <div class="dflg-card p-4 h-100">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="dflg-card-icon <?= $quaseQuitado ? '' : 'is-blue' ?>">
                                        <i class="bi bi-credit-card"></i>
                                    </span>
                                    <div>
                                        <h3 class="dflg-parcela-title"><?= htmlspecialchars($p['descricao']) ?></h3>
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <span class="dflg-badge-soft"><?= htmlspecialchars($p['categoria']) ?></span>
                                            <span class="text-dflg-muted small d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-calendar3"></i> <?= dflg_data_br($p['proximoVencimento']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <p class="mb-0 text-white fw-bold fs-5"><?= dflg_money((float) $p['valor_parcela']) ?></p>
                                    <p class="text-dflg-muted small mb-0">por mês</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2 small">
                                    <span class="text-dflg-muted"><span class="text-white fw-medium"><?= $p['parcelaAtual'] ?></span> de <?= $p['numero_parcelas'] ?> pagas</span>
                                    <span class="fw-bold" style="color: <?= $quaseQuitado ? 'var(--dflg-green-500)' : '#3b82f6' ?>;"><?= $p['percentual'] ?>%</span>
                                </div>
                                <div class="dflg-progress">
                                    <div class="dflg-progress-bar <?= $quaseQuitado ? '' : 'is-blue' ?>" style="width: <?= $p['percentual'] ?>%"></div>
                                </div>
                            </div>

                            <div class="row g-2 pt-3 dflg-pagination-border">
                                <div class="col-4">
                                    <p class="text-dflg-muted small mb-1">Valor Total</p>
                                    <p class="text-white fw-medium small mb-0"><?= dflg_money((float) $p['valor_total']) ?></p>
                                </div>
                                <div class="col-4">
                                    <p class="text-dflg-muted small mb-1">Restam</p>
                                    <p class="fw-bold small mb-0" style="color: var(--dflg-orange-500);"><?= $p['restam'] ?>x</p>
                                </div>
                                <div class="col-4">
                                    <p class="text-dflg-muted small mb-1">A Pagar</p>
                                    <p class="fw-bold small mb-0" style="color: var(--dflg-orange-500);"><?= dflg_money($p['valorAPagar']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
