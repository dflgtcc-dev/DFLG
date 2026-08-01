<?php

namespace app\models;

class Parcela
{
    private ?int $id = null;
    private ?int $usuarioId = null;
    private string $descricao = '';
    private string $categoria = '';
    private float $valorTotal = 0.0;
    private int $numeroParcelas = 1;
    private float $valorParcela = 0.0;
    private string $dataPrimeiraParcela = ''; // Y-m-d — data da compra / 1ª parcela

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUsuarioId(): ?int
    {
        return $this->usuarioId;
    }

    public function setUsuarioId(?int $usuarioId): void
    {
        $this->usuarioId = $usuarioId;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): void
    {
        $this->categoria = $categoria;
    }

    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    public function setValorTotal(float $valorTotal): void
    {
        $this->valorTotal = $valorTotal;
    }

    public function getNumeroParcelas(): int
    {
        return $this->numeroParcelas;
    }

    public function setNumeroParcelas(int $numeroParcelas): void
    {
        $this->numeroParcelas = max(1, $numeroParcelas);
    }

    public function getValorParcela(): float
    {
        return $this->valorParcela;
    }

    public function setValorParcela(float $valorParcela): void
    {
        $this->valorParcela = $valorParcela;
    }

    public function getDataPrimeiraParcela(): string
    {
        return $this->dataPrimeiraParcela;
    }

    public function setDataPrimeiraParcela(string $dataPrimeiraParcela): void
    {
        $this->dataPrimeiraParcela = $dataPrimeiraParcela;
    }

    public static function arrayParaObjeto(array $dados): Parcela
    {
        $p = new Parcela();
        $p->setId(isset($dados['id']) ? (int) $dados['id'] : null);
        $p->setUsuarioId(isset($dados['usuario_id']) && $dados['usuario_id'] !== null ? (int) $dados['usuario_id'] : null);
        $p->setDescricao($dados['descricao'] ?? '');
        $p->setCategoria($dados['categoria'] ?? '');
        $p->setValorTotal(isset($dados['valor_total']) ? (float) $dados['valor_total'] : 0.0);
        $p->setNumeroParcelas(isset($dados['numero_parcelas']) ? (int) $dados['numero_parcelas'] : 1);
        $p->setValorParcela(isset($dados['valor_parcela']) ? (float) $dados['valor_parcela'] : 0.0);
        $p->setDataPrimeiraParcela($dados['data_primeira_parcela'] ?? '');
        return $p;
    }
}
