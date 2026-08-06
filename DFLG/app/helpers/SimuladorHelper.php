<?php

namespace app\helpers;

/**
 * Concentra as fórmulas de TODAS as calculadoras financeiras da tela
 * /calculadoras. Cada método aqui é independente e devolve um array
 * "pronto pra view" — o Controller só decide qual método chamar de
 * acordo com o "tipo" escolhido pelo usuário (ver SimuladorController).
 *
 * Importante (RNF05 / MANUAL.md): a regra de cálculo fica só aqui, nunca
 * na View e nunca duplicada em JavaScript.
 */
class SimuladorHelper
{
    /**
     * Tabela regressiva de Imposto de Renda para renda fixa (Tesouro Direto,
     * CDB, etc.), conforme a legislação brasileira. Usada só para dar uma
     * NOÇÃO didática do efeito do IR — não substitui uma calculadora fiscal.
     */
    private static function aliquotaIR(int $prazoDias): float
    {
        if ($prazoDias <= 180) return 0.225;
        if ($prazoDias <= 360) return 0.20;
        if ($prazoDias <= 720) return 0.175;
        return 0.15;
    }

    // =====================================================================
    // 1) INVESTIMENTOS (genérico) — juros compostos com aporte mensal fixo.
    // =====================================================================
    public static function calcularInvestimento(float $valorInicial, float $aporteMensal, float $taxaMensal, int $periodoMeses): array
    {
        $total = $valorInicial;
        $evolucao = [];

        for ($i = 1; $i <= $periodoMeses; $i++) {
            $total = $total * (1 + $taxaMensal / 100) + $aporteMensal;
            if ($i % max(1, intdiv($periodoMeses, 12) ?: 1) === 0 || $i === $periodoMeses) {
                $evolucao[] = ['periodo' => $i, 'valor' => $total];
            }
        }

        $totalInvestido = $valorInicial + ($aporteMensal * $periodoMeses);
        $rendimento = $total - $totalInvestido;

        return [
            'valorFinal' => $total,
            'totalInvestido' => $totalInvestido,
            'rendimento' => $rendimento,
            'evolucao' => $evolucao,
        ];
    }

    // =====================================================================
    // 2) JUROS COMPOSTOS — comparação didática: juros simples x compostos,
    //    sem aporte, só pra mostrar visualmente a curva exponencial.
    // =====================================================================
    public static function calcularJurosCompostos(float $valorInicial, float $taxaMensal, int $periodoMeses): array
    {
        $evolucao = [];
        $composto = $valorInicial;
        $passo = max(1, intdiv($periodoMeses, 10) ?: 1);

        for ($i = 0; $i <= $periodoMeses; $i++) {
            if ($i > 0) {
                $composto = $composto * (1 + $taxaMensal / 100);
            }
            $simples = $valorInicial * (1 + ($taxaMensal / 100) * $i);

            if ($i % $passo === 0 || $i === $periodoMeses) {
                $evolucao[] = ['mes' => $i, 'composto' => $composto, 'simples' => $simples];
            }
        }

        $simplesFinal = $valorInicial * (1 + ($taxaMensal / 100) * $periodoMeses);

        return [
            'valorFinalComposto' => $composto,
            'jurosComposto' => $composto - $valorInicial,
            'valorFinalSimples' => $simplesFinal,
            'jurosSimples' => $simplesFinal - $valorInicial,
            'diferenca' => $composto - $simplesFinal,
            'evolucao' => $evolucao,
        ];
    }

    // =====================================================================
    // 3) TESOURO DIRETO — emula o crescimento exponencial do dinheiro
    //    aplicado em Tesouro Direto (Selic, Prefixado ou IPCA+), com aporte
    //    mensal opcional e desconto de IR regressivo no final.
    // =====================================================================
    public static function calcularTesouroDireto(float $valorInicial, float $aporteMensal, float $taxaAnual, int $prazoAnos): array
    {
        $taxaMensal = (pow(1 + $taxaAnual / 100, 1 / 12) - 1) * 100;
        $meses = max(1, $prazoAnos * 12);

        $total = $valorInicial;
        $evolucaoAnual = [];

        for ($m = 1; $m <= $meses; $m++) {
            $total = $total * (1 + $taxaMensal / 100) + $aporteMensal;
            if ($m % 12 === 0) {
                $evolucaoAnual[] = ['ano' => intdiv($m, 12), 'valor' => $total];
            }
        }

        $totalInvestido = $valorInicial + ($aporteMensal * $meses);
        $rendimentoBruto = $total - $totalInvestido;

        $aliquota = self::aliquotaIR($prazoAnos * 365);
        $ir = max(0, $rendimentoBruto) * $aliquota;
        $rendimentoLiquido = $rendimentoBruto - $ir;
        $valorFinalLiquido = $totalInvestido + $rendimentoLiquido;

        return [
            'totalInvestido' => $totalInvestido,
            'rendimentoBruto' => $rendimentoBruto,
            'aliquotaIR' => $aliquota * 100,
            'valorIR' => $ir,
            'rendimentoLiquido' => $rendimentoLiquido,
            'valorFinalLiquido' => $valorFinalLiquido,
            'evolucaoAnual' => $evolucaoAnual,
        ];
    }

