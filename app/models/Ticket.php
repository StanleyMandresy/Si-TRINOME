<?php
namespace app\models;

use PDO;
use Exception;

class Ticket{

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


   

?>