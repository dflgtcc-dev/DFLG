<?php
$abaInicial = $abaInicial ?? 'login';
$erros = $erros ?? [];
$errosCadastro = $errosCadastro ?? [];
$emailAntigo = $emailAntigo ?? '';
$nomeAntigo = $nomeAntigo ?? '';
$emailAntigoCadastro = $emailAntigoCadastro ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar • DFLG Investiments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/dflg.css" rel="stylesheet">
</head>

<body class="dflg-app">

    <div class="dflg-auth-page">
        <div class="dflg-auth-bg-grid"></div>
        <div class="dflg-auth-orb dflg-auth-orb-tl"></div>
        <div class="dflg-auth-orb dflg-auth-orb-br"></div>
        <div class="dflg-auth-divider-line d-none d-lg-block"></div>

        <a href="<?= URL_BASE ?>/" class="dflg-explore-btn">
            <i class="bi bi-house"></i>
            Voltar para a página inicial
        </a>

        <!-- ===================== Coluna esquerda: branding ===================== -->
        <div class="dflg-auth-left d-none d-lg-flex">
            <div class="dflg-auth-brand">
                <div class="dflg-auth-logo-wrap">
                    <img src="<?= URL_BASE ?>/assets/img/dflg-logo.jpg" alt="DFLG" class="dflg-auth-logo">
                </div>
                <div>
                    <h1>DFLG</h1>
                    <p>Investiments</p>
                </div>
            </div>

            <div class="mb-5">
                <h2 class="dflg-auth-slogan">
                    Dream.<br>
                    <span class="text-green">Fund.</span><br>
                    Learn.<br>
                    <span class="dflg-auth-slogan-grow">Grow.</span>
                </h2>
                <p class="dflg-auth-slogan-desc">
                    Transforme seus sonhos em realidade. Financie suas metas, aprenda a investir e
                    cresça financeiramente com inteligência.
                </p>
            </div>

            <div class="dflg-auth-features">
                <div class="dflg-auth-feature"><span class="dot"></span>Controle financeiro completo</div>
                <div class="dflg-auth-feature"><span class="dot"></span>Simuladores inteligentes</div>
                <div class="dflg-auth-feature"><span class="dot"></span>Sistema de gamificação</div>
            </div>
        </div>

        <!-- ===================== Coluna direita: formulário ===================== -->
        <div class="dflg-auth-right">

            <div class="dflg-auth-mobile-logo d-flex d-lg-none">
                <img src="<?= URL_BASE ?>/assets/img/dflg-logo.jpg" alt="DFLG" class="dflg-auth-logo dflg-auth-logo-sm">
                <div>
                    <h1>DFLG</h1>
                    <p>Investiments</p>
                </div>
            </div>

            <div class="dflg-auth-card-wrap">
                <div class="dflg-auth-card">

                    <div class="dflg-auth-tabs">
                        <button type="button" class="dflg-auth-tab <?= $abaInicial === 'login' ? 'active' : '' ?>" data-tab="login" onclick="dflgSwitchTab('login')">Entrar</button>
                        <button type="button" class="dflg-auth-tab <?= $abaInicial === 'cadastro' ? 'active' : '' ?>" data-tab="cadastro" onclick="dflgSwitchTab('cadastro')">Cadastrar</button>
                    </div>

                    <!-- ---------- Formulário de Login ---------- -->
                    <form id="formLogin" action="<?= URL_BASE ?>/logar" method="post" class="dflg-auth-form <?= $abaInicial === 'login' ? '' : 'd-none' ?>">

                        <?php if (!empty($erros)): ?>
                            <div class="dflg-auth-alert">
                                <?php foreach ($erros as $erro): ?>
                                    <div><?= htmlspecialchars($erro) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="dflg-auth-label">E-mail</label>
                            <div class="dflg-input-group">
                                <i class="bi bi-envelope dflg-input-icon"></i>
                                <input type="email" name="email" class="dflg-input" placeholder="seu@email.com" value="<?= htmlspecialchars($emailAntigo) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="dflg-auth-label">Senha</label>
                            <div class="dflg-input-group">
                                <i class="bi bi-lock dflg-input-icon"></i>
                                <input type="password" name="senha" id="senhaLogin" class="dflg-input dflg-input-has-toggle" placeholder="••••••••" required>
                                <button type="button" class="dflg-input-toggle" onclick="dflgTogglePassword('senhaLogin', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-3 dflg-auth-row-small">
                            <label class="dflg-auth-remember">
                                <input type="checkbox" name="lembrar">
                                Lembrar-me
                            </label>
                            <button type="button" class="dflg-auth-link-btn">Esqueceu a senha?</button>
                        </div>

                        <button type="submit" class="dflg-auth-submit">
                            Entrar na plataforma <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>

                    <!-- ---------- Formulário de Cadastro ---------- -->
                    <form id="formCadastro" action="<?= URL_BASE ?>/cadastro" method="post" class="dflg-auth-form <?= $abaInicial === 'cadastro' ? '' : 'd-none' ?>">

                        <?php if (!empty($errosCadastro)): ?>
                            <div class="dflg-auth-alert">
                                <?php foreach ($errosCadastro as $erro): ?>
                                    <div><?= htmlspecialchars($erro) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="dflg-auth-label">Nome completo</label>
                            <div class="dflg-input-group">
                                <i class="bi bi-person dflg-input-icon"></i>
                                <input type="text" name="nome" class="dflg-input" placeholder="João Silva" value="<?= htmlspecialchars($nomeAntigo) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="dflg-auth-label">E-mail</label>
                            <div class="dflg-input-group">
                                <i class="bi bi-envelope dflg-input-icon"></i>
                                <input type="email" name="email" class="dflg-input" placeholder="seu@email.com" value="<?= htmlspecialchars($emailAntigoCadastro) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="dflg-auth-label">Senha</label>
                            <div class="dflg-input-group">
                                <i class="bi bi-lock dflg-input-icon"></i>
                                <input type="password" name="senha" id="senhaCadastro" class="dflg-input dflg-input-has-toggle" placeholder="••••••••" required>
                                <button type="button" class="dflg-input-toggle" onclick="dflgTogglePassword('senhaCadastro', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="dflg-auth-submit mt-2">
                            Criar minha conta <i class="bi bi-arrow-right"></i>
                        </button>

                        <p class="dflg-auth-terms">
                            Ao criar uma conta, você concorda com nossos
                            <span class="dflg-auth-terms-link">Termos de Uso</span>
                            e
                            <span class="dflg-auth-terms-link">Política de Privacidade</span>
                        </p>
                    </form>

                    <div class="dflg-auth-divider">
                        <span>Ou continue com</span>
                    </div>

                    <div class="dflg-auth-socials">
                        <button type="button" class="dflg-social-btn" title="Em breve" disabled>
                            <svg width="16" height="16" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                            </svg>
                            Google
                        </button>
                        <button type="button" class="dflg-social-btn" title="Em breve" disabled>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.17 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.646.35-1.086.636-1.336-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0112 6.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.167 22 16.418 22 12c0-5.523-4.477-10-10-10z" />
                            </svg>
                            GitHub
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function dflgSwitchTab(aba) {
            document.querySelectorAll('.dflg-auth-tab').forEach(function (btn) {
                btn.classList.toggle('active', btn.dataset.tab === aba);
            });
            document.getElementById('formLogin').classList.toggle('d-none', aba !== 'login');
            document.getElementById('formCadastro').classList.toggle('d-none', aba !== 'cadastro');
        }

        function dflgTogglePassword(inputId, btn) {
            var input = document.getElementById(inputId);
            var mostrando = input.type === 'text';
            input.type = mostrando ? 'password' : 'text';
            btn.innerHTML = mostrando ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        }
    </script>
</body>

</html>
