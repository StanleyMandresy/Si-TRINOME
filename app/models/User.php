<?php
namespace app\models;

use PDO;
use Exception;

class User{

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    public function findUser($nom,$mdp) {
        try {
          
            $sql = "SELECT * FROM 
            User where Nom='$nom' and Motdepasse='$mdp';";  
            
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            if($stmt->rowCount() >= 1){
                $user=$stmt->fetch(PDO::FETCH_ASSOC); 
          
                return $user;
           
            }else{
                return null;
            }
        
        } catch (Exception $e) {
            
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
    
    public function findAllUser() {
        try {
          
            $sql = "SELECT * FROM 
            User  join Departement on User.idDepartement=Departement.IdDepartement;";  
            
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();


            return $stmt->fetchAll(PDO::FETCH_ASSOC);

              
           
          
        
    } catch (Exception $e) {
            
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
    public function findAllUserByDepart($idD) {
        try {
          
            $sql = "SELECT * FROM 
            User  where idDepartement='$idD';";  
            
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();


            return $stmt->fetchAll(PDO::FETCH_ASSOC);

              
           
          
        
    } catch (Exception $e) {
            
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
  
  
    public function InsertUser($nom,$mdp,$genre,$idDepartement) {
        try {
          
            $sql = "insert into User(Nom,Motdepasse,Genre,idDepartement)
            values('$nom','$mdp','$genre','$idDepartement')";  
            
            
            

            if(!empty($nom) && !empty($genre) && !empty($mdp)  && !empty($idDepartement)){
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                return 1;
            }else{
                return 0;
            }
        
        } catch (Exception $e) {
            
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
  
    public function changeChef($idDepartement, $idChef) {
        try {
            // 1. Récupérer l'id du chef actuel du département
            $sql = "SELECT idChef FROM Departement WHERE idDepartement = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idDepartement]);
            $currentChef = $stmt->fetchColumn(); // Chef actuel du département
    
            // Si un chef est déjà affecté au département
            if ($currentChef) {
                // 2. Mettre à jour l'ancien chef de "Chef" à "Non Chef"
                $sql = "UPDATE User SET Position = 'Non Chef' WHERE idUser = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$currentChef]);
    
                // 3. Mettre à jour le nouveau chef de "Non Chef" à "Chef"
                $sql = "UPDATE User SET Position = 'Chef' WHERE idUser = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$idChef]);
    
                // 4. Mettre à jour l'idChef dans le département
                $sql = "UPDATE Departement SET idChef = ? WHERE idDepartement = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$idChef, $idDepartement]);
    
                return "Chef changé avec succès.";
    
            } else {
                // Si aucun chef n'est affecté au département
                // 5. Insérer idChef dans Departement et mettre sa position à "Chef"
                $sql = "UPDATE Departement SET idChef = ? WHERE idDepartement = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$idChef, $idDepartement]);
    
                // 6. Mettre à jour la position de l'utilisateur idChef en "Chef"
                $sql = "UPDATE User SET Position = 'Chef' WHERE idUser = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$idChef]);
    
                return "Chef ajouté avec succès.";
            }
        } catch (Exception $e) {
            // Gestion des erreurs
            return "Erreur : " . $e->getMessage();
        }
    }
    

    }

?>