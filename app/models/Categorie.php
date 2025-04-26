<?php
namespace app\models;

use PDO;
use Exception;

class Categorie {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    public function getAllnature(){
        try{
 
            $sql = "select * from Nature ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        } catch (Exception $e) {
            
            echo "Error: " . $e->getMessage();
            return [];
        }
    
    }
    public function getAllCategorie() {

    try{
 
        $sql = "select * from Categorie join Nature on Categorie.idNature=Nature.idNature ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    } catch (Exception $e) {
        
        echo "Error: " . $e->getMessage();
        return [];
    }


    }   


    public function getAllCategorieByDept($id) {

        try{
     
            $sql = "select * from Categorie WHERE idDepartement='$id' ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        } catch (Exception $e) {
            
            echo "Error: " . $e->getMessage();
            return [];
        }
    
    
        }  

    public function getCategoriebyID($id) {

        try{
     
            $sql = "select * from Categorie where idCategorie='$id'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        } catch (Exception $e) {
            
            echo "Error: " . $e->getMessage();
            return [];
        }
    
    
        }
   
      
        public function addCategorie($nom, $nature, $idDepartement) {
            try {
               
                if (!empty($nom) && !empty($nature)  && !empty($idDepartement)) {
                   
                    $sql = "INSERT INTO Categorie (nomCategorie, idNature, idDepartement) 
                            VALUES ('$nom', $nature, $idDepartement)";
                    
                 
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute();
                    
                    return 1; 
                }
                
                return 0; 
                
            } catch (Exception $e) {

                echo "Error: " . $e->getMessage();
                return [];
            }
        }
        
        
        
        public function changeCategorie($idcategorie, $nom, $nature, $idDepartement) {
            try {
               
                $fields = [];
                
          
                if (!empty($nom)) {
                    $fields[] = "nomCategorie = '$nom'";
                }
                if (!empty($nature)) {
                    $fields[] = "idNature = $nature";
                }
              
                if (!empty($idDepartement)) {
                    $fields[] = "idDepartement = $idDepartement";
                }
                
                
                if (empty($fields)) {
                    return "Aucun champ à mettre à jour.";
                }
                
              
                $sql = "UPDATE Categorie SET " . implode(", ", $fields) . " WHERE idCategorie = $idcategorie";
                
               
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                
                return 1; 
                
            } catch (Exception $e) {
                
                return "Erreur : " . $e->getMessage();
            }
        }
        
        
        public function removeCategorie($idcategorie) {
            try {
            
                
                $sql = "Delete from Categorie where idCategorie='$idcategorie'";
        
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
        
                return "Habitation mise à jour avec succès.";
            } catch (Exception $e) {
                return "Erreur : " . $e->getMessage();
            }
        }






}

  