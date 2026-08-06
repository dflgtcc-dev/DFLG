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
        <p class="dflg-page-subtitle mb-5">Calculadoras financeiras diversificadas para te dar uma noção prática de investimentos, metas e financiamentos. Escolha uma abaixo.</p>

        <?php foreach ($porCategoria as $categoriaNome => $itens): ?>
            <div class="d-flex align-items-center gap-2 mb-3 mt-2">
                <span class="dflg-sim-category-dot"></span>
                <h2 class="dflg-sim-hub-category"><?= htmlspecialchars($categoriaNome) ?></h2>
            </div>

            <div class="row g-4 mb-5">
                <?php foreach ($itens as $slug => $c): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="<?= URL_BASE ?>/calculadoras/<?= urlencode($slug) ?>" class="dflg-sim-hub-card">
                            <span class="dflg-card-icon"><i class="bi <?= $c['icone'] ?>"></i></span>
                            <h3><?= htmlspecialchars($c['nome']) ?></h3>
                            <p><?= htmlspecialchars($c['resumo']) ?></p>
                            <span class="dflg-sim-hub-card-cta">Abrir calculadora <i class="bi bi-arrow-right"></i></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
