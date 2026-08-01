<?php

namespace app\models;

class Categoria
{
    private ?int $id = null;
    private ?int $usuarioId = null;
    private string $nome = '';
    private float $orcamentoMensal = 0.0;

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

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getOrcamentoMensal(): float
    {
        return $this->orcamentoMensal;
    }

    public function setOrcamentoMensal(float $orcamentoMensal): void
    {
        $this->orcamentoMensal = $orcamentoMensal;
    }

    public static function arrayParaObjeto(array $dados): Categoria
    {
        $c = new Categoria();
        $c->setId(isset($dados['id']) ? (int) $dados['id'] : null);
        $c->setUsuarioId(isset($dados['usuario_id']) && $dados['usuario_id'] !== null ? (int) $dados['usuario_id'] : null);
        $c->setNome($dados['nome'] ?? '');
        $c->setOrcamentoMensal(isset($dados['orcamento_mensal']) ? (float) $dados['orcamento_mensal'] : 0.0);
        return $c;
    }
}
