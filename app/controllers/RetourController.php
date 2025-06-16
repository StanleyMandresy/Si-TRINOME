<?php 
namespace app\controllers;

use app\models\RetourModel;
use Flight;

class RetourController {
    public function formulaireRetour() {
        $model = new RetourModel(Flight::db());

        $typeCRM = $model->getTypesCRM();
        $produits = $model->getProduits();

        Flight::render('formulaire_retour', [
            'typeCRM' => $typeCRM,
            'produits' => $produits
        ]);
    }
    // public function formulaireRetour() {
    //     $model = new RetourModel(Flight::db());

    //     $typeCRM = $model->getTypesCRM();
    //     // Debug pour vérifier les données
    // var_dump($typeCRM);

    //     Flight::render('formulaire_retour.php', ['typeCRM' => $typeCRM]);
    //     print($typeCRM);
    // }

    public function envoyerRetour() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $idClient = $_SESSION['idClient'] ?? null;

        if (!$idClient) {
            Flight::json(['error' => 'Utilisateur non connecté'], 401);
            return;
        }

        $idTypeCRM = $_POST['idTypeCRM'] ?? null;
        $idProduit = $_POST['idProduit'] ?? null;
        $notes = $_POST['notesSupplementaires'] ?? null;

        if (!$idTypeCRM || !$idProduit) {
            Flight::json(['error' => 'Champs obligatoires manquants'], 400);
            return;
        }

        $model = new RetourModel(Flight::db());
        $success = $model->insererRetour($idClient, $idTypeCRM, $idProduit, $notes);

        if ($success) {
            Flight::json(['success' => true, 'message' => 'Retour enregistré.']);
        } else {
            Flight::json(['error' => 'Échec de l\'enregistrement du retour.'], 500);
        }
    }
}
