<?php

namespace app\helpers;

/**
 * Validação de CPF e CNPJ pelo algoritmo oficial de dígito verificador
 * (não é só contagem de caracteres — realmente calcula os dígitos).
 */
class DocumentoHelper
{
    /** Remove tudo que não for dígito. */
    public static function apenasDigitos(string $valor): string
    {
        return preg_replace('/\D/', '', $valor) ?? '';
    }

    public static function validarCpf(string $cpf): bool
    {
        $cpf = self::apenasDigitos($cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += ((int) $cpf[$i]) * (($t + 1) - $i);
            }
            $digito = ((10 * $soma) % 11) % 10;
            if ((int) $cpf[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }

    public static function validarCnpj(string $cnpj): bool
    {
        $cnpj = self::apenasDigitos($cnpj);

        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $pesos1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $pesos2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        foreach ([$pesos1, $pesos2] as $indice => $pesos) {
            $soma = 0;
            foreach ($pesos as $i => $peso) {
                $soma += ((int) $cnpj[$i]) * $peso;
            }
            $resto = $soma % 11;
            $digito = $resto < 2 ? 0 : 11 - $resto;
            if ((int) $cnpj[12 + $indice] !== $digito) {
                return false;
            }
        }

        return true;
    }

    /** Aceita tanto CPF (11 dígitos) quanto CNPJ (14 dígitos), validando o que for informado. */
    public static function validarCpfOuCnpj(string $valor): bool
    {
        $limpo = self::apenasDigitos($valor);

        if (strlen($limpo) === 11) {
            return self::validarCpf($limpo);
        }

        if (strlen($limpo) === 14) {
            return self::validarCnpj($limpo);
        }

        return false;
    }
}
