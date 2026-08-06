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

        // Os cards do topo refletem só os compromissos ATIVOS (em andamento)
        $parcelasAtivas = $this->service->listarComProgresso($usuarioId, true);
        $resumo = $this->service->resumo($parcelasAtivas);

        // A tabela abaixo lista tudo (ativos + concluídos), filtrável
        $busca = trim($_GET['busca'] ?? '');
        $categoriaFiltro = $_GET['categoriaFiltro'] ?? 'all';
        $statusFiltro = $_GET['status'] ?? 'all';
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $tamanhoPagina = 8;

        $todasParcelas = $this->service->listarComProgresso($usuarioId, false);

        $filtradas = array_values(array_filter($todasParcelas, function ($p) use ($busca, $categoriaFiltro, $statusFiltro) {
            if ($busca !== '' && stripos($p['descricao'], $busca) === false) {
                return false;
            }
            if ($categoriaFiltro !== 'all' && $p['categoria'] !== $categoriaFiltro) {
                return false;
            }
            if ($statusFiltro === 'andamento' && $p['quitado']) {
                return false;
            }
            if ($statusFiltro === 'concluido' && !$p['quitado']) {
                return false;
            }
            return true;
        }));

        usort($filtradas, fn($a, $b) => $a['quitado'] <=> $b['quitado']);

        $totalFiltradas = count($filtradas);
        $totalPaginas = max(1, (int) ceil($totalFiltradas / $tamanhoPagina));
        $pagina = min($pagina, $totalPaginas);
        $parcelasPagina = array_slice($filtradas, ($pagina - 1) * $tamanhoPagina, $tamanhoPagina);

        $erros = $_SESSION['flash_erros_parcela'] ?? [];
        $formAntigo = $_SESSION['flash_form_parcela'] ?? [];
        $abrirModal = $_SESSION['flash_abrir_modal_parcela'] ?? false;
        unset($_SESSION['flash_erros_parcela'], $_SESSION['flash_form_parcela'], $_SESSION['flash_abrir_modal_parcela']);

        $this->view('parcelas/index', [
            'activePage' => 'installments',
            'parcelas' => $parcelasPagina,
            'categorias' => TransacaoService::CATEGORIAS,
            'busca' => $busca,
            'categoriaFiltro' => $categoriaFiltro,
            'statusFiltro' => $statusFiltro,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalFiltradas' => $totalFiltradas,
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

    /** Edita um parcelamento existente (POST /parcelamentos/{id}/atualizar). */
    public function atualizar($id)
    {
        $parcela = $this->pegarParcelaDoUsuario((int) $id);
        if (!$parcela) {
            $this->redirect(URL_BASE . '/parcelamentos');
        }

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
            $validador->adicionarErro('valorTotal', 'Informe um valor total maior que zero');
        }
        if (!$validador->temErros() && $numeroParcelas < 1) {
            $validador->adicionarErro('numeroParcelas', 'Informe em quantas parcelas foi dividido (mínimo 1)');
        }

        if ($validador->temErros()) {
            $_SESSION['flash_erros_parcela'] = $validador->getErros();
            $this->redirect(URL_BASE . '/parcelamentos');
        }

        $parcela->setDescricao($descricao);
        $parcela->setCategoria($categoria);
        $parcela->setValorTotal((float) $valorTotal);
        $parcela->setNumeroParcelas($numeroParcelas);
        $parcela->setValorParcela(round(((float) $valorTotal) / $numeroParcelas, 2));
        $parcela->setDataPrimeiraParcela($dataPrimeiraParcela);

        $this->service->atualizar($parcela);

        $this->redirect(URL_BASE . '/parcelamentos');
    }

    /** Exclui um parcelamento (POST /parcelamentos/{id}/excluir). */
    public function excluir($id)
    {
        $parcela = $this->pegarParcelaDoUsuario((int) $id);
        if ($parcela) {
            $this->service->remover((int) $id);
        }

        $this->redirect(URL_BASE . '/parcelamentos');
    }

    /** Busca o parcelamento garantindo que ele pertence ao usuário logado (ou é público/demo). */
    private function pegarParcelaDoUsuario(int $id): ?Parcela
    {
        $parcela = $this->service->getById($id);
        if (!$parcela) {
            return null;
        }

        $usuarioId = isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado']->getId() : null;

        if ($parcela->getUsuarioId() !== null && $parcela->getUsuarioId() !== $usuarioId) {
            return null;
        }

        return $parcela;
    }
}
