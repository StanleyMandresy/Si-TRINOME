<?php
namespace app\models;

use Flight;
use PDO;
use Exception;
use app\models\User; 

class Departement {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Récupérer tous les départements
    public function getAllDepartement() {
        try {
            $sql = "SELECT * FROM Departement";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

    public function getAllIdDepartement() {
        try {
            $sql = "SELECT idDepartement FROM Departement";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

    // Récupérer un département par ID
    public function getDepartementByID($id) {
        try {
            $sql = "SELECT * FROM Departement WHERE idDepartement = '$id'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

    // Ajouter un département
    public function addDepartement($nom) {
        try {
            if (!empty($nom)) {
                $sql = "INSERT INTO Departement ( NomDepartement) 
                        VALUES ('$nom')";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                return 1;
            }
            return 0;
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return 0;
        }
    }

    // Modifier un département
    public function updateDepartement($id, $nom, $idChef) {
        $user=new User(Flight::db());
        try {
            $fields = [];
            if (!empty($nom)) {
                $fields[] = "NomDepartement = '$nom'";
            }
            if (!empty($idChef)) {
                $fields[] = "idChef = '$idChef'";
                $user->changeChef($id,$idChef);
            }

            if (empty($fields)) {
                return "Aucune mise à jour à effectuer.";
            }

            $sql = "UPDATE Departement SET " . implode(", ", $fields) . " WHERE idDepartement = '$id'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return 1;
        } catch (Exception $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    // Supprimer un département
    public function removeDepartement($id) {
        try {
            $sql = "DELETE FROM Departement WHERE idDepartement = '$id'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return "Département supprimé avec succès.";
        } catch (Exception $e) {
            return "Erreur : " . $e->getMessage();
        }
    }
    public function isFinance($id) {
        try {
            // Ensure the $id is an integer to prevent SQL injection risks
            $id = (int)$id;
    
            // Correct SQL query to check if the department exists and is 'finance'
            $sql = "SELECT COUNT(*) FROM Departement WHERE idDepartement = $id AND NomDepartement = 'finance'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            // Check if a matching record is found
            $result = $stmt->fetchColumn();
            
            if ($result > 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return "Erreur : " . $e->getMessage();
        }
    }
    
}
