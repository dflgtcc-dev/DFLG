<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\SimuladorHelper;

class SimuladorController extends Controller
{
    /**
     * Catálogo de calculadoras disponíveis: nome, ícone, categoria e resumo
     * (usado nos cards do hub) — RF10 / UC-10. A chave é o slug usado na URL:
     * /calculadoras/{slug}
     */
    private function calculadoras(): array
    {
        return [
            'tesouro-direto' => [
                'nome' => 'Tesouro Direto',
                'categoria' => 'Investimentos',
                'icone' => 'bi-bank',
                'resumo' => 'Veja o crescimento exponencial do dinheiro aplicado em títulos públicos.',
            ],
            'juros-compostos' => [
                'nome' => 'Juros Compostos',
                'categoria' => 'Investimentos',
                'icone' => 'bi-percent',
                'resumo' => 'Compare juros simples x compostos e entenda os "juros sobre juros".',
            ],
            'investimentos' => [
                'nome' => 'Investimentos com Aporte Mensal',
                'categoria' => 'Investimentos',
                'icone' => 'bi-graph-up-arrow',
                'resumo' => 'Projete o crescimento de uma carteira com valor inicial e aportes mensais.',
            ],
            'renda-fixa' => [
                'nome' => 'Renda Fixa',
                'categoria' => 'Investimentos',
                'icone' => 'bi-currency-dollar',
                'resumo' => 'Estime o rendimento líquido de qualquer aplicação pós-fixada.',
            ],
            'cdb' => [
                'nome' => 'CDB',
                'categoria' => 'Investimentos',
                'icone' => 'bi-credit-card',
                'resumo' => 'Calcule o retorno de um CDB a partir do percentual do CDI.',
            ],
            'aposentadoria' => [
                'nome' => 'Aposentadoria',
                'categoria' => 'Planejamento',
                'icone' => 'bi-piggy-bank',
                'resumo' => 'Descubra quanto guardar por mês para viver de renda no futuro.',
            ],
            'educacao' => [
                'nome' => 'Educação',
                'categoria' => 'Planejamento',
                'icone' => 'bi-mortarboard',
                'resumo' => 'Planeje o quanto guardar todo mês para custear estudos.',
            ],
            'reserva-emergencia' => [
                'nome' => 'Reserva de Emergência',
                'categoria' => 'Planejamento',
                'icone' => 'bi-heart',
                'resumo' => 'Calcule o valor ideal da sua reserva e em quanto tempo formá-la.',
            ],
            'viagem' => [
                'nome' => 'Planejamento de Viagem',
                'categoria' => 'Planejamento',
                'icone' => 'bi-airplane',
                'resumo' => 'Saiba quanto guardar por mês para a viagem dos sonhos.',
            ],
            'financiamento-imovel' => [
                'nome' => 'Financiamento Imobiliário',
                'categoria' => 'Financiamentos',
                'icone' => 'bi-house',
                'resumo' => 'Compare os sistemas Price e SAC na hora de financiar um imóvel.',
            ],
            'financiamento-veiculo' => [
                'nome' => 'Financiamento de Veículo',
                'categoria' => 'Financiamentos',
                'icone' => 'bi-car-front',
                'resumo' => 'Calcule a parcela e o total de juros ao financiar um carro ou moto.',
            ],
        ];
    }

