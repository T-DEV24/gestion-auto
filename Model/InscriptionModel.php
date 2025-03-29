<?php
require_once '../Config/connexion.php';

class InscriptionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($apprenant_id, $formation_id, $date_inscription) {
        $sql = "INSERT INTO inscriptions (apprenant_id, formation_id, date_inscription) VALUES (:apprenant_id, :formation_id, :date_inscription)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':apprenant_id', $apprenant_id);
        $stmt->bindParam(':formation_id', $formation_id);
        $stmt->bindParam(':date_inscription', $date_inscription);
        return $stmt->execute();
    }

    public function read($id) {
        $sql = "SELECT * FROM inscriptions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readAll() {
        $sql = "SELECT i.*, a.nom AS apprenant_nom, f.titre AS formation_titre
                FROM inscriptions i
                JOIN apprenants a ON i.apprenant_id = a.id
                JOIN formations f ON i.formation_id = f.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $apprenant_id, $formation_id, $date_inscription) {
        $sql = "UPDATE inscriptions SET apprenant_id = :apprenant_id, formation_id = :formation_id, date_inscription = :date_inscription WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':apprenant_id', $apprenant_id);
        $stmt->bindParam(':formation_id', $formation_id);
        $stmt->bindParam(':date_inscription', $date_inscription);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM inscriptions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}