<?php

namespace app\services;

use app\models\Usuario;
use app\repositories\UsuarioRepository;

class UsuarioService
{
    /** Quantos pontos cada nível exige (nível 1 = 0 a 499, nível 2 = 500 a 999, ...). */
    private const PONTOS_POR_NIVEL = 500;

    private UsuarioRepository $repository;

    public function __construct()
    {
        $this->repository = new UsuarioRepository();
    }

    public function getUsuarios(): array
    {
        return $this->repository->getUsuarios();
    }

    public function saveUsuario(Usuario $usuario): bool
    {
        // 1. Verifica se o e-mail já está cadastrado
        $usuarioExistente = $this->repository->getUsuarioByEmail($usuario->getEmail());

        if ($usuarioExistente) {
            // Se o usuário existe, a regra de negócio impede o cadastro
            return false;
        }

        // 2. Se não existir, prossegue com o salvamento
        return $this->repository->saveUsuario($usuario);
    }

    public function getUsuarioById(int $id)
    {
        return $this->repository->getUsuarioById($id);
    }

    /** Usado na validação de unicidade do e-mail no cadastro (RN02). */
    public function getUsuarioByEmail(string $email)
    {
        return $this->repository->getUsuarioByEmail($email);
    }

    /** Usado na validação de unicidade do nickname no cadastro (RN02). */
    public function getUsuarioByNickname(string $nickname)
    {
        return $this->repository->getUsuarioByNickname($nickname);
    }

    public function getUsuarioCompletoById(int $id): ?Usuario
    {
        return $this->repository->getUsuarioCompletoById($id);
    }

    public function updateUsuario(Usuario $usuario): bool
    {
        return $this->repository->updateUsuario($usuario);
    }

    public function atualizarDadosPessoais(
        int $id,
        string $nome,
        ?string $sobrenome,
        ?string $nickname,
        ?string $telefone,
        ?string $localizacao,
        ?string $dataNascimento
    ): bool {
        return $this->repository->atualizarDadosPessoais($id, $nome, $sobrenome, $nickname, $telefone, $localizacao, $dataNascimento);
    }

    public function atualizarFoto(int $id, string $nomeArquivo): bool
    {
        return $this->repository->atualizarFoto($id, $nomeArquivo);
    }

    public function adicionarPontos(int $id, int $pontos): bool
    {
        return $this->repository->adicionarPontos($id, $pontos);
    }

    /** RN07: só soma XP se ainda não foi ganho hoje. */
    public function adicionarPontosSeElegivel(int $id, int $pontos): bool
    {
        return $this->repository->adicionarPontosSeElegivel($id, $pontos);
    }

    public function registrarAcesso(int $id): array
    {
        return $this->repository->registrarAcesso($id);
    }

    public function deleteUsuario(int $id): bool
    {
        return $this->repository->deleteUsuario($id);
    }

    /**
     * Calcula nível, progresso e pontos que faltam para o próximo nível
     * a partir do total de pontos acumulados pelo usuário.
     */
    public function calcularNivel(int $pontosTotais): array
    {
        $nivel = intdiv($pontosTotais, self::PONTOS_POR_NIVEL) + 1;
        $pontosNivelAtual = ($nivel - 1) * self::PONTOS_POR_NIVEL;
        $pontosProximoNivel = $nivel * self::PONTOS_POR_NIVEL;
        $progresso = $pontosProximoNivel > 0
            ? (($pontosTotais - $pontosNivelAtual) / self::PONTOS_POR_NIVEL) * 100
            : 0;

        return [
            'nivel' => $nivel,
            'pontosProximoNivel' => $pontosProximoNivel,
            'pontosFaltantes' => max(0, $pontosProximoNivel - $pontosTotais),
            'progresso' => min(100, max(0, $progresso)),
        ];
    }

    /** Posição do usuário no ranking geral por pontos (1 = primeiro lugar). */
    public function getPosicaoRanking(int $id): int
    {
        $ranking = $this->repository->getRankingPorPontos();

        foreach ($ranking as $index => $u) {
            if ((int) $u['id'] === $id) {
                return $index + 1;
            }
        }

        return count($ranking) + 1;
    }
}
