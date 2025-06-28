<?php

namespace app\controllers;

use app\models\ProductModel;
use app\models\RequeteModel;
use app\models\Ticket;
use app\models\Typedemande;
use app\models\User;
use Flight;

class TicketController {

	public function __construct() {

	}

	    public function savePrevision() {
        try {
            (new Ticket(Flight::db()))->SetPrevisionTicket(
                Flight::request()->data->periode_id,
                (float)Flight::request()->data->montant
            );
            Flight::redirect('/pagevalidation');
        } catch (Exception $e) {
            Flight::redirect('/pagevalidation?error=1');
        }
    }

     public function listeTicketsAssignes() {
        $ticket = new Ticket(Flight::db());
        $tickets = $ticket->getTicketsAssignes($_SESSION['idUser']);
        Flight::render('Ticket/tickets_assignes', ['tickets' => $tickets]);
    }

    public function traiterTicket() {
        try {
            if (!isset($_POST['id_ticket']) || !isset($_POST['periode'])) {
                throw new Exception("Paramètres manquants.");
            }

            $id_ticket = intval($_POST['id_ticket']);
            $periode_id = intval($_POST['periode']);

            $ticket = new Ticket(Flight::db());
            $result = $ticket->executerTicket($id_ticket, $periode_id);

            if ($result) {
                // Redirection ou message
                $_SESSION['message'] = "Le ticket #$id_ticket a été traité avec succès.";
            } else {
                $_SESSION['error'] = "Le ticket n’a pas pu être traité.";
            }

        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
        }

        // Redirection vers la liste des tickets assignés
        // header("Location: /Ticket/tickets-assignes");
        // exit;
    }
    public function listeTicketsNonAssignes() {
        $ticket = new Ticket(Flight::db());
        $tickets = $ticket->getTicketsNonAssignes();
        $users = (new User(Flight::db()))->findAllUser();
        Flight::render('Ticket/tickets_non_assignes', [
            'tickets' => $tickets,
            'agents' => $users
        ]);
    }

    public function assignerTicket() {
        if (isset($_POST['id_ticket'], $_POST['id_agent'], $_POST['duree'], $_POST['coutHoraire'])) {
            $ticket = new Ticket(Flight::db());
            $ticket->assignerTicket($_POST['id_ticket'], $_POST['id_agent'], $_POST['duree'], $_POST['coutHoraire']);
            Flight::redirect('/Ticket/liste-ticket');
        }
    }

    public function listeRequetesClient() {
        $requete = new RequeteModel(Flight::db());
        $requetes = $requete->getAllRequetes();
             
        $type = new Typedemande(Flight::db());
           $types=$type->getAllTypes();
        Flight::render('Ticket/requetes_a_classifier', [
            'requetes' => $requetes,
            'types' => $types
        ]);
    }

    public function classifierRequete() {
        if (isset($_POST['id_requete_client'], $_POST['id_type'], $_POST['priorite'], $_POST['description'])) {
            $requete = (new RequeteModel(Flight::db()))->getRequeteById($_POST['id_requete_client']);
            $ticket = new Ticket(Flight::db());

            $data = [
                'id_client' => $requete['idclient'],
                'idproduit_concerne' => $requete['idproduit_concerne'],
                'id_type_demande' => $_POST['id_type'],
                'priorite' => $_POST['priorite'],
                'id_requete_client' => $_POST['id_requete_client'],
                'description' => $_POST['description']
            ];

            $ticket->creerTicketDepuisRequete($data);
            Flight::redirect('/Ticket/liste-ticket');
        }
    }
     public function FormEvaluation()
    {
        $ticketId = $_GET['id'] ?? null;

        if (!$ticketId) {
            Flight::json(['error' => 'ID du ticket manquant'], 400);
            return;
        }

           $ticket = new Ticket(Flight::db());
        if ($ticket->evaluationExiste($ticketId)) {
            Flight::redirect('/Ticket'); 
            return;
        }

        Flight::render('Ticket/form-evaluation', ['ticketId' => $ticketId]);
    }
    public function soumettreEvaluation() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ticketId = $_POST['id_ticket'] ?? null;
        $note = $_POST['note'] ?? null;
        $commentaire = trim($_POST['commentaire'] ?? '');

        if (!$ticketId || !$note || empty($commentaire)) {
            Flight::json(['error' => 'Tous les champs sont requis.'], 400);
            return;
        }

     $ticket = new Ticket(Flight::db());

        if ($ticket->evaluationExiste($ticketId)) {
            Flight::json(['error' => 'Évaluation déjà soumise.'], 409);
            return;
        }

        try {
            $ticket->ajouterEvaluation($ticketId, $note, $commentaire);
            Flight::redirect('/Ticket'); // ou message de succès
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }
}

   
}