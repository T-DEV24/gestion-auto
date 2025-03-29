<?php
define('USER', 'root');
define('DNS', 'mysql:host=localhost;dbname=gestion_auto');
define('PASSWORD', '');

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO(DNS, USER, PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            error_log("Erreur de connexion à la BD : " . $ex->getMessage());
            echo "Erreur de connexion : " . $ex->getMessage();
            exit();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>