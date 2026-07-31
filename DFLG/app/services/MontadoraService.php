<?php 

namespace app\services;

use app\models\Montadora;
use app\repositories\MontadoraRepository;

class MontadoraService {

    private MontadoraRepository $repository;

    public function __construct(){
        $this->repository = new MontadoraRepository();
    }

    // Listar todas as montadoras
    public function getMontadoras(){
        return $this->repository->getAll();
    }

    // Buscar uma montadora pelo ID
    public function getMontadora(int $id){
        return $this->repository->getById($id);
    }

    // Salvar nova montadora
    public function saveMontadora(Montadora $montadora){
        return $this->repository->create($montadora);
    }

    // Atualizar montadora existente
    public function updateMontadora(Montadora $montadora){
        return $this->repository->update($montadora);
    }

    // Excluir montadora
    public function deleteMontadora(int $id){
        return $this->repository->delete($id);
    }

    // Regra de negócio: verificar duplicados
    public function existeNomeDuplicado(string $nome, ?int $idExcluir = null){
        return $this->repository->existeNomeDuplicado($nome, $idExcluir);
    }
}
