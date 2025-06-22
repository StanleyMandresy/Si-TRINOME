<?php

namespace app\controllers;

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
   
}