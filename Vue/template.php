<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Auto-École</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6b65ea;
            --secondary-color: #5550c7;
            --light-bg: #FFFFFF;
            --light-text: #333333;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--light-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar styles */
        .navbar {
            background-color: var(--primary-color);
            padding: 0.8rem 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.4rem;
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            padding: 0.6rem 1rem;
            transition: all 0.3s;
            border-radius: 5px;
            margin: 0 2px;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: rgba(255,255,255,0.15);
            color: white !important;
        }

        /* Optional menu styles */
        .optional-menu {
            display: none;
            position: absolute;
            background: white;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 0.5rem;
            z-index: 1000;
            min-width: 200px;
        }

        .optional-menu.show {
            display: block;
        }

        .optional-item {
            display: block;
            padding: 0.6rem 1rem;
            border-radius: 5px;
            transition: all 0.2s;
            color: var(--light-text);
            text-decoration: none;
        }

        .optional-item:hover,
        .optional-item:focus {
            background-color: rgba(107, 101, 234, 0.1);
            color: var(--primary-color);
        }

        .optional-item i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
            color: var(--primary-color);
        }

        .nav-item {
            position: relative;
        }

        .toggle-menu::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-left: 5px;
        }

        /* Welcome message */
        .welcome {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid var(--primary-color);
            color: var(--light-text);
            font-size: 1.1rem;
        }

        /* Tables */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #e0e0e0;
        }

        th {
            background-color: #f5f5f5;
            color: #333;
            font-weight: 600;
            padding: 12px 15px;
        }

        td {
            padding: 10px 15px;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Main content area */
        .main-content {
            flex: 1;
            padding: 20px;
        }

        /* Footer */
        footer {
            background-color: #f8f9fa;
            padding: 15px 0;
            margin-top: auto;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-car-side me-2"></i>Auto-École Pro
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Accueil</a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Tableau de bord</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link toggle-menu" href="#" id="addToggle">
                                <i class="fas fa-plus-circle me-1"></i>Ajouter
                            </a>
                            <div class="optional-menu" id="addOptional">
                                <a class="optional-item" href="formulaireApprenant.php"><i class="fas fa-user-graduate"></i>Apprenant</a>
                                <a class="optional-item" href="formulaireFormation.php"><i class="fas fa-chalkboard-teacher"></i>Formation</a>
                                <a class="optional-item" href="formulaireInscription.php"><i class="fas fa-clipboard-list"></i>Inscription</a>
                                <a class="optional-item" href="formulairePersonnel.php"><i class="fas fa-user-tie"></i>Personnel</a>
                                <a class="optional-item" href="formulaireUser.php"><i class="fas fa-user-plus"></i>Utilisateur</a>
                                <a class="optional-item" href="formulairePaiement.php"><i class="fas fa-money-bill-wave"></i>Paiement</a>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link toggle-menu" href="#" id="viewToggle">
                                <i class="fas fa-list me-1"></i>Consulter
                            </a>
                            <div class="optional-menu" id="viewOptional">
                                <a class="optional-item" href="listeApprenant.php"><i class="fas fa-users"></i>Apprenants</a>
                                <a class="optional-item" href="listeFormation.php"><i class="fas fa-book"></i>Formations</a>
                                <a class="optional-item" href="listeInscription.php"><i class="fas fa-clipboard-check"></i>Inscriptions</a>
                                <a class="optional-item" href="listePersonnel.php"><i class="fas fa-id-card"></i>Personnels</a>
                                <a class="optional-item" href="listeUser.php"><i class="fas fa-user-shield"></i>Utilisateurs</a>
                                <a class="optional-item" href="listePaiement.php"><i class="fas fa-file-invoice-dollar"></i>Paiements</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="main.php">Tableau de bord</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Mes factures</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link toggle-menu" href="#" id="userToggle">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <div class="optional-menu" id="userOptional">
                            <a class="optional-item" href="#"><i class="fas fa-user-cog"></i>Mon profil</a>
                            <div class="dropdown-divider my-2"></div>
                            <a class="optional-item" href="../Controller/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i>Se déconnecter</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Se connecter</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content container">
    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>
        <div class="welcome">
            <i class="fas fa-hand-sparkles me-2"></i>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <span class="badge bg-primary ms-2">Administrateur</span>
            <?php else: ?>
                <span class="badge bg-success ms-2">Utilisateur</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="content-wrapper">
        <?php echo $content; ?>
    </div>
</div>

<!-- Footer -->
<footer class="text-center">
    <div class="container">
        <p class="mb-0">© 2025 Tcheudjo Auto-École Pro. Tous droits réservés.</p>
    </div>
</footer>

<!-- Bootstrap JS with Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

<!-- JS personnalisé pour les menus optionnels -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des menus optionnels
        const toggles = document.querySelectorAll('.toggle-menu');

        toggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();

                // Identifiant du menu à afficher
                const targetId = this.id.replace('Toggle', 'Optional');
                const targetMenu = document.getElementById(targetId);

                // Fermer tous les autres menus optionnels
                document.querySelectorAll('.optional-menu').forEach(menu => {
                    if (menu.id !== targetId) {
                        menu.classList.remove('show');
                    }
                });

                // Basculer l'affichage du menu ciblé
                targetMenu.classList.toggle('show');
            });
        });

        // Fermer les menus optionnels lorsqu'on clique ailleurs sur la page
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.toggle-menu') && !e.target.closest('.optional-menu')) {
                document.querySelectorAll('.optional-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });

        // Pour la compatibilité mobile
        document.querySelectorAll('.optional-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // Fermer le menu après avoir cliqué sur un élément
                this.closest('.optional-menu').classList.remove('show');
            });
        });

        console.log('Menus optionnels initialisés avec succès!');
    });
</script>

</body>
</html>