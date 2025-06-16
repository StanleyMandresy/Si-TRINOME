create database CRM;
use CRM;

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
INSERT INTO Departement (NomDepartement) VALUES ('Technicien');
INSERT INTO Departement (NomDepartement) VALUES ('Vente');

INSERT INTO Categorie (idDepartement, NomCategorie, idNature)
VALUES
(1, 'CRM', 2), 
(2,'reparation moto',1),
(2,'piece',2),
(3,'vente',1);



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

CREATE TABLE ChiffreAffaire (
    periode_id INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (periode_id) REFERENCES periodes(periode_id)
);
CREATE TABLE TypeCRM (
    idTypeCRM INT PRIMARY KEY AUTO_INCREMENT,
    nomTypeCRM VARCHAR(50) NOT NULL,
    description TEXT
);
CREATE TABLE CRM (
    idCRM INT PRIMARY KEY AUTO_INCREMENT,
    NomCRM VARCHAR(60),
    cout DECIMAL(10,2),
    pourcentChiffreAffaire DECIMAL(5,2)   
    
);

-- CREATE TABLE Plainte (
--     idPlainte INT PRIMARY KEY AUTO_INCREMENT,
--     descriptionPlainte TEXT NOT NULL,
--     typePlainte ENUM('positif', 'negatif') NOT NULL,
--     categorie VARCHAR(50)
-- );

CREATE TABLE Produit (
    idProduit INT PRIMARY KEY AUTO_INCREMENT,
    nomProduit VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2),
    dateMiseEnMarche DATE
);

CREATE TABLE Client (
    idClient INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50),
    email VARCHAR(100),
    mdp VARCHAR(100)
);


CREATE TABLE RetourClient (
    idRetour INT PRIMARY KEY AUTO_INCREMENT,
    idClient INT NOT NULL,
    idTypeCRM INT NOT NULL,
    idProduit INT,
    dateRetour DATETIME DEFAULT CURRENT_TIMESTAMP,
    notesSupplementaires TEXT,
    FOREIGN KEY (idClient) REFERENCES Client(idClient),
    FOREIGN KEY (idTypeCRM) REFERENCES TypeCRM(idTypeCRM),
    FOREIGN KEY (idProduit) REFERENCES Produit(idProduit)
);
CREATE TABLE CRMRETOUR(
    id INT PRIMARY KEY AUTO_INCREMENT,
    idCRM INT,
    idRetour INT,
    periode_id int,
    isApproved BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (periode_id) REFERENCES periodes(periode_id),  

FOREIGN KEY (idRetour) REFERENCES RetourClient(idRetour),
FOREIGN KEY (idCRM) REFERENCES CRM(idCRM)
);
-- CREATE TABLE PlainteRetour (
--     idPlainteRetour INT PRIMARY KEY AUTO_INCREMENT,
--     idRetour INT NOT NULL,
--     idPlainte INT NOT NULL,
--     commentaire TEXT,
--     FOREIGN KEY (idRetour) REFERENCES RetourClient(idRetour),
--     FOREIGN KEY (idPlainte) REFERENCES Plainte(idPlainte),
--     UNIQUE KEY (idRetour, idPlainte)
-- );

-- CREATE TABLE produit (
--     idProduit INT PRIMARY KEY AUTO_INCREMENT,
--     Marque VARCHAR(255) NOT NULL,
--     prix_unitaire DECIMAL(15,2) NOT NULL
-- );

CREATE TABLE vente (
    idProduit INT,
    quantite INT NOT NULL,

    idClient int,
    periode_id INT NOT NULL,
    FOREIGN KEY (idClient) REFERENCES Client(idClient),
    FOREIGN KEY (idProduit) REFERENCES Produit(idProduit),
    FOREIGN KEY (periode_id) REFERENCES periodes(periode_id)
);



--------------------------Ticket--------------------------------
-- Table des types de demande
CREATE TABLE type_demande (
    id_type SERIAL PRIMARY KEY,
    nom_type VARCHAR(50) NOT NULL UNIQUE
);

-- Insertion des données types demandés
INSERT INTO type_demande (nom_type) VALUES
('Réparation'),
('Réclamation'),
('Assistance technique'),
('Demande d\'information'),
('Maintenance');

-- Table des tickets
CREATE TABLE tickets (
    id_ticket SERIAL PRIMARY KEY,                        -- Identifiant unique
    id_client INT NOT NULL,                              -- Clé étrangère vers table clients
    id_type_demande INT NOT NULL,                        -- Clé étrangère vers type_demande
    sujet TEXT NOT NULL,                                
    description TEXT,                                    -- Détail complet du problème
    statut VARCHAR(20) DEFAULT 'Reçu',                  -- Statut : Reçu, En traitement, Traité, etc.
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Date création ticket
    date_debut_traitement TIMESTAMP,                     -- Date début traitement
    date_cloture TIMESTAMP,                              -- Date clôture ticket
    assigne_a INT,                                       -- Clé étrangère vers table utilisateurs/agents
    cout NUMERIC(10,2) DEFAULT 0.00,                     -- Coût estimé ou réel du ticket

    -- Contraintes d'intégrité référentielle
    CONSTRAINT fk_type_demande FOREIGN KEY (id_type_demande) REFERENCES type_demande(id_type)
);




