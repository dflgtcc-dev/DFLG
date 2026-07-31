<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\SimuladorHelper;

class SimuladorController extends Controller
{
    /**
     * Lista de calculadoras disponíveis (equivalente ao array "simulators"
     * do SimulatorPage.tsx). Só a de "investimento" está funcional por
     * enquanto — as demais aparecem como "em desenvolvimento", igual no Figma.
     */
    private function simuladores(): array
    {
        return [
            'investimento' => ['nome' => 'Investimento', 'descricao' => 'Calcule investimentos com aportes mensais', 'icone' => 'bi-graph-up-arrow', 'categoria' => 'Investimentos'],
            'jurosCompostos' => ['nome' => 'Juros Compostos', 'descricao' => 'Veja o poder dos juros compostos ao longo do tempo', 'icone' => 'bi-percent', 'categoria' => 'Investimentos'],
            'rendaFixa' => ['nome' => 'Renda Fixa', 'descricao' => 'Simule investimentos em renda fixa', 'icone' => 'bi-currency-dollar', 'categoria' => 'Investimentos'],
            'tesouro' => ['nome' => 'Tesouro Direto', 'descricao' => 'Calcule rendimentos do Tesouro Direto', 'icone' => 'bi-bank', 'categoria' => 'Investimentos'],
            'cdb' => ['nome' => 'CDB', 'descricao' => 'Simule investimentos em CDB', 'icone' => 'bi-credit-card', 'categoria' => 'Investimentos'],
            'aposentadoria' => ['nome' => 'Aposentadoria', 'descricao' => 'Planeje sua aposentadoria e independência financeira', 'icone' => 'bi-piggy-bank', 'categoria' => 'Planejamento'],
            'educacao' => ['nome' => 'Educação', 'descricao' => 'Planeje investimentos para educação dos filhos', 'icone' => 'bi-mortarboard', 'categoria' => 'Planejamento'],
            'emergencia' => ['nome' => 'Reserva de Emergência', 'descricao' => 'Calcule sua reserva de emergência ideal', 'icone' => 'bi-heart', 'categoria' => 'Planejamento'],
            'viagem' => ['nome' => 'Planejamento de Viagem', 'descricao' => 'Economize para realizar a viagem dos sonhos', 'icone' => 'bi-airplane', 'categoria' => 'Planejamento'],
            'financiamentoImovel' => ['nome' => 'Financiamento Imobiliário', 'descricao' => 'Calcule parcelas e custo total do financiamento', 'icone' => 'bi-house', 'categoria' => 'Financiamentos'],
            'financiamentoVeiculo' => ['nome' => 'Financiamento de Veículo', 'descricao' => 'Simule financiamento de carros e motos', 'icone' => 'bi-car-front', 'categoria' => 'Financiamentos'],
        ];
    }

    /** Textos de apoio ("Como usar") exibidos nas calculadoras ainda não implementadas. */
    private function textosApoio(): array
    {
        return [
            'jurosCompostos' => ['titulo' => 'Calculadora de Juros Compostos', 'texto' => 'Os juros compostos são conhecidos como a "oitava maravilha do mundo". Esta calculadora mostra como seu dinheiro cresce exponencialmente ao longo do tempo.', 'dica' => 'Defina um valor inicial, a taxa de juros anual e o período. Veja como os juros sobre juros multiplicam seu patrimônio.'],
            'aposentadoria' => ['titulo' => 'Planejamento de Aposentadoria', 'texto' => 'Planeje sua aposentadoria e descubra quanto precisa investir mensalmente para alcançar a independência financeira.', 'dica' => 'Informe sua idade atual, quando quer se aposentar, quanto precisa de renda mensal e a taxa de retorno esperada.'],
            'financiamentoImovel' => ['titulo' => 'Financiamento Imobiliário', 'texto' => 'Simule o financiamento da casa própria e veja o valor das parcelas, juros pagos e custo total.', 'dica' => 'Digite o valor do imóvel, entrada, prazo em anos e taxa de juros. Compare sistemas SAC e Price para escolher o melhor.'],
            'financiamentoVeiculo' => ['titulo' => 'Financiamento de Veículo', 'texto' => 'Calcule as parcelas do financiamento do seu carro ou moto e tome a melhor decisão.', 'dica' => 'Insira o valor do veículo, entrada que possui, prazo desejado e taxa de juros oferecida pelo banco.'],
            'educacao' => ['titulo' => 'Planejamento para Educação', 'texto' => 'Planeje e economize para a educação dos seus filhos, seja escola particular ou faculdade.', 'dica' => 'Defina o custo estimado da educação, quando será necessário e quanto pode investir mensalmente.'],
            'emergencia' => ['titulo' => 'Reserva de Emergência', 'texto' => 'Calcule o valor ideal da sua reserva de emergência baseado no seu custo de vida mensal.', 'dica' => 'Informe suas despesas mensais e quantos meses de segurança deseja (recomendado: 6 a 12 meses).'],
            'viagem' => ['titulo' => 'Planejamento de Viagem', 'texto' => 'Realize a viagem dos seus sonhos! Calcule quanto precisa economizar mensalmente.', 'dica' => 'Digite o custo total da viagem, quando pretende viajar e quanto pode guardar por mês.'],
            'rendaFixa' => ['titulo' => 'Simulador de Renda Fixa', 'texto' => 'Simule investimentos em títulos de renda fixa e compare diferentes opções.', 'dica' => 'Escolha o tipo de título, valor a investir, prazo e taxa oferecida. Veja o rendimento líquido após impostos.'],
            'tesouro' => ['titulo' => 'Tesouro Direto', 'texto' => 'Simule investimentos nos diferentes títulos do Tesouro Direto (Selic, IPCA+, Prefixado).', 'dica' => 'Selecione o tipo de título do Tesouro, valor do investimento e prazo até o vencimento.'],
            'cdb' => ['titulo' => 'CDB - Certificado de Depósito Bancário', 'texto' => 'Calcule o rendimento de CDBs e compare diferentes ofertas de bancos.', 'dica' => 'Informe o percentual do CDI oferecido, valor do investimento e prazo de vencimento.'],
        ];
    }

    public function index()
    {
        $simuladores = $this->simuladores();

        $tipoAtivo = $_GET['tipo'] ?? 'investimento';
        if (!array_key_exists($tipoAtivo, $simuladores)) {
            $tipoAtivo = 'investimento';
        }

        // --- Calculadora de Investimentos (a única funcional por enquanto) ---
        $valorInicial = isset($_GET['valorInicial']) ? (float) $_GET['valorInicial'] : 10000.0;
        $aporteMensal = isset($_GET['aporteMensal']) ? (float) $_GET['aporteMensal'] : 500.0;
        $taxaMensal = isset($_GET['taxaMensal']) ? (float) $_GET['taxaMensal'] : 0.8;
        $periodoMeses = isset($_GET['periodoMeses']) ? (int) $_GET['periodoMeses'] : 12;

        $resultado = SimuladorHelper::calcularInvestimento($valorInicial, $aporteMensal, $taxaMensal, $periodoMeses);

        $this->view('simulador/index', [
            'activePage' => 'calculators',
            'simuladores' => $simuladores,
            'textosApoio' => $this->textosApoio(),
            'tipoAtivo' => $tipoAtivo,
            'valorInicial' => $valorInicial,
            'aporteMensal' => $aporteMensal,
            'taxaMensal' => $taxaMensal,
            'periodoMeses' => $periodoMeses,
            'resultado' => $resultado,
        ]);
    }
}
