<?php 

namespace app\services;

use app\repositories\UsuarioRepository;

class AutenticacaoService {

    private UsuarioRepository $usuarioRepository;

    public function __construct(){
        $this->usuarioRepository = new UsuarioRepository();
    }


    public function logar(string $email, string $senha) : bool {

        $usuario = $this->usuarioRepository->getUsuarioByEmail($email);

        if ($usuario && password_verify($senha, $usuario->getSenha())) {

            // Atualiza a sequência de acesso (streak) e recarrega o usuário
            // já com os pontos/streak em dia antes de guardar na sessão.
            $this->usuarioRepository->registrarAcesso($usuario->getId());
            $_SESSION['usuario_logado'] = $this->usuarioRepository->getUsuarioCompletoById($usuario->getId());
            return true;
            
        }

        return false;
    }

    public function logout(){
        session_destroy();
    }




}