<?php

namespace app\services;

use app\models\Meta;
use app\repositories\MetaRepository;

class MetaService
{
    private MetaRepository $repository;

    public function __construct()
    {
        $this->repository = new MetaRepository();
    }

    public function criar(Meta $meta): bool
    {
        return $this->repository->create($meta);
    }

    public function atualizar(Meta $meta): bool
    {
        return $this->repository->update($meta);
    }

    public function remover(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getById(int $id): ?Meta
    {
        return $this->repository->getById($id);
    }

    /** Lista as metas já com o progresso (%) e os dias restantes calculados. */
    public function listar(?int $usuarioId): array
    {
        $linhas = $this->repository->getAll($usuarioId);
        $resultado = [];

        foreach ($linhas as $linha) {
            // Normaliza 'fixada' aqui (à prova de banco sem a coluna ainda,
            // ou de linhas antigas) pra nunca faltar essa chave adiante.
            $linha['fixada'] = !empty($linha['fixada']);
            $resultado[] = array_merge($linha, $this->calcularProgresso($linha));
        }

        return $resultado;
    }

    /**
     * Metas exibidas no Dashboard: no máximo $limite (padrão 3). As que o
     * usuário fixou manualmente (fixada = 1) aparecem sempre primeiro; as
     * vagas restantes são preenchidas pelas metas mais próximas de bater
     * 100% (ordenadas pelo percentual de progresso, decrescente).
     */
    public function listarParaDashboard(?int $usuarioId, int $limite = 3): array
    {
        $ativas = array_values(array_filter($this->listar($usuarioId), fn($m) => !$m['concluida']));

        usort($ativas, function ($a, $b) {
            if ($a['fixada'] !== $b['fixada']) {
                return $b['fixada'] <=> $a['fixada']; // fixadas primeiro
            }
            return $b['percentual'] <=> $a['percentual']; // depois, mais próxima de concluir
        });

        return array_slice($ativas, 0, $limite);
    }

    /** Fixa/desafixa a meta no Dashboard (botão de "..." na tela de Metas). */
    public function alternarFixada(int $id): bool
    {
        $meta = $this->repository->getById($id);
        if (!$meta) {
            return false;
        }

        return $this->repository->definirFixada($id, !$meta->isFixada());
    }

    public function calcularProgresso(array $meta): array
    {
        $valorMeta = (float) $meta['valor_meta'];
        $valorAtual = (float) $meta['valor_atual'];
        $percentual = $valorMeta > 0 ? min(100, (int) round(($valorAtual / $valorMeta) * 100)) : 0;

        $hoje = new \DateTime('today');
        $limite = new \DateTime($meta['data_limite']);
        $diasRestantes = (int) $hoje->diff($limite)->format('%r%a');

        return [
            'percentual' => $percentual,
            'faltam' => max(0, $valorMeta - $valorAtual),
            'diasRestantes' => $diasRestantes,
            'atrasada' => !$meta['concluida'] && $diasRestantes < 0,
        ];
    }

    /**
     * RF19 — "Cumprir Meta": registra um aporte (dinheiro guardado/investido)
     * na meta. O valor é somado ao que já existia, sem ultrapassar o valor
     * alvo. Quando o valor acumulado atinge a meta, ela é marcada como
     * concluída. Retorna se essa chamada foi o que concluiu a meta agora
     * (usado pra decidir se dá XP de bônus só uma vez).
     */
    public function aportar(int $id, float $valorAporte): array
    {
        $meta = $this->repository->getById($id);

        if (!$meta || $meta->isConcluida()) {
            return ['sucesso' => false, 'concluidaAgora' => false, 'meta' => $meta];
        }

        $novoValorAtual = min($meta->getValorMeta(), $meta->getValorAtual() + $valorAporte);
        $concluida = $novoValorAtual >= $meta->getValorMeta();

        $this->repository->atualizarProgresso($id, $novoValorAtual, $concluida);

        $meta->setValorAtual($novoValorAtual);
        $meta->setConcluida($concluida);

        return ['sucesso' => true, 'concluidaAgora' => $concluida, 'meta' => $meta];
    }

    /** Resumo (cards do topo) a partir da lista COMPLETA (ativas + concluídas) já processada por listar(). */
    public function resumo(array $todasMetas): array
    {
        $ativas = 0;
        $concluidas = 0;
        $valorAcumulado = 0.0;
        $valorNecessario = 0.0;

        foreach ($todasMetas as $m) {
            $valorAcumulado += (float) $m['valor_atual'];
            if ($m['concluida']) {
                $concluidas++;
            } else {
                $ativas++;
                $valorNecessario += $m['faltam'];
            }
        }

        $totalMetas = $ativas + $concluidas;

        return [
            'ativas' => $ativas,
            'concluidas' => $concluidas,
            'valorAcumulado' => $valorAcumulado,
            'valorNecessario' => $valorNecessario,
            'taxaSucesso' => $totalMetas > 0 ? (int) round(($concluidas / $totalMetas) * 100) : 0,
        ];
    }
}
