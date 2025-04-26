<?php
require_once('../app/models/Budget.php');
require_once('../app/BudgetPDF.php'); // Le fichier que nous venons de créer

// Récupération des paramètres
$idDepartement = $_GET['id_departement'] ?? null;
$startPeriod = $_GET['debut'] ?? 1;
$endPeriod = $_GET['fin'] ?? 1;
$isTotal = isset($_GET['total']);

// Génération des données
$model = new Budget();
if($isTotal) {
    $tableau = $model->generateTotalBudgetTable($startPeriod, $endPeriod);
    $title = "Tableau Budgétaire Consolidé";
} else {
    $tableau = $model->generateBudgetTable($idDepartement, $startPeriod, $endPeriod);
    $title = "Tableau Budgétaire - Département ".$idDepartement;
}

// Extraction des périodes uniques
$periods = array_column($tableau, 'periode');

// Génération du PDF
$pdf = new BudgetPDF($title, $periods, $tableau, $isTotal);
$pdf->AliasNbPages();
$pdf->GenerateTable();

// Output
$pdf->Output('D', 'budget_'.date('Y-m-d').'.pdf');
?>