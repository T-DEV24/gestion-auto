<?php
$action = isset($_GET['action']) ? $_GET['action'] : '';
$controller = isset($_GET['controller']) ? $_GET['controller'] : '';

switch ($controller) {
    case 'Apprenant':
        require_once 'Controller/ApprenantController.php';
        $controller = new ApprenantController();
        if ($action === 'create') $controller->create();
        elseif ($action === 'update') $controller->update($_GET['id']);
        elseif ($action === 'delete') $controller->delete($_GET['id']);
        break;

    case 'Formation':
        require_once 'Controller/FormationController.php';
        $controller = new FormationController();
        if ($action === 'create') $controller->create();
        elseif ($action === 'update') $controller->update($_GET['id']);
        elseif ($action === 'delete') $controller->delete($_GET['id']);
        break;

    case 'Inscription':
        require_once 'Controller/InscriptionController.php';
        $controller = new InscriptionController();
        if ($action === 'create') $controller->create();
        elseif ($action === 'update') $controller->update($_GET['id']);
        elseif ($action === 'delete') $controller->delete($_GET['id']);
        break;

    case 'Personnel':
        require_once 'Controller/PersonnelController.php';
        $controller = new PersonnelController();
        if ($action === 'create') $controller->create();
        elseif ($action === 'update') $controller->update($_GET['id']);
        elseif ($action === 'delete') $controller->delete($_GET['id']);
        break;

    case 'User':
        require_once 'Controller/UserController.php';
        $controller = new UserController();
        if ($action === 'create') $controller->create();
        elseif ($action === 'update') $controller->update($_GET['id']);
        elseif ($action === 'delete') $controller->delete($_GET['id']);
        break;

    case 'Paiement':
        require_once 'Controller/PaiementController.php';
        $controller = new PaiementController();
        if ($action === 'create') $controller->create();
        elseif ($action === 'update') $controller->update($_GET['id']);
        elseif ($action === 'delete') $controller->delete($_GET['id']);
        break;

    case 'Facture':
        require_once 'Controller/FactureController.php';
        $controller = new FactureController();
        if ($action === 'create') $controller->create($_GET['apprenant_id']);
        break;

    case 'Auth':
        require_once 'Controller/AuthController.php';
        $controller = new AuthController();
        if ($action === 'login') $controller->login();
        elseif ($action === 'logout') $controller->logout();
        break;
}
?>