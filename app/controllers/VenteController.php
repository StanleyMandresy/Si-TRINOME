<?php
namespace app\controllers;

use app\models\ImportModel;

use app\models\Statistiques;
use Flight;
class VenteController {
   

    public static function showForm() {
     
        $venteModel = new Statistiques(Flight::db());
         // Supposons que vous avez ce modèle
        
        // Récupérer toutes les périodes disponibles
        $periodes = $venteModel->getPeriodes();
        
        // Récupérer la période sélectionnée (si envoyée en GET)
        $selectedPeriode = Flight::request()->query->periode_id ?? null;
        
        // Récupérer les ventes (filtrées si période sélectionnée)
        $ventes = $venteModel->VenteByPeriode($selectedPeriode);
        
        Flight::render('list_vente', [
            'ventes' => $ventes ?: [],
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode
        ]);
    }

    public static function handleImport() {
      
        $model = new ImportModel(Flight::db());
    
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === 0) {
            $csvType = $_POST['csv_type'];
            $fileTmp = $_FILES['csv_file']['tmp_name'];
    
            $handle = fopen($fileTmp, 'r');
            if ($handle !== false) {
                $header = fgetcsv($handle);
                $rows = [];
                while (($data = fgetcsv($handle)) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
    
                $inserted = 0;
                if ($csvType === 'produits') {
                    $inserted = $model->importProduits($rows);
                } elseif ($csvType === 'ventes') {
                    $inserted = $model->importVentes($rows);
                }
    
                // Redirection avec message de succès
                Flight::redirect('/ventes?success='.urlencode("$inserted lignes insérées"));
                return;
            }
        }
    
        // Redirection avec message d'erreur
        Flight::redirect('/ventes?error='.urlencode('Erreur lors de l\'import'));
    }
}
