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

    /** Atualiza os dados pessoais completos do perfil (POST /perfil), incluindo a foto. */
    public function atualizar()
    {
        $this->autenticacaoRequired();

        $usuarioId = $_SESSION['usuario_logado']->getId();

        $nome = trim($_POST['nome'] ?? '');
        $sobrenome = trim($_POST['sobrenome'] ?? '') ?: null;
        $nickname = trim($_POST['nickname'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '') ?: null;
        $localizacao = trim($_POST['localizacao'] ?? '') ?: null;
        $dataNascimento = trim($_POST['dataNascimento'] ?? '') ?: null;

        $validador = new Validador();
        $validador->obrigatorio('nome', $nome)
                  ->obrigatorio('nickname', $nickname)
                  ->formato(
                      'nickname',
                      $nickname,
                      '/^[a-zA-Z0-9._]{3,20}$/',
                      'O nickname deve ter de 3 a 20 caracteres, sem espaços ou símbolos (só letras, números, ponto e underline)'
                  )
                  ->dataPassada('dataNascimento', $dataNascimento, 'Informe uma data de nascimento válida');

        // Nickname precisa continuar único, mas sem contar o próprio usuário
        $donoDoNickname = $nickname ? $this->usuarioService->getUsuarioByNickname($nickname) : false;
        if ($donoDoNickname && $donoDoNickname->getId() !== $usuarioId) {
            $validador->adicionarErro('nickname', 'Este nickname já está em uso, escolha outro');
        }

        if ($validador->temErros()) {
            $_SESSION['flash_erros_perfil'] = $validador->getErros();
            $this->redirect(URL_BASE . '/perfil');
        }

        // CPF/CNPJ não é passado aqui de propósito: assim como e-mail e senha,
        // não pode ser alterado por este formulário depois do cadastro.
        $this->usuarioService->atualizarDadosPessoais(
            $usuarioId,
            $nome,
            $sobrenome,
            $nickname,
            $telefone,
            $localizacao,
            $dataNascimento
        );

        $this->processarUploadFoto($usuarioId);

        // Atualiza a sessão para refletir as mudanças imediatamente
        $_SESSION['usuario_logado'] = $this->usuarioService->getUsuarioCompletoById($usuarioId);

        $this->redirect(URL_BASE . '/perfil');
    }

    /** Valida e salva a foto de perfil enviada (campo opcional do formulário). */
    private function processarUploadFoto(int $usuarioId): void
    {
        if (empty($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            return;
        }

        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_erros_perfil'] = ['foto' => 'Não foi possível enviar a foto. Tente novamente.'];
            return;
        }

        $tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $tipoReal = mime_content_type($_FILES['foto']['tmp_name']);

        if (!isset($tiposPermitidos[$tipoReal])) {
            $_SESSION['flash_erros_perfil'] = ['foto' => 'Formato inválido. Envie uma imagem JPG, PNG ou WEBP.'];
            return;
        }

        if ($_FILES['foto']['size'] > 3 * 1024 * 1024) {
            $_SESSION['flash_erros_perfil'] = ['foto' => 'A imagem deve ter no máximo 3MB.'];
            return;
        }

        $pastaDestino = __DIR__ . '/../../public/assets/img/perfil';
        if (!is_dir($pastaDestino)) {
            mkdir($pastaDestino, 0755, true);
        }

        $nomeArquivo = 'usuario_' . $usuarioId . '_' . time() . '.' . $tiposPermitidos[$tipoReal];

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $pastaDestino . '/' . $nomeArquivo)) {
            // Remove a foto antiga para não acumular arquivo órfão no servidor
            $usuarioAtual = $this->usuarioService->getUsuarioCompletoById($usuarioId);
            if ($usuarioAtual && $usuarioAtual->getFoto()) {
                $arquivoAntigo = $pastaDestino . '/' . $usuarioAtual->getFoto();
                if (is_file($arquivoAntigo)) {
                    unlink($arquivoAntigo);
                }
            }

            $this->usuarioService->atualizarFoto($usuarioId, $nomeArquivo);
        }
    }
}
