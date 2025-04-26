create database SI;
use SI;

CREATE TABLE Nature (
  idNature INT PRIMARY KEY AUTO_INCREMENT,
  nomNature VARCHAR(50) NOT NULL
);

-- Insérer les valeurs "Recette" et "Dépense"
INSERT INTO Nature (nomNature) VALUES ('Recette'), ('Dépense');

CREATE TABLE Departement (
    idDepartement INT PRIMARY KEY AUTO_INCREMENT,  -- identifiant unique pour le département
    NomDepartement VARCHAR(255) NOT NULL  -- nom du département (ex: Finance)
);



CREATE TABLE User (
    idUser INT PRIMARY KEY AUTO_INCREMENT,  -- identifiant unique pour l'utilisateur
    Nom VARCHAR(255) NOT NULL,              -- nom de l'utilisateur
    Motdepasse varchar(30) NOT NULL,
    Genre ENUM('H', 'F') NOT NULL,  -- genre de l'utilisateur
    Position ENUM('Chef', 'Non Chef') NOT NULL DEFAULT 'Non Chef',  -- Position (Chef ou Non Chef)
    IdDepartement INT,                      -- référence à l'id du département
    FOREIGN KEY (IdDepartement) REFERENCES Departement(idDepartement) ON DELETE CASCADE   -- clé étrangère vers la table Departement
);

ALTER TABLE Departement 
ADD COLUMN idChef INT NULL,  -- ajout de la colonne idChef
ADD FOREIGN KEY (idChef) REFERENCES User(idUser) ON DELETE CASCADE  ;

CREATE TABLE Categorie (
    idCategorie INT PRIMARY KEY AUTO_INCREMENT,  -- identifiant unique pour la catégorie
    idDepartement INT,                           -- référence à l'id du département
    NomCategorie VARCHAR(255) NOT NULL,               -- libellé de la catégorie (ex: Fourniture)
    idNature INT,                                -- référence à l'id de la nature (Dépense ou Recette)
    FOREIGN KEY (idDepartement) REFERENCES Departement(idDepartement) ON DELETE CASCADE  ,  -- clé étrangère vers la table Departement
    FOREIGN KEY (idNature) REFERENCES Nature(idNature)  ON DELETE CASCADE   -- clé étrangère vers la table Nature
);


CREATE TABLE Type (
  idType INT PRIMARY KEY AUTO_INCREMENT,
  idCategorie int,
  Nomtype varchar(100),
  FOREIGN KEY (idCategorie) REFERENCES Categorie(idCategorie) ON DELETE CASCADE
);
INSERT INTO Departement (NomDepartement) VALUES ('Finance');
;
INSERT INTO Departement (NomDepartement) VALUES ('Informatique');
INSERT INTO Departement (NomDepartement) VALUES ('Maintenance');

CREATE TABLE periodes (
    periode_id INT PRIMARY KEY AUTO_INCREMENT,
    nom_periode VARCHAR(50) NOT NULL,
    mois INT NOT NULL -- Mois pour référence (1-12)
);
INSERT INTO periodes (periode_id,nom_periode, mois) VALUES
(1,'P1', 1),
(2,'P2', 2),
(3,'P3', 3),
(4,'P4', 4),
(5,'P5', 5),
(6,'P6', 6),
(7,'P7', 7),
(8,'P8', 8),
(9,'P9', 9),
(10,'P10', 10),
(11,'P11', 11),
(12,'P12', 12);

-- Table Budget
CREATE TABLE budget(
  idBudget INT PRIMARY KEY AUTO_INCREMENT,
  idDepartement INT NOT NULL,
  idCategorie INT NOT NULL,
  Prevision DECIMAL(15,2) DEFAULT 0.00,
  Realisation DECIMAL(15,2) DEFAULT 0.00,
  Ecart DECIMAL(15,2) GENERATED ALWAYS AS (Realisation - Prevision) STORED,
  DateBudget DATE NOT NULL DEFAULT CURRENT_DATE,
  periode_id INT NOT NULL, -- Lier le budget à une période
 
  isApproved BOOLEAN NOT NULL DEFAULT FALSE,  -- Valeur par défaut FALSE pour approbation
  FOREIGN KEY (idDepartement) REFERENCES Departement(idDepartement),
  FOREIGN KEY (idCategorie) REFERENCES Categorie(idCategorie),
  FOREIGN KEY (periode_id) REFERENCES periodes(periode_id) -- Lier à la table des périodes
);


-- Ajouter une colonne pour l'approbation dans la table Budget
ALTER TABLE budget ADD COLUMN isApproved BOOLEAN NOT NULL DEFAULT FALSE;

-- Table des Périodes


-- Table SoldePrevision
CREATE TABLE SoldePrevision(
  idSolde INT PRIMARY KEY AUTO_INCREMENT,
  solde_debut DECIMAL(15,2)DEFAULT 0.00,  -- Valeur par défaut de 0 pour solde_debut
  solde_fin DECIMAL(15,2)  DEFAULT 0.00,  -- Valeur par défaut de 0 pour solde_fin
  Recette DECIMAL(15,2) DEFAULT 0.00,  -- Valeur par défaut de 0 pour Recette
  Depense DECIMAL(15,2)  DEFAULT 0.00,  -- Valeur par défaut de 0 pour Depense
  periode_id INT NOT NULL,
  idDepartement INT NOT NULL,
  FOREIGN KEY (idDepartement) REFERENCES Departement(idDepartement) ON DELETE CASCADE,
  FOREIGN KEY (periode_id) REFERENCES periodes(periode_id) ON DELETE CASCADE
);


-- Table SoldeRealisation
CREATE TABLE SoldeRealisation(
  idSolde INT PRIMARY KEY AUTO_INCREMENT,
  solde_debut DECIMAL(15,2) DEFAULT 0.00,  -- Valeur par défaut de 0 pour solde_debut
  solde_fin DECIMAL(15,2) DEFAULT 0.00,  -- Valeur par défaut de 0 pour solde_fin
  Recette DECIMAL(15,2) DEFAULT 0.00,  -- Valeur par défaut de 0 pour Recette
  Depense DECIMAL(15,2) DEFAULT 0.00,  -- Valeur par défaut de 0 pour Depense
  periode_id INT NOT NULL,
  idDepartement INT NOT NULL,
  FOREIGN KEY (idDepartement) REFERENCES Departement(idDepartement) ON DELETE CASCADE,
  FOREIGN KEY (periode_id) REFERENCES periodes(periode_id) ON DELETE CASCADE
);





-- SOURCE /opt/lampp/htdocs/SI-RUBRIQUE/sql/base-tp.sql;





