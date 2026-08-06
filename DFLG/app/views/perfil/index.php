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

                    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-3 mb-4 text-center text-sm-start">
                        <?php if ($usuario->getFoto()): ?>
                            <img src="<?= URL_BASE ?>/assets/img/perfil/<?= htmlspecialchars($usuario->getFoto()) ?>" alt="Foto de perfil" class="dflg-profile-avatar">
                        <?php else: ?>
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?= urlencode($usuario->getNomeUsuario()) ?>" alt="Avatar" class="dflg-profile-avatar">
                        <?php endif; ?>
                        <div>
                            <h3 class="dflg-profile-name mb-1"><?= htmlspecialchars(trim($usuario->getNomeUsuario() . ' ' . ($usuario->getSobrenome() ?? ''))) ?></h3>
                            <p class="text-dflg-muted mb-2">@<?= htmlspecialchars($usuario->getNickname() ?: '—') ?> · Membro desde <?= dflg_membro_desde($usuario->getCriadoEm(), $mesesPt) ?></p>
                            <span class="dflg-level-badge">
                                <i class="bi bi-award"></i> Nível <?= $nivel['nivel'] ?>
                            </span>
                        </div>
                    </div>

                    <div class="dflg-info-list">
                        <div class="dflg-info-row">
                            <span class="dflg-info-icon"><i class="bi bi-envelope"></i></span>
                            <div>
                                <p class="label">Email</p>
                                <p class="value"><?= htmlspecialchars($usuario->getEmail()) ?></p>
                            </div>
                            <span class="dflg-locked-badge" title="Não pode ser alterado por aqui"><i class="bi bi-lock-fill"></i> fixo</span>
                        </div>
                        <div class="dflg-info-row">
                            <span class="dflg-info-icon"><i class="bi bi-card-text"></i></span>
                            <div>
                                <p class="label">CPF/CNPJ</p>
                                <p class="value"><?= htmlspecialchars($usuario->getCpfCnpj() ?: 'Não informado') ?></p>
                            </div>
                            <span class="dflg-locked-badge" title="Não pode ser alterado por aqui"><i class="bi bi-lock-fill"></i> fixo</span>
                        </div>
                        <div class="dflg-info-row">
                            <span class="dflg-info-icon"><i class="bi bi-telephone"></i></span>
                            <div>
                                <p class="label">Telefone</p>
                                <p class="value"><?= htmlspecialchars($usuario->getTelefone() ?: 'Não informado') ?></p>
                            </div>
                        </div>
                        <div class="dflg-info-row">
                            <span class="dflg-info-icon"><i class="bi bi-geo-alt"></i></span>
                            <div>
                                <p class="label">Localização</p>
                                <p class="value"><?= htmlspecialchars($usuario->getLocalizacao() ?: 'Não informado') ?></p>
                            </div>
                        </div>
                        <div class="dflg-info-row">
                            <span class="dflg-info-icon"><i class="bi bi-cake2"></i></span>
                            <div>
                                <p class="label">Data de nascimento</p>
                                <p class="value"><?= $usuario->getDataNascimento() ? (new \DateTime($usuario->getDataNascimento()))->format('d/m/Y') : 'Não informado' ?></p>
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

                    <div class="dflg-ranking-points">
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
                <div class="dflg-panel mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-fire" style="color: var(--dflg-orange-500); font-size: 1.2rem;"></i>
                        <h2 class="mb-0" style="font-size: 1.1rem;">Streak de Acesso</h2>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="dflg-streak-badge-sm"><i class="bi bi-fire"></i> <?= $usuario->getSequenciaAtual() ?></span>
                        <span class="text-dflg-muted small">dias seguidos</span>
                    </div>

                    <div class="dflg-streak-row">
                        <span class="text-dflg-muted small">Maior sequência</span>
                        <span class="d-flex align-items-center gap-2 text-white fw-semibold small">
                            <i class="bi bi-trophy" style="color: #eab308;"></i> <?= $usuario->getMaiorSequencia() ?> dias
                        </span>
                    </div>
                    <div class="dflg-streak-row">
                        <span class="text-dflg-muted small">Último acesso</span>
                        <span class="d-flex align-items-center gap-2 text-white fw-medium small">
                            <i class="bi bi-calendar3 text-success"></i>
                            <?= $usuario->getUltimoAcesso() ? (new \DateTime($usuario->getUltimoAcesso()))->format('d/m/Y') : '—' ?>
                        </span>
                    </div>

                    <div class="dflg-streak-tip mt-3">
                        <i class="bi bi-fire"></i>
                        <span>Continue acessando diariamente para manter sua sequência ativa e ganhar mais pontos!</span>
                    </div>
                </div>

                <!-- Sair da conta -->
                <div class="dflg-logout-card mt-4">
                    <div class="d-flex align-items-center gap-3">
                        <span class="dflg-logout-icon"><i class="bi bi-box-arrow-right"></i></span>
                        <div>
                            <p class="mb-0 text-white fw-medium">Sair da conta</p>
                            <p class="mb-0 text-dflg-muted small">Você precisará entrar novamente</p>
                        </div>
                    </div>
                    <a href="<?= URL_BASE ?>/logout" class="dflg-logout-btn" onclick="return confirm('Tem certeza que deseja sair da sua conta?')">
                        Sair <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- ===================== Modal Editar Perfil ===================== -->
    <div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dflg-modal">
                <form method="post" action="<?= URL_BASE ?>/perfil" enctype="multipart/form-data">
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

                        <!-- Foto de perfil -->
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <img id="previewFotoPerfil"
                                 src="<?= $usuario->getFoto() ? URL_BASE . '/assets/img/perfil/' . htmlspecialchars($usuario->getFoto()) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($usuario->getNomeUsuario()) ?>"
                                 alt="Foto de perfil" class="dflg-profile-avatar-sm">
                            <div>
                                <label for="inputFotoPerfil" class="dflg-btn-outline-green dflg-btn-sm" style="cursor:pointer;">
                                    <i class="bi bi-upload me-1"></i> Trocar foto
                                </label>
                                <input type="file" name="foto" id="inputFotoPerfil" accept="image/png, image/jpeg, image/webp" class="d-none" onchange="dflgPreviewFoto(event)">
                                <p class="text-dflg-muted small mb-0 mt-1">JPG, PNG ou WEBP até 3MB</p>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <label class="dflg-auth-label">Primeiro nome</label>
                                <input type="text" name="nome" class="dflg-input" style="padding-left:1rem;" value="<?= htmlspecialchars($usuario->getNomeUsuario()) ?>">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="dflg-auth-label">Sobrenome</label>
                                <input type="text" name="sobrenome" class="dflg-input" style="padding-left:1rem;" value="<?= htmlspecialchars($usuario->getSobrenome() ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="dflg-auth-label">Nickname</label>
                            <input type="text" name="nickname" class="dflg-input" style="padding-left:1rem;" minlength="3" maxlength="20" pattern="[a-zA-Z0-9._]{3,20}" value="<?= htmlspecialchars($usuario->getNickname() ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="dflg-auth-label">Data de nascimento</label>
                            <input type="date" name="dataNascimento" class="dflg-input" style="padding-left:1rem;" value="<?= htmlspecialchars($usuario->getDataNascimento() ?? '') ?>">
                        </div>

                        <div class="dflg-info-note mb-3">
                            <i class="bi bi-lock-fill"></i>
                            E-mail e CPF/CNPJ não podem ser alterados por aqui, assim como a senha.
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
    <script>
        function dflgPreviewFoto(event) {
            var arquivo = event.target.files[0];
            if (!arquivo) return;
            document.getElementById('previewFotoPerfil').src = URL.createObjectURL(arquivo);
        }
    </script>
    <?php if (!empty($erros)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('modalEditarPerfil')).show();
            });
        </script>
    <?php endif; ?>
</body>

</html>
