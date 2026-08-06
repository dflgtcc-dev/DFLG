<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\models\Parcela;
use app\services\ParcelaService;
use app\services\TransacaoService;
use app\services\UsuarioService;

class ParcelaController extends Controller
{
    private ParcelaService $service;

    public function __construct()
    {
        $this->service = new ParcelaService();
    }

    public function index()
    {
        $usuarioId = isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado']->getId() : null;

        $parcelasAtivas = $this->service->listarComProgresso($usuarioId, true);
        $resumo = $this->service->resumo($parcelasAtivas);

        $erros = $_SESSION['flash_erros_parcela'] ?? [];
        $formAntigo = $_SESSION['flash_form_parcela'] ?? [];
        $abrirModal = $_SESSION['flash_abrir_modal_parcela'] ?? false;
        unset($_SESSION['flash_erros_parcela'], $_SESSION['flash_form_parcela'], $_SESSION['flash_abrir_modal_parcela']);

        $this->view('parcelas/index', [
            'activePage' => 'installments',
            'parcelas' => $parcelasAtivas,
            'categorias' => TransacaoService::CATEGORIAS,
            'ativos' => $resumo['ativos'],
            'totalMensal' => $resumo['totalMensal'],
            'totalPendente' => $resumo['totalPendente'],
            'totalPago' => $resumo['totalPago'],
            'erros' => $erros,
            'formAntigo' => $formAntigo,
            'abrirModal' => $abrirModal,
        ]);
    }

    /**
     * Processa o formulário "Novo Parcelamento" (POST /parcelamentos).
     * O front só pede Valor Total + Nº de Parcelas; o valor de cada
     * parcela é calculado aqui (valorTotal / numeroParcelas).
     */
    public function criar()
    {
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = $_POST['categoria'] ?? '';
        $valorTotal = str_replace(',', '.', $_POST['valorTotal'] ?? '');
        $numeroParcelas = (int) ($_POST['numeroParcelas'] ?? 0);
        $dataPrimeiraParcela = $_POST['dataPrimeiraParcela'] ?? '';

        $validador = new Validador();
        $validador->obrigatorio('descricao', $descricao)
                  ->obrigatorio('categoria', $categoria)
                  ->obrigatorio('valorTotal', $valorTotal)
                  ->obrigatorio('dataPrimeiraParcela', $dataPrimeiraParcela);

        if (!$validador->temErros() && (!is_numeric($valorTotal) || (float) $valorTotal <= 0)) {
            $validador->obrigatorio('valorInvalido', '', 'Informe um valor total maior que zero');
        }

        if (!$validador->temErros() && $numeroParcelas < 1) {
            $validador->obrigatorio('numeroParcelas', '', 'Informe em quantas parcelas foi dividido (mínimo 1)');
        }

        if ($validador->temErros()) {
            $_SESSION['flash_erros_parcela'] = $validador->getErros();
            $_SESSION['flash_form_parcela'] = compact('descricao', 'categoria', 'valorTotal', 'numeroParcelas', 'dataPrimeiraParcela');
            $_SESSION['flash_abrir_modal_parcela'] = true;
            $this->redirect(URL_BASE . '/parcelamentos');
        }

        $parcela = new Parcela();
        $parcela->setUsuarioId(isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado']->getId() : null);
        $parcela->setDescricao($descricao);
        $parcela->setCategoria($categoria);
        $parcela->setValorTotal((float) $valorTotal);
        $parcela->setNumeroParcelas($numeroParcelas);
        $parcela->setValorParcela(round(((float) $valorTotal) / $numeroParcelas, 2));
        $parcela->setDataPrimeiraParcela($dataPrimeiraParcela);

        $this->service->criar($parcela);

        // Gamificação: mesma regra da tela de Transações — só no 1º lançamento do dia (RN07)
        if (isset($_SESSION['usuario_logado'])) {
            (new UsuarioService())->adicionarPontosSeElegivel($_SESSION['usuario_logado']->getId(), 10);
        }

        $this->redirect(URL_BASE . '/parcelamentos');
    }
}
