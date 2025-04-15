<?php
require_once '../Config/connexion.php';

class PaiementModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($user_id, $formation_id, $montant, $statut = 'en attente', $date_paiement = null) {
        $sql = "INSERT INTO paiements (user_id, formation_id, montant, statut, date_paiement) VALUES (:user_id, :formation_id, :montant, :statut, :date_paiement)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':formation_id', $formation_id);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':date_paiement', $date_paiement);
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
        $sql = "SELECT p.*, a.nom AS apprenant_nom, f.titre AS formation_titre 
                FROM paiements p 
                JOIN apprenants a ON p.user_id = a.user_id 
                JOIN formations f ON p.formation_id = f.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $user_id, $formation_id, $montant, $statut, $date_paiement) {
        $sql = "UPDATE paiements SET user_id = :user_id, formation_id = :formation_id, montant = :montant, statut = :statut, date_paiement = :date_paiement WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':formation_id', $formation_id);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':date_paiement', $date_paiement);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM paiements WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>