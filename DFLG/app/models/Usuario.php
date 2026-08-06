<?php

namespace app\models;

class Usuario
{
    private ?int $id = null;
    private string $nomeUsuario = '';
    private ?string $sobrenome = null;
    private ?string $nickname = null;
    private string $email = '';
    private string $senha = '';
    private string $perfil = 'usuario';
    private ?string $telefone = null;
    private ?string $localizacao = null;
    private ?string $foto = null;
    private ?string $cpfCnpj = null;
    private ?string $dataNascimento = null; // formato Y-m-d
    private int $pontosTotais = 0;
    private int $sequenciaAtual = 0;
    private int $maiorSequencia = 0;
    private ?string $ultimoAcesso = null; // formato Y-m-d
    private ?string $dataUltimoXp = null; // formato Y-m-d — usado pela RN07
    private ?string $criadoEm = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNomeUsuario(): string
    {
        return $this->nomeUsuario;
    }

    public function setNomeUsuario(string $nomeUsuario): void
    {
        $this->nomeUsuario = $nomeUsuario;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getSobrenome(): ?string
    {
        return $this->sobrenome;
    }

    public function setSobrenome(?string $sobrenome): void
    {
        $this->sobrenome = $sobrenome;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function setSenha(string $senha): void
    {
        $this->senha = $senha;
    }

    public function getPerfil(): string
    {
        return $this->perfil;
    }

    public function setPerfil(string $perfil): void
    {
        $this->perfil = $perfil;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone(?string $telefone): void
    {
        $this->telefone = $telefone;
    }

    public function getLocalizacao(): ?string
    {
        return $this->localizacao;
    }

    public function setLocalizacao(?string $localizacao): void
    {
        $this->localizacao = $localizacao;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): void
    {
        $this->foto = $foto;
    }

    public function getCpfCnpj(): ?string
    {
        return $this->cpfCnpj;
    }

    public function setCpfCnpj(?string $cpfCnpj): void
    {
        $this->cpfCnpj = $cpfCnpj;
    }

    public function getDataNascimento(): ?string
    {
        return $this->dataNascimento;
    }

    public function setDataNascimento(?string $dataNascimento): void
    {
        $this->dataNascimento = $dataNascimento;
    }

    public function getPontosTotais(): int
    {
        return $this->pontosTotais;
    }

    public function setPontosTotais(int $pontosTotais): void
    {
        $this->pontosTotais = $pontosTotais;
    }

    public function getSequenciaAtual(): int
    {
        return $this->sequenciaAtual;
    }

    public function setSequenciaAtual(int $sequenciaAtual): void
    {
        $this->sequenciaAtual = $sequenciaAtual;
    }

    public function getMaiorSequencia(): int
    {
        return $this->maiorSequencia;
    }

    public function setMaiorSequencia(int $maiorSequencia): void
    {
        $this->maiorSequencia = $maiorSequencia;
    }

    public function getUltimoAcesso(): ?string
    {
        return $this->ultimoAcesso;
    }

    public function setUltimoAcesso(?string $ultimoAcesso): void
    {
        $this->ultimoAcesso = $ultimoAcesso;
    }

    public function getDataUltimoXp(): ?string
    {
        return $this->dataUltimoXp;
    }

    public function setDataUltimoXp(?string $dataUltimoXp): void
    {
        $this->dataUltimoXp = $dataUltimoXp;
    }

    public function getCriadoEm(): ?string
    {
        return $this->criadoEm;
    }

    public function setCriadoEm(?string $criadoEm): void
    {
        $this->criadoEm = $criadoEm;
    }

    /**
     * Hidrata um objeto Usuario a partir de um array vindo do banco.
     * Aceita tanto a chave "nomeUsuario" (quando o SQL já faz o alias)
     * quanto "nome_usuario" (quando vem um SELECT * puro).
     */
    public static function arrayParaObjeto(array $dados): Usuario
    {
        $usuario = new Usuario();
        $usuario->setId(isset($dados['id']) ? (int) $dados['id'] : null);
        $usuario->setNomeUsuario($dados['nomeUsuario'] ?? $dados['nome_usuario'] ?? '');
        $usuario->setSobrenome($dados['sobrenome'] ?? null);
        $usuario->setNickname($dados['nickname'] ?? null);
        $usuario->setEmail($dados['email'] ?? '');
        $usuario->setSenha($dados['senha'] ?? '');
        $usuario->setPerfil($dados['perfil'] ?? 'usuario');
        $usuario->setTelefone($dados['telefone'] ?? null);
        $usuario->setLocalizacao($dados['localizacao'] ?? null);
        $usuario->setFoto($dados['foto'] ?? null);
        $usuario->setCpfCnpj($dados['cpf_cnpj'] ?? null);
        $usuario->setDataNascimento($dados['data_nascimento'] ?? null);
        $usuario->setPontosTotais(isset($dados['pontos_totais']) ? (int) $dados['pontos_totais'] : 0);
        $usuario->setSequenciaAtual(isset($dados['sequencia_atual']) ? (int) $dados['sequencia_atual'] : 0);
        $usuario->setMaiorSequencia(isset($dados['maior_sequencia']) ? (int) $dados['maior_sequencia'] : 0);
        $usuario->setUltimoAcesso($dados['ultimo_acesso'] ?? null);
        $usuario->setDataUltimoXp($dados['data_ultimo_xp'] ?? null);
        $usuario->setCriadoEm($dados['criado_em'] ?? null);
        return $usuario;
    }
}
