<?php

namespace app\controllers;
use app\models\Ticket;
use app\models\ProductModel;
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

        public function ShowTicklet() {
            try {
                $tickets = (new Ticket(Flight::db()))->getAllTicket();
                Flight::render('ticket_list', ['tickets' => $tickets]);
            } catch (Exception $e) {
                Flight::json(['error' => $e->getMessage()], 500);
            } 
        }
   
}