    // =====================================================================
    // 4) RENDA FIXA (genérica) — valor investido a uma taxa anual, com IR
    //    regressivo. Serve pra qualquer título pós-fixado simples.
    // =====================================================================
    public static function calcularRendaFixa(float $valorInvestido, float $taxaAnual, int $prazoMeses): array
    {
        $taxaMensal = pow(1 + $taxaAnual / 100, 1 / 12) - 1;
        $valorFinalBruto = $valorInvestido * pow(1 + $taxaMensal, $prazoMeses);
        $rendimentoBruto = $valorFinalBruto - $valorInvestido;

        $aliquota = self::aliquotaIR((int) round($prazoMeses * 30.44));
        $ir = max(0, $rendimentoBruto) * $aliquota;
        $rendimentoLiquido = $rendimentoBruto - $ir;

        return [
            'valorFinalBruto' => $valorFinalBruto,
            'rendimentoBruto' => $rendimentoBruto,
            'aliquotaIR' => $aliquota * 100,
            'valorIR' => $ir,
            'rendimentoLiquido' => $rendimentoLiquido,
            'valorFinalLiquido' => $valorInvestido + $rendimentoLiquido,
        ];
    }

    // =====================================================================
    // 5) CDB — percentual do CDI aplicado sobre o CDI informado; reaproveita
    //    a mesma lógica de renda fixa por dentro.
    // =====================================================================
    public static function calcularCDB(float $valorInvestido, float $percentualCDI, float $cdiAnual, int $prazoMeses): array
    {
        $taxaAnualEfetiva = $cdiAnual * ($percentualCDI / 100);
        $resultado = self::calcularRendaFixa($valorInvestido, $taxaAnualEfetiva, $prazoMeses);
        $resultado['taxaAnualEfetiva'] = $taxaAnualEfetiva;

        return $resultado;
    }

    // =====================================================================
    // 6) APOSENTADORIA — quanto preciso guardar por mês pra viver de renda
    //    (regra da perpetuidade: patrimônio que sustenta a renda mensal
    //    desejada só com os juros, sem consumir o principal).
    // =====================================================================
    public static function calcularAposentadoria(int $idadeAtual, int $idadeAposentadoria, float $rendaMensalDesejada, float $taxaMensal): array
    {
        $meses = max(1, ($idadeAposentadoria - $idadeAtual) * 12);
        $i = $taxaMensal / 100;

        $patrimonioAlvo = $i > 0 ? $rendaMensalDesejada / $i : $rendaMensalDesejada * $meses;

        $aporteMensal = $i > 0
            ? $patrimonioAlvo * $i / (pow(1 + $i, $meses) - 1)
            : $patrimonioAlvo / $meses;

        return [
            'mesesAteAposentar' => $meses,
            'anosAteAposentar' => round($meses / 12, 1),
            'patrimonioAlvo' => $patrimonioAlvo,
            'aporteMensalNecessario' => $aporteMensal,
            'totalAportado' => $aporteMensal * $meses,
        ];
    }

    // =====================================================================
    // 7) EDUCAÇÃO — aporte mensal necessário pra chegar num valor-alvo
    //    (fórmula do valor futuro de uma série de aportes, invertida).
    // =====================================================================
    public static function calcularEducacao(float $custoEstimado, int $anosAteNecessario, float $taxaMensal): array
    {
        $meses = max(1, $anosAteNecessario * 12);
        $i = $taxaMensal / 100;

        $aporteMensal = $i > 0
            ? $custoEstimado * $i / (pow(1 + $i, $meses) - 1)
            : $custoEstimado / $meses;

        return [
            'meses' => $meses,
            'aporteMensalNecessario' => $aporteMensal,
            'totalAportado' => $aporteMensal * $meses,
            'rendimentoEstimado' => max(0, $custoEstimado - ($aporteMensal * $meses)),
        ];
    }

