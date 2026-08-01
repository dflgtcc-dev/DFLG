<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\services\CategoriaService;

class CategoriaController extends Controller
{
    private CategoriaService $service;

    public function __construct()
    {
        $this->service = new CategoriaService();
    }

    public function index()
    {
        $usuarioId = isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado']->getId() : null;

        $categorias = $this->service->listarComGasto($usuarioId);
        $resumo = $this->service->resumo($categorias);

        $erros = $_SESSION['flash_erros_categoria'] ?? [];
        $abrirModalPara = $_SESSION['flash_abrir_modal_categoria'] ?? null;
        unset($_SESSION['flash_erros_categoria'], $_SESSION['flash_abrir_modal_categoria']);

        $this->view('categorias/index', [
            'activePage' => 'categories',
            'categorias' => $categorias,
            'totalGasto' => $resumo['totalGasto'],
            'totalOrcamento' => $resumo['totalOrcamento'],
            'categoriasAtivas' => $resumo['categoriasAtivas'],
            'pertoDoLimite' => $resumo['pertoDoLimite'],
            'erros' => $erros,
            'abrirModalPara' => $abrirModalPara,
        ]);
    }

    /** Define/atualiza o orçamento mensal de uma categoria (POST /categorias). */
    public function atualizar()
    {
        $nome = $_POST['nome'] ?? '';
        $orcamento = str_replace(',', '.', $_POST['orcamento'] ?? '');

        $validador = new Validador();
        $validador->obrigatorio('nome', $nome)
                  ->obrigatorio('orcamento', $orcamento);

        if (!$validador->temErros() && (!is_numeric($orcamento) || (float) $orcamento < 0)) {
            $validador->obrigatorio('orcamentoInvalido', '', 'Informe um valor de orçamento válido');
        }

        if ($validador->temErros()) {
            $_SESSION['flash_erros_categoria'] = $validador->getErros();
            $_SESSION['flash_abrir_modal_categoria'] = $nome;
            $this->redirect(URL_BASE . '/categorias');
        }

        $usuarioId = isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado']->getId() : null;
        $this->service->atualizarOrcamento($usuarioId, $nome, (float) $orcamento);

        $this->redirect(URL_BASE . '/categorias');
    }
}
