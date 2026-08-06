<?php 

namespace app\helpers;

class Validador {

    private array $erros = [];

    public function obrigatorio(string $campo, mixed $valor, ?string $mensagem = null) {

        //! = = 
        if (empty($valor) && $valor !== '0') {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} é obrigatório";
        }

        return $this;

    }

    /** Valida se o valor bate com um padrão regex (ex: nickname sem espaços/caracteres especiais). */
    public function formato(string $campo, mixed $valor, string $regex, ?string $mensagem = null) {

        if (!empty($valor) && !preg_match($regex, (string) $valor)) {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} está em um formato inválido";
        }

        return $this;

    }

    /** Valida um CPF ou CNPJ pelo dígito verificador oficial (usa DocumentoHelper). */
    public function cpfOuCnpj(string $campo, mixed $valor, ?string $mensagem = null) {

        if (!empty($valor) && !\app\helpers\DocumentoHelper::validarCpfOuCnpj((string) $valor)) {
            $this->erros[$campo] = $mensagem ?? 'CPF/CNPJ inválido';
        }

        return $this;

    }

    /** Valida se o valor é uma data no formato Y-m-d, válida e não futura (ex: data de nascimento). */
    public function dataPassada(string $campo, mixed $valor, ?string $mensagem = null) {

        if (empty($valor)) {
            return $this;
        }

        $data = \DateTime::createFromFormat('Y-m-d', (string) $valor);
        $valida = $data && $data->format('Y-m-d') === $valor;

        if (!$valida || $data > new \DateTime('today')) {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} deve ser uma data válida e não pode estar no futuro";
        }

        return $this;

    }

    /** Valida se o valor é uma data no formato Y-m-d, válida e posterior a hoje (RN08: prazo de metas). */
    public function dataFutura(string $campo, mixed $valor, ?string $mensagem = null) {

        if (empty($valor)) {
            return $this;
        }

        $data = \DateTime::createFromFormat('Y-m-d', (string) $valor);
        $valida = $data && $data->format('Y-m-d') === $valor;

        if (!$valida || $data <= new \DateTime('today')) {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} deve ser uma data válida e posterior a hoje";
        }

        return $this;

    }

    public function temErros() : bool {

        return !empty($this->erros);

    }

    /** Adiciona um erro manualmente — usado para validações que dependem de consulta ao banco (ex: unicidade). */
    public function adicionarErro(string $campo, string $mensagem) {

        $this->erros[$campo] = $mensagem;

        return $this;

    }

    public function getErros(){
        return $this->erros;
    }

}

