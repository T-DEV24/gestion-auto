<?php
require_once '../Model/ApprenantModel.php';
require_once '../Model/FormationModel.php';
require_once '../Model/InscriptionModel.php';
require_once '../Model/PersonnelModel.php';
require_once '../Model/PaiementModel.php';
require_once '../Model/FactureModel.php';

class DashboardController {
    private $apprenantModel;
    private $formationModel;
    private $inscriptionModel;
    private $personnelModel;
    private $paiementModel;
    private $factureModel;

    public function __construct() {
        $this->apprenantModel = new ApprenantModel();
        $this->formationModel = new FormationModel();
        $this->inscriptionModel = new InscriptionModel();
        $this->personnelModel = new PersonnelModel();
        $this->paiementModel = new PaiementModel();
        $this->factureModel = new FactureModel();
    }

    public function getStats() {
        $stats = [
            'apprenants' => count($this->apprenantModel->readAll()),
            'formations' => count($this->formationModel->readAll()),
            'inscriptions' => count($this->inscriptionModel->readAll()),
            'personnel' => count($this->personnelModel->readAll()),
            'paiements' => count($this->paiementModel->readAll()),
            'factures' => count($this->factureModel->readAll()),
        ];
        return $stats;
    }
}