<?php

namespace app\models;

class Usuario
{
    private ?int $id = null;
    private string $nomeUsuario = '';
    private string $email = '';
    private string $senha = '';
    private string $perfil = 'usuario';

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
        $usuario->setEmail($dados['email'] ?? '');
        $usuario->setSenha($dados['senha'] ?? '');
        $usuario->setPerfil($dados['perfil'] ?? 'usuario');
        return $usuario;
    }
}
