-- Insérer des données pour le département avec idDepartement = 1
INSERT INTO Categorie (idDepartement, NomCategorie, idNature)
VALUES
(1, 'Fournitures Bureau', 2),    -- Exemple de catégorie de type "Dépense" pour le département 1
(1, 'Matériels Informatiques', 2),  -- Exemple de catégorie de type "Dépense" pour le département 1
(1, 'Recettes Diverses', 1);      -- Exemple de catégorie de type "Recette" pour le département 1


-- Période 1, Catégorie 1 (Fournitures Bureau) - Nouvelle ligne avec des valeurs différentes
INSERT INTO budget(idDepartement, idCategorie, Prevision, Realisation, DateBudget, periode_id)
VALUES
(1, 1, 6500.00, 5900.00, NOW(), 1);  -- budgetsupplémentaire pour le département 1, catégorie 1, période 1

-- Période 1, Catégorie 2 (Matériels Informatiques) - Nouvelle ligne
INSERT INTO budget(idDepartement, idCategorie, Prevision, Realisation, DateBudget, periode_id)
VALUES
(1, 2, 3500.00, 3800.00, NOW(), 1);  -- budgetsupplémentaire pour le département 1, catégorie 2, période 1

-- Période 1, Catégorie 3 (Recettes Diverses) - Nouvelle ligne
INSERT INTO budget(idDepartement, idCategorie, Prevision, Realisation, DateBudget, periode_id)
VALUES
(1, 3, 8000.00, 7800.00, NOW(), 1);  -- budgetsupplémentaire pour le département 1, catégorie 3, période 1

-- Période 2, Catégorie 1 (Fournitures Bureau) - Nouvelle ligne
INSERT INTO budget(idDepartement, idCategorie, Prevision, Realisation, DateBudget, periode_id)
VALUES
(1, 1, 6200.00, 6100.00, NOW(), 2);  -- budgetsupplémentaire pour le département 1, catégorie 1, période 2

-- Période 2, Catégorie 2 (Matériels Informatiques) - Nouvelle ligne
INSERT INTO budget(idDepartement, idCategorie, Prevision, Realisation, DateBudget, periode_id)
VALUES
(1, 2, 3600.00, 3500.00, NOW(), 2);  -- budgetsupplémentaire pour le département 1, catégorie 2, période 2

-- Période 2, Catégorie 3 (Recettes Diverses) - Nouvelle ligne
INSERT INTO budget(idDepartement, idCategorie, Prevision, Realisation, DateBudget, periode_id)
VALUES
(1, 3, 8200.00, 8000.00, NOW(), 2);  -- budgetsupplémentaire pour le département 1, catégorie 3, période 2

-- Période 3, Catégorie 1 (Fournitures Bureau) - Nouvelle ligne
INSERT INTO budget(idDepartement, idCategorie, Prevision, Realisation, DateBudget, periode_id)
VALUES
(1, 1, 6400.00, 6300.00, NOW(), 3);  -- budgetsupplémentaire pour le département 1, catégorie 1, période 3

-- Période 3, Catégorie 2 (Matériels Informatiques) - Nouvelle ligne
INSERT INTO budget(idDepartement, idCategorie, Prevision, Realisation, DateBudget, periode_id)
VALUES
(1, 2, 3700.00, 3600.00, NOW(), 3);  -- budgetsupplémentaire pour le département 1, catégorie 2, période 3

-- Période 3, Catégorie 3 (Recettes Diverses) - Nouvelle ligne
INSERT INTO budget(idDepartement, idCategorie, Prevision, Realisation, DateBudget, periode_id)
VALUES
(1, 3, 8400.00, 8200.00, NOW(), 3);  -- budgetsupplémentaire pour le département 1, catégorie 3, période 3
