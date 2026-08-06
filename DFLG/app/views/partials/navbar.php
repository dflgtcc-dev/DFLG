<?php
/**
 * Navbar reaproveitável para as telas internas.
 *
 * Variável esperada (opcional):
 *   $activePage  -> 'overview' | 'calculators' | 'transactions' | 'installments' | 'categories' | 'goals'
 */
$activePage = $activePage ?? '';

function dflg_nav_class(string $page, string $active): string
{
    return 'dflg-nav-link' . ($page === $active ? ' active' : '');
}
?>
<nav class="dflg-navbar">
    <div class="container-xxl px-4">
        <div class="d-flex align-items-center justify-content-between" style="height: 80px;">

            <a href="<?= URL_BASE ?>/" class="navbar-brand-wrap">
                <img src="<?= URL_BASE ?>/assets/img/dflg-logo.jpg" alt="DFLG" class="navbar-logo">
                <div>
                    <span class="brand-title">DFLG</span>
                    <span class="brand-subtitle">Investiments</span>
                </div>
            </a>

            <div class="d-none d-md-flex align-items-center gap-2">
                <a href="<?= URL_BASE ?>/dashboard" class="<?= dflg_nav_class('overview', $activePage) ?>">Visão geral</a>
                <a href="<?= URL_BASE ?>/calculadoras" class="<?= dflg_nav_class('calculators', $activePage) ?>">Calculadoras</a>
                <a href="<?= URL_BASE ?>/transacoes" class="<?= dflg_nav_class('transactions', $activePage) ?>">Transações</a>
                <a href="<?= URL_BASE ?>/parcelamentos" class="<?= dflg_nav_class('installments', $activePage) ?>">Parcelamentos</a>
                <a href="<?= URL_BASE ?>/metas" class="<?= dflg_nav_class('goals', $activePage) ?>">Metas</a>
                <a href="<?= URL_BASE ?>/categorias" class="<?= dflg_nav_class('categories', $activePage) ?>">Categorias</a>

                <div class="dropdown ms-3">
                    <button type="button" class="dflg-nav-profile <?= $activePage === 'profile' ? 'is-active' : '' ?>" data-bs-toggle="dropdown" aria-expanded="false" title="Conta">
                        <?php if (!empty($_SESSION['usuario_logado']) && $_SESSION['usuario_logado']->getFoto()): ?>
                            <img src="<?= URL_BASE ?>/assets/img/perfil/<?= htmlspecialchars($_SESSION['usuario_logado']->getFoto()) ?>" alt="Foto de perfil" class="dflg-nav-profile-img">
                        <?php else: ?>
                            <i class="bi bi-person-fill"></i>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dflg-nav-dropdown">
                        <li><a class="dropdown-item" href="<?= URL_BASE ?>/perfil"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item dflg-nav-dropdown-danger" href="<?= URL_BASE ?>/logout" onclick="return confirm('Tem certeza que deseja sair da sua conta?')"><i class="bi bi-box-arrow-right me-2"></i>Sair da conta</a></li>
                    </ul>
                </div>
            </div>

            <div class="d-flex d-md-none align-items-center gap-3">
                <div class="dropdown">
                    <button type="button" class="dflg-nav-profile" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($_SESSION['usuario_logado']) && $_SESSION['usuario_logado']->getFoto()): ?>
                            <img src="<?= URL_BASE ?>/assets/img/perfil/<?= htmlspecialchars($_SESSION['usuario_logado']->getFoto()) ?>" alt="Foto de perfil" class="dflg-nav-profile-img">
                        <?php else: ?>
                            <i class="bi bi-person-fill"></i>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dflg-nav-dropdown">
                        <li><a class="dropdown-item" href="<?= URL_BASE ?>/perfil"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item dflg-nav-dropdown-danger" href="<?= URL_BASE ?>/logout" onclick="return confirm('Tem certeza que deseja sair da sua conta?')"><i class="bi bi-box-arrow-right me-2"></i>Sair da conta</a></li>
                    </ul>
                </div>
                <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="collapse" data-bs-target="#dflgMobileMenu">
                    <i class="bi bi-list" style="font-size: 1.75rem;"></i>
                </button>
            </div>
        </div>

        <div class="collapse d-md-none" id="dflgMobileMenu">
            <div class="d-flex flex-column gap-3 py-3 border-top border-secondary-subtle">
                <a href="<?= URL_BASE ?>/dashboard" class="<?= dflg_nav_class('overview', $activePage) ?>">Visão geral</a>
                <a href="<?= URL_BASE ?>/calculadoras" class="<?= dflg_nav_class('calculators', $activePage) ?>">Calculadoras</a>
                <a href="<?= URL_BASE ?>/transacoes" class="<?= dflg_nav_class('transactions', $activePage) ?>">Transações</a>
                <a href="<?= URL_BASE ?>/parcelamentos" class="<?= dflg_nav_class('installments', $activePage) ?>">Parcelamentos</a>
                <a href="<?= URL_BASE ?>/metas" class="<?= dflg_nav_class('goals', $activePage) ?>">Metas</a>
                <a href="<?= URL_BASE ?>/categorias" class="<?= dflg_nav_class('categories', $activePage) ?>">Categorias</a>
            </div>
        </div>
    </div>
</nav>
