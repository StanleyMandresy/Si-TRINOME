<?php

namespace app\controllers;

use app\models\ClientModel; 
use app\models\RetourModel; 
use Flight;

class ClientController {
    public function __construct() {
       
    }
    public function home(){
        Flight::render('ClientLog', []);
    }
    public function accueil() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }
    
        $email = $_POST['email'] ?? null;
        $mdp = $_POST['mdp'] ?? null;
    
        if (empty($email) || empty($mdp)) {
            Flight::json(['error' => 'Email et mot de passe requis'], 400);
            return;
        }
    
        $clientModel = new ClientModel(Flight::db());
        $loginResult = $clientModel->login($email, $mdp);
    
        if ($loginResult) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
    
            $_SESSION['idClient'] = $loginResult['idClient'];
            $model = new RetourModel(Flight::db());

            $typeCRM = $model->getTypesCRM();
            $produits = $model->getProduits();
    
            Flight::render('formulaire_retour', [
                'typeCRM' => $typeCRM,
                'produits' => $produits
            ]);
          
            // Flight::json(['success' => true, 'user' => $loginResult]);
        } else {
            Flight::json(['error' => 'Identifiants incorrects'], 401);
        }
    }
    
}