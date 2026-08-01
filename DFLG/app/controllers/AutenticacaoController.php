<?php

namespace app\controllers;

use app\core\Controller;
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
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $validador = new Validador();
        $validador->obrigatorio('nome', $nome)
                  ->obrigatorio('email', $email)
                  ->obrigatorio('senha', $senha);

        if ($validador->temErros()) {
            return $this->view('autenticacao/login', [
                'abaInicial' => 'cadastro',
                'errosCadastro' => $validador->getErros(),
                'nomeAntigo' => $nome,
                'emailAntigoCadastro' => $email,
            ]);
        }

        $usuario = new Usuario();
        $usuario->setNomeUsuario($nome);
        $usuario->setEmail($email);
        $usuario->setSenha($senha);
        $usuario->setPerfil('usuario');

        if (!$this->usuarioService->saveUsuario($usuario)) {
            return $this->view('autenticacao/login', [
                'abaInicial' => 'cadastro',
                'errosCadastro' => ['email' => 'Este e-mail já está cadastrado'],
                'nomeAntigo' => $nome,
                'emailAntigoCadastro' => $email,
            ]);
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
            $this->redirect(URL_BASE . '/dashboard');
            return;
        }

        $this->redirect(URL_BASE . '/login');
    }
}
