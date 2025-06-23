INSERT INTO TypeCRM (nomTypeCRM, description) VALUES
('Support technique', 'Gestion des problèmes techniques rencontrés par les clients.'),
('Fidélisation', 'Programme visant à fidéliser les clients existants.'),
('Satisfaction client', 'Collecte et analyse du retour des clients.'),
('Réclamation', 'Traitement des plaintes ou insatisfactions des clients.'),
('Service après-vente', 'Suivi après achat et assistance personnalisée.');


INSERT INTO Client (nom, prenom, email, mdp)
VALUES ('Rakoto', 'Jean', 'jean.rakoto@example.com', '123');

INSERT INTO Client (nom, prenom, email, mdp)
VALUES ('Rakoto', 'Jean', 'jean.rakoto@example.com', '1234');


INSERT INTO Produit (nomProduit, description, prix, dateMiseEnMarche) VALUES
('Moto Électrique X1', 'Modèle urbain avec autonomie de 120 km.', 4599.99, '2023-03-15'),
('Moto Électrique X2', 'Modèle tout-terrain avec batterie renforcée.', 5899.50, '2023-06-01'),
('Moto Électrique CityLite', 'Moto légère idéale pour la ville.', 3899.00, '2022-11-10'),
('Moto Électrique ProMax', 'Haute performance pour longs trajets.', 7499.00, '2024-01-20'),
('Moto Électrique EcoRide', 'Modèle économique pour débutants.', 3199.95, '2022-08-05');

INSERT INTO CRM (NomCRM, cout, pourcentChiffreAffaire) VALUES
('Freshdesk', 150.00, 2.00),
('Salesforce', 1200.00, 5.00);

------------Ticket----------

INSERT INTO User (Nom, Motdepasse, Genre, Position, IdDepartement) VALUES
('Cindy', ' ', 'F', 'Chef', 1);




