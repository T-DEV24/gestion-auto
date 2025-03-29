<?php
require_once '../Config/connexion.php';

class FormationModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($titre, $description, $duree, $prix) {
        $sql = "INSERT INTO formations (titre, description, duree, prix) VALUES (:titre, :description, :duree, :prix)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':duree', $duree);
        $stmt->bindParam(':prix', $prix);
        return $stmt->execute();
    }

    public function read($id) {
        $sql = "SELECT * FROM formations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readAll() {
        $sql = "SELECT * FROM formations";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $titre, $description, $duree, $prix) {
        $sql = "UPDATE formations SET titre = :titre, description = :description, duree = :duree, prix = :prix WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':duree', $duree);
        $stmt->bindParam(':prix', $prix);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM formations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}