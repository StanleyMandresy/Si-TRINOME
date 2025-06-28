<?php
namespace app\models;

use PDO;
use Exception;

class ImportModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getIDVente() {
        try {
            $sql = "SELECT idCategorie FROM Categorie WHERE NomCategorie = 'Vente' LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['idCategorie'])) {
                return (int)$result['idCategorie'];
            }
            
            throw new Exception("Catégorie 'Vente' non trouvée dans la base de données");
            
        } catch (Exception $e) {
            error_log("Erreur dans getIDVente: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération de l'ID Vente");
        }
    }

    public function InsertVenteBudget() {
        try {
            // 1. Récupérer l'ID de la catégorie Vente
            $idCategorieVente = $this->getIDVente();
            $idDepartement = 1; // À adapter selon votre structure
    
            // 2. Activer les transactions pour plus de sécurité
            $this->db->beginTransaction();
    
            // 3. Récupérer toutes les périodes avec ventes
            $sqlPeriodes = "SELECT DISTINCT v.periode_id 
                           FROM vente v
                           JOIN periodes p ON v.periode_id = p.periode_id";
            $periodes = $this->db->query($sqlPeriodes)->fetchAll(PDO::FETCH_COLUMN);
    
            // 4. Pour chaque période, calculer et mettre à jour le budget
            foreach ($periodes as $periode_id) {
                // Calcul du total des ventes
                $sqlTotal = "SELECT SUM(p.prix * v.quantite) AS total
                            FROM vente v
                            JOIN Produit p ON v.idProduit = p.idProduit
                            WHERE v.periode_id = ?";
                
                $stmt = $this->db->prepare($sqlTotal);
                $stmt->execute([$periode_id]);
                $totalVentes = $stmt->fetchColumn();

                // Insertion ou mise à jour
                $sqlCheck = "SELECT COUNT(*) FROM budget 
                            WHERE idCategorie = ? 
                            AND periode_id = ?";
                $stmt = $this->db->prepare($sqlCheck);
                $stmt->execute([$idCategorieVente, $periode_id]);
                $exists = $stmt->fetchColumn() > 0;
    
                if ($exists) {
                    $sql = "UPDATE budget SET Realisation = ? 
                           WHERE idCategorie = ? AND periode_id = ?";
                } else {
                    $sql = "INSERT INTO budget 
                           (idDepartement, idCategorie, Realisation, periode_id) 
                           VALUES (?, ?, ?, ?)";
                }
    
                $params = $exists ? 
                    [$totalVentes, $idCategorieVente, $periode_id] :
                    [$idDepartement, $idCategorieVente, $totalVentes, $periode_id];
    
                $this->db->prepare($sql)->execute($params);
            }
    
            $this->db->commit();
            return count($periodes); // Retourne le nombre de périodes traitées
    
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Erreur updateAllPeriodesBudgetVente: " . $e->getMessage());
            throw new Exception("Erreur lors de la mise à jour des budgets");
        }
    } 

    public function importProduits($rows) {
        $stmt = $this->db->prepare("INSERT INTO Produit (nomProduit, description, prix, dateMiseEnMarche)
                                    VALUES (?, ?, ?, ?)");
        $count = 0;
        foreach ($rows as $row) {
            if (count($row) >= 4) {
                $stmt->execute([$row[0], $row[1], $row[2], $row[3]]);
                $count++;
            }
        }
        return $count;
    }

    public function importVentes($rows) {
        $stmt = $this->db->prepare("INSERT INTO vente (idProduit, quantite,idClient,periode_id)
                                    VALUES (?, ?,?, ?)");
        $count = 0;
        foreach ($rows as $row) {
            if (count($row) >= 4) {
                $stmt->execute([$row[0], $row[1], $row[2], $row[3]]);
                $count++;
            }
        }
        $this->InsertVenteBudget();
        return $count;
    }
}