    /**
     * Texto de apoio + passo a passo de cada calculadora — exibidos na
     * página própria de cada uma, no formato "editorial" pedido pelo time
     * (inspirado no Investidor Sardinha, com a cara do DFLG).
     */
    private function textosApoio(): array
    {
        return [
            'tesouro-direto' => [
                'titulo' => 'Calculadora de Tesouro Direto',
                'texto' => 'Mostra como o dinheiro cresce de forma exponencial ao ser aplicado em títulos públicos (Tesouro Selic, Prefixado ou IPCA+), incluindo o desconto do Imposto de Renda regressivo no resgate.',
                'passos' => [
                    'Escolha o tipo de título: Selic (pós-fixado, acompanha a taxa básica de juros), Prefixado (taxa fixa contratada) ou IPCA+ (protege da inflação).',
                    'Informe a taxa anual do título — você encontra esse número no site do Tesouro Direto no momento da compra.',
                    'Digite o valor inicial que pretende investir e, se for o caso, um aporte mensal fixo.',
                    'Defina o prazo em anos e clique em Calcular para ver o valor final líquido de IR e a evolução ano a ano.',
                ],
                'formula' => [
                    'expressao' => 'M = C × (1 + i)ᵗ + aportes acumulados',
                    'legenda' => [
                        'M' => 'montante final acumulado',
                        'C' => 'capital inicial investido',
                        'i' => 'taxa de juros equivalente ao período (aqui, convertida da taxa anual para mensal)',
                        't' => 'número de períodos (meses) que o dinheiro fica investido',
                    ],
                ],
                'aplicacoes' => [
                    'Reserva de emergência de baixo risco (Tesouro Selic), já que tem liquidez diária.',
                    'Objetivos de médio/longo prazo com taxa travada (Tesouro Prefixado).',
                    'Proteção do poder de compra contra a inflação no longo prazo (Tesouro IPCA+).',
                    'É considerado o investimento de menor risco do país, pois quem garante o pagamento é o governo federal.',
                ],
                'exemplo' => 'Investindo R$ 1.000,00 iniciais + R$ 200,00 por mês no Tesouro Selic a 10,75% ao ano, em 5 anos você acumularia um valor bem acima do total aportado — e a calculadora já mostra esse rendimento líquido, descontando o IR regressivo (que cai de 22,5% para 15% quanto mais tempo o dinheiro fica aplicado).',
            ],
            'juros-compostos' => [
                'titulo' => 'Calculadora de Juros Compostos',
                'texto' => 'Os juros compostos são conhecidos como "juros sobre juros": diferente dos juros simples, que incidem sempre sobre o valor inicial, os juros compostos incidem sobre o montante já acumulado (capital + juros anteriores). É por isso que Einstein teria chamado os juros compostos de "a oitava maravilha do mundo" — quem entende, ganha; quem não entende, paga.',
                'passos' => [
                    'Informe o valor inicial que você tem disponível para investir.',
                    'Digite a taxa de juros mensal esperada.',
                    'Escolha o período em meses que deseja simular.',
                    'Clique em Calcular e compare os dois resultados: observe como a diferença entre juros simples e compostos cresce junto com o tempo — esse é o efeito da capitalização composta.',
                ],
                'formula' => [
                    'expressao' => 'M = C × (1 + i)ᵗ',
                    'legenda' => [
                        'M' => 'montante final (valor acumulado ao fim do período)',
                        'C' => 'capital inicial aplicado',
                        'i' => 'taxa de juros do período, em formato decimal (ex: 1% = 0,01)',
                        't' => 'número de períodos (a taxa e o tempo precisam estar na mesma unidade: se a taxa é mensal, o tempo deve estar em meses)',
                    ],
                ],
                'aplicacoes' => [
                    'Investimentos de renda fixa (CDB, Tesouro Direto, LCI/LCA), que remuneram com juros compostos.',
                    'Ações, quando os dividendos recebidos são reinvestidos ao longo do tempo.',
                    'Financiamentos e empréstimos — mas aqui o efeito "joga contra" quem deve, fazendo a dívida crescer cada vez mais rápido se não for paga.',
                    'Contas em atraso, onde os juros compostos podem transformar uma dívida pequena numa bola de neve.',
                ],
                'exemplo' => 'Aplicando R$ 5.000,00 a 1% ao mês, sem novos aportes: em 5 anos os juros simples renderiam R$ 3.000,00, enquanto os compostos renderiam R$ 4.083,48. Em 30 anos essa diferença explode: R$ 18.000,00 no juros simples contra R$ 174.748,21 no composto — mostrando por que o tempo é o maior aliado de quem investe cedo.',
            ],
            'investimentos' => [
                'titulo' => 'Calculadora de Investimentos com Aporte Mensal',
                'texto' => 'Calcula o crescimento do seu patrimônio quando, além do valor inicial, você investe uma quantia fixa todo mês, a uma taxa de retorno mensal esperada. É a calculadora mais próxima da vida real de quem investe recorrentemente, e não só uma vez.',
                'passos' => [
                    'Informe o valor inicial que já possui investido (pode ser zero).',
                    'Defina o valor do aporte mensal que pretende fazer.',
                    'Digite a taxa de retorno mensal esperada para esse investimento.',
                    'Escolha o período em meses e veja o total investido, o rendimento e o valor final projetado.',
                ],
                'formula' => [
                    'expressao' => 'Mₜ = Mₜ₋₁ × (1 + i) + aporte, repetido mês a mês até o fim do período',
                    'legenda' => [
                        'Mₜ' => 'montante acumulado no mês t',
                        'i' => 'taxa de retorno mensal esperada',
                        'aporte' => 'valor investido todo mês, somado depois de aplicar os juros do mês',
                    ],
                ],
                'aplicacoes' => [
                    'Planejar carteiras diversificadas com aporte recorrente (fundos, ações, ETFs, renda fixa).',
                    'Comparar cenários: "e se eu aumentar meu aporte mensal em R$ 100?"',
                    'Visualizar o efeito da disciplina de investir todo mês, mesmo com valores pequenos.',
                ],
                'exemplo' => 'Começando com R$ 10.000,00 e aportando R$ 500,00 por mês a 0,8% ao mês, em 1 ano você já teria investido R$ 16.000,00 no total, mas o valor final seria maior que isso por causa dos juros compostos sobre os aportes.',
            ],
            'renda-fixa' => [
                'titulo' => 'Calculadora de Renda Fixa',
                'texto' => 'Simula qualquer aplicação de renda fixa pós-fixada simples a partir de uma taxa anual, mostrando o rendimento bruto, o desconto de IR regressivo e o valor líquido no resgate. Serve como uma calculadora "genérica" pra quem quer comparar diferentes ofertas de bancos e corretoras.',
                'passos' => [
                    'Informe o valor que pretende investir.',
                    'Digite a taxa anual oferecida pela aplicação (verifique no extrato ou na oferta do banco/corretora).',
                    'Defina o prazo em meses que o dinheiro ficará aplicado.',
                    'Veja o rendimento líquido já com o IR regressivo descontado automaticamente.',
                ],
                'formula' => [
                    'expressao' => 'M = C × (1 + i)ᵗ, com IR regressivo aplicado sobre o rendimento no resgate',
                    'legenda' => [
                        'C' => 'valor investido',
                        'i' => 'taxa de juros equivalente mensal (derivada da taxa anual informada)',
                        't' => 'prazo em meses',
                        'IR' => 'alíquota regressiva: 22,5% (até 180 dias), 20% (até 360), 17,5% (até 720) e 15% (acima de 720 dias)',
                    ],
                ],
                'aplicacoes' => [
                    'Comparar rapidamente diferentes ofertas de renda fixa antes de decidir onde investir.',
                    'Entender o impacto real do Imposto de Renda no rendimento líquido, que muita gente esquece de considerar.',
                    'Planejar o prazo ideal de uma aplicação para pagar menos imposto (quanto mais tempo, menor a alíquota).',
                ],
                'exemplo' => 'R$ 5.000,00 aplicados a 11,5% ao ano por 24 meses rendem um valor bruto considerável, mas o IR de 17,5% (faixa de 1 a 2 anos) reduz esse ganho — a calculadora já mostra o valor líquido que realmente cai na sua conta.',
            ],
            'cdb' => [
                'titulo' => 'Calculadora de CDB',
                'texto' => 'Calcula o rendimento de um CDB (Certificado de Depósito Bancário) a partir do percentual do CDI oferecido pelo banco, já descontando o Imposto de Renda regressivo. É um dos investimentos de renda fixa mais populares no Brasil.',
                'passos' => [
                    'Informe o valor que pretende investir no CDB.',
                    'Digite o percentual do CDI oferecido pelo banco (ex: 110%, 120%).',
                    'Preencha o CDI anual atual (você encontra essa taxa atualizada em qualquer corretora).',
                    'Defina o prazo em meses e veja o rendimento líquido no vencimento.',
                ],
                'formula' => [
                    'expressao' => 'taxa efetiva = CDI anual × (% do CDI ÷ 100), depois aplicada como juros compostos até o vencimento',
                    'legenda' => [
                        'CDI' => 'Certificado de Depósito Interbancário, taxa que serve de referência pra maioria dos investimentos de renda fixa pós-fixados',
                        '% do CDI' => 'percentual do CDI que o banco promete pagar (quanto maior, melhor a oferta)',
                    ],
                ],
                'aplicacoes' => [
                    'Reserva de emergência em bancos que oferecem liquidez diária.',
                    'Diversificação de carteira em renda fixa, com garantia do FGC até R$ 250 mil por CPF/instituição.',
                    'Comparar CDBs de bancos diferentes só olhando pro percentual do CDI oferecido.',
                ],
                'exemplo' => 'Um CDB de 110% do CDI, com o CDI a 10,75% ao ano, equivale a uma taxa efetiva de aproximadamente 11,83% ao ano — a calculadora já faz essa conversão e mostra o valor líquido no prazo escolhido.',
            ],
            'aposentadoria' => [
                'titulo' => 'Calculadora de Aposentadoria',
                'texto' => 'Estima o patrimônio necessário para viver de renda (sem consumir o principal) e quanto você precisaria investir por mês, a partir de hoje, para chegar lá. A lógica usada é a de uma "renda perpétua": seu patrimônio investido gera juros suficientes pra pagar sua renda mensal, sem nunca precisar mexer no valor principal.',
                'passos' => [
                    'Informe sua idade atual e a idade em que pretende se aposentar.',
                    'Digite a renda mensal que gostaria de ter na aposentadoria, em valores de hoje.',
                    'Informe a taxa de retorno mensal esperada dos seus investimentos no longo prazo.',
                    'Veja o patrimônio-alvo necessário e o aporte mensal que precisa fazer até lá.',
                ],
                'formula' => [
                    'expressao' => 'Patrimônio-alvo = renda mensal desejada ÷ taxa mensal; Aporte mensal = Patrimônio-alvo × i ÷ [(1 + i)ⁿ − 1]',
                    'legenda' => [
                        'n' => 'número de meses até a aposentadoria',
                        'i' => 'taxa de retorno mensal esperada',
                    ],
                ],
                'aplicacoes' => [
                    'Planejamento de longo prazo pra quem quer construir independência financeira.',
                    'Definir quanto guardar por mês hoje pra manter o padrão de vida no futuro.',
                    'Entender o efeito de começar mais cedo: quanto antes você começa, menor o aporte mensal necessário.',
                ],
                'exemplo' => 'Se você tem 25 anos, quer se aposentar aos 60 com R$ 5.000,00 de renda mensal e espera 0,6% ao mês de retorno, a calculadora mostra o patrimônio-alvo (baseado na renda perpétua) e o quanto precisa guardar todo mês, a partir de hoje, pra chegar lá.',
            ],
            'educacao' => [
                'titulo' => 'Calculadora de Planejamento para Educação',
                'texto' => 'Ajuda a planejar e economizar para a educação — escola particular, intercâmbio ou faculdade — calculando o aporte mensal necessário até a data prevista, usando a mesma lógica de "valor futuro de uma série de aportes" usada em qualquer meta financeira.',
                'passos' => [
                    'Estime o custo total que vai precisar (pesquise valores de escolas, cursos ou faculdades hoje).',
                    'Informe em quantos anos esse valor será necessário.',
                    'Digite a taxa de retorno mensal esperada dos seus investimentos.',
                    'Veja o aporte mensal necessário para chegar ao valor desejado no prazo.',
                ],
                'formula' => [
                    'expressao' => 'Aporte mensal = Custo estimado × i ÷ [(1 + i)ⁿ − 1]',
                    'legenda' => [
                        'i' => 'taxa de retorno mensal esperada',
                        'n' => 'número de meses até o custo se tornar necessário',
                    ],
                ],
                'aplicacoes' => [
                    'Planejar a poupança para escola, faculdade ou intercâmbio de filhos.',
                    'Investir hoje em vez de recorrer a financiamento estudantil no futuro (evitando juros contra você).',
                    'Entender quanto o tempo disponível reduz o esforço mensal necessário.',
                ],
                'exemplo' => 'Se você estima gastar R$ 60.000,00 com a faculdade em 10 anos e espera 0,6% ao mês de retorno, a calculadora mostra exatamente quanto precisa guardar todo mês pra chegar nesse valor, já contando com o rendimento dos investimentos.',
            ],
            'reserva-emergencia' => [
                'titulo' => 'Calculadora de Reserva de Emergência',
                'texto' => 'Calcula o valor ideal da sua reserva de emergência com base no seu custo de vida mensal, e estima em quanto tempo você consegue formá-la. A reserva de emergência é considerada a base de qualquer planejamento financeiro, antes de começar a investir em ativos de maior risco.',
                'passos' => [
                    'Some todas as suas despesas mensais fixas (aluguel, contas, alimentação, etc.).',
                    'Escolha quantos meses de segurança deseja ter guardado — o recomendado geralmente é entre 6 e 12 meses.',
                    'Informe quanto consegue guardar por mês hoje.',
                    'Veja o valor ideal da reserva e o tempo estimado para completá-la.',
                ],
                'formula' => [
                    'expressao' => 'Valor ideal = despesas mensais × meses de segurança; Tempo pra atingir = valor ideal ÷ aporte mensal',
                    'legenda' => [
                        'meses de segurança' => 'quantos meses você quer conseguir se manter sem renda, em caso de imprevisto',
                    ],
                ],
                'aplicacoes' => [
                    'Proteção contra imprevistos: perda de emprego, problemas de saúde, reparos urgentes.',
                    'Evitar recorrer a empréstimos ou cartão de crédito rotativo em emergências.',
                    'Servir de "colchão" antes de começar a investir em ativos de maior risco, como ações.',
                ],
                'exemplo' => 'Com R$ 3.000,00 de despesas mensais e a meta de 6 meses de segurança, o valor ideal da reserva é R$ 18.000,00 — guardando R$ 500,00 por mês, você atinge essa meta em 36 meses.',
            ],
            'viagem' => [
                'titulo' => 'Calculadora de Planejamento de Viagem',
                'texto' => 'Calcula quanto você precisa guardar por mês para realizar a viagem dos sonhos até a data planejada, considerando (ou não) rendimento no período em que o dinheiro fica guardado.',
                'passos' => [
                    'Pesquise e informe o custo total estimado da viagem (passagens, hospedagem, passeios).',
                    'Digite quantos meses faltam até a data da viagem.',
                    'Se for guardar o dinheiro em algum investimento, informe a taxa de retorno mensal esperada (ou deixe zero).',
                    'Veja o quanto precisa guardar por mês para chegar ao valor da viagem no prazo.',
                ],
                'formula' => [
                    'expressao' => 'Aporte mensal = Custo da viagem × i ÷ [(1 + i)ⁿ − 1], ou Custo ÷ n se não houver rendimento',
                    'legenda' => [
                        'n' => 'número de meses até a viagem',
                        'i' => 'taxa de retorno mensal esperada do investimento onde o dinheiro ficará guardado',
                    ],
                ],
                'aplicacoes' => [
                    'Planejar metas de curto/médio prazo de forma organizada, em vez de parcelar a viagem no cartão.',
                    'Visualizar quanto o rendimento de um investimento reduz o esforço mensal necessário.',
                    'Evitar juros do parcelamento, guardando o dinheiro com antecedência.',
                ],
                'exemplo' => 'Para uma viagem de R$ 8.000,00 daqui a 12 meses, guardando o dinheiro a 0,6% ao mês, você precisa aportar um pouco menos por mês do que guardaria "no colchão" — a diferença é o rendimento que o dinheiro gera enquanto você economiza.',
            ],
            'financiamento-imovel' => [
                'titulo' => 'Calculadora de Financiamento Imobiliário',
                'texto' => 'Simula o financiamento da casa própria comparando os dois sistemas de amortização mais usados no Brasil: Price (parcela fixa) e SAC (parcela decrescente). A escolha entre os dois muda bastante o total pago ao final do contrato.',
                'passos' => [
                    'Informe o valor total do imóvel que deseja financiar.',
                    'Digite o valor da entrada que já possui disponível.',
                    'Escolha o prazo do financiamento em anos.',
                    'Informe a taxa de juros anual do banco e compare o total pago nos sistemas Price e SAC.',
                ],
                'formula' => [
                    'expressao' => 'Price: parcela = VF × [i × (1+i)ⁿ] ÷ [(1+i)ⁿ − 1] (fixa) | SAC: amortização constante, juros e parcela decrescentes',
                    'legenda' => [
                        'VF' => 'valor financiado (valor do imóvel menos a entrada)',
                        'i' => 'taxa de juros mensal',
                        'n' => 'número de parcelas',
                    ],
                ],
                'aplicacoes' => [
                    'Decidir entre Price (parcelas fixas, mais fácil de planejar o orçamento) e SAC (parcelas decrescentes, menor total pago no fim).',
                    'Simular diferentes valores de entrada e ver o impacto na parcela mensal.',
                    'Comparar propostas de bancos diferentes antes de assinar o contrato.',
                ],
                'exemplo' => 'Financiando R$ 280.000,00 (imóvel de R$ 350 mil com R$ 70 mil de entrada) em 30 anos a 10,5% ao ano: no Price a parcela é fixa do início ao fim; no SAC a primeira parcela é mais alta, mas vai diminuindo — e o total pago costuma ser menor.',
            ],
            'financiamento-veiculo' => [
                'titulo' => 'Calculadora de Financiamento de Veículo',
                'texto' => 'Calcula as parcelas fixas (sistema Price) do financiamento de um carro ou moto, e o total de juros pago no fim do contrato — o sistema mais comum usado por bancos e financeiras nesse tipo de financiamento.',
                'passos' => [
                    'Informe o valor do veículo que deseja financiar.',
                    'Digite o valor da entrada que possui disponível.',
                    'Escolha o prazo do financiamento em meses.',
                    'Informe a taxa de juros mensal oferecida pelo banco e veja o valor da parcela e o total de juros.',
                ],
                'formula' => [
                    'expressao' => 'parcela = VF × [i × (1+i)ⁿ] ÷ [(1+i)ⁿ − 1]',
                    'legenda' => [
                        'VF' => 'valor financiado (valor do veículo menos a entrada)',
                        'i' => 'taxa de juros mensal',
                        'n' => 'número de parcelas (meses)',
                    ],
                ],
                'aplicacoes' => [
                    'Comparar propostas de financiamento de diferentes bancos e concessionárias.',
                    'Avaliar o impacto de dar uma entrada maior no valor final da parcela.',
                    'Entender quanto do valor total pago é só juros — muitas vezes uma fração enorme do preço do veículo.',
                ],
                'exemplo' => 'Financiando R$ 60.000,00 (veículo de R$ 80 mil com R$ 20 mil de entrada) em 48 meses a 1,5% ao mês, boa parte do total pago acaba sendo juros — a calculadora mostra exatamente quanto.',
            ],
        ];
    }

