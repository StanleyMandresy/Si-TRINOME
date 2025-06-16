<?php
namespace app\controllers;

use app\models\EtatModel;
use app\models\Statistiques;
use Flight;

class StatController {

    public function home() {
        $statistique = new Statistiques(Flight::db());
        $etat = new EtatModel(Flight::db());


         //pour tout
        
        $ventes = $statistique->VenteAllPeriode();
      
        $periodes = $statistique->getPeriodes();

        foreach ($periodes as $periode) {
            $statistique->calculerChiffreAffaire($periode['periode_id']);
        }

        $chiffres = $statistique->getAllCA();
        $caPeriode = null;
        $meilleurClient = null;
        $topProduits = null;
        $flopProduits = null;
        
        if (!empty($_POST['periode_id'])) {
            $periode_id = $_POST['periode_id'];
            $caPeriode = $statistique->getCA($periode_id);
            
            // Ajout des nouvelles statistiques pour la période
            $meilleurClient = $etat->getTop5Clients($periode_id);
            $topProduits = $etat->getTop5Produits($periode_id);
            $flopProduits = $etat->getFlop5Produits($periode_id);
        }
    
        Flight::render('Statpage', [
            'ventes' => $ventes,
            'chiffres' => $chiffres,
            'periodes' => $periodes,
            'caPeriode' => $caPeriode,
            'meilleurClient' => $meilleurClient,
            'topProduits' => $topProduits,
            'flopProduits' => $flopProduits
        ]);
    }
}