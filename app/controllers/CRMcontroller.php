<?php

namespace app\controllers;

use app\models\CRMmodel;
use Flight;
use Exception;

class CRMcontroller {
    public function __construct() {
        // Initialisation si nécessaire
    }

    public function AfficherRetoursClients() {
        $CRMmodel = new CRMmodel(Flight::db());
        // $RetourModel = new RetourModel(Flight::db());
    
        $retours = $CRMmodel->getAllRetourClient();
        $crms = $CRMmodel->getAllCRMs();
    
        Flight::render('liste_retours', [
            'retours' => $retours,
            'crms' => $crms
        ]);
    }
    
    public function associateCRM() {
        $CRMmodel = new CRMmodel(Flight::db());
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Flight::json(['success' => false, 'message' => 'Méthode non autorisée'], 405);
            return;
        }

        $idCRM = $_POST['idCRM'] ?? null;
        $idRetour = $_POST['idRetour'] ?? null;
        $periode_id=$_POST['periode_id'] ?? null;

        if (empty($idCRM) || empty($idRetour)) {
            Flight::json(['success' => false, 'message' => 'Données manquantes'], 400);
            return;
        }

        $result = $CRMmodel->associateCRMToRetour($idCRM, $idRetour,$periode_id);

        if ($result) {
            Flight::json(['success' => true, 'message' => 'Association réussie']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de l\'association'], 500);
        }
    }
    public function showCRMretour() {
        $db = Flight::db();
        $crmModel = new CRMModel($db);
        
        // Récupérer toutes les périodes
        $periodes = $crmModel->getAllPeriodes();
        
        // Récupérer la période sélectionnée
        $selectedPeriode = Flight::request()->query->periode_id ?? ($periodes[0]['periode_id'] ?? null);
        
        $data = [
            'crmRetours' => [],
            'prevision' => 0,
            'totalCout' => 0,
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
           
        ];
        
        if ($selectedPeriode) {
            $data['crmRetours'] = $crmModel->getAllCRMRetour($selectedPeriode);
            $data['totalCout'] = array_sum(array_column($data['crmRetours'], 'cout'));
            
            $previsionData = $crmModel->GetPrevisionCRM($selectedPeriode);
            $data['prevision'] = $previsionData['Prevision'] ?? 0;
          
        }
        
        Flight::render('list_crm', $data);
    }
    
    public function validateCRM() {
        $db = Flight::db();
        $crmModel = new CRMModel($db);
        
        $periode_id = Flight::request()->data->periode_id;
        $montant = Flight::request()->data->montant;
        $ok = Flight::request()->data->ok;
        try {
            $crmModel->validationCRM($montant, $periode_id,$ok );
            Flight::json(['success' => true]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function savePrevision() {
        try {
            (new CRMModel(Flight::db()))->SetPrevisionCRM(
                Flight::request()->data->periode_id,
                (float)Flight::request()->data->montant
            );
            Flight::redirect('/pagevalidation');
        } catch (Exception $e) {
            Flight::redirect('/pagevalidation?error=1');
        }
    }

}