    // =====================================================================
    // 8) RESERVA DE EMERGÊNCIA — valor ideal (despesas x meses de segurança)
    //    e, se houver aporte mensal disponível, quanto tempo falta pra juntar.
    // =====================================================================
    public static function calcularReservaEmergencia(float $despesasMensais, int $mesesSeguranca, float $aporteMensalDisponivel): array
    {
        $valorIdeal = $despesasMensais * $mesesSeguranca;
        $mesesParaAtingir = $aporteMensalDisponivel > 0 ? (int) ceil($valorIdeal / $aporteMensalDisponivel) : null;

        return [
            'valorIdeal' => $valorIdeal,
            'mesesParaAtingir' => $mesesParaAtingir,
        ];
    }

    // =====================================================================
    // 9) VIAGEM — aporte mensal necessário até a data da viagem (com ou
    //    sem rendimento no período de acumulação).
    // =====================================================================
    public static function calcularViagem(float $custoViagem, int $mesesAteViagem, float $taxaMensal): array
    {
        $meses = max(1, $mesesAteViagem);
        $i = $taxaMensal / 100;

        $aporteMensal = $i > 0
            ? $custoViagem * $i / (pow(1 + $i, $meses) - 1)
            : $custoViagem / $meses;

        return [
            'aporteMensalNecessario' => $aporteMensal,
            'totalAportado' => $aporteMensal * $meses,
            'rendimentoEstimado' => max(0, $custoViagem - ($aporteMensal * $meses)),
        ];
    }

    // =====================================================================
    // 10) FINANCIAMENTO IMOBILIÁRIO — compara sistema Price (parcela fixa)
    //     e SAC (parcela decrescente), como sugerido no texto de apoio.
    // =====================================================================
    public static function calcularFinanciamentoImovel(float $valorImovel, float $entrada, int $prazoAnos, float $taxaAnual): array
    {
        $valorFinanciado = max(0, $valorImovel - $entrada);
        $n = max(1, $prazoAnos * 12);
        $i = pow(1 + $taxaAnual / 100, 1 / 12) - 1;

        // Sistema Price (parcela fixa)
        $parcelaPrice = $i > 0
            ? $valorFinanciado * ($i * pow(1 + $i, $n)) / (pow(1 + $i, $n) - 1)
            : $valorFinanciado / $n;
        $totalPrice = $parcelaPrice * $n;

        // Sistema SAC (amortização constante, juros e parcela decrescentes)
        $amortizacao = $valorFinanciado / $n;
        $primeiraParcelaSac = $amortizacao + ($valorFinanciado * $i);
        $ultimaParcelaSac = $amortizacao + ($amortizacao * $i);
        $totalJurosSac = $i * $amortizacao * $n * ($n + 1) / 2;
        $totalSac = $valorFinanciado + $totalJurosSac;

        return [
            'valorFinanciado' => $valorFinanciado,
            'parcelas' => $n,
            'price' => [
                'parcela' => $parcelaPrice,
                'totalPago' => $totalPrice,
                'totalJuros' => $totalPrice - $valorFinanciado,
            ],
            'sac' => [
                'primeiraParcela' => $primeiraParcelaSac,
                'ultimaParcela' => $ultimaParcelaSac,
                'totalPago' => $totalSac,
                'totalJuros' => $totalJurosSac,
            ],
        ];
    }

    // =====================================================================
    // 11) FINANCIAMENTO DE VEÍCULO — sistema Price (parcela fixa), o mais
    //     comum em financiamentos de carro/moto.
    // =====================================================================
    public static function calcularFinanciamentoVeiculo(float $valorVeiculo, float $entrada, int $prazoMeses, float $taxaMensal): array
    {
        $valorFinanciado = max(0, $valorVeiculo - $entrada);
        $n = max(1, $prazoMeses);
        $i = $taxaMensal / 100;

        $parcela = $i > 0
            ? $valorFinanciado * ($i * pow(1 + $i, $n)) / (pow(1 + $i, $n) - 1)
            : $valorFinanciado / $n;
        $totalPago = $parcela * $n;

        return [
            'valorFinanciado' => $valorFinanciado,
            'parcelas' => $n,
            'parcela' => $parcela,
            'totalPago' => $totalPago,
            'totalJuros' => $totalPago - $valorFinanciado,
        ];
    }
}
