<?php
namespace app\models;

use PDO;
use Exception;

class ChatModel {
    private $db;
 public function __construct($db) {
        $this->db = $db;
    }
 
public function getClientInfo($clientId) {
    $sql = "SELECT * FROM client WHERE idClient = :clientId";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':clientId' => $clientId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function getTicketWithClient($ticketId) {
    $sql = "SELECT t.*, c.nom, c.prenom, c.email 
            FROM Ticket t
            JOIN client c ON t.id_client = c.idClient
            WHERE t.id_ticket = :ticketId";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':ticketId' => $ticketId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
    // Envoyer un message du client
    public function sendClientMessage($ticketId, $clientId, $message) {
        try {
            // Vérifier si le ticket existe et récupérer l'agent assigné
            $ticket = $this->getTicketInfo($ticketId);
            if (!$ticket) {
                throw new Exception("Ticket non trouvé.");
            }

            $agentId = $ticket['id_agent_assigne'];
            if (!$agentId) {
                throw new Exception("Aucun agent assigné à ce ticket.");
            }

            $sql = "INSERT INTO Chat 
                    (idclient, id_agent_assigne, commentaire_client, id_ticket) 
                    VALUES (:clientId, :agentId, :message, :ticketId)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':clientId' => $clientId,
                ':agentId' => $agentId,
                ':message' => $message,
                ':ticketId' => $ticketId
            ]);

            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'envoi du message: " . $e->getMessage());
        }
    }

    // Envoyer un message de l'agent
    public function sendAgentMessage($ticketId, $agentId, $message) {
        try {
            // Vérifier si le ticket existe et récupérer le client
            $ticket = $this->getTicketInfo($ticketId);
            if (!$ticket) {
                throw new Exception("Ticket non trouvé.");
            }

            $clientId = $ticket['id_client'];

            $sql = "INSERT INTO Chat 
                    (idclient, id_agent_assigne, commentaire_agent, id_ticket) 
                    VALUES (:clientId, :agentId, :message, :ticketId)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':clientId' => $clientId,
                ':agentId' => $agentId,
                ':message' => $message,
                ':ticketId' => $ticketId
            ]);

            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'envoi du message: " . $e->getMessage());
        }
    }

    // Lister les messages d'un ticket
    public function getTicketMessages($ticketId) {
        $sql = "SELECT * FROM Chat 
                WHERE id_ticket = :ticketId 
                ORDER BY timestamp ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ticketId' => $ticketId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marquer les messages comme lus (pour l'agent ou le client)
    public function markMessagesAsRead($ticketId, $userType) {
        try {
            if ($userType === 'agent') {
                $sql = "UPDATE Chat SET commentaire_client = NULL 
                        WHERE id_ticket = :ticketId AND commentaire_client IS NOT NULL";
            } elseif ($userType === 'client') {
                $sql = "UPDATE Chat SET commentaire_agent = NULL 
                        WHERE id_ticket = :ticketId AND commentaire_agent IS NOT NULL";
            } else {
                throw new Exception("Type d'utilisateur invalide.");
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':ticketId' => $ticketId]);

            return $stmt->rowCount();
        } catch (Exception $e) {
            throw new Exception("Erreur lors du marquage des messages: " . $e->getMessage());
        }
    }

    // Compter les messages non lus
    public function countUnreadMessages($ticketId, $userType) {
        try {
            if ($userType === 'agent') {
                $sql = "SELECT COUNT(*) as unread_count FROM Chat 
                        WHERE id_ticket = :ticketId AND commentaire_client IS NOT NULL";
            } elseif ($userType === 'client') {
                $sql = "SELECT COUNT(*) as unread_count FROM Chat 
                        WHERE id_ticket = :ticketId AND commentaire_agent IS NOT NULL";
            } else {
                throw new Exception("Type d'utilisateur invalide.");
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':ticketId' => $ticketId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['unread_count'] ?? 0;
        } catch (Exception $e) {
            throw new Exception("Erreur lors du comptage des messages: " . $e->getMessage());
        }
    }

    // Récupérer les informations du ticket
    private function getTicketInfo($ticketId) {
        $sql = "SELECT * FROM Ticket WHERE id_ticket = :ticketId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ticketId' => $ticketId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}