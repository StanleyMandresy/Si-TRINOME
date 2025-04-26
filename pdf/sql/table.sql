CREATE DATABASE IF NOT EXISTS gestion_budget;
USE gestion_budget;

CREATE TABLE IF NOT EXISTS periodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mois DATE NOT NULL,
    nom VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    type ENUM('solde', 'recette', 'depense') NOT NULL
);

CREATE TABLE IF NOT EXISTS donnees_budget (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_periode INT,
    id_categorie INT,
    prevision DECIMAL(10,2),
    realisation DECIMAL(10,2),
    FOREIGN KEY (id_periode) REFERENCES periodes(id),
    FOREIGN KEY (id_categorie) REFERENCES categories(id)
);

-- Données de test
INSERT INTO periodes (mois, nom) VALUES 
('2023-01-01', 'Janvier 2023'),
('2023-02-01', 'Février 2023'),
('2023-03-01', 'Mars 2023'),
('2023-04-01', 'Avril 2023');

INSERT INTO categories (nom, type) VALUES

('Solde début', 'solde'),
('Recette type A', 'recette'),
('Dépense type X', 'depense'),
('Solde fin', 'solde');

INSERT INTO donnees_budget (id_periode, id_categorie, prevision, realisation) VALUES
-- Janvier
(1, 1, 0, 0),
(1, 2, 1000, 800),
(1, 3, 500, 600),
(1, 4, 500, 200),

-- Février
(2, 1, 500, 200),
(2, 2, 1200, 900),
(2, 3, 600, 700),
(2, 4, 1100, 400),

-- Mars
(3, 1, 1100, 400),
(3, 2, 1500, 1800),
(3, 3, 800, 600),
(3, 4, 1800, 1600),

-- Avril
(4, 1, 1800, 1600),
(4, 2, 2000, 2100),
(4, 3, 1000, 900),
(4, 4, 2800, 2800);





