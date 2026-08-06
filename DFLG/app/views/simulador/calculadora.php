<?php
if (!function_exists('dflg_money')) {
    function dflg_money(float $valor): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
}
if (!function_exists('dflg_pct')) {
    function dflg_pct(float $valor, int $casas = 2): string
    {
        return number_format($valor, $casas, ',', '.') . '%';
    }
}

$c = $calculadoras[$slug];
$in = $inputs;
$r = $resultado;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($c['nome']) ?> • Calculadoras • DFLG Investiments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/dflg.css" rel="stylesheet">
</head>

<body class="dflg-app">

    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container-xxl px-4 py-5">

        <!-- Breadcrumb -->
        <nav class="dflg-sim-breadcrumb mb-4">
            <a href="<?= URL_BASE ?>/calculadoras">Calculadoras</a>
            <i class="bi bi-chevron-right"></i>
            <span><?= htmlspecialchars($c['nome']) ?></span>
        </nav>

        <div class="row g-4">
            <div class="col-12 col-lg-8">

                <!-- Cabeçalho + explicação -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="dflg-card-icon"><i class="bi <?= $c['icone'] ?>"></i></span>
                    <h1 class="mb-0" style="font-size:1.7rem;"><?= htmlspecialchars($info['titulo']) ?></h1>
                </div>
                <p class="dflg-panel-sub mb-4"><?= htmlspecialchars($info['texto']) ?></p>

                <!-- Formulário + resultado -->
                <div class="dflg-panel">
                    <h2 class="mb-4" style="font-size:1.05rem;"><i class="bi bi-calculator text-success me-2"></i>Faça sua simulação</h2>

                    <form method="get" action="<?= URL_BASE ?>/calculadoras/<?= urlencode($slug) ?>" class="mb-4">

                        <?php // ===================== FORMULÁRIOS POR CALCULADORA ===================== ?>

                        <?php if ($slug === 'tesouro-direto'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Título</label>
                                    <select name="tipoTitulo" class="dflg-input" style="padding-left:1rem;">
                                        <option value="selic" <?= $in['tipoTitulo'] === 'selic' ? 'selected' : '' ?>>Tesouro Selic</option>
                                        <option value="prefixado" <?= $in['tipoTitulo'] === 'prefixado' ? 'selected' : '' ?>>Tesouro Prefixado</option>
                                        <option value="ipca" <?= $in['tipoTitulo'] === 'ipca' ? 'selected' : '' ?>>Tesouro IPCA+</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Taxa Anual do Título (%)</label>
                                    <input type="number" step="0.01" name="taxaAnual" class="dflg-input" style="padding-left:1rem;" value="<?= $in['taxaAnual'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Valor Inicial</label>
                                    <input type="number" step="0.01" name="valorInicial" class="dflg-input" style="padding-left:1rem;" value="<?= $in['valorInicial'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Aporte Mensal (opcional)</label>
                                    <input type="number" step="0.01" name="aporteMensal" class="dflg-input" style="padding-left:1rem;" value="<?= $in['aporteMensal'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Prazo (anos)</label>
                                    <input type="number" name="prazoAnos" class="dflg-input" style="padding-left:1rem;" value="<?= $in['prazoAnos'] ?>">
                                </div>
                            </div>

                        <?php elseif ($slug === 'juros-compostos'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Valor Inicial</label>
                                    <input type="number" step="0.01" name="valorInicial" class="dflg-input" style="padding-left:1rem;" value="<?= $in['valorInicial'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Taxa Mensal (%)</label>
                                    <input type="number" step="0.01" name="taxaMensal" class="dflg-input" style="padding-left:1rem;" value="<?= $in['taxaMensal'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Período (meses)</label>
                                    <input type="number" name="periodoMeses" class="dflg-input" style="padding-left:1rem;" value="<?= $in['periodoMeses'] ?>">
                                </div>
                            </div>

                        <?php elseif ($slug === 'investimentos'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Valor Inicial</label>
                                    <input type="number" step="0.01" name="valorInicial" class="dflg-input" style="padding-left:1rem;" value="<?= $in['valorInicial'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Aporte Mensal</label>
                                    <input type="number" step="0.01" name="aporteMensal" class="dflg-input" style="padding-left:1rem;" value="<?= $in['aporteMensal'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Taxa de Retorno Mensal (%)</label>
                                    <input type="number" step="0.01" name="taxaMensal" class="dflg-input" style="padding-left:1rem;" value="<?= $in['taxaMensal'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Período (meses)</label>
                                    <input type="number" name="periodoMeses" class="dflg-input" style="padding-left:1rem;" value="<?= $in['periodoMeses'] ?>">
                                </div>
                            </div>

                        <?php elseif ($slug === 'renda-fixa'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Valor Investido</label>
                                    <input type="number" step="0.01" name="valorInvestido" class="dflg-input" style="padding-left:1rem;" value="<?= $in['valorInvestido'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Taxa Anual (%)</label>
                                    <input type="number" step="0.01" name="taxaAnual" class="dflg-input" style="padding-left:1rem;" value="<?= $in['taxaAnual'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Prazo (meses)</label>
                                    <input type="number" name="prazoMeses" class="dflg-input" style="padding-left:1rem;" value="<?= $in['prazoMeses'] ?>">
                                </div>
                            </div>

                        <?php elseif ($slug === 'cdb'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Valor Investido</label>
                                    <input type="number" step="0.01" name="valorInvestido" class="dflg-input" style="padding-left:1rem;" value="<?= $in['valorInvestido'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">% do CDI oferecido</label>
                                    <input type="number" step="0.01" name="percentualCDI" class="dflg-input" style="padding-left:1rem;" value="<?= $in['percentualCDI'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">CDI Anual Atual (%)</label>
                                    <input type="number" step="0.01" name="cdiAnual" class="dflg-input" style="padding-left:1rem;" value="<?= $in['cdiAnual'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Prazo (meses)</label>
                                    <input type="number" name="prazoMeses" class="dflg-input" style="padding-left:1rem;" value="<?= $in['prazoMeses'] ?>">
                                </div>
                            </div>

                        <?php elseif ($slug === 'aposentadoria'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Idade Atual</label>
                                    <input type="number" name="idadeAtual" class="dflg-input" style="padding-left:1rem;" value="<?= $in['idadeAtual'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Idade de Aposentadoria</label>
                                    <input type="number" name="idadeAposentadoria" class="dflg-input" style="padding-left:1rem;" value="<?= $in['idadeAposentadoria'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Renda Mensal Desejada</label>
                                    <input type="number" step="0.01" name="rendaMensalDesejada" class="dflg-input" style="padding-left:1rem;" value="<?= $in['rendaMensalDesejada'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Taxa de Retorno Mensal (%)</label>
                                    <input type="number" step="0.01" name="taxaMensal" class="dflg-input" style="padding-left:1rem;" value="<?= $in['taxaMensal'] ?>">
                                </div>
                            </div>

                        <?php elseif ($slug === 'educacao'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Custo Estimado</label>
                                    <input type="number" step="0.01" name="custoEstimado" class="dflg-input" style="padding-left:1rem;" value="<?= $in['custoEstimado'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Anos até ser necessário</label>
                                    <input type="number" name="anosAteNecessario" class="dflg-input" style="padding-left:1rem;" value="<?= $in['anosAteNecessario'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Taxa de Retorno Mensal (%)</label>
                                    <input type="number" step="0.01" name="taxaMensal" class="dflg-input" style="padding-left:1rem;" value="<?= $in['taxaMensal'] ?>">
                                </div>
                            </div>

                        <?php elseif ($slug === 'reserva-emergencia'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Despesas Mensais</label>
                                    <input type="number" step="0.01" name="despesasMensais" class="dflg-input" style="padding-left:1rem;" value="<?= $in['despesasMensais'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Meses de Segurança</label>
                                    <input type="number" name="mesesSeguranca" class="dflg-input" style="padding-left:1rem;" value="<?= $in['mesesSeguranca'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Aporte Mensal Disponível</label>
                                    <input type="number" step="0.01" name="aporteMensalDisponivel" class="dflg-input" style="padding-left:1rem;" value="<?= $in['aporteMensalDisponivel'] ?>">
                                </div>
                            </div>

                        <?php elseif ($slug === 'viagem'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Custo Total da Viagem</label>
                                    <input type="number" step="0.01" name="custoViagem" class="dflg-input" style="padding-left:1rem;" value="<?= $in['custoViagem'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Meses até a Viagem</label>
                                    <input type="number" name="mesesAteViagem" class="dflg-input" style="padding-left:1rem;" value="<?= $in['mesesAteViagem'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="dflg-sim-label">Taxa de Retorno Mensal (%)</label>
                                    <input type="number" step="0.01" name="taxaMensal" class="dflg-input" style="padding-left:1rem;" value="<?= $in['taxaMensal'] ?>">
                                </div>
                            </div>

                        <?php elseif ($slug === 'financiamento-imovel'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Valor do Imóvel</label>
                                    <input type="number" step="0.01" name="valorImovel" class="dflg-input" style="padding-left:1rem;" value="<?= $in['valorImovel'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Entrada</label>
                                    <input type="number" step="0.01" name="entrada" class="dflg-input" style="padding-left:1rem;" value="<?= $in['entrada'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Prazo (anos)</label>
                                    <input type="number" name="prazoAnos" class="dflg-input" style="padding-left:1rem;" value="<?= $in['prazoAnos'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Taxa de Juros Anual (%)</label>
                                    <input type="number" step="0.01" name="taxaAnual" class="dflg-input" style="padding-left:1rem;" value="<?= $in['taxaAnual'] ?>">
                                </div>
                            </div>

                        <?php elseif ($slug === 'financiamento-veiculo'): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Valor do Veículo</label>
                                    <input type="number" step="0.01" name="valorVeiculo" class="dflg-input" style="padding-left:1rem;" value="<?= $in['valorVeiculo'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Entrada</label>
                                    <input type="number" step="0.01" name="entrada" class="dflg-input" style="padding-left:1rem;" value="<?= $in['entrada'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Prazo (meses)</label>
                                    <input type="number" name="prazoMeses" class="dflg-input" style="padding-left:1rem;" value="<?= $in['prazoMeses'] ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="dflg-sim-label">Taxa de Juros Mensal (%)</label>
                                    <input type="number" step="0.01" name="taxaMensal" class="dflg-input" style="padding-left:1rem;" value="<?= $in['taxaMensal'] ?>">
                                </div>
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="dflg-btn-solid-green">
                            <i class="bi bi-calculator me-2"></i> Calcular
                        </button>
                    </form>

                    <?php // ===================== RESULTADOS POR CALCULADORA ===================== ?>

                    <?php if ($slug === 'tesouro-direto'): ?>
                        <div class="dflg-sim-result">
                            <div class="dflg-sim-result-row"><span>Total Investido</span><span class="text-white fw-medium"><?= dflg_money($r['totalInvestido']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Rendimento Bruto</span><span class="text-success fw-medium"><?= dflg_money($r['rendimentoBruto']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Imposto de Renda (<?= dflg_pct($r['aliquotaIR'], 1) ?>)</span><span class="text-danger fw-medium">- <?= dflg_money($r['valorIR']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Rendimento Líquido</span><span class="text-success fw-medium"><?= dflg_money($r['rendimentoLiquido']) ?></span></div>
                            <div class="dflg-sim-result-row dflg-sim-result-final"><span>Valor Final Líquido</span><span><?= dflg_money($r['valorFinalLiquido']) ?></span></div>
                        </div>
                        <?php if (!empty($r['evolucaoAnual'])): ?>
                            <p class="dflg-sim-label mt-4">Evolução do valor investido, ano a ano</p>
                            <table class="dflg-sim-evo-table">
                                <thead><tr><th>Ano</th><th>Valor Acumulado</th></tr></thead>
                                <tbody>
                                    <?php foreach ($r['evolucaoAnual'] as $linha): ?>
                                        <tr><td>Ano <?= $linha['ano'] ?></td><td><?= dflg_money($linha['valor']) ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                    <?php elseif ($slug === 'juros-compostos'): ?>
                        <div class="row g-4 mb-4">
                            <div class="col-12 col-md-6">
                                <div class="dflg-sim-result">
                                    <p class="dflg-sim-label mb-2">Juros Simples</p>
                                    <div class="dflg-sim-result-row"><span>Rendimento</span><span class="fw-medium"><?= dflg_money($r['jurosSimples']) ?></span></div>
                                    <div class="dflg-sim-result-row dflg-sim-result-final"><span>Valor Final</span><span><?= dflg_money($r['valorFinalSimples']) ?></span></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="dflg-sim-result">
                                    <p class="dflg-sim-label mb-2">Juros Compostos</p>
                                    <div class="dflg-sim-result-row"><span>Rendimento</span><span class="text-success fw-medium"><?= dflg_money($r['jurosComposto']) ?></span></div>
                                    <div class="dflg-sim-result-row dflg-sim-result-final"><span>Valor Final</span><span><?= dflg_money($r['valorFinalComposto']) ?></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="dflg-sim-tip">📈 A diferença entre os dois regimes, no fim do período, é de <strong><?= dflg_money($r['diferenca']) ?></strong> — esse é o efeito dos "juros sobre juros".</div>
                        <?php if (!empty($r['evolucao'])): ?>
                            <p class="dflg-sim-label mt-4">Evolução mês a mês (amostragem)</p>
                            <table class="dflg-sim-evo-table">
                                <thead><tr><th>Mês</th><th>Juros Simples</th><th>Juros Compostos</th></tr></thead>
                                <tbody>
                                    <?php foreach ($r['evolucao'] as $linha): ?>
                                        <tr><td>Mês <?= $linha['mes'] ?></td><td><?= dflg_money($linha['simples']) ?></td><td><?= dflg_money($linha['composto']) ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                    <?php elseif ($slug === 'investimentos'): ?>
                        <div class="dflg-sim-result">
                            <div class="dflg-sim-result-row"><span>Total Investido</span><span class="text-white fw-medium"><?= dflg_money($r['totalInvestido']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Rendimento</span><span class="text-success fw-medium"><?= dflg_money($r['rendimento']) ?></span></div>
                            <div class="dflg-sim-result-row dflg-sim-result-final"><span>Valor Final</span><span><?= dflg_money($r['valorFinal']) ?></span></div>
                        </div>

                    <?php elseif ($slug === 'renda-fixa'): ?>
                        <div class="dflg-sim-result">
                            <div class="dflg-sim-result-row"><span>Rendimento Bruto</span><span class="text-white fw-medium"><?= dflg_money($r['rendimentoBruto']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Imposto de Renda (<?= dflg_pct($r['aliquotaIR'], 1) ?>)</span><span class="text-danger fw-medium">- <?= dflg_money($r['valorIR']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Rendimento Líquido</span><span class="text-success fw-medium"><?= dflg_money($r['rendimentoLiquido']) ?></span></div>
                            <div class="dflg-sim-result-row dflg-sim-result-final"><span>Valor Final Líquido</span><span><?= dflg_money($r['valorFinalLiquido']) ?></span></div>
                        </div>

                    <?php elseif ($slug === 'cdb'): ?>
                        <div class="dflg-sim-result">
                            <div class="dflg-sim-result-row"><span>Taxa Anual Efetiva</span><span class="text-white fw-medium"><?= dflg_pct($r['taxaAnualEfetiva']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Rendimento Bruto</span><span class="text-white fw-medium"><?= dflg_money($r['rendimentoBruto']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Imposto de Renda (<?= dflg_pct($r['aliquotaIR'], 1) ?>)</span><span class="text-danger fw-medium">- <?= dflg_money($r['valorIR']) ?></span></div>
                            <div class="dflg-sim-result-row dflg-sim-result-final"><span>Valor Final Líquido</span><span><?= dflg_money($r['valorFinalLiquido']) ?></span></div>
                        </div>

                    <?php elseif ($slug === 'aposentadoria'): ?>
                        <div class="dflg-sim-result">
                            <div class="dflg-sim-result-row"><span>Tempo até a aposentadoria</span><span class="text-white fw-medium"><?= $r['anosAteAposentar'] ?> anos</span></div>
                            <div class="dflg-sim-result-row"><span>Patrimônio-alvo (renda perpétua)</span><span class="text-white fw-medium"><?= dflg_money($r['patrimonioAlvo']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Total que você vai aportar</span><span class="text-white fw-medium"><?= dflg_money($r['totalAportado']) ?></span></div>
                            <div class="dflg-sim-result-row dflg-sim-result-final"><span>Aporte Mensal Necessário</span><span><?= dflg_money($r['aporteMensalNecessario']) ?></span></div>
                        </div>

                    <?php elseif ($slug === 'educacao'): ?>
                        <div class="dflg-sim-result">
                            <div class="dflg-sim-result-row"><span>Total Aportado</span><span class="text-white fw-medium"><?= dflg_money($r['totalAportado']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Rendimento Estimado</span><span class="text-success fw-medium"><?= dflg_money($r['rendimentoEstimado']) ?></span></div>
                            <div class="dflg-sim-result-row dflg-sim-result-final"><span>Aporte Mensal Necessário</span><span><?= dflg_money($r['aporteMensalNecessario']) ?></span></div>
                        </div>

                    <?php elseif ($slug === 'reserva-emergencia'): ?>
                        <div class="dflg-sim-result">
                            <div class="dflg-sim-result-row dflg-sim-result-final"><span>Valor Ideal da Reserva</span><span><?= dflg_money($r['valorIdeal']) ?></span></div>
                        </div>
                        <?php if ($r['mesesParaAtingir'] !== null): ?>
                            <div class="dflg-sim-tip mt-3">🎯 Guardando o valor mensal informado, você atinge a reserva ideal em aproximadamente <strong><?= $r['mesesParaAtingir'] ?> meses</strong>.</div>
                        <?php endif; ?>

                    <?php elseif ($slug === 'viagem'): ?>
                        <div class="dflg-sim-result">
                            <div class="dflg-sim-result-row"><span>Total Aportado</span><span class="text-white fw-medium"><?= dflg_money($r['totalAportado']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Rendimento Estimado</span><span class="text-success fw-medium"><?= dflg_money($r['rendimentoEstimado']) ?></span></div>
                            <div class="dflg-sim-result-row dflg-sim-result-final"><span>Aporte Mensal Necessário</span><span><?= dflg_money($r['aporteMensalNecessario']) ?></span></div>
                        </div>

                    <?php elseif ($slug === 'financiamento-imovel'): ?>
                        <div class="row g-4 mb-3">
                            <div class="col-12 col-md-6">
                                <div class="dflg-sim-result">
                                    <p class="dflg-sim-label mb-2">Sistema Price (parcela fixa)</p>
                                    <div class="dflg-sim-result-row"><span>Parcela Mensal</span><span class="fw-medium"><?= dflg_money($r['price']['parcela']) ?></span></div>
                                    <div class="dflg-sim-result-row"><span>Total de Juros</span><span class="text-danger fw-medium"><?= dflg_money($r['price']['totalJuros']) ?></span></div>
                                    <div class="dflg-sim-result-row dflg-sim-result-final"><span>Total Pago</span><span><?= dflg_money($r['price']['totalPago']) ?></span></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="dflg-sim-result">
                                    <p class="dflg-sim-label mb-2">Sistema SAC (parcela decrescente)</p>
                                    <div class="dflg-sim-result-row"><span>1ª Parcela</span><span class="fw-medium"><?= dflg_money($r['sac']['primeiraParcela']) ?></span></div>
                                    <div class="dflg-sim-result-row"><span>Última Parcela</span><span class="fw-medium"><?= dflg_money($r['sac']['ultimaParcela']) ?></span></div>
                                    <div class="dflg-sim-result-row dflg-sim-result-final"><span>Total Pago</span><span><?= dflg_money($r['sac']['totalPago']) ?></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="dflg-sim-tip">🏠 Financiando <?= dflg_money($r['valorFinanciado']) ?> em <?= $r['parcelas'] ?> parcelas: o SAC custa <?= dflg_money(abs($r['price']['totalPago'] - $r['sac']['totalPago'])) ?> <?= $r['sac']['totalPago'] < $r['price']['totalPago'] ? 'a menos' : 'a mais' ?> que o Price no total, mas começa com parcelas mais altas.</div>

                    <?php elseif ($slug === 'financiamento-veiculo'): ?>
                        <div class="dflg-sim-result">
                            <div class="dflg-sim-result-row"><span>Valor Financiado</span><span class="text-white fw-medium"><?= dflg_money($r['valorFinanciado']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Total de Juros</span><span class="text-danger fw-medium"><?= dflg_money($r['totalJuros']) ?></span></div>
                            <div class="dflg-sim-result-row"><span>Total Pago</span><span class="text-white fw-medium"><?= dflg_money($r['totalPago']) ?></span></div>
                            <div class="dflg-sim-result-row dflg-sim-result-final"><span>Parcela Mensal (<?= $r['parcelas'] ?>x)</span><span><?= dflg_money($r['parcela']) ?></span></div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Saiba mais: passo a passo, fórmula, aplicações e exemplo (compacto, fechado por padrão) -->
                <div class="accordion dflg-sim-accordion mt-4" id="acc-<?= htmlspecialchars($slug) ?>">

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc-passos-<?= htmlspecialchars($slug) ?>">
                                <i class="bi bi-list-check text-success me-2"></i> Passo a passo de como usar
                            </button>
                        </h2>
                        <div id="acc-passos-<?= htmlspecialchars($slug) ?>" class="accordion-collapse collapse" data-bs-parent="#acc-<?= htmlspecialchars($slug) ?>">
                            <div class="accordion-body">
                                <ol class="dflg-sim-steps">
                                    <?php foreach ($info['passos'] as $passo): ?>
                                        <li><?= htmlspecialchars($passo) ?></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc-formula-<?= htmlspecialchars($slug) ?>">
                                <i class="bi bi-calculator text-success me-2"></i> Como o cálculo é feito
                            </button>
                        </h2>
                        <div id="acc-formula-<?= htmlspecialchars($slug) ?>" class="accordion-collapse collapse" data-bs-parent="#acc-<?= htmlspecialchars($slug) ?>">
                            <div class="accordion-body">
                                <div class="dflg-sim-formula"><?= htmlspecialchars($info['formula']['expressao']) ?></div>
                                <ul class="dflg-sim-legenda">
                                    <?php foreach ($info['formula']['legenda'] as $termo => $significado): ?>
                                        <li><strong><?= htmlspecialchars($termo) ?></strong> — <?= htmlspecialchars($significado) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc-aplicacoes-<?= htmlspecialchars($slug) ?>">
                                <i class="bi bi-lightbulb text-success me-2"></i> Para que serve na prática
                            </button>
                        </h2>
                        <div id="acc-aplicacoes-<?= htmlspecialchars($slug) ?>" class="accordion-collapse collapse" data-bs-parent="#acc-<?= htmlspecialchars($slug) ?>">
                            <div class="accordion-body">
                                <ul class="dflg-sim-aplicacoes">
                                    <?php foreach ($info['aplicacoes'] as $aplicacao): ?>
                                        <li><i class="bi bi-check2"></i> <?= htmlspecialchars($aplicacao) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc-exemplo-<?= htmlspecialchars($slug) ?>">
                                <i class="bi bi-journal-text text-success me-2"></i> Exemplo prático
                            </button>
                        </h2>
                        <div id="acc-exemplo-<?= htmlspecialchars($slug) ?>" class="accordion-collapse collapse" data-bs-parent="#acc-<?= htmlspecialchars($slug) ?>">
                            <div class="accordion-body">
                                🧮 <?= htmlspecialchars($info['exemplo']) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ===================== Sidebar: outras calculadoras da categoria ===================== -->
            <div class="col-12 col-lg-4">
                <div class="dflg-panel dflg-sim-sidebar">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-collection text-success"></i>
                        <h2 class="mb-0" style="font-size:1.05rem;">Veja também</h2>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($relacionadas as $slugRel => $cRel): ?>
                            <a href="<?= URL_BASE ?>/calculadoras/<?= urlencode($slugRel) ?>" class="dflg-sim-link">
                                <i class="bi <?= $cRel['icone'] ?>"></i>
                                <span><?= htmlspecialchars($cRel['nome']) ?></span>
                            </a>
                        <?php endforeach; ?>
                        <a href="<?= URL_BASE ?>/calculadoras" class="dflg-sim-link mt-2">
                            <i class="bi bi-grid"></i>
                            <span>Ver todas as calculadoras</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
