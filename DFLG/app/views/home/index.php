<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DFLG Investiments • Dream. Fund. Learn. Grow.</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/dflg.css" rel="stylesheet">
</head>

<body class="dflg-app">

    <!-- ===================== Navbar pública (leve) ===================== -->
    <nav class="dflg-home-nav">
        <div class="container-xxl px-4">
            <div class="d-flex align-items-center justify-content-between" style="height: 80px;">
                <a href="<?= URL_BASE ?>/" class="navbar-brand-wrap">
                    <img src="<?= URL_BASE ?>/assets/img/dflg-logo.jpg" alt="DFLG" class="navbar-logo">
                    <div>
                        <span class="brand-title">DFLG</span>
                        <span class="brand-subtitle">Investiments</span>
                    </div>
                </a>

                <div class="d-flex align-items-center gap-3">
                    <a href="<?= URL_BASE ?>/login" class="dflg-btn-ghost-green">Entrar</a>
                    <a href="<?= URL_BASE ?>/login?aba=cadastro" class="dflg-btn-solid-green">Cadastrar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===================== Hero ===================== -->
    <section class="dflg-home-hero">
        <div class="dflg-home-hero-glow"></div>
        <div class="dflg-auth-bg-grid"></div>

        <div class="container-xxl px-4 position-relative">
            <div class="text-center mx-auto" style="max-width: 900px;">
                <div class="dflg-home-badge">
                    <span class="pulse"></span>
                    Dream. Fund. Learn. Grow.
                </div>

                <h1 class="dflg-home-title">
                    Transforme sonhos<br>
                    em <span class="dflg-home-title-grad">realidade financeira</span>
                </h1>

                <p class="dflg-home-subtitle">
                    Tecnologia avançada para controlar receitas, despesas e investimentos.
                    Tome decisões inteligentes baseadas em dados.
                </p>

                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="<?= URL_BASE ?>/login" class="dflg-home-cta-primary">
                        Começar Agora <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="<?= URL_BASE ?>/dashboard" class="dflg-home-cta-secondary">
                        Demonstração
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== Recursos ===================== -->
    <section class="dflg-section">
        <div class="container-xxl px-4">
            <div class="text-center mb-5">
                <div class="dflg-home-eyebrow-pill">Recursos</div>
                <h2 class="dflg-home-h2">Tudo que você precisa</h2>
                <p class="dflg-home-lead">Ferramentas profissionais para gestão financeira completa</p>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="dflg-home-feature">
                        <span class="dflg-home-feature-icon"><i class="bi bi-currency-dollar"></i></span>
                        <h3>Controle de Receitas e Despesas</h3>
                        <p>Registre todas as suas entradas e saídas de dinheiro de forma simples e organizada.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="dflg-home-feature">
                        <span class="dflg-home-feature-icon"><i class="bi bi-bullseye"></i></span>
                        <h3>Acompanhe suas Metas</h3>
                        <p>Defina objetivos financeiros e monitore seu progresso em tempo real.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="dflg-home-feature">
                        <span class="dflg-home-feature-icon"><i class="bi bi-bar-chart-line"></i></span>
                        <h3>Visualize seus Dados</h3>
                        <p>Gráficos intuitivos para entender para onde seu dinheiro está indo.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="dflg-home-feature">
                        <span class="dflg-home-feature-icon"><i class="bi bi-piggy-bank"></i></span>
                        <h3>Economize Mais</h3>
                        <p>Veja quanto conseguiu guardar mensalmente e desenvolva hábitos financeiros saudáveis.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="dflg-home-feature">
                        <span class="dflg-home-feature-icon"><i class="bi bi-calculator"></i></span>
                        <h3>Simulador de Investimentos</h3>
                        <p>Simule diferentes cenários de investimento e planeje seu futuro financeiro.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="dflg-home-feature">
                        <span class="dflg-home-feature-icon"><i class="bi bi-graph-up-arrow"></i></span>
                        <h3>Tome Decisões Inteligentes</h3>
                        <p>Dados claros e organizados para decisões financeiras mais assertivas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== Como funciona ===================== -->
    <section class="dflg-section">
        <div class="container-xxl px-4">
            <div class="text-center mb-5">
                <h2 class="dflg-home-h2">Como funciona?</h2>
                <p class="dflg-home-lead">Em 3 passos simples você está no controle total das suas finanças</p>
            </div>

            <div class="row g-4 text-center">
                <div class="col-12 col-md-4">
                    <div class="dflg-home-step-number">1</div>
                    <h3 class="dflg-home-step-title">Cadastre suas Transações</h3>
                    <p class="dflg-home-step-desc">Adicione suas receitas e despesas mensais de forma rápida e intuitiva.</p>
                </div>
                <div class="col-12 col-md-4">
                    <div class="dflg-home-step-number">2</div>
                    <h3 class="dflg-home-step-title">Defina suas Metas</h3>
                    <p class="dflg-home-step-desc">Estabeleça objetivos financeiros e acompanhe o progresso até alcançá-los.</p>
                </div>
                <div class="col-12 col-md-4">
                    <div class="dflg-home-step-number">3</div>
                    <h3 class="dflg-home-step-title">Simule e Planeje</h3>
                    <p class="dflg-home-step-desc">Use nosso simulador para planejar investimentos e tomar decisões informadas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== CTA final ===================== -->
    <section class="dflg-section">
        <div class="container-xxl px-4">
            <div class="dflg-home-cta-box mx-auto" style="max-width: 960px;">
                <div class="text-center position-relative">
                    <h2 class="dflg-home-h2">Pronto para transformar suas finanças?</h2>
                    <p class="dflg-home-lead mb-5">Junte-se a milhares de usuários que já tomam decisões financeiras mais inteligentes</p>

                    <div class="row g-3 text-start mb-5">
                        <div class="col-12 col-md-6">
                            <div class="dflg-home-check">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <h4>Visão Clara das Finanças</h4>
                                    <p>Entenda exatamente para onde seu dinheiro está indo</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="dflg-home-check">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <h4>Economia Garantida</h4>
                                    <p>Identifique gastos desnecessários e economize mais</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="dflg-home-check">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <h4>Metas Alcançadas</h4>
                                    <p>Mantenha o foco e conquiste seus objetivos financeiros</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="dflg-home-check">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <h4>Decisões Informadas</h4>
                                    <p>Simule cenários antes de investir seu dinheiro</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="<?= URL_BASE ?>/login" class="dflg-home-cta-primary">
                        Começar Agora Gratuitamente <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== Footer ===================== -->
    <footer class="dflg-home-footer">
        <div class="container-xxl px-4">
            <p>© 2026 DFLG Investiments. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
