-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_auto;
USE gestion_auto;

-- Table des utilisateurs (pour l'authentification)
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) UNIQUE NOT NULL,
                       password VARCHAR(255) NOT NULL, 
                       email VARCHAR(191) UNIQUE NOT NULL, 
                       role VARCHAR(50) NOT NULL,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des apprenants
CREATE TABLE apprenants (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            nom VARCHAR(100) NOT NULL,
                            prenom VARCHAR(100) NOT NULL,
                            email VARCHAR(191) NOT NULL UNIQUE, 
                            user_id INT NOT NULL,
                            FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table des formations
CREATE TABLE formations (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            titre VARCHAR(255) NOT NULL,
                            description TEXT,
                            prix DECIMAL(10, 2) NOT NULL,
                            date_debut DATE,
                            date_fin DATE
);

-- Table des inscriptions
CREATE TABLE inscriptions (
                              id INT AUTO_INCREMENT PRIMARY KEY,
                              user_id INT NOT NULL,
                              formation_id INT NOT NULL,
                              date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              FOREIGN KEY (user_id) REFERENCES users(id),
                              FOREIGN KEY (formation_id) REFERENCES formations(id)
);

-- Table du personnel
CREATE TABLE personnel (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           nom VARCHAR(100) NOT NULL,
                           prenom VARCHAR(100) NOT NULL,
                           role VARCHAR(50) NOT NULL, 
                           email VARCHAR(191) UNIQUE NOT NULL, 
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des paiements
CREATE TABLE paiements (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           user_id INT NOT NULL,
                           formation_id INT NOT NULL,
                           montant DECIMAL(10, 2) NOT NULL,
                           date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           statut ENUM('payé', 'en attente') DEFAULT 'en attente',
                           FOREIGN KEY (user_id) REFERENCES users(id),
                           FOREIGN KEY (formation_id) REFERENCES formations(id)
);

-- Table des factures
CREATE TABLE factures (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          apprenant_id INT NOT NULL,
                          montant_total DECIMAL(10, 2) NOT NULL,
                          date_facture DATE NOT NULL,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          FOREIGN KEY (apprenant_id) REFERENCES apprenants(id) ON DELETE CASCADE
);

-- Insertion d'un utilisateur admin par défaut
INSERT INTO users (username, password, email, role) VALUES
    ('admin', '$2y$10$KXSerQxvsSVPMJ/5lb0.GeHZAdXcVwmLuHiqBjhAkPw21aFas628y', 'admin@autoecole.com', 'admin');
-- Mot de passe : "admin123" (haché avec password_hash)

-- Insertion d'un utilisateur normal pour tester
INSERT INTO users (username, password, email, role) VALUES
    ('clo', '$2y$10$.phP5aBLLxg2NAkhnLhap.YXr/.mZj5rrXw189T.qfbHt3P1iK.mu', 'clo@autoecole.com', 'user');
-- Mot de passe : "clo123" (haché avec password_hash)

-- Lier l'utilisateur 'clo' à un apprenant
INSERT INTO apprenants (nom, prenom, email, user_id) VALUES
    ('paul', 'douala', 'cameroun@autoecole.com', 2);

-- Ajouter quelques formations
INSERT INTO formations (titre, description, prix, date_debut, date_fin) VALUES
                                                                            ('Permis B', 'Formation pour le permis de conduire B', 1200.00, '2025-04-01', '2025-06-30'),
                                                                            ('Code de la route', 'Cours théorique pour le code', 300.00, '2025-04-01', '2025-04-30');

-- Inscription de l'utilisateur 'clo' à une formation
INSERT INTO inscriptions (user_id, formation_id) VALUES
    (2, 1); -- Inscription de 'clo' au Permis B

-- Ajouter un paiement pour l'utilisateur 'clo'
INSERT INTO paiements (user_id, formation_id, montant, statut) VALUES
    (2, 1, 1200.00, 'en attente');

-- Ajouter une facture pour l'apprenant 'clo'
INSERT INTO factures (apprenant_id, montant_total, date_facture) VALUES
    (1, 1200.00, '2025-03-26');