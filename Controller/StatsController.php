<?php
require_once '../Config/connexion.php';

class StatsController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(DNS, USER, PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function getStats() {
        $stats = [];

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM formations");
        $stats['formations'] = $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        $stats['users'] = $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM apprenants");
        $stats['apprenants'] = $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM inscriptions");
        $stats['inscriptions'] = $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM paiements");
        $stats['paiements'] = $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM factures");
        $stats['factures'] = $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM personnel");
        $stats['personnel'] = $stmt->fetchColumn();

        return $stats;
    }
}
?>