    /**
     * /calculadoras — hub com todas as calculadoras organizadas por categoria.
     */
    public function index()
    {
        $calculadoras = $this->calculadoras();

        $porCategoria = [];
        foreach ($calculadoras as $slug => $c) {
            $porCategoria[$c['categoria']][$slug] = $c;
        }

        $this->view('simulador/hub', [
            'activePage' => 'calculators',
            'porCategoria' => $porCategoria,
        ]);
    }

    /**
     * /calculadoras/{slug} — página própria de cada calculadora, com
     * explicação, passo a passo e o formulário/resultado.
     */
    public function calculadora($slug)
    {
        $calculadoras = $this->calculadoras();

        if (!array_key_exists($slug, $calculadoras)) {
            header('Location: ' . URL_BASE . '/calculadoras');
            exit;
        }

        $g = fn(string $chave, $padrao) => isset($_GET[$chave]) && $_GET[$chave] !== '' ? $_GET[$chave] : $padrao;

        $inputs = [];
        $resultado = [];

        switch ($slug) {
            case 'tesouro-direto':
                $inputs = [
                    'tipoTitulo' => $g('tipoTitulo', 'selic'),
                    'valorInicial' => (float) $g('valorInicial', 1000),
                    'aporteMensal' => (float) $g('aporteMensal', 200),
                    'taxaAnual' => (float) $g('taxaAnual', 10.75),
                    'prazoAnos' => (int) $g('prazoAnos', 5),
                ];
                $resultado = SimuladorHelper::calcularTesouroDireto(
                    $inputs['valorInicial'],
                    $inputs['aporteMensal'],
                    $inputs['taxaAnual'],
                    $inputs['prazoAnos']
                );
                break;

            case 'juros-compostos':
                $inputs = [
                    'valorInicial' => (float) $g('valorInicial', 5000),
                    'taxaMensal' => (float) $g('taxaMensal', 1.0),
                    'periodoMeses' => (int) $g('periodoMeses', 24),
                ];
                $resultado = SimuladorHelper::calcularJurosCompostos(
                    $inputs['valorInicial'],
                    $inputs['taxaMensal'],
                    $inputs['periodoMeses']
                );
                break;

            case 'investimentos':
                $inputs = [
                    'valorInicial' => (float) $g('valorInicial', 10000),
                    'aporteMensal' => (float) $g('aporteMensal', 500),
                    'taxaMensal' => (float) $g('taxaMensal', 0.8),
                    'periodoMeses' => (int) $g('periodoMeses', 12),
                ];
                $resultado = SimuladorHelper::calcularInvestimento(
                    $inputs['valorInicial'],
                    $inputs['aporteMensal'],
                    $inputs['taxaMensal'],
                    $inputs['periodoMeses']
                );
                break;

            case 'renda-fixa':
                $inputs = [
                    'valorInvestido' => (float) $g('valorInvestido', 5000),
                    'taxaAnual' => (float) $g('taxaAnual', 11.5),
                    'prazoMeses' => (int) $g('prazoMeses', 24),
                ];
                $resultado = SimuladorHelper::calcularRendaFixa(
                    $inputs['valorInvestido'],
                    $inputs['taxaAnual'],
                    $inputs['prazoMeses']
                );
                break;

            case 'cdb':
                $inputs = [
                    'valorInvestido' => (float) $g('valorInvestido', 5000),
                    'percentualCDI' => (float) $g('percentualCDI', 110),
                    'cdiAnual' => (float) $g('cdiAnual', 10.75),
                    'prazoMeses' => (int) $g('prazoMeses', 24),
                ];
                $resultado = SimuladorHelper::calcularCDB(
                    $inputs['valorInvestido'],
                    $inputs['percentualCDI'],
                    $inputs['cdiAnual'],
                    $inputs['prazoMeses']
                );
                break;

            case 'aposentadoria':
                $inputs = [
                    'idadeAtual' => (int) $g('idadeAtual', 25),
                    'idadeAposentadoria' => (int) $g('idadeAposentadoria', 60),
                    'rendaMensalDesejada' => (float) $g('rendaMensalDesejada', 5000),
                    'taxaMensal' => (float) $g('taxaMensal', 0.6),
                ];
                $resultado = SimuladorHelper::calcularAposentadoria(
                    $inputs['idadeAtual'],
                    $inputs['idadeAposentadoria'],
                    $inputs['rendaMensalDesejada'],
                    $inputs['taxaMensal']
                );
                break;

            case 'educacao':
                $inputs = [
                    'custoEstimado' => (float) $g('custoEstimado', 60000),
                    'anosAteNecessario' => (int) $g('anosAteNecessario', 10),
                    'taxaMensal' => (float) $g('taxaMensal', 0.6),
                ];
                $resultado = SimuladorHelper::calcularEducacao(
                    $inputs['custoEstimado'],
                    $inputs['anosAteNecessario'],
                    $inputs['taxaMensal']
                );
                break;

            case 'reserva-emergencia':
                $inputs = [
                    'despesasMensais' => (float) $g('despesasMensais', 3000),
                    'mesesSeguranca' => (int) $g('mesesSeguranca', 6),
                    'aporteMensalDisponivel' => (float) $g('aporteMensalDisponivel', 500),
                ];
                $resultado = SimuladorHelper::calcularReservaEmergencia(
                    $inputs['despesasMensais'],
                    $inputs['mesesSeguranca'],
                    $inputs['aporteMensalDisponivel']
                );
                break;

            case 'viagem':
                $inputs = [
                    'custoViagem' => (float) $g('custoViagem', 8000),
                    'mesesAteViagem' => (int) $g('mesesAteViagem', 12),
                    'taxaMensal' => (float) $g('taxaMensal', 0.6),
                ];
                $resultado = SimuladorHelper::calcularViagem(
                    $inputs['custoViagem'],
                    $inputs['mesesAteViagem'],
                    $inputs['taxaMensal']
                );
                break;

            case 'financiamento-imovel':
                $inputs = [
                    'valorImovel' => (float) $g('valorImovel', 350000),
                    'entrada' => (float) $g('entrada', 70000),
                    'prazoAnos' => (int) $g('prazoAnos', 30),
                    'taxaAnual' => (float) $g('taxaAnual', 10.5),
                ];
                $resultado = SimuladorHelper::calcularFinanciamentoImovel(
                    $inputs['valorImovel'],
                    $inputs['entrada'],
                    $inputs['prazoAnos'],
                    $inputs['taxaAnual']
                );
                break;

            case 'financiamento-veiculo':
                $inputs = [
                    'valorVeiculo' => (float) $g('valorVeiculo', 80000),
                    'entrada' => (float) $g('entrada', 20000),
                    'prazoMeses' => (int) $g('prazoMeses', 48),
                    'taxaMensal' => (float) $g('taxaMensal', 1.5),
                ];
                $resultado = SimuladorHelper::calcularFinanciamentoVeiculo(
                    $inputs['valorVeiculo'],
                    $inputs['entrada'],
                    $inputs['prazoMeses'],
                    $inputs['taxaMensal']
                );
                break;
        }

        // Sugestão de outras calculadoras da mesma categoria (rodapé "veja também")
        $categoriaAtual = $calculadoras[$slug]['categoria'];
        $relacionadas = array_filter($calculadoras, function ($c, $s) use ($categoriaAtual, $slug) {
            return $c['categoria'] === $categoriaAtual && $s !== $slug;
        }, ARRAY_FILTER_USE_BOTH);

        $this->view('simulador/calculadora', [
            'activePage' => 'calculators',
            'calculadoras' => $calculadoras,
            'info' => $this->textosApoio()[$slug],
            'slug' => $slug,
            'inputs' => $inputs,
            'resultado' => $resultado,
            'relacionadas' => $relacionadas,
        ]);
    }
}
