<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Auto-École Pro</title>
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

        /* Login container */
        .login-container {
            background-color: var(--light-bg);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(107, 101, 234, 0.2);
            padding: 30px;
            max-width: 450px;
            width: 100%;
            margin: auto;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-title {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
        }

        /* Form elements */
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            height: auto;
            font-size: 16px;
            color: var(--light-text);
            border-color: rgba(107, 101, 234, 0.3);
            background-color: var(--light-bg);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(107, 101, 234, 0.25);
            color: var(--light-text);
        }

        /* Button */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
        }

        .alert-success {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }

        /* Password field */
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: var(--primary-color);
        }

        /* Form check */
        .form-check-label {
            color: var(--light-text);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
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

        /* Animation voiture */
        .car-animation {
            position: fixed;
            bottom: 80px;
            left: -100px;
            width: 80px;
            height: 40px;
            animation: drive 15s linear infinite;
        }

        @keyframes drive {
            from { left: -100px; }
            to { left: calc(100% + 100px); }
        }

        /* Return home link */
        .return-home {
            margin-top: 20px;
            text-align: center;
        }

        .return-home a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .return-home a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        /* Forgot password link */
        .forgot-password {
            color: var(--primary-color);
            transition: all 0.3s;
        }

        .forgot-password:hover {
            color: var(--secondary-color);
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
                    <a class="nav-link" href="landing.php">Accueil</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Section Connexion -->
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-container">
                <div class="login-logo">
                    <!-- Logo SVG -->
                    <svg width="80" height="80" viewBox="0 0 80 80">
                        <circle cx="40" cy="40" r="35" fill="#6b65ea" />
                        <circle cx="40" cy="40" r="25" fill="white" />
                        <path d="M30 50 L30 30 L50 30 L50 50 Z" fill="#6b65ea" stroke="white" stroke-width="2" />
                        <text x="40" y="44" font-size="14" font-weight="bold" text-anchor="middle" fill="white">AE</text>
                    </svg>
                </div>
                <h2 class="login-title">Connexion</h2>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="../Controller/AuthController.php?action=login">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Entrez votre nom d'utilisateur" required>
                    </div>

                    <div class="mb-3 position-relative">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Se souvenir de moi</label>
                        </div>
                        <a href="#" class="forgot-password text-decoration-none">Mot de passe oublié?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                    </button>
                </form>

                <div class="return-home mt-4">
                    <a href="landing.php">
                        <i class="fas fa-arrow-left me-1"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Élément animé -->
<div class="car-animation">
    <svg width="80" height="40" viewBox="0 0 80 40">
        <rect x="5" y="15" width="60" height="15" rx="5" fill="#6b65ea" />
        <rect x="20" y="5" width="30" height="10" rx="3" fill="#6b65ea" />
        <circle cx="20" cy="30" r="6" fill="#333" />
        <circle cx="20" cy="30" r="3" fill="#666" />
        <circle cx="50" cy="30" r="6" fill="#333" />
        <circle cx="50" cy="30" r="3" fill="#666" />
        <rect x="60" y="18" width="10" height="3" fill="#f1c40f" />
        <rect x="65" y="15" width="5" height="3" fill="#f1c40f" />
    </svg>
</div>

<!-- Footer -->
<footer class="text-center">
    <div class="container">
        <p class="mb-0">© 2025 Auto-École Pro. Tous droits réservés.</p>
    </div>
</footer>

<!-- Bootstrap JS with Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
</body>
</html>