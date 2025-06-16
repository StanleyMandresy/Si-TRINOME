<?php
namespace app\models;

use PDO;
use Exception;

class Statistiques {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Fonction pour récupérer les ventes totales d'une période et mettre à jour le budget
    public function VenteAllPeriode() {
        try {
            $sql = "
                SELECT periode_id, SUM(p.prix*v.quantite) AS total_ventes
                FROM vente v
                join Produit p
                on v.idProduit=p.idProduit
                GROUP BY periode_id
                ORDER BY periode_id
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            
        } catch (Exception $e) {
            error_log("Erreur dans VenteAllPeriode: " . $e->getMessage());
            return [];
        }
    }
    public function VenteByPeriode($periode_id = null) {
        try {
            $sql = "SELECT * FROM vente JOIN Produit on vente.idProduit=Produit.idProduit";
            
            if ($periode_id !== null) {
                $sql .= " WHERE periode_id = :periode_id";
            }
            
            $sql .= " GROUP BY periode_id,idClient ORDER BY periode_id DESC";
            
            $stmt = $this->db->prepare($sql);
            
            $params = [];
            if ($periode_id !== null) {
                $params[':periode_id'] = $periode_id;
            }
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erreur VenteByPeriode: " . $e->getMessage());
            return [];
        }
    }
   
    // Récupérer l'id de la catégorie "Recette"
    public function getIdCategorieVente() {
        $sql = "SELECT idCategorie FROM Categorie WHERE NomCategorie = 'vente' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row['idCategorie'];
        } else {
            throw new Exception("Catégorie Recette non trouvée.");
        }
    }



    public function getPeriodes() {
        $sql = "SELECT periode_id, nom_periode FROM periodes ORDER BY mois";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function calculerChiffreAffaire($periode_id) {
        try {
            // 1. Récupérer le chiffre d'affaires existant (s'il y en a un)
            $sqlCA = "SELECT montant FROM ChiffreAffaire WHERE periode_id = :periode_id";
            $stmtCA = $this->db->prepare($sqlCA);
            $stmtCA->execute(['periode_id' => $periode_id]);
            $existingCA = $stmtCA->fetch(PDO::FETCH_ASSOC);
            
            $baseCA = $existingCA['montant'] ?? 0; // Utilise le CA existant comme base ou 0
    
            // 2. Calculer les recettes (comme avant)
            $sqlRecettes = "SELECT SUM(b.Realisation) AS total_recettes
                           FROM budget b
                           JOIN Categorie c ON b.idCategorie = c.idCategorie
                           WHERE c.idNature = 1
                           AND b.periode_id = :periode_id";
            
            $stmtRecettes = $this->db->prepare($sqlRecettes);
            $stmtRecettes->execute(['periode_id' => $periode_id]);
            $recettes = $stmtRecettes->fetch(PDO::FETCH_ASSOC)['total_recettes'] ?? 0;
    
            // 3. Calculer l'impact CRM
            $sqlCrm = "SELECT SUM(c.pourcentChiffreAffaire) AS total_crm_pourcent 
                      FROM CRM c
                      JOIN CRMRETOUR cr ON c.idCRM = cr.idCRM 
                      WHERE cr.periode_id = 12
                      AND cr.isApproved = 1";


            
            $stmtCrm = $this->db->prepare($sqlCrm);
            $stmtCrm->execute();
            $crm = $stmtCrm->fetch(PDO::FETCH_ASSOC) ;
            var_dump($crm);
            $crmPourcent=$crm['total_crm_pourcent'];
            // 4. Calcul final (CA existant + recettes + impact CRM sur recettes)
            $nouveauCA =$baseCA + ($recettes + ($recettes * ($crmPourcent / 100)));
    
            // 5. Mise à jour en base
            if ($existingCA) {
                $sql = "UPDATE ChiffreAffaire SET montant = :montant WHERE periode_id = :periode_id";
            } else {
                $sql = "INSERT INTO ChiffreAffaire (periode_id, montant) VALUES (:periode_id, :montant)";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'montant' => $nouveauCA,
                'periode_id' => $periode_id
            ]);
    
            return $nouveauCA;
    
        } catch (Exception $e) {
            error_log("Erreur calcul CA: " . $e->getMessage());
            throw new Exception("Erreur lors du calcul du chiffre d'affaires");
        }
    }
    public function getCA($periode_id) {
        $sql = "SELECT montant FROM ChiffreAffaire WHERE periode_id = :periode_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['periode_id' => $periode_id]);
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['montant'] : 0;
    }   


    
    public function getAllCA() {
        $sql = "SELECT  periode_id, montant as chiffre_affaires FROM ChiffreAffaire group by periode_id ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
       
        
    }   
    
}
