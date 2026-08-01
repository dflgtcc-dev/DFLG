<?php

namespace app\services;

use app\models\Parcela;
use app\repositories\ParcelaRepository;

class ParcelaService
{
    private ParcelaRepository $repository;

    public function __construct()
    {
        $this->repository = new ParcelaRepository();
    }

    public function criar(Parcela $parcela): bool
    {
        return $this->repository->create($parcela);
    }

    public function remover(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Lista os parcelamentos já com o progresso calculado, e separa em
     * "ativos" (ainda não quitados) e "quitados" — sem precisar de nenhum
     * campo manual de "parcela atual": tudo é derivado da data da compra
     * comparada com a data de hoje, então avança sozinho a cada mês.
     */
    public function listarComProgresso(?int $usuarioId, bool $apenasAtivos = true): array
    {
        $linhas = $this->repository->getAll($usuarioId);
        $resultado = [];

        foreach ($linhas as $linha) {
            $progresso = $this->calcularProgresso($linha);

            if ($apenasAtivos && $progresso['quitado']) {
                continue;
            }

            $resultado[] = array_merge($linha, $progresso);
        }

        return $resultado;
    }

    /**
     * A partir da data da 1ª parcela e de hoje, calcula quantas parcelas já
     * "venceram" (parcelaAtual), quantas restam, valores pagos/a pagar,
     * percentual do progresso e a data da próxima parcela em aberto.
     */
    public function calcularProgresso(array $parcela): array
    {
        $inicio = new \DateTime($parcela['data_primeira_parcela']);
        $hoje = new \DateTime('today');
        $numeroParcelas = (int) $parcela['numero_parcelas'];
        $valorParcela = (float) $parcela['valor_parcela'];

        $mesesDecorridos = (($hoje->format('Y') - $inicio->format('Y')) * 12) + ($hoje->format('n') - $inicio->format('n'));
        $parcelaAtual = min(max($mesesDecorridos + 1, 1), $numeroParcelas);
        $restam = $numeroParcelas - $parcelaAtual;
        $quitado = $parcelaAtual >= $numeroParcelas;

        $proximoVencimento = (clone $inicio)->modify('+' . $parcelaAtual . ' months');

        return [
            'parcelaAtual' => $parcelaAtual,
            'restam' => $restam,
            'quitado' => $quitado,
            'percentual' => (int) round(($parcelaAtual / $numeroParcelas) * 100),
            'valorPago' => $parcelaAtual * $valorParcela,
            'valorAPagar' => $restam * $valorParcela,
            'proximoVencimento' => $quitado ? null : $proximoVencimento->format('Y-m-d'),
        ];
    }

    /** Resumo (cards do topo) a partir de uma lista já processada por listarComProgresso(). */
    public function resumo(array $parcelasAtivas): array
    {
        $totalMensal = 0.0;
        $totalPendente = 0.0;
        $totalPago = 0.0;

        foreach ($parcelasAtivas as $p) {
            $totalMensal += (float) $p['valor_parcela'];
            $totalPendente += $p['valorAPagar'];
            $totalPago += $p['valorPago'];
        }

        return [
            'ativos' => count($parcelasAtivas),
            'totalMensal' => $totalMensal,
            'totalPendente' => $totalPendente,
            'totalPago' => $totalPago,
        ];
    }
}
