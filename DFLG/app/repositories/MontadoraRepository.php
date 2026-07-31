<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Montadora;
use PDO;

class MontadoraRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    // Listar todas as montadoras
    public function getAll(): array
    {
        $stm = $this->connection->prepare("SELECT * FROM montadoras ORDER BY nome ASC");
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar uma montadora pelo ID
    public function getById(int $id): ?array
    {
        $stm = $this->connection->prepare("SELECT * FROM montadoras WHERE id = :id");
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();

        $montadora = $stm->fetch(PDO::FETCH_ASSOC);
        return $montadora ?: null;
    }

    // Criar nova montadora
    public function create(Montadora $montadora): bool
    {
        $sql = "INSERT INTO montadoras (nome, pais, ano_fundacao, website, logo) 
                VALUES (:nome, :pais, :ano_fundacao, :website, :logo)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $montadora->getNome());
        $stmt->bindValue(':pais', $montadora->getPais());
        $stmt->bindValue(':ano_fundacao', $montadora->getAnoFundacao());
        $stmt->bindValue(':website', $montadora->getWebsite());
        $stmt->bindValue(':logo', $montadora->getLogo());

        return $stmt->execute();
    }

    // Atualizar montadora existente
    public function update(Montadora $montadora): bool
    {
        $sql = "UPDATE montadoras 
                SET nome = :nome, pais = :pais, ano_fundacao = :ano_fundacao, website = :website, logo = :logo 
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $montadora->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':nome', $montadora->getNome());
        $stmt->bindValue(':pais', $montadora->getPais());
        $stmt->bindValue(':ano_fundacao', $montadora->getAnoFundacao());
        $stmt->bindValue(':website', $montadora->getWebsite());
        $stmt->bindValue(':logo', $montadora->getLogo());

        return $stmt->execute();
    }

    // Excluir montadora
    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM montadoras WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Regra de negócio: verificar duplicados
    public function existeNomeDuplicado(string $nome, ?int $idExcluir = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM montadoras WHERE nome = :nome";
        if ($idExcluir !== null) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        if ($idExcluir !== null) {
            $stmt->bindValue(':id', $idExcluir, PDO::PARAM_INT);
        }

        $stmt->execute();
        $total = (int)$stmt->fetch()['total'];
        return $total > 0;
    }
}
