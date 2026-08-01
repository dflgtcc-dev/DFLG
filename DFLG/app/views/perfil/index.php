<?php
$mesesPt = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];

function dflg_membro_desde(?string $criadoEm, array $mesesPt): string
{
    if (!$criadoEm) {
        return '—';
    }
    $dt = new \DateTime($criadoEm);
    return $mesesPt[(int) $dt->format('n')] . ' ' . $dt->format('Y');
}

function dflg_data_atividade(string $data): string
{
    $dt = \DateTime::createFromFormat('Y-m-d', $data);
    return $dt ? $dt->format('d/m/Y') : $data;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil • DFLG Investiments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/dflg.css" rel="stylesheet">
</head>

<body class="dflg-app">

    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container-xxl px-4 py-5">

        <div class="dflg-page-title">
            <span class="bar"></span>
            <h1>Meu Perfil</h1>
        </div>
        <p class="dflg-page-subtitle mb-4">Gerencie suas informações e acompanhe seu progresso</p>

        <div class="row g-4">
            <!-- ===================== Coluna principal ===================== -->
            <div class="col-12 col-lg-8">

                <!-- Informações pessoais -->
                <div class="dflg-panel mb-4">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <h2 class="mb-0">Informações Pessoais</h2>
                        <button type="button" class="dflg-icon-btn" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>

                    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4 mb-4 text-center text-sm-start">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?= urlencode($usuario->getNomeUsuario()) ?>" alt="Avatar" class="dflg-profile-avatar">
                        <div>
                            <h3 class="dflg-profile-name"><?= htmlspecialchars($usuario->getNomeUsuario()) ?></h3>
                            <p class="text-dflg-muted mb-3">Membro desde <?= dflg_membro_desde($usuario->getCriadoEm(), $mesesPt) ?></p>
                            <span class="dflg-level-badge">
                                <i class="bi bi-award"></i> Nível <?= $nivel['nivel'] ?>
                            </span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="dflg-info-item">
                                <i class="bi bi-envelope"></i>
                                <div>
                                    <p class="label">Email</p>
                                    <p class="value"><?= htmlspecialchars($usuario->getEmail()) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="dflg-info-item">
                                <i class="bi bi-telephone"></i>
                                <div>
                                    <p class="label">Telefone</p>
                                    <p class="value"><?= htmlspecialchars($usuario->getTelefone() ?: 'Não informado') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="dflg-info-item">
                                <i class="bi bi-geo-alt"></i>
                                <div>
                                    <p class="label">Localização</p>
                                    <p class="value"><?= htmlspecialchars($usuario->getLocalizacao() ?: 'Não informado') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="dflg-info-item">
                                <i class="bi bi-calendar3"></i>
                                <div>
                                    <p class="label">Membro desde</p>
                                    <p class="value"><?= dflg_membro_desde($usuario->getCriadoEm(), $mesesPt) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Atividades recentes -->
                <div class="dflg-panel">
                    <h2 class="mb-4">Atividades Recentes</h2>

                    <?php if (empty($atividades)): ?>
                        <div class="text-center py-4 text-dflg-muted">
                            <i class="bi bi-clock-history d-block mb-2" style="font-size: 2rem; opacity: .3;"></i>
                            <p class="mb-0">Nenhuma atividade registrada ainda. Cadastre sua primeira transação para começar a ganhar pontos!</p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($atividades as $a): ?>
                                <div class="dflg-activity-item">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="dflg-activity-icon"><i class="bi bi-graph-up-arrow"></i></span>
                                        <div>
                                            <p class="mb-0 text-white"><?= htmlspecialchars($a['acao']) ?></p>
                                            <p class="mb-0 text-dflg-muted small"><?= dflg_data_atividade($a['data']) ?></p>
                                        </div>
                                    </div>
                                    <span class="text-success fw-semibold">+<?= $a['pontos'] ?> pts</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ===================== Coluna lateral ===================== -->
            <div class="col-12 col-lg-4">

                <!-- Ranking -->
                <div class="dflg-ranking-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="dflg-ranking-icon"><i class="bi bi-trophy-fill"></i></span>
                        <div>
                            <h2 class="mb-0" style="font-size: 1.15rem;">Ranking</h2>
                            <p class="text-dflg-muted small mb-0">Sua posição</p>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <div class="dflg-ranking-position">
                            <i class="bi bi-award-fill"></i>
                            <span>#<?= $posicaoRanking ?></span>
                        </div>
                        <p class="text-dflg-muted mb-0 mt-2">no ranking geral</p>
                    </div>

                    <div class="dflg-ranking-points mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-dflg-muted small">Pontos Totais</span>
                            <span class="text-success fs-5 fw-semibold"><?= number_format($usuario->getPontosTotais(), 0, ',', '.') ?></span>
                        </div>
                        <div class="dflg-progress">
                            <div class="dflg-progress-bar" style="width: <?= $nivel['progresso'] ?>%"></div>
                        </div>
                        <p class="text-dflg-muted small mt-2 mb-0">
                            Faltam <?= number_format($nivel['pontosFaltantes'], 0, ',', '.') ?> pontos para o nível <?= $nivel['nivel'] + 1 ?>
                        </p>
                    </div>
                </div>

                <!-- Streak de acesso -->
                <div class="dflg-panel">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-fire" style="color: var(--dflg-orange-500); font-size: 1.3rem;"></i>
                        <h2 class="mb-0" style="font-size: 1.15rem;">Streak de Acesso</h2>
                    </div>

                    <div class="text-center mb-4">
                        <div class="dflg-streak-badge">
                            <i class="bi bi-fire"></i>
                            <span class="num"><?= $usuario->getSequenciaAtual() ?></span>
                            <span class="unit">dias</span>
                        </div>
                        <p class="text-dflg-muted small mt-3 mb-0">Sequência atual</p>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="dflg-streak-row">
                            <span class="text-dflg-muted small">Maior sequência</span>
                            <span class="d-flex align-items-center gap-2 text-white fw-semibold">
                                <i class="bi bi-trophy" style="color: #eab308;"></i> <?= $usuario->getMaiorSequencia() ?> dias
                            </span>
                        </div>
                        <div class="dflg-streak-row">
                            <span class="text-dflg-muted small">Último acesso</span>
                            <span class="d-flex align-items-center gap-2 text-white fw-medium">
                                <i class="bi bi-calendar3 text-success"></i>
                                <?= $usuario->getUltimoAcesso() ? (new \DateTime($usuario->getUltimoAcesso()))->format('d/m/Y') : '—' ?>
                            </span>
                        </div>
                    </div>

                    <div class="dflg-streak-tip mt-4">
                        <i class="bi bi-fire"></i>
                        <span>Continue acessando diariamente para manter sua sequência ativa e ganhar mais pontos!</span>
                    </div>
                </div>

                <!-- Logout -->
                <div class="mt-4">
                    <a href="<?= URL_BASE ?>/logout" class="dflg-profile-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Sair da conta</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- ===================== Modal Editar Perfil ===================== -->
    <div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dflg-modal">
                <form method="post" action="<?= URL_BASE ?>/perfil">
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title">Editar Perfil</h2>
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
                            <label class="dflg-auth-label">Nome completo</label>
                            <input type="text" name="nome" class="dflg-input" style="padding-left:1rem;" value="<?= htmlspecialchars($usuario->getNomeUsuario()) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="dflg-auth-label">Telefone</label>
                            <input type="text" name="telefone" class="dflg-input" style="padding-left:1rem;" placeholder="(11) 98765-4321" value="<?= htmlspecialchars($usuario->getTelefone() ?? '') ?>">
                        </div>
                        <div class="mb-1">
                            <label class="dflg-auth-label">Localização</label>
                            <input type="text" name="localizacao" class="dflg-input" style="padding-left:1rem;" placeholder="São Paulo, SP" value="<?= htmlspecialchars($usuario->getLocalizacao() ?? '') ?>">
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

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (!empty($erros)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('modalEditarPerfil')).show();
            });
        </script>
    <?php endif; ?>
</body>

</html>
