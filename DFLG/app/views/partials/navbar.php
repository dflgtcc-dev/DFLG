<?php
/**
 * Navbar reaproveitável para as telas internas.
 *
 * Variável esperada (opcional):
 *   $activePage  -> 'overview' | 'calculators' | 'transactions' | 'installments' | 'categories'
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
                <a href="<?= URL_BASE ?>/categorias" class="<?= dflg_nav_class('categories', $activePage) ?>">Categorias</a>
                <a href="<?= URL_BASE ?>/perfil" class="dflg-nav-profile ms-3 <?= $activePage === 'profile' ? 'is-active' : '' ?>" title="Perfil">
                    <i class="bi bi-person-fill"></i>
                </a>
            </div>

            <div class="d-flex d-md-none align-items-center gap-3">
                <a href="<?= URL_BASE ?>/perfil" class="dflg-nav-profile"><i class="bi bi-person-fill"></i></a>
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
                <a href="<?= URL_BASE ?>/categorias" class="<?= dflg_nav_class('categories', $activePage) ?>">Categorias</a>
            </div>
        </div>
    </div>
</nav>
