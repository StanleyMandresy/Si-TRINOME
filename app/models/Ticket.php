<?php
namespace app\models;

use PDO;
use Exception;

class Ticket{

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

   public function getTicketsByClient($idClient) {
    $sql = "SELECT t.*, c.nom, c.prenom, p.nomProduit
            FROM Ticket t
            JOIN Client c ON c.idClient = t.id_client
            JOIN Produit p ON p.idProduit = t.idproduit_concerne
            WHERE t.id_client = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$idClient]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


   public function getTicketsAssignes($idAgent) {
        $sql = "SELECT t.*, c.nom, c.prenom, p.nomProduit
                FROM Ticket t
                JOIN Client c ON c.idClient = t.id_client
                JOIN Produit p ON p.idProduit = t.idproduit_concerne
                WHERE t.id_agent_assigne = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idAgent]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicketsNonAssignes() {
        $sql = "SELECT t.*, c.nom, c.prenom, p.nomProduit
                FROM Ticket t
                JOIN Client c ON c.idClient = t.id_client
                JOIN Produit p ON p.idProduit = t.idproduit_concerne
                WHERE t.id_agent_assigne IS NULL";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignerTicket($idTicket, $idAgent, $duree, $coutHoraire) {
        $sql = "UPDATE Ticket 
                SET id_agent_assigne = ?, duree = ?, coutHoraire = ?, statut = 1
                WHERE id_ticket = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idAgent, $duree, $coutHoraire, $idTicket]);
    }

    public function marquerCommeTraite($idTicket) {
        $sql = "UPDATE Ticket SET statut = 2 WHERE id_ticket = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idTicket]);
    }

   public function creerTicketDepuisRequete($data) {
    try {
        $this->db->beginTransaction();

        $sql = "INSERT INTO Ticket (
                    id_client, idproduit_concerne, id_type_demande,
                    priorite, id_requete_client, description, statut
                ) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['id_client'],
            $data['idproduit_concerne'],
            $data['id_type_demande'],
            $data['priorite'],
            $data['id_requete_client'],
            $data['description']
        ]);

        // Mise à jour classified dans Requete_client
        $sqlUpdate = "UPDATE Requete_client SET classified = TRUE WHERE id = ?";
        $stmtUpdate = $this->db->prepare($sqlUpdate);
        $stmtUpdate->execute([$data['id_requete_client']]);

        $this->db->commit();

        return true;
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;  // ou gérer l'erreur selon ton besoin
    }
}

       public function getIdTicket() {
        $sql = "SELECT idCategorie FROM Categorie WHERE NomCategorie = 'Ticket' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row['idCategorie'];
        } else {
            throw new Exception("Catégorie CRM non trouvée.");
        }
        }
    
        private function getTicketById($id) {
            $sql = "SELECT * FROM Ticket WHERE id_ticket = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getAllPeriodes() {
        $sql = "SELECT * FROM periodes ORDER BY periode_id DESC";
        $stmt = $this->db->query($sql);
        
        // Vérifier si on a des résultats
        $periodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Retourner un tableau vide si aucun résultat
        return $periodes ?: [];
    }

        public function SetPrevisionTicket($periode_id, $montantPrevision) {
        try {
            $idCategorie = $this->getIdTicket();
            $idDepartement = 4; // À adapter selon votre structure
            
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
            throw new Exception("Erreur lors de la mise à jour du budget Ticket: " . $e->getMessage());
        }
    }
    
    public function GetPrevisionTicket($periode_id) {
        $idCategorie = $this->getIdTicket();
        
        $sql = "SELECT Prevision,Realisation
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
            'Realisation' => 0,
        
        ];
    }

    public function executerTicket($TicketId, $periode_id) {
        try {
          
            $ticket = $this->getTicketById($TicketId);
            if (!$ticket) {
                throw new Exception("Ticket non trouvé.");
            }

            
            $budgetInfo = $this->GetPrevisionTicket($periode_id);
            $prevision = $budgetInfo['Prevision'] ?? 0;
            $realisation =$budgetInfo['Realisation'] ?? 0;

          
            $dureeHeures = $this->convertTimeToHours($ticket['duree']);
            $ticketCost = $ticket['coutHoraire'] * $dureeHeures;

        
            $isApproved = ($realisation + $ticketCost <= $prevision);
       

            if ($isApproved) {
               
                $updateTicketSql = "UPDATE Ticket SET statut = 2 WHERE id_ticket = :ticketId";
                $stmt = $this->db->prepare($updateTicketSql);
            
                $stmt->execute([':ticketId' => $TicketId]);
               
            }
            $idCategorie = $this->getIdTicket();
            $idDepartement = 4; 

           
            $checkSql = "SELECT idBudget, Realisation FROM budget 
                        WHERE idCategorie = :idCategorie 
                        AND periode_id = :periode_id";
            $stmt = $this->db->prepare($checkSql);
            $stmt->execute([
                ':idCategorie' => $idCategorie,
                ':periode_id' => $periode_id
            ]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            $newRealisation = ($existing['Realisation'] ?? 0) + $ticketCost;

            if ($existing) {
                // Update existing budget
                $sql = "UPDATE budget SET 
                        Realisation = :realisation,
                        isApproved = :isApproved
                        WHERE idBudget = :idBudget";
                $params = [
                    ':realisation' => $newRealisation,
                    ':isApproved' => $isApproved,
                    ':idBudget' => $existing['idBudget']
                ];
            } else {
                // Insert new budget record
                $sql = "INSERT INTO budget 
                        (idDepartement, idCategorie, Prevision, Realisation, periode_id, isApproved)
                        VALUES (:idDepartement, :idCategorie, 0, :realisation, :periode_id, :isApproved)";
                $params = [
                    ':idDepartement' => $idDepartement,
                    ':idCategorie' => $idCategorie,
                    ':realisation' => $newRealisation,
                    ':periode_id' => $periode_id,
                    ':isApproved' => $isApproved
                ];
            }

            // Execute budget update
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

        

            // Add movement record with prise_en_charge date
            $movementSql = "INSERT INTO Mouvement_ticket 
                        (idticket, date_prise_en_charge)
                        VALUES (:ticketId, NOW())";
            $stmt = $this->db->prepare($movementSql);
            $stmt->execute([':ticketId' => $TicketId]);

            return true;

        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'exécution du ticket: " . $e->getMessage());
        }
    }


    private function convertTimeToHours($time) {
        if (!$time) return 0;
        
        $parts = explode(':', $time);
        $hours = (int)$parts[0];
        $minutes = (int)$parts[1];
        $seconds = isset($parts[2]) ? (int)$parts[2] : 0;
        
        return $hours + ($minutes / 60) + ($seconds / 3600);
    }

    public function validerTicketPeriode($periode_id) {
    try {
 
        $stmt = $this->db->prepare("SELECT 1 FROM periodes WHERE periode_id = ?");
        $stmt->execute([$periode_id]);
        if (!$stmt->fetch()) {
            throw new Exception("Période invalide");
        }
 
        var_dump($periode_id);

        $sql = "UPDATE Ticket 
                SET statut = 2 
                WHERE id_ticket IN (
                    SELECT t.id_ticket
                    FROM Ticket t
                    JOIN budget b ON b.periode_id = ?
                    WHERE t.statut = 1
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$periode_id]);

     
        return $stmt->rowCount();

    } catch (Exception $e) {
        error_log("Erreur validation tickets période: " . $e->getMessage());
        throw new Exception("Erreur lors de la validation des tickets");
    }
}
public function ajouterEvaluation($idTicket, $note, $commentaire) {
    try {
        $this->db->beginTransaction();

        // Insérer l'évaluation
        $sqlEval = "INSERT INTO Evaluation (idticket, note_evaluation, commentaire)
                    VALUES (:idticket, :note, :commentaire)";
        $stmtEval = $this->db->prepare($sqlEval);
        $stmtEval->execute([
            ':idticket' => $idTicket,
            ':note' => $note,
            ':commentaire' => $commentaire
        ]);

        // Mettre à jour le statut du ticket à 3 (Fermé)
        $sqlTicket = "UPDATE Ticket SET statut = 3 WHERE id_ticket = :idticket";
        $stmtTicket = $this->db->prepare($sqlTicket);
        $stmtTicket->execute([':idticket' => $idTicket]);

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}

public function evaluationExiste($idTicket) {
    $sql = "SELECT 1 FROM Evaluation WHERE idticket = :idticket";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':idticket' => $idTicket]);

    return $stmt->fetchColumn() !== false;
}


    }


?>