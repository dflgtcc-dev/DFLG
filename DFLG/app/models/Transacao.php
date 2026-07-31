<?php

namespace app\models;

class Transacao
{
    private ?int $id = null;
    private ?int $usuarioId = null;
    private string $descricao = '';
    private float $valor = 0.0;
    private string $categoria = '';
    private string $tipo = 'despesa'; // 'receita' | 'despesa'
    private string $data = '';        // formato Y-m-d
    private string $moeda = 'BRL';

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

    public function getValor(): float
    {
        return $this->valor;
    }

    public function setValor(float $valor): void
    {
        $this->valor = $valor;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): void
    {
        $this->categoria = $categoria;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo === 'receita' ? 'receita' : 'despesa';
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function getMoeda(): string
    {
        return $this->moeda;
    }

    public function setMoeda(string $moeda): void
    {
        $this->moeda = $moeda;
    }

    public static function arrayParaObjeto(array $dados): Transacao
    {
        $t = new Transacao();
        $t->setId(isset($dados['id']) ? (int) $dados['id'] : null);
        $t->setUsuarioId(isset($dados['usuario_id']) && $dados['usuario_id'] !== null ? (int) $dados['usuario_id'] : null);
        $t->setDescricao($dados['descricao'] ?? '');
        $t->setValor(isset($dados['valor']) ? (float) $dados['valor'] : 0.0);
        $t->setCategoria($dados['categoria'] ?? '');
        $t->setTipo($dados['tipo'] ?? 'despesa');
        $t->setData($dados['data_transacao'] ?? $dados['data'] ?? '');
        $t->setMoeda($dados['moeda'] ?? 'BRL');
        return $t;
    }
}
