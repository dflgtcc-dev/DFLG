<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\services\TransacaoService;
use app\services\UsuarioService;

class UsuarioController extends Controller
{
    private UsuarioService $usuarioService;
    private TransacaoService $transacaoService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        $this->transacaoService = new TransacaoService();
    }

    public function perfil()
    {
        $this->autenticacaoRequired();

        $usuarioId = $_SESSION['usuario_logado']->getId();
        $usuario = $this->usuarioService->getUsuarioCompletoById($usuarioId);

        $nivel = $this->usuarioService->calcularNivel($usuario->getPontosTotais());
        $posicaoRanking = $this->usuarioService->getPosicaoRanking($usuarioId);

        $atividades = [];
        foreach ($this->transacaoService->recentesPorUsuario($usuarioId, 5) as $t) {
            $atividades[] = [
                'acao' => 'Registrou transação: ' . $t['descricao'],
                'pontos' => 10,
                'data' => $t['data_transacao'],
            ];
        }

        $erros = $_SESSION['flash_erros_perfil'] ?? [];
        unset($_SESSION['flash_erros_perfil']);

        $this->view('perfil/index', [
            'activePage' => 'profile',
            'usuario' => $usuario,
            'nivel' => $nivel,
            'posicaoRanking' => $posicaoRanking,
            'atividades' => $atividades,
            'erros' => $erros,
        ]);
    }

    /** Atualiza nome, telefone e localização (POST /perfil). */
    public function atualizar()
    {
        $this->autenticacaoRequired();

        $usuarioId = $_SESSION['usuario_logado']->getId();

        $nome = trim($_POST['nome'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '') ?: null;
        $localizacao = trim($_POST['localizacao'] ?? '') ?: null;

        $validador = new Validador();
        $validador->obrigatorio('nome', $nome);

        if ($validador->temErros()) {
            $_SESSION['flash_erros_perfil'] = $validador->getErros();
            $this->redirect(URL_BASE . '/perfil');
        }

        $this->usuarioService->atualizarDadosPessoais($usuarioId, $nome, $telefone, $localizacao);

        // Atualiza a sessão para refletir as mudanças imediatamente
        $_SESSION['usuario_logado'] = $this->usuarioService->getUsuarioCompletoById($usuarioId);

        $this->redirect(URL_BASE . '/perfil');
    }
}
