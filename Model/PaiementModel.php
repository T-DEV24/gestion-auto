<?php
require_once '../Config/connexion.php';

class PaiementModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($apprenant_id, $montant, $date_paiement, $methode_paiement) {
        $sql = "INSERT INTO paiements (apprenant_id, montant, date_paiement, methode_paiement) VALUES (:apprenant_id, :montant, :date_paiement, :methode_paiement)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':apprenant_id', $apprenant_id);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':date_paiement', $date_paiement);
        $stmt->bindParam(':methode_paiement', $methode_paiement);
        return $stmt->execute();
    }

    public function read($id) {
        $sql = "SELECT * FROM paiements WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readAll() {
        $sql = "SELECT p.*, a.nom AS apprenant_nom FROM paiements p JOIN apprenants a ON p.apprenant_id = a.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $apprenant_id, $montant, $date_paiement, $methode_paiement) {
        $sql = "UPDATE paiements SET apprenant_id = :apprenant_id, montant = :montant, date_paiement = :date_paiement, methode_paiement = :methode_paiement WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':apprenant_id', $apprenant_id);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':date_paiement', $date_paiement);
        $stmt->bindParam(':methode_paiement', $methode_paiement);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM paiements WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}