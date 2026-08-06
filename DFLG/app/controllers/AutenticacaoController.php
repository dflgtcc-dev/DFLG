<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\DocumentoHelper;
use app\helpers\Validador;
use app\models\Usuario;
use app\services\AutenticacaoService;
use app\services\UsuarioService;

class AutenticacaoController extends Controller
{
    private AutenticacaoService $autenticacaoService;
    private UsuarioService $usuarioService;

    public function __construct()
    {
        $this->autenticacaoService = new AutenticacaoService();
        $this->usuarioService = new UsuarioService();
    }

    /**
     * Tela de Login / Cadastro (GET /login).
     * Aceita ?aba=cadastro para já abrir na aba de cadastro.
     */
    public function login()
    {
       if (
            isset($_SESSION['usuario_logado']) &&
            !isset($_GET['aba'])
        ){
        $this->redirect(URL_BASE . '/dashboard');
        }

        $abaInicial = ($_GET['aba'] ?? 'login') === 'cadastro' ? 'cadastro' : 'login';

        $this->view('autenticacao/login', [
            'abaInicial' => $abaInicial,
        ]);
    }

    /**
     * Processa o formulário de login (POST /logar).
     */
    public function logar()
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $validador = new Validador();
        $validador->obrigatorio('email', $email)
                  ->obrigatorio('senha', $senha);

        if ($validador->temErros()) {
            return $this->view('autenticacao/login', [
                'abaInicial' => 'login',
                'erros' => $validador->getErros(),
                'emailAntigo' => $email,
            ]);
        }

        if ($this->autenticacaoService->logar($email, $senha)) {
            $this->redirect(URL_BASE . '/dashboard');
        }

        return $this->view('autenticacao/login', [
            'abaInicial' => 'login',
            'erros' => ['login' => 'E-mail ou senha inválidos'],
            'emailAntigo' => $email,
        ]);
    }

    /**
     * Processa o formulário de cadastro (POST /cadastro).
     */
    public function cadastrar()
    {
        $nome = trim($_POST['nome'] ?? '');
        $sobrenome = trim($_POST['sobrenome'] ?? '');
        $nickname = trim($_POST['nickname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $cpfCnpj = trim($_POST['cpfCnpj'] ?? '');
        $naoPossuiCpf = isset($_POST['naoPossuiCpf']);
        $dataNascimento = trim($_POST['dataNascimento'] ?? '');

        // Campos coletados na "etapa 2" do cadastro — se algum deles falhar,
        // a tela já reabre direto na etapa 2 em vez da etapa 1 (email/senha).
        $camposEtapa2 = ['nome', 'sobrenome', 'nickname', 'cpfCnpj', 'dataNascimento'];

        $validador = new Validador();
        $validador->obrigatorio('nome', $nome)
                  ->obrigatorio('sobrenome', $sobrenome)
                  ->obrigatorio('nickname', $nickname)
                  ->obrigatorio('email', $email)
                  ->obrigatorio('senha', $senha)
                  ->obrigatorio('dataNascimento', $dataNascimento)
                  ->formato(
                      'nickname',
                      $nickname,
                      '/^[a-zA-Z0-9._]{3,20}$/',
                      'O nickname deve ter de 3 a 20 caracteres, sem espaços ou símbolos (só letras, números, ponto e underline)'
                  )
                  ->dataPassada('dataNascimento', $dataNascimento, 'Informe uma data de nascimento válida');

        // CPF/CNPJ só é obrigatório se o usuário não marcou "Não possuo CPF/CNPJ"
        if (!$naoPossuiCpf) {
            $validador->obrigatorio('cpfCnpj', $cpfCnpj, 'CPF/CNPJ inválido. Este campo é obrigatório.')
                      ->cpfOuCnpj('cpfCnpj', $cpfCnpj);
        } else {
            $cpfCnpj = null;
        }

        $dadosAntigos = [
            'nomeAntigo' => $nome,
            'sobrenomeAntigo' => $sobrenome,
            'nicknameAntigo' => $nickname,
            'emailAntigoCadastro' => $email,
            'cpfCnpjAntigo' => $cpfCnpj,
            'naoPossuiCpfAntigo' => $naoPossuiCpf,
            'dataNascimentoAntiga' => $dataNascimento,
        ];

        if ($validador->temErros()) {
            $mostrarEtapa2 = count(array_intersect($camposEtapa2, array_keys($validador->getErros()))) > 0;
            return $this->view('autenticacao/login', array_merge($dadosAntigos, [
                'abaInicial' => 'cadastro',
                'errosCadastro' => $validador->getErros(),
                'cadastroEtapa2' => $mostrarEtapa2,
            ]));
        }

        // RN02: e-mail e nickname não podem se repetir — verificados e reportados separadamente
        if ($this->usuarioService->getUsuarioByEmail($email)) {
            return $this->view('autenticacao/login', array_merge($dadosAntigos, [
                'abaInicial' => 'cadastro',
                'errosCadastro' => ['email' => 'Este e-mail já está cadastrado'],
                'cadastroEtapa2' => false,
            ]));
        }

        if ($this->usuarioService->getUsuarioByNickname($nickname)) {
            return $this->view('autenticacao/login', array_merge($dadosAntigos, [
                'abaInicial' => 'cadastro',
                'errosCadastro' => ['nickname' => 'Este nickname já está em uso, escolha outro'],
                'cadastroEtapa2' => true,
            ]));
        }

        $usuario = new Usuario();
        $usuario->setNomeUsuario($nome);
        $usuario->setSobrenome($sobrenome);
        $usuario->setNickname($nickname);
        $usuario->setEmail($email);
        $usuario->setSenha($senha);
        $usuario->setPerfil('usuario');
        $usuario->setCpfCnpj($cpfCnpj ? DocumentoHelper::apenasDigitos($cpfCnpj) : null);
        $usuario->setDataNascimento($dataNascimento);

        if (!$this->usuarioService->saveUsuario($usuario)) {
            return $this->view('autenticacao/login', array_merge($dadosAntigos, [
                'abaInicial' => 'cadastro',
                'errosCadastro' => ['email' => 'Este e-mail já está cadastrado'],
                'cadastroEtapa2' => false,
            ]));
        }

        // Loga automaticamente logo após o cadastro, como no fluxo do Figma
        $this->autenticacaoService->logar($email, $senha);
        $this->redirect(URL_BASE . '/dashboard');
    }

    /**
     * Encerra a sessão (GET /logout).
     */
   public function logout()
    {
        $this->autenticacaoService->logout();

        if (isset($_GET['redirect']) && $_GET['redirect'] === 'demo') {
            // Precisa de uma sessão nova pra guardar a flag do modo demo,
            // já que a anterior acabou de ser destruída em logout() acima.
            session_start();
            $_SESSION['modo_demo'] = true;
            $this->redirect(URL_BASE . '/dashboard');
            return;
        }

        $this->redirect(URL_BASE . '/login');
    }
}
