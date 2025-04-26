<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/Periode.php';
require_once 'classes/Categorie.php';
require_once 'classes/DonneeBudget.php';
require_once 'classes/BudgetManager.php';

// Initialisation de la base de données
$config = require 'config/database.php';
$db = Database::getInstance($config)->getConnection();

// Création du gestionnaire de budget
$manager = new BudgetManager($db);

// Récupérer toutes les périodes pour le formulaire
$allPeriodes = $manager->getAllPeriodes();

// Gérer la sélection
$moisDebut = $_POST['mois_debut'] ?? $allPeriodes[0]->id;
$moisFin = $_POST['mois_fin'] ?? end($allPeriodes)->id;

// Récupérer les données filtrées
$data = $manager->prepareBudgetData($moisDebut, $moisFin);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau Budgétaire Mensuel</title>
</head>
<body>
    <h1>Tableau Budgétaire Mensuel</h1>
    
    <?php include 'templates/budget_table.php'; ?>
    
    <script>
    // Surbrillance des lignes au survol
    document.querySelectorAll('.budget-table tr').forEach(row => {
        row.addEventListener('mouseover', function() {
            this.style.backgroundColor = '#f5f5f5';
        });
        row.addEventListener('mouseout', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        const debut = parseInt(document.getElementById('mois_debut').value);
        const fin = parseInt(document.getElementById('mois_fin').value);
        
        if (debut > fin) {
            alert("Le mois de début doit être avant le mois de fin");
            e.preventDefault();
        }
    });
    </script>
</body>
</html>