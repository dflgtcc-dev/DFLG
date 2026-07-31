<?php

namespace app\helpers;

class SimuladorHelper
{
    /**
     * Mesma fórmula usada no protótipo do Figma: juros compostos com
     * aporte mensal fixo, aplicado sobre o saldo já corrigido.
     */
    public static function calcularInvestimento(float $valorInicial, float $aporteMensal, float $taxaMensal, int $periodoMeses): array
    {
        $total = $valorInicial;

        for ($i = 0; $i < $periodoMeses; $i++) {
            $total = $total * (1 + $taxaMensal / 100) + $aporteMensal;
        }

        $totalInvestido = $valorInicial + ($aporteMensal * $periodoMeses);
        $rendimento = $total - $totalInvestido;

        return [
            'valorFinal' => $total,
            'totalInvestido' => $totalInvestido,
            'rendimento' => $rendimento,
        ];
    }
}
