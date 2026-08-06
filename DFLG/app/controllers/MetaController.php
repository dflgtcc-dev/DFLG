<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\models\Meta;
use app\services\MetaService;
use app\services\UsuarioService;

class MetaController extends Controller
{
    private MetaService $service;

    /** Bônus de XP concedido quando uma meta é concluída (RN04). */
    private const XP_META_CONCLUIDA = 50;

    public function __construct()
    {
        $this->service = new MetaService();
    }

    public function index()
    {
        $usuarioId = isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado']->getId() : null;

        $metas = $this->service->listar($usuarioId);
        $metasAtivas = array_values(array_filter($metas, fn($m) => !$m['concluida']));
        $metasConcluidas = array_values(array_filter($metas, fn($m) => $m['concluida']));
        $resumo = $this->service->resumo($metas);

        $erros = $_SESSION['flash_erros_meta'] ?? [];
        $formAntigo = $_SESSION['flash_form_meta'] ?? [];
        $abrirModal = $_SESSION['flash_abrir_modal_meta'] ?? false;
        unset($_SESSION['flash_erros_meta'], $_SESSION['flash_form_meta'], $_SESSION['flash_abrir_modal_meta']);

        $this->view('metas/index', [
            'activePage' => 'goals',
            'metasAtivas' => $metasAtivas,
            'metasConcluidas' => $metasConcluidas,
            'tipos' => Meta::TIPOS,
            'ativas' => $resumo['ativas'],
            'concluidas' => $resumo['concluidas'],
            'totalGuardado' => $resumo['totalGuardado'],
            'progressoMedio' => $resumo['progressoMedio'],
            'erros' => $erros,
            'formAntigo' => $formAntigo,
            'abrirModal' => $abrirModal,
        ]);
    }

    /** Cria uma nova meta financeira (POST /metas) — RF08. */
    public function criar()
    {
        $nomeMeta = trim($_POST['nomeMeta'] ?? '');
        $tipo = $_POST['tipo'] ?? '';
        $valorMeta = str_replace(',', '.', $_POST['valorMeta'] ?? '');
        $dataLimite = $_POST['dataLimite'] ?? '';

        $validador = new Validador();
        $validador->obrigatorio('nomeMeta', $nomeMeta)
                  ->obrigatorio('tipo', $tipo)
                  ->obrigatorio('valorMeta', $valorMeta)
                  ->obrigatorio('dataLimite', $dataLimite)
                  // RN08: a data de conclusão deve ser sempre posterior à data atual
                  ->dataFutura('dataLimite', $dataLimite, 'A data-limite da meta deve ser posterior a hoje');

        if (!$validador->temErros() && !array_key_exists($tipo, Meta::TIPOS)) {
            $validador->adicionarErro('tipo', 'Selecione um tipo de meta válido');
        }

        if (!$validador->temErros() && (!is_numeric($valorMeta) || (float) $valorMeta <= 0)) {
            $validador->adicionarErro('valorMeta', 'Informe um valor-alvo maior que zero');
        }

        if ($validador->temErros()) {
            $_SESSION['flash_erros_meta'] = $validador->getErros();
            $_SESSION['flash_form_meta'] = compact('nomeMeta', 'tipo', 'valorMeta', 'dataLimite');
            $_SESSION['flash_abrir_modal_meta'] = true;
            $this->redirect(URL_BASE . '/metas');
        }

        $meta = new Meta();
        $meta->setUsuarioId(isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado']->getId() : null);
        $meta->setNomeMeta($nomeMeta);
        $meta->setTipo($tipo);
        $meta->setValorMeta((float) $valorMeta);
        $meta->setDataLimite($dataLimite);

        $this->service->criar($meta);

        $this->redirect(URL_BASE . '/metas');
    }

    /** Edita uma meta existente (POST /metas/{id}/atualizar) — RF08. */
    public function atualizar($id)
    {
        $meta = $this->pegarMetaDoUsuario((int) $id);
        if (!$meta) {
            $this->redirect(URL_BASE . '/metas');
        }

        $nomeMeta = trim($_POST['nomeMeta'] ?? '');
        $tipo = $_POST['tipo'] ?? '';
        $valorMeta = str_replace(',', '.', $_POST['valorMeta'] ?? '');
        $dataLimite = $_POST['dataLimite'] ?? '';

        $validador = new Validador();
        $validador->obrigatorio('nomeMeta', $nomeMeta)
                  ->obrigatorio('tipo', $tipo)
                  ->obrigatorio('valorMeta', $valorMeta)
                  ->obrigatorio('dataLimite', $dataLimite)
                  ->dataFutura('dataLimite', $dataLimite, 'A data-limite da meta deve ser posterior a hoje');

        if (!$validador->temErros() && (!is_numeric($valorMeta) || (float) $valorMeta <= 0)) {
            $validador->adicionarErro('valorMeta', 'Informe um valor-alvo maior que zero');
        }

        if ($validador->temErros()) {
            $_SESSION['flash_erros_meta'] = $validador->getErros();
            $this->redirect(URL_BASE . '/metas');
        }

        $meta->setNomeMeta($nomeMeta);
        $meta->setTipo($tipo);
        $meta->setValorMeta((float) $valorMeta);
        $meta->setDataLimite($dataLimite);

        $this->service->atualizar($meta);

        $this->redirect(URL_BASE . '/metas');
    }

    /** Exclui uma meta (POST /metas/{id}/excluir). */
    public function excluir($id)
    {
        $meta = $this->pegarMetaDoUsuario((int) $id);
        if ($meta) {
            $this->service->remover((int) $id);
        }

        $this->redirect(URL_BASE . '/metas');
    }

    /** Fixa/desafixa a meta no Dashboard (POST /metas/{id}/fixar) — menu de "...". */
    public function fixar($id)
    {
        $meta = $this->pegarMetaDoUsuario((int) $id);
        if ($meta) {
            $this->service->alternarFixada((int) $id);
        }

        $this->redirect(URL_BASE . '/metas');
    }

    /**
     * RF19 — "Cumprir Meta": registra que o usuário guardou/investiu um
     * valor rumo à meta (POST /metas/{id}/aportar). Ao concluir, dá um
     * bônus de XP (RN04: nível também deve considerar metas cumpridas).
     */
    public function aportar($id)
    {
        $meta = $this->pegarMetaDoUsuario((int) $id);
        $voltarPara = ($_POST['origem'] ?? '') === 'dashboard' ? URL_BASE . '/dashboard' : URL_BASE . '/metas';

        if (!$meta) {
            $this->redirect($voltarPara);
        }

        $valorAporte = str_replace(',', '.', $_POST['valorAporte'] ?? '');

        if (!is_numeric($valorAporte) || (float) $valorAporte <= 0) {
            $this->redirect($voltarPara);
        }

        $resultado = $this->service->aportar((int) $id, (float) $valorAporte);

        if ($resultado['concluidaAgora'] && isset($_SESSION['usuario_logado'])) {
            (new UsuarioService())->adicionarPontos($_SESSION['usuario_logado']->getId(), self::XP_META_CONCLUIDA);
        }

        $this->redirect($voltarPara);
    }

    /** Busca a meta garantindo que ela pertence ao usuário logado (ou é pública/demo). */
    private function pegarMetaDoUsuario(int $id): ?Meta
    {
        $meta = $this->service->getById($id);
        if (!$meta) {
            return null;
        }

        $usuarioId = isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado']->getId() : null;

        if ($meta->getUsuarioId() !== null && $meta->getUsuarioId() !== $usuarioId) {
            return null;
        }

        return $meta;
    }
}
