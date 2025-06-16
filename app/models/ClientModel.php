<?php
namespace app\models;

use Flight;
use PDO;
use Exception;
use app\models\User; 

class ClientModel{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($email, $mdp) {
        try {
            $sql = "SELECT idClient, nom, prenom, email
                    FROM Client 
                    WHERE email = :email AND mdp = :mdp";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':mdp' => $mdp
            ]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ? $user : false;
            
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }


}