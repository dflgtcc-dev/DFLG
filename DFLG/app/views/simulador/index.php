<?php
if (!function_exists('dflg_money')) {
    function dflg_money(float $valor): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
}

// Agrupa os simuladores por categoria, preservando a ordem de inserção.
$porCategoria = [];
foreach ($simuladores as $id => $s) {
    $porCategoria[$s['categoria']][$id] = $s;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadoras • DFLG Investiments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/dflg.css" rel="stylesheet">
</head>

<body class="dflg-app">

    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container-xxl px-4 py-5">

        <div class="dflg-page-title">
            <span class="bar"></span>
            <h1>Calculadoras</h1>
        </div>
        <p class="dflg-page-subtitle mb-5">Simuladores e calculadoras financeiras para planejar seu futuro</p>

        <div class="row g-4">
            <!-- ===================== Sidebar ===================== -->
            <div class="col-12 col-lg-3">
                <div class="dflg-panel dflg-sim-sidebar">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-calculator text-success"></i>
                        <h2 class="mb-0" style="font-size:1.1rem;">Calculadoras</h2>
                    </div>

                    <?php foreach ($porCategoria as $categoriaNome => $itens): ?>
                        <p class="dflg-sim-category-label"><?= htmlspecialchars($categoriaNome) ?></p>
                        <div class="d-flex flex-column gap-2 mb-4">
                            <?php foreach ($itens as $id => $s): ?>
                                <a href="<?= URL_BASE ?>/calculadoras?tipo=<?= urlencode($id) ?>" class="dflg-sim-link <?= $tipoAtivo === $id ? 'active' : '' ?>">
                                    <i class="bi <?= $s['icone'] ?>"></i>
                                    <span><?= htmlspecialchars($s['nome']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ===================== Conteúdo ===================== -->
            <div class="col-12 col-lg-9">
                <div class="dflg-panel">

                    <?php if ($tipoAtivo === 'investimento'): ?>

                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="dflg-card-icon"><i class="bi bi-calculator"></i></span>
                            <h2 class="mb-0">Calculadora de Investimentos</h2>
                        </div>
                        <p class="dflg-panel-sub mb-4" style="margin-left: 4.25rem;">Calcule o potencial de crescimento do seu capital</p>

                        <form method="get" action="<?= URL_BASE ?>/calculadoras" id="formSimulador">
                            <input type="hidden" name="tipo" value="investimento">
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Valor Inicial</label>
                                    <input type="number" step="0.01" id="simValorInicial" name="valorInicial" class="dflg-input" style="padding-left:1rem;" value="<?= $valorInicial ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Aporte Mensal</label>
                                    <input type="number" step="0.01" id="simAporteMensal" name="aporteMensal" class="dflg-input" style="padding-left:1rem;" value="<?= $aporteMensal ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Taxa de Retorno Mensal (%)</label>
                                    <input type="number" step="0.1" id="simTaxaMensal" name="taxaMensal" class="dflg-input" style="padding-left:1rem;" value="<?= $taxaMensal ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Período (meses)</label>
                                    <input type="number" id="simPeriodoMeses" name="periodoMeses" class="dflg-input" style="padding-left:1rem;" value="<?= $periodoMeses ?>">
                                </div>
                            </div>
                        </form>

                        <div class="dflg-sim-result">
                            <div class="dflg-sim-result-row">
                                <span>Total Investido</span>
                                <span id="resTotalInvestido" class="text-white fw-medium"><?= dflg_money($resultado['totalInvestido']) ?></span>
                            </div>
                            <div class="dflg-sim-result-row">
                                <span>Rendimento</span>
                                <span id="resRendimento" class="text-success fw-medium"><?= dflg_money($resultado['rendimento']) ?></span>
                            </div>
                            <div class="dflg-sim-result-row dflg-sim-result-final">
                                <span>Valor Final</span>
                                <span id="resValorFinal"><?= dflg_money($resultado['valorFinal']) ?></span>
                            </div>
                        </div>

                    <?php else: ?>

                        <?php $info = $textosApoio[$tipoAtivo]; ?>
                        <h2 class="mb-2"><?= htmlspecialchars($info['titulo']) ?></h2>
                        <p class="dflg-panel-sub mb-3"><?= htmlspecialchars($info['texto']) ?></p>
                        <div class="dflg-sim-tip">
                            💡 <strong>Como usar:</strong> <?= htmlspecialchars($info['dica']) ?>
                        </div>

                        <div class="text-center py-5">
                            <span class="dflg-sim-placeholder-icon"><i class="bi bi-calculator"></i></span>
                            <p class="text-dflg-muted mb-0">Calculadora em desenvolvimento...</p>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php if ($tipoAtivo === 'investimento'): ?>
        <script>
            // Recalcula instantaneamente ao digitar, sem precisar recarregar a página
            // (mesma fórmula usada no PHP, em SimuladorHelper::calcularInvestimento).
            function dflgFormatarMoeda(valor) {
                return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            }

            function dflgCalcularInvestimento() {
                const valorInicial = parseFloat(document.getElementById('simValorInicial').value) || 0;
                const aporteMensal = parseFloat(document.getElementById('simAporteMensal').value) || 0;
                const taxaMensal = parseFloat(document.getElementById('simTaxaMensal').value) || 0;
                const periodoMeses = parseInt(document.getElementById('simPeriodoMeses').value) || 0;

                let total = valorInicial;
                for (let i = 0; i < periodoMeses; i++) {
                    total = total * (1 + taxaMensal / 100) + aporteMensal;
                }

                const totalInvestido = valorInicial + (aporteMensal * periodoMeses);
                const rendimento = total - totalInvestido;

                document.getElementById('resTotalInvestido').textContent = dflgFormatarMoeda(totalInvestido);
                document.getElementById('resRendimento').textContent = dflgFormatarMoeda(rendimento);
                document.getElementById('resValorFinal').textContent = dflgFormatarMoeda(total);
            }

            ['simValorInicial', 'simAporteMensal', 'simTaxaMensal', 'simPeriodoMeses'].forEach(function (id) {
                document.getElementById(id).addEventListener('input', dflgCalcularInvestimento);
            });
        </script>
    <?php endif; ?>
</body>

</html>
