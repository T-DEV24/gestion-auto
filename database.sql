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

-- Table des formateurs
CREATE TABLE formateurs (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            user_id INT NULL,
                            nom VARCHAR(100) NOT NULL,
                            prenom VARCHAR(100) NOT NULL,
                            email VARCHAR(191) UNIQUE NOT NULL,
                            specialite VARCHAR(255),
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Association apprenants/formateurs
CREATE TABLE apprenant_formateur (
                                     apprenant_id INT NOT NULL,
                                     formateur_id INT NOT NULL,
                                     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                     PRIMARY KEY (apprenant_id, formateur_id),
                                     FOREIGN KEY (apprenant_id) REFERENCES apprenants(id) ON DELETE CASCADE,
                                     FOREIGN KEY (formateur_id) REFERENCES formateurs(id) ON DELETE CASCADE
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

-- Table des journaux d'authentification
CREATE TABLE auth_logs (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           user_id INT NULL,
                           action VARCHAR(50) NOT NULL,
                           ip_address VARCHAR(64) NULL,
                           user_agent VARCHAR(255) NULL,
                           details TEXT NULL,
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des refresh tokens
CREATE TABLE refresh_tokens (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                user_id INT NOT NULL,
                                token_hash CHAR(64) NOT NULL,
                                expires_at DATETIME NOT NULL,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                revoked_at DATETIME NULL,
                                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                                UNIQUE KEY uniq_refresh_token_hash (token_hash)
);

-- Table des chats
CREATE TABLE chats (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       type ENUM('direct', 'groupe') NOT NULL,
                       name VARCHAR(255) NULL,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE chat_participants (
                                   chat_id INT NOT NULL,
                                   user_id INT NOT NULL,
                                   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                   PRIMARY KEY (chat_id, user_id),
                                   FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
                                   FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE chat_messages (
                               id INT AUTO_INCREMENT PRIMARY KEY,
                               chat_id INT NOT NULL,
                               user_id INT NOT NULL,
                               message TEXT NOT NULL,
                               created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                               FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
                               FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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



ALTER TABLE paiements MODIFY COLUMN statut VARCHAR(50);

UPDATE paiements 
SET statut = 'à payer à la caisse' 
WHERE statut = 'en attente';

ALTER TABLE factures ADD COLUMN code_facture VARCHAR(20) UNIQUE;

CREATE INDEX idx_users_username ON users (username);
CREATE INDEX idx_users_email ON users (email);
CREATE INDEX idx_users_role ON users (role);
CREATE INDEX idx_apprenants_user_id ON apprenants (user_id);
CREATE INDEX idx_apprenants_email ON apprenants (email);
CREATE INDEX idx_apprenant_formateur_formateur_id ON apprenant_formateur (formateur_id);
CREATE INDEX idx_formateurs_user_id ON formateurs (user_id);
CREATE INDEX idx_inscriptions_user_id ON inscriptions (user_id);
CREATE INDEX idx_inscriptions_formation_id ON inscriptions (formation_id);
CREATE INDEX idx_paiements_user_id ON paiements (user_id);
CREATE INDEX idx_paiements_formation_id ON paiements (formation_id);
CREATE INDEX idx_factures_apprenant_id ON factures (apprenant_id);
CREATE INDEX idx_auth_logs_user_id ON auth_logs (user_id);
CREATE INDEX idx_auth_logs_action ON auth_logs (action);
CREATE INDEX idx_refresh_tokens_user_id ON refresh_tokens (user_id);
CREATE INDEX idx_chat_participants_user_id ON chat_participants (user_id);
CREATE INDEX idx_chat_messages_chat_id ON chat_messages (chat_id);
CREATE INDEX idx_chat_messages_user_id ON chat_messages (user_id);

SET @counter = 0;
UPDATE factures 
SET code_facture = CONCAT('FACT-', YEAR(CURDATE()), '-', LPAD(@counter := @counter + 1, 4, '0'))
WHERE code_facture IS NULL OR code_facture = '';
