<?php
require_once '../Config/auth.php';
requireAdmin('main.php', 'login.php');

require_once '../Controller/StatsController.php';
$statsController = new StatsController();
$stats = $statsController->getStats();

ob_start();
?>
    <!-- Intégration de Bootstrap et Font Awesome si pas déjà dans template.php -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #FFFFFF !important;
            color: #333333;
        }
        .stats-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            border-left: 4px solid #6b65ea;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(107, 101, 234, 0.15);
        }
        .stats-icon {
            color: #6b65ea;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .stats-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #6b65ea;
        }
        .stats-title {
            color: #666;
            font-size: 1rem;
        }
        .action-btn {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 8px;
            background-color: white;
            color: #444;
            margin-bottom: 10px;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }
        .action-btn:hover {
            background-color: #f8f7ff;
            color: #6b65ea;
            transform: translateX(5px);
        }
        .action-icon {
            background-color: #f0f0ff;
            color: #6b65ea;
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
        .section-title {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background-color: #6b65ea;
        }
        .dashboard-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #6b65ea;
        }
        .dashboard-title {
            color: #333;
            margin: 0;
            font-weight: 600;
        }
        .dashboard-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>

    <div class="container-fluid py-4">
        <!-- En-tête du tableau de bord -->
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="dashboard-title">Tableau de bord - Administration</h2>
                <p class="dashboard-subtitle">Gérez votre auto-école en un coup d'œil</p>
            </div>
            <div>
                <span class="badge bg-primary">Administrateur</span>
            </div>
        </div>

        <!-- Section des statistiques -->
        <h3 class="section-title">Statistiques</h3>

        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['formations']; ?></div>
                    <div class="stats-title">Formations</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['users']; ?></div>
                    <div class="stats-title">Utilisateurs</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['apprenants']; ?></div>
                    <div class="stats-title">Apprenants</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['inscriptions']; ?></div>
                    <div class="stats-title">Inscriptions</div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['paiements']; ?></div>
                    <div class="stats-title">Paiements</div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['factures']; ?></div>
                    <div class="stats-title">Factures</div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['personnel']; ?></div>
                    <div class="stats-title">Personnels</div>
                </div>
            </div>
        </div>

        <!-- Section des actions -->
        <h3 class="section-title mt-5"></h3>

        <div class="row">
            <div class="col-lg-6">
                <h4 class="text-muted mb-3">Ajouter</h4>
                <a href="formulaireApprenant.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <span>Ajouter un apprenant</span>
                </a>

                <a href="formulaireFormation.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <span>Ajouter une formation</span>
                </a>

                <a href="formulaireInscription.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <span>Ajouter une inscription</span>
                </a>

                <a href="formulairePersonnel.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <span>Ajouter un personnel</span>
                </a>

                <a href="formulaireUser.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <span>Ajouter un utilisateur</span>
                </a>

                <a href="formulairePaiement.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <span>Ajouter un paiement</span>
                </a>
            </div>

            <div class="col-lg-6">
                <h4 class="text-muted mb-3">Consulter</h4>
                <a href="listeApprenant.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <span>Liste des apprenants</span>
                </a>

                <a href="listeFormation.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <span>Liste des formations</span>
                </a>

                <a href="listeInscription.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <span>Liste des inscriptions</span>
                </a>

                <a href="listePersonnel.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <span>Liste des personnels</span>
                </a>

                <a href="listeUser.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <span>Liste des utilisateurs</span>
                </a>

                <a href="listePaiement.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <span>Liste des paiements</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optionnel) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require 'template.php';
?>
