<?php
if (!function_exists('dflg_money')) {
    function dflg_money(float $valor): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias • DFLG Investiments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/dflg.css" rel="stylesheet">
</head>

<body class="dflg-app">

    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container-xxl px-4 py-5">

        <div class="dflg-page-title">
            <span class="bar"></span>
            <h1>Categorias</h1>
        </div>
        <p class="dflg-page-subtitle mb-4">Organize e monitore seus gastos por categoria</p>

        <!-- ===================== Cards de resumo ===================== -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="dflg-card h-100 p-4">
                    <p class="dflg-eyebrow mb-2">Total Gasto</p>
                    <p class="dflg-metric mb-1" style="font-size:1.9rem;"><?= dflg_money($totalGasto) ?></p>
                    <p class="text-dflg-muted small mb-0">de <?= dflg_money($totalOrcamento) ?> orçado</p>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="dflg-card h-100 p-4">
                    <p class="dflg-eyebrow mb-2">Categorias Ativas</p>
                    <p class="dflg-metric mb-1" style="font-size:1.9rem;"><?= $categoriasAtivas ?></p>
                    <p class="text-dflg-muted small mb-0"><?= $pertoDoLimite ?> próximas do limite</p>
                </div>
            </div>
        </div>

        <!-- ===================== Grid de categorias ===================== -->
        <div class="row g-3">
            <?php foreach ($categorias as $c): ?>
                <div class="col-12 col-md-6">
                    <div class="dflg-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="dflg-cat-icon" style="background: <?= $c['cor'] ?>20; border-color: <?= $c['cor'] ?>40; color: <?= $c['cor'] ?>;">
                                    <i class="bi <?= $c['icone'] ?>"></i>
                                </span>
                                <div>
                                    <h3 class="dflg-parcela-title mb-0"><?= htmlspecialchars($c['nome']) ?></h3>
                                    <p class="text-dflg-muted small mb-0"><?= $c['transacoes'] ?> transações</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($c['pertoDoLimite']): ?>
                                    <span class="dflg-attention-badge">Atenção</span>
                                <?php endif; ?>
                                <button type="button" class="dflg-icon-btn" title="Definir orçamento"
                                        data-bs-toggle="modal" data-bs-target="#modalOrcamento"
                                        data-nome="<?= htmlspecialchars($c['nome']) ?>"
                                        data-orcamento="<?= $c['orcamento'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                        </div>

                        <?php if ($c['orcamento'] > 0): ?>
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2 small">
                                    <span class="text-dflg-muted"><?= dflg_money($c['gasto']) ?></span>
                                    <span class="text-dflg-muted"><?= dflg_money($c['orcamento']) ?></span>
                                </div>
                                <div class="dflg-progress">
                                    <div class="dflg-progress-bar" style="width: <?= $c['percentualExibido'] ?>%; background: <?= $c['pertoDoLimite'] ? 'var(--dflg-orange-500)' : $c['cor'] ?>; box-shadow: 0 0 12px <?= $c['pertoDoLimite'] ? 'var(--dflg-orange-500)' : $c['cor'] ?>80;"></div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-3 dflg-pagination-border">
                                <span class="text-dflg-muted small">Usado</span>
                                <span class="fw-bold" style="color: <?= $c['pertoDoLimite'] ? 'var(--dflg-orange-500)' : $c['cor'] ?>;"><?= round($c['percentual']) ?>%</span>
                                <span class="text-dflg-muted small">Restam <?= dflg_money($c['restante']) ?></span>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <p class="text-dflg-muted small mb-2">
                                    <?= $c['gasto'] > 0 ? dflg_money($c['gasto']) . ' gastos, sem orçamento definido' : 'Nenhum orçamento definido ainda' ?>
                                </p>
                                <button type="button" class="dflg-btn-outline-green"
                                        data-bs-toggle="modal" data-bs-target="#modalOrcamento"
                                        data-nome="<?= htmlspecialchars($c['nome']) ?>"
                                        data-orcamento="0">
                                    Definir orçamento
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- ===================== Modal Orçamento ===================== -->
    <div class="modal fade" id="modalOrcamento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dflg-modal">
                <form method="post" action="<?= URL_BASE ?>/categorias">
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title">Orçamento — <span id="modalCategoriaNome"></span></h2>
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

                        <input type="hidden" name="nome" id="modalCategoriaNomeInput" value="<?= htmlspecialchars($abrirModalPara ?? '') ?>">

                        <label class="dflg-auth-label">Orçamento mensal</label>
                        <div class="dflg-input-group">
                            <i class="bi bi-cash-coin dflg-input-icon"></i>
                            <input type="number" step="0.01" min="0" name="orcamento" id="modalOrcamentoInput" class="dflg-input" placeholder="0,00">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="dflg-btn-cancel flex-fill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="dflg-auth-submit flex-fill">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preenche o modal com a categoria clicada (mesmo modal reaproveitado para todas)
        document.getElementById('modalOrcamento').addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            document.getElementById('modalCategoriaNome').textContent = btn.dataset.nome;
            document.getElementById('modalCategoriaNomeInput').value = btn.dataset.nome;
            document.getElementById('modalOrcamentoInput').value = btn.dataset.orcamento;
        });
    </script>
    <?php if (!empty($abrirModalPara)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('modalCategoriaNome').textContent = <?= json_encode($abrirModalPara) ?>;
                new bootstrap.Modal(document.getElementById('modalOrcamento')).show();
            });
        </script>
    <?php endif; ?>
</body>

</html>
