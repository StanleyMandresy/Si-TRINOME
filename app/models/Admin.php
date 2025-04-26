<?php
namespace app\models;

use PDO;
use Exception;

class Admin {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    public function findAdmin($nom,$mdp) {
        try {
          
            $sql = "SELECT * FROM hb_admin where pseudo='$nom' and passwords='$mdp';";  
            
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            if($stmt->rowCount() >= 1){
                return 1;
            }else{
                return 0;
            }
        
        } catch (Exception $e) {
            
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
  



    }

?>