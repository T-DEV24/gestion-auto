<?php
require_once '../Config/connexion.php';

class PersonnelModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($nom, $prenom, $role, $email) {
        $sql = "INSERT INTO personnel (nom, prenom, role, email) VALUES (:nom, :prenom, :role, :email)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    public function read($id) {
        $sql = "SELECT * FROM personnel WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readAll() {
        $sql = "SELECT * FROM personnel";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $nom, $prenom, $role, $email) {
        $sql = "UPDATE personnel SET nom = :nom, prenom = :prenom, role = :role, email = :email WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM personnel WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}