<?php

namespace app\models;

class Meta
{
    public const TIPOS = [
        'economizar' => 'Economizar',
        'comprar' => 'Comprar algo',
        'investir' => 'Investir',
    ];

    private ?int $id = null;
    private ?int $usuarioId = null;
    private string $nomeMeta = '';
    private string $tipo = 'economizar';
    private float $valorMeta = 0.0;
    private float $valorAtual = 0.0;
    private string $dataLimite = ''; // Y-m-d
    private bool $concluida = false;
    private bool $fixada = false;
    private ?string $criadoEm = null;

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

    public function getNomeMeta(): string
    {
        return $this->nomeMeta;
    }

    public function setNomeMeta(string $nomeMeta): void
    {
        $this->nomeMeta = $nomeMeta;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = array_key_exists($tipo, self::TIPOS) ? $tipo : 'economizar';
    }

    public function getValorMeta(): float
    {
        return $this->valorMeta;
    }

    public function setValorMeta(float $valorMeta): void
    {
        $this->valorMeta = max(0, $valorMeta);
    }

    public function getValorAtual(): float
    {
        return $this->valorAtual;
    }

    public function setValorAtual(float $valorAtual): void
    {
        $this->valorAtual = max(0, $valorAtual);
    }

    public function getDataLimite(): string
    {
        return $this->dataLimite;
    }

    public function setDataLimite(string $dataLimite): void
    {
        $this->dataLimite = $dataLimite;
    }

    public function isConcluida(): bool
    {
        return $this->concluida;
    }

    public function setConcluida(bool $concluida): void
    {
        $this->concluida = $concluida;
    }

    public function isFixada(): bool
    {
        return $this->fixada;
    }

    public function setFixada(bool $fixada): void
    {
        $this->fixada = $fixada;
    }

    public function getCriadoEm(): ?string
    {
        return $this->criadoEm;
    }

    public function setCriadoEm(?string $criadoEm): void
    {
        $this->criadoEm = $criadoEm;
    }

    public static function arrayParaObjeto(array $dados): Meta
    {
        $m = new Meta();
        $m->setId(isset($dados['id']) ? (int) $dados['id'] : null);
        $m->setUsuarioId(isset($dados['usuario_id']) && $dados['usuario_id'] !== null ? (int) $dados['usuario_id'] : null);
        $m->setNomeMeta($dados['nome_meta'] ?? '');
        $m->setTipo($dados['tipo'] ?? 'economizar');
        $m->setValorMeta(isset($dados['valor_meta']) ? (float) $dados['valor_meta'] : 0.0);
        $m->setValorAtual(isset($dados['valor_atual']) ? (float) $dados['valor_atual'] : 0.0);
        $m->setDataLimite($dados['data_limite'] ?? '');
        $m->setConcluida(!empty($dados['concluida']));
        $m->setFixada(!empty($dados['fixada']));
        $m->setCriadoEm($dados['criado_em'] ?? null);
        return $m;
    }
}
