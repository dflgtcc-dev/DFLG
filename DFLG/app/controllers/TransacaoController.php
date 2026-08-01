<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\models\Transacao;
use app\services\TransacaoService;
use app\services\UsuarioService;

class TransacaoController extends Controller
{
    private TransacaoService $service;

    public function __construct()
    {
        $this->service = new TransacaoService();
    }

    public function index()
    {
        // --- Filtros vindos da query string (?periodo=&tipo=&categoria=&ordenar=&busca=&pagina=&tamanho=) ---
        $periodo = $_GET['periodo'] ?? '3months';
        $tipo = $_GET['tipo'] ?? 'all';
        $categoria = $_GET['categoria'] ?? 'all';
        $ordenar = $_GET['ordenar'] ?? 'newest';
        $busca = trim($_GET['busca'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $tamanhoPagina = (int) ($_GET['tamanho'] ?? 10);
        if (!in_array($tamanhoPagina, [5, 10, 25], true)) {
            $tamanhoPagina = 10;
        }

        $filtros = [
            'dataInicio' => $this->service->dataInicioPeriodo($periodo),
            'tipo' => $tipo,
            'categoria' => $categoria,
            'busca' => $busca,
            'ordenar' => $ordenar,
        ];

        $todasFiltradas = $this->service->listar($filtros);
        $resumo = $this->service->resumo($todasFiltradas);

        $totalPaginas = max(1, (int) ceil($resumo['total'] / $tamanhoPagina));
        $pagina = min($pagina, $totalPaginas);

        $paginadas = array_slice($todasFiltradas, ($pagina - 1) * $tamanhoPagina, $tamanhoPagina);

        // --- Flash de erro/valores antigos vindos de uma tentativa de criação que falhou ---
        $erros = $_SESSION['flash_erros_transacao'] ?? [];
        $formAntigo = $_SESSION['flash_form_transacao'] ?? [];
        $abrirModal = $_SESSION['flash_abrir_modal'] ?? false;
        unset($_SESSION['flash_erros_transacao'], $_SESSION['flash_form_transacao'], $_SESSION['flash_abrir_modal']);

        $this->view('transacoes/index', [
            'activePage' => 'transactions',
            'transacoes' => $paginadas,
            'categorias' => TransacaoService::CATEGORIAS,
            'periodo' => $periodo,
            'tipo' => $tipo,
            'categoria' => $categoria,
            'ordenar' => $ordenar,
            'busca' => $busca,
            'pagina' => $pagina,
            'tamanhoPagina' => $tamanhoPagina,
            'totalPaginas' => $totalPaginas,
            'totalReceitas' => $resumo['totalReceitas'],
            'totalDespesas' => $resumo['totalDespesas'],
            'saldo' => $resumo['saldo'],
            'totalTransacoes' => $resumo['total'],
            'erros' => $erros,
            'formAntigo' => $formAntigo,
            'abrirModal' => $abrirModal,
        ]);
    }

    /**
     * Processa o formulário "Nova Transação" (POST /transacoes).
     * Sempre redireciona de volta para /transacoes (padrão POST-Redirect-GET).
     */
    public function criar()
    {
        $descricao = trim($_POST['descricao'] ?? '');
        $valor = str_replace(',', '.', $_POST['valor'] ?? '');
        $categoria = $_POST['categoria'] ?? '';
        $tipo = $_POST['tipo'] ?? 'despesa';
        $data = $_POST['data'] ?? '';
        $moeda = $_POST['moeda'] ?? 'BRL';

        $validador = new Validador();
        $validador->obrigatorio('descricao', $descricao)
                  ->obrigatorio('valor', $valor)
                  ->obrigatorio('categoria', $categoria)
                  ->obrigatorio('data', $data);

        if (!$validador->temErros() && (!is_numeric($valor) || (float) $valor <= 0)) {
            $validador->obrigatorio('valorInvalido', '', 'Informe um valor numérico maior que zero');
        }

        if ($validador->temErros()) {
            $_SESSION['flash_erros_transacao'] = $validador->getErros();
            $_SESSION['flash_form_transacao'] = compact('descricao', 'valor', 'categoria', 'tipo', 'data', 'moeda');
            $_SESSION['flash_abrir_modal'] = true;
            $this->redirect(URL_BASE . '/transacoes');
        }

        $transacao = new Transacao();
        $transacao->setUsuarioId(isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado']->getId() : null);
        $transacao->setDescricao($descricao);
        $transacao->setValor((float) $valor);
        $transacao->setCategoria($categoria);
        $transacao->setTipo($tipo);
        $transacao->setData($data);
        $transacao->setMoeda($moeda);

        $this->service->criar($transacao);

        // Gamificação: +10 pontos por transação registrada (só para usuários logados)
        if (isset($_SESSION['usuario_logado'])) {
            $usuarioService = new UsuarioService();
            $usuarioService->adicionarPontos($_SESSION['usuario_logado']->getId(), 10);
        }

        $this->redirect(URL_BASE . '/transacoes');
    }
}
