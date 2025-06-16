<?php
namespace app\models;

use Flight;
use PDO;
use Exception;
use app\models\User; 

class CRMmodel{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getAllPeriodes() {
        $sql = "SELECT * FROM periodes ORDER BY periode_id DESC";
        $stmt = $this->db->query($sql);
        
        // Vérifier si on a des résultats
        $periodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Retourner un tableau vide si aucun résultat
        return $periodes ?: [];
    }
    public function getAllRetourClient(){
        $sql="SELECT * FROM RetourClient JOIN TypeCRM ON RetourClient.idTypeCRM = TypeCRM.idTypeCRM;";
        $stmt = $this->db->prepare($sql);
            $stmt->execute();
        
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllCRMs() {
        try {
            $sql = "SELECT idCRM, NomCRM FROM CRM";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur dans getAllCRMs: " . $e->getMessage());
            return false;
        }
    }
    public function associateCRMToRetour($idCRM, $idRetour, $periode_id) {
        try {
            $sql = "INSERT INTO CRMRETOUR (idCRM, idRetour, periode_id) 
                    VALUES (:idCRM, :idRetour, :periode_id)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':idCRM' => $idCRM,
                ':idRetour' => $idRetour,
                ':periode_id' => $periode_id
            ]);
        } catch (Exception $e) {
            error_log("Erreur dans associateCRMToRetour: " . $e->getMessage());
            return false;
        }
    }
    
    public function getIdCRM() {
        $sql = "SELECT idCategorie FROM Categorie WHERE NomCategorie = 'CRM' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row['idCategorie'];
        } else {
            throw new Exception("Catégorie CRM non trouvée.");
        }
    }
    
    public function getAllCRMRetour($periode_id) {
        $sql = "
        SELECT 
            cr.id AS idCRMRETOUR,
            crm.NomCRM,
            crm.cout,
            crm.pourcentChiffreAffaire,
            p.nomProduit
        FROM CRMRETOUR cr
        JOIN CRM crm ON cr.idCRM = crm.idCRM
        JOIN RetourClient rc ON cr.idRetour = rc.idRetour
        JOIN Produit p ON rc.idProduit = p.idProduit
        WHERE cr.periode_id = " . intval($periode_id) . " AND isApproved = false";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function SetPrevisionCRM($periode_id, $montantPrevision) {
        try {
            $idCategorie = $this->getIdCRM();
            $idDepartement = 1; // À adapter selon votre structure
            
            // Vérifier si un budget existe déjà
            $checkSql = "SELECT idBudget FROM budget 
                        WHERE idCategorie = $idCategorie 
                        AND periode_id = " . intval($periode_id);
            
            $stmt = $this->db->prepare($checkSql);
            $stmt->execute();
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Mise à jour si existe déjà
                $sql = "UPDATE budget SET 
                        Prevision = " . floatval($montantPrevision) . ",
                        isApproved = FALSE
                        WHERE idBudget = " . intval($existing['idBudget']);
            } else {
                // Insertion si nouveau
                $sql = "INSERT INTO budget 
                        (idDepartement, idCategorie, Prevision, periode_id)
                        VALUES ($idDepartement, $idCategorie, " 
                        . floatval($montantPrevision) . ", " 
                        . intval($periode_id) . ")";
            }
            
            $this->db->exec($sql);
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour du budget CRM: " . $e->getMessage());
        }
    }
    
    public function GetPrevisionCRM($periode_id) {
        $idCategorie = $this->getIdCRM();
        
        $sql = "SELECT Prevision
                FROM budget 
                WHERE idCategorie = :idCategorie 
                AND periode_id = :periode_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':idCategorie' => $idCategorie,
            ':periode_id' => $periode_id
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Retourne un tableau avec des valeurs à 0 si aucun résultat
        return $result ?: [
            'Prevision' => 0,
        
        ];
    }
    
    public function validationCRM($montantCrm, $periode_id, $bool) {
        try {
            $idCategorie = $this->getIdCRM();
            $boolValue = $bool ? 1 : 0;
            
            // 1. Mettre à jour le budget (réalisation et approbation)
            $sqlBudget = "UPDATE budget SET 
                         Realisation = " . floatval($montantCrm) . ",
                         isApproved = $boolValue
                         WHERE idCategorie = $idCategorie
                         AND periode_id = " . intval($periode_id);
            
            $this->db->exec($sqlBudget);
            
            // 2. Mettre à jour les CRMRETOUR (approbation)
            $sqlCRMRetour = "UPDATE CRMRETOUR SET 
                            isApproved = $bool
                            WHERE periode_id = " . intval($periode_id);
          
            $this->db->exec($sqlCRMRetour);

            if($bool==1){
                $stat= new Statistiques(Flight::db());
                $stat->calculerChiffreAffaire($periode_id);

                }
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la validation CRM: " . $e->getMessage());
        }
    }

    public function validerCRMPeriod($periode_id) {
        try {
            // 1. Vérifier que la période existe
            $stmt = $this->db->prepare("SELECT 1 FROM periodes WHERE periode_id = ?");
            $stmt->execute([$periode_id]);
            if (!$stmt->fetch()) {
                throw new Exception("Période invalide");
            }
    
          
    
           
            $sqlCRM = "UPDATE CRMRETOUR 
                      SET isApproved = 1 
                      WHERE periode_id = ?";
            $stmt = $this->db->prepare($sqlCRM);
            $stmt->execute([$periode_id]);
            $stat= new Statistiques(Flight::db());
            $stat->calculerChiffreAffaire($periode_id);
            // 5. Retourner le nombre de CRM validés
            return $stmt->rowCount();
            
        } catch (Exception $e) {
            error_log("Erreur validation CRM période: " . $e->getMessage());
            throw new Exception("Erreur lors de la validation des CRM");
        }
    }

}