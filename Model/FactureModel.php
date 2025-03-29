<?php
require_once '../Config/connexion.php';

class FactureModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($apprenant_id, $montant_total, $date_facture) {
        $sql = "INSERT INTO factures (apprenant_id, montant_total, date_facture) VALUES (:apprenant_id, :montant_total, :date_facture)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':apprenant_id', $apprenant_id);
        $stmt->bindParam(':montant_total', $montant_total);
        $stmt->bindParam(':date_facture', $date_facture);
        return $stmt->execute();
    }

    public function read($id) {
        $sql = "SELECT f.*, a.nom AS apprenant_nom, a.prenom AS apprenant_prenom
                FROM factures f
                JOIN apprenants a ON f.apprenant_id = a.id
                WHERE f.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readAll() {
        $sql = "SELECT f.*, a.nom AS apprenant_nom FROM factures f JOIN apprenants a ON f.apprenant_id = a.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $apprenant_id, $montant_total, $date_facture) {
        $sql = "UPDATE factures SET apprenant_id = :apprenant_id, montant_total = :montant_total, date_facture = :date_facture WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':apprenant_id', $apprenant_id);
        $stmt->bindParam(':montant_total', $montant_total);
        $stmt->bindParam(':date_facture', $date_facture);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM factures WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}