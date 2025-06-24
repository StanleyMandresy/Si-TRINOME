<?php
namespace app\models;

use PDO;
use Exception;

class Ticket{

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
        public function getAllTicket() {
            $sql = "SELECT * FROM Ticket ORDER BY id_ticket DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
       public function getIdCRM() {
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
    public function executerTicket($TicketId, $periode_id) {
        try {
            // Get ticket information
            $ticket = $this->getTicketById($TicketId);
            if (!$ticket) {
                throw new Exception("Ticket non trouvé.");
            }

            // Get budget prevision for the period
            $budgetInfo = $this->GetPrevisionTicket($periode_id);
            $prevision = $budgetInfo['Prevision'] ?? 0;

            // Calculate ticket cost (coutHoraire * duree)
            // Note: You'll need to convert TIME to hours for calculation
            $dureeHeures = $this->convertTimeToHours($ticket['duree']);
            $ticketCost = $ticket['coutHoraire'] * $dureeHeures;

            // Check if cost exceeds prevision
            $isApproved = ($ticketCost <= $prevision);

            // Update or insert budget realization
            $idCategorie = $this->getIdTicket();
            $idDepartement = 4; // As per your structure

            // Check if budget exists
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

            // Update ticket status to "En cours" (1)
            $updateTicketSql = "UPDATE Ticket SET statut = 1 WHERE id_ticket = :ticketId";
            $stmt = $this->db->prepare($updateTicketSql);
            $stmt->execute([':ticketId' => $TicketId]);

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


    }


?>