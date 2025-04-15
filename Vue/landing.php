<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Auto-École | Accueil</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #5cbbde;
            --secondary-color: #4aa0af;
            --light-bg: #FFFFFF;
            --light-text: #212529;
            --dark-bg: #181818;
            --dark-text: #f8f9fa;
        }

        body {
            background-color: var(--light-bg);
            color: var(--light-text);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        .dark body {
            background-color: var(--dark-bg);
            color: var(--dark-text);
        }

        .hero-section {
            background: linear-gradient(135deg, #5D5CDE 0%, #7977E6 100%);
            color: white;
            padding: 80px 0;
            border-radius: 0 0 50px 50px;
            margin-bottom: 50px;
        }

        .illustration-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }

        .feature-card {
            border-radius: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            background-color: var(--light-bg);
            border: 1px solid rgba(0,0,0,0.1);
        }

        .dark .feature-card {
            background-color: #242424;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(93, 92, 222, 0.2);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 50px;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .footer {
            background-color: #f5f5f7;
            padding: 30px 0;
            margin-top: 50px;
        }

        .dark .footer {
            background-color: #242424;
        }

        .footer-links a {
            color: var(--light-text);
            margin: 0 15px;
            text-decoration: none;
        }

        .dark .footer-links a {
            color: var(--dark-text);
        }

        /* Animation du bouton CTA */
        .cta-btn {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(93, 92, 222, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(93, 92, 222, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(93, 92, 222, 0);
            }
        }

        /* Dessin SVG animé */
        .car-svg, .student-svg, .teacher-svg {
            position: absolute;
            transition: all 0.5s ease;
        }

        .car-svg {
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            animation: drive 3s infinite alternate;
        }

        @keyframes drive {
            from { transform: translateX(-70%); }
            to { transform: translateX(-30%); }
        }

        .student-svg {
            bottom: 40px;
            right: 20%;
        }

        .teacher-svg {
            bottom: 40px;
            left: 20%;
        }

        .road {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 20px;
            background-color: #555;
            border-top: 3px solid #fff;
            border-bottom: 3px solid #fff;
        }

        .road-lines {
            position: absolute;
            bottom: 8px;
            width: 100%;
            height: 4px;
        }

        .road-line {
            position: absolute;
            width: 50px;
            height: 4px;
            background-color: #fff;
        }

        .training-type {
            border-left: 4px solid var(--primary-color);
            padding-left: 15px;
            margin: 20px 0;
        }

        /* Stats counter animation */
        .stats-counter {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0;
        }

        .testimonial-card {
            border-radius: 15px;
            overflow: hidden;
            margin: 10px 0;
            background-color: var(--light-bg);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .dark .testimonial-card {
            background-color: #242424;
            box-shadow: 0 4px 8px rgba(255,255,255,0.05);
        }

        .testimonial-content {
            padding: 20px;
            position: relative;
        }

        .testimonial-content::before {
            content: '\201C';
            font-size: 60px;
            position: absolute;
            top: -10px;
            left: 10px;
            color: rgba(93, 92, 222, 0.2);
        }

        /* Bouton flottant pour changer de thème */
        .theme-toggle-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s;
        }

        .theme-toggle-btn:hover {
            background-color: var(--secondary-color);
        }

        .theme-toggle-btn i {
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
<!-- Bouton flottant pour changer de thème -->
<button class="theme-toggle-btn" id="themeToggleBtn">
    <i class="fas fa-moon"></i>
</button>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">
            <span class="text-primary fw-bold">Auto-École</span> <span class="fw-light">Pro</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item ms-2">
                    <a href="login.php" class="btn btn-primary">Se connecter</a>       
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Gestion Auto-École</h1>
                <p class="lead">Une plateforme complète pour gérer efficacement votre auto-école, vos élèves, et vos formations.</p>
                <a href="login.php" class="btn btn-light btn-lg mt-3 cta-btn">Commencer maintenant</a>
            </div>
            <div class="col-md-6">
                <div class="illustration-container">
                    <!-- Route -->
                    <div class="road"></div>
                    <div class="road-lines" id="roadLines"></div>

                    <!-- Car SVG -->
                    <svg class="car-svg" width="120" height="60" viewBox="0 0 120 60">
                        <rect x="10" y="20" width="80" height="25" rx="5" fill="#e74c3c" />
                        <rect x="25" y="10" width="40" height="15" rx="3" fill="#e74c3c" />
                        <circle cx="30" cy="45" r="8" fill="#333" />
                        <circle cx="30" cy="45" r="4" fill="#666" />
                        <circle cx="70" cy="45" r="8" fill="#333" />
                        <circle cx="70" cy="45" r="4" fill="#666" />
                        <rect x="75" y="25" width="15" height="5" fill="#f1c40f" />
                        <rect x="85" y="20" width="5" height="5" fill="#f1c40f" />
                        <rect x="25" y="25" width="25" height="10" fill="#ecf0f1" />
                        <text x="40" y="30" font-size="8" fill="#333" text-anchor="middle" dominant-baseline="middle">AUTO</text>
                    </svg>

                    <!-- Teacher SVG -->
                    <svg class="teacher-svg" width="60" height="80" viewBox="0 0 60 80">
                        <circle cx="30" cy="20" r="15" fill="#3498db" />
                        <path d="M15 70 Q30 40 45 70" fill="#3498db" />
                        <rect x="15" y="45" width="30" height="25" fill="#3498db" />
                        <circle cx="24" cy="16" r="3" fill="white" />
                        <circle cx="36" cy="16" r="3" fill="white" />
                        <path d="M24 30 Q30 35 36 30" fill="none" stroke="white" stroke-width="2" />
                    </svg>

                    <!-- Student SVG -->
                    <svg class="student-svg" width="60" height="80" viewBox="0 0 60 80">
                        <circle cx="30" cy="20" r="15" fill="#e67e22" />
                        <path d="M15 70 Q30 40 45 70" fill="#e67e22" />
                        <rect x="15" y="45" width="30" height="25" fill="#e67e22" />
                        <circle cx="24" cy="16" r="3" fill="white" />
                        <circle cx="36" cy="16" r="3" fill="white" />
                        <path d="M24 28 Q30 33 36 28" fill="none" stroke="white" stroke-width="2" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Notre système de gestion tout-en-un</h2>
            <p class="lead">Gérez facilement tous les aspects de votre auto-école</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card p-4 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Gestion des apprenants</h3>
                    <p>Suivez les progrès de chaque élève, gérez leurs informations personnelles et leurs documents.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Planification avancée</h3>
                    <p>Organisez facilement vos leçons théoriques et pratiques avec calendrier interactif.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>Suivi des paiements</h3>
                    <p>Gérez les forfaits, les factures et les paiements en quelques clics.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Gestion des formations</h3>
                    <p>Créez et personnalisez vos programmes de formation selon vos besoins.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Statistiques en temps réel</h3>
                    <p>Accédez à des tableaux de bord détaillés sur les performances de votre auto-école.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Application mobile</h3>
                    <p>Pas encore disponible mais pour bientôt !!!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Training Programs Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Nos formations</h2>
            <p class="lead">Des programmes adaptés à tous les types de permis</p>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="training-type">
                    <h3>Permis B - Voiture</h3>
                    <p>Formation complète incluant code de la route et 20 heures de conduite minimum. Cours théoriques en salle et pratique sur différents types de routes.</p>
                </div>
                <div class="training-type">
                    <h3>Permis A - Moto</h3>
                    <p>Formation adaptée aux différentes cylindrées (A1, A2, A). Exercices sur plateau technique et circulation en conditions réelles.</p>
                </div>
                <div class="training-type">
                    <h3>Code de la route</h3>
                    <p>Accès illimité à notre plateforme d'entraînement et séances en salle avec formateur. Plus de 2000 questions pour une préparation optimale.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="training-type">
                    <h3>Conduite accompagnée (AAC)</h3>
                    <p>Dès 15 ans, formation en 3 phases : initiale, accompagnée et préparation à l'examen. Rendez-vous pédagogiques inclus.</p>
                </div>
                <div class="training-type">
                    <h3>Perfectionnement</h3>
                    <p>Stages de remise à niveau, conduite écologique, conduite à risque et perfectionnement pour les personnes déjà titulaires du permis.</p>
                </div>
                <div class="training-type">
                    <h3>Formation professionnelle</h3>
                    <p>Permis C, D et formations FIMO/FCO pour les conducteurs professionnels. Programmes spécifiques pour entreprises.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4">
                <p class="stats-counter" id="studentsCounter">0</p>
                <p>Élèves formés</p>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <p class="stats-counter" id="successCounter">0</p>
                <p>Taux de réussite</p>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <p class="stats-counter" id="vehiclesCounter">0</p>
                <p>Véhicules modernes</p>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <p class="stats-counter" id="instructorsCounter">0</p>
                <p>Instructeurs qualifiés</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Ce que disent nos clients</h2>
            <p class="lead">Des auto-écoles qui utilisent notre plateforme</p>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Depuis que nous utilisons cette plateforme, notre administration est beaucoup plus efficace. Nous pouvons nous concentrer sur l'enseignement plutôt que sur la paperasse."</p>
                        <p class="fw-bold mb-0">Tcheudjo Clotaire</p>
                        <small>Directeur, Auto-École Centrale</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Le suivi des paiements est devenu un jeu d'enfant. Plus d'erreurs de facturation et nos élèves apprécient la transparence du système."</p>
                        <p class="fw-bold mb-0">Tcheudjo Abraham</p>
                        <small>Gérant, École de Conduite Express</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Les statistiques en temps réel nous ont permis d'identifier nos points faibles et d'améliorer nos méthodes d'enseignement. Résultat : +15% de réussite !"</p>
                        <p class="fw-bold mb-0">Kwekam Tcheudjo</p>
                        <small>Responsable pédagogique, Auto-École Prestige</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold mb-4">Prêt à transformer votre auto-école ?</h2>
                <p class="lead mb-4">Rejoignez des centaines d'auto-écoles qui ont déjà optimisé leur gestion grâce à notre plateforme.</p>
                <a href="login.php" class="btn btn-primary btn-lg">Se connecter maintenant</a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="fw-bold">Auto-École Pro</h5>
                <p>La solution complète pour la gestion de votre auto-école.</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="fw-bold">Contact</h5>
                <p><i class="fas fa-envelope me-2"></i> tcheuatatcheudjoclotaire@gmail.com</p>
                <p><i class="fas fa-phone me-2"></i> 690 90 77 99</p>
            </div>
            <div class="col-md-4">
                <h5 class="fw-bold">Liens rapides</h5>
                <div class="footer-links">
                    <a href="landing.php">Accueil</a>
                    <a href="#">Fonctionnalités</a>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <p class="mb-0">© 2025 Auto-École Pro. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Gestion du changement de thème
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const htmlElement = document.documentElement;

    // Vérifier la préférence sauvegardée dans localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        htmlElement.classList.add(savedTheme);
        updateThemeIcon(savedTheme);
    }

    themeToggleBtn.addEventListener('click', () => {
        if (htmlElement.classList.contains('dark')) {
            htmlElement.classList.remove('dark');
            localStorage.setItem('theme', '');
            updateThemeIcon('');
        } else {
            htmlElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            updateThemeIcon('dark');
        }
    });

    function updateThemeIcon(theme) {
        const icon = themeToggleBtn.querySelector('i');
        if (theme === 'dark') {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
    }

    // Animation route
    function createRoadLines() {
        const roadLinesElement = document.getElementById('roadLines');
        const containerWidth = document.querySelector('.illustration-container').offsetWidth;

        for (let i = 0; i < containerWidth; i += 80) {
            const roadLine = document.createElement('div');
            roadLine.className = 'road-line';
            roadLine.style.left = i + 'px';
            roadLinesElement.appendChild(roadLine);
        }
    }

    // Stats counter animation
    function animateCounter(element, target, duration = 2000, prefix = '', suffix = '') {
        let start = 0;
        const increment = Math.ceil(target / (duration / 16));
        const timer = setInterval(() => {
            start += increment;
            if (start > target) {
                start = target;
                clearInterval(timer);
            }
            element.textContent = prefix + start + suffix;
        }, 16);
    }

    function startCountersWhenVisible() {
        const counters = document.querySelectorAll('.stats-counter');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    let target;

                    switch(element.id) {
                        case 'studentsCounter':
                            target = 5000;
                            break;
                        case 'successCounter':
                            target = 95;
                            suffix = '%';
                            break;
                        case 'vehiclesCounter':
                            target = 30;
                            break;
                        case 'instructorsCounter':
                            target = 25;
                            break;
                        default:
                            target = 100;
                    }

                    animateCounter(element, target, 2000, '', element.id === 'successCounter' ? '%' : '');
                    observer.unobserve(element);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => {
            observer.observe(counter);
        });
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        createRoadLines();
        startCountersWhenVisible();
    });
</script>
</body>
</html>