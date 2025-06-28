<?php

namespace app\controllers;

use app\models\ClientModel; 
use app\models\RetourModel; 
use app\models\RequeteModel;
use app\models\Ticket;

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

     // 🔹 Liste des requêtes du client
  public function mesRequetes() {
    if (!isset($_SESSION['idClient'])) {
        Flight::redirect('/home');
        return;
    }

    $db = Flight::db();

    $requeteModel = new RequeteModel($db);
    $requetes = $requeteModel->getRequetesByClient($_SESSION['idClient']);

    $ticketModel = new Ticket($db);
    $tickets = $ticketModel->getTicketsByClient($_SESSION['idClient']);

    Flight::render('Ticket/listerequete', [
        'client' => $_SESSION['idClient'],
        'requetes' => $requetes,
        'tickets' => $tickets
    ]);
}


    // 🔹 Formulaire "Faire une requête"
    public function faireRequetePage() {
        if (!isset($_SESSION['idClient'])) {
            Flight::redirect('/home');
            return;
        }
 $model = new RetourModel(Flight::db());
          $produits = $model->getProduits();


        Flight::render('Ticket/form_requete', ['produits' => $produits]);
    }

    // 🔹 Insertion d'une requête
    public function insererRequete() {
        if (!isset($_SESSION['idClient'])) {
            Flight::redirect('/home');
            return;
        }

        $idProduit = $_POST['idProduit'] ?? null;
        $texte = trim($_POST['sujet'] ?? '');

        if (empty($idProduit) || empty($texte)) {
            Flight::render('Ticket/form_requete', ['error' => 'Veuillez remplir tous les champs']);
            return;
        }

        $requeteModel = new RequeteModel(Flight::db());
        $requeteModel->insertRequete($_SESSION['idClient'], $idProduit, $texte);

        Flight::redirect('/Ticket');
    }

    
}