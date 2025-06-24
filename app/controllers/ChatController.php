<?php

namespace app\controllers;

use app\models\ChatModel;
use Flight;

class ChatController {
    private $chatModel;



    public function __construct() {
  
    }

    // Afficher l'interface de chat pour un ticket
    public function showChat() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $ticketId = $_GET['ticket_id'] ?? null;
        $clientId = $_GET['client_id'] ?? null;
        $userType = $_GET['user_type'] ?? 'client'; // 'client' ou 'agent'

        if (!$ticketId || !$clientId) {
            Flight::json(['error' => 'ID du ticket ou du client manquant'], 400);
            return;
        }

        try {
            $this->chatModel = new ChatModel(Flight::db());
$messages = $this->chatModel->getTicketMessages($ticketId);
            
            // Compter les messages non lus
            $unreadCount = $this->chatModel->countUnreadMessages($ticketId, $userType);

            // Récupérer les infos du client si nécessaire
            $clientInfo = $this->chatModel->getClientInfo($clientId);

            Flight::render('chat_interface', [
                'ticketId' => $ticketId,
                'clientId' => $clientId,
                'clientInfo' => $clientInfo,
                'messages' => $messages,
                'userType' => $userType,
                'unreadCount' => $unreadCount,
                'userId' => $_SESSION['idClient'] ?? $_SESSION['idAgent'] ?? null
            ]);

        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    
    public function sendMessage() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Flight::json(['error' => 'Méthode non autorisée'], 405);
        return;
    }

    // Récupérer les données du formulaire
    $ticketId = $_POST['ticket_id'] ?? null;
    $message = $_POST['message'] ?? null;
    $userType = $_POST['user_type'] ?? 'client';

    if (!$ticketId || !$message) {
        Flight::json(['error' => 'Données manquantes'], 400);
        return;
    }

        try {
            $messageId = null;

            if ($userType === 'client') {
                $clientId = 1 /*$_SESSION['idClient'] */ ?? null;
                if (!$clientId) {
                    Flight::json(['error' => 'Session client non trouvée'], 401);
                    return;
                }
                $this->chatModel = new ChatModel(Flight::db());
                $messageId = $this->chatModel->sendClientMessage($ticketId, $clientId, $message);
            } else {
                $agentId = 1 /*$_SESSION['id_agent'] */ ?? null;
                if (!$agentId) {
                    Flight::json(['error' => 'Session agent non trouvée'], 401);
                    return;
                }
            $this->chatModel = new ChatModel(Flight::db());

                $messageId = $this->chatModel->sendAgentMessage($ticketId, $agentId, $message);
            }

            Flight::json([
                'success' => true,
                'message_id' => $messageId,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // Récupérer les nouveaux messages (polling)
    public function getMessages() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        $ticketId = $_GET['ticket_id'] ?? null;
        $lastMessageId = $_GET['last_message_id'] ?? 0;

        if (!$ticketId) {
            Flight::json(['error' => 'ID du ticket manquant'], 400);
            return;
        }

        try {


            $this->chatModel = new ChatModel(Flight::db());
            $messages = $this->chatModel->getTicketMessages($ticketId);
            
            // Filtrer les nouveaux messages
            $newMessages = array_filter($messages, function($msg) use ($lastMessageId) {
                return $msg['id'] > $lastMessageId;
            });

            Flight::json([
                'success' => true,
                'messages' => array_values($newMessages)
            ]);

        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // Marquer les messages comme lus
    public function markAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $ticketId = $_POST['ticket_id'] ?? null;
        $userType = $_POST['user_type'] ?? 'client';

        if (!$ticketId) {
            Flight::json(['error' => 'ID du ticket manquant'], 400);
            return;
        }

        try {
            $this->chatModel = new ChatModel(Flight::db());
            $updatedCount = $this->chatModel->markMessagesAsRead($ticketId, $userType);
            
            Flight::json([
                'success' => true,
                'updated_count' => $updatedCount
            ]);

        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // Compter les messages non lus
    public function getUnreadCount() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        $ticketId = $_GET['ticket_id'] ?? null;
        $userType = $_GET['user_type'] ?? 'client';

        if (!$ticketId) {
            Flight::json(['error' => 'ID du ticket manquant'], 400);
            return;
        }

        try {

            $this->chatModel = new ChatModel(Flight::db());
            $unreadCount = $this->chatModel->countUnreadMessages($ticketId, $userType);
            
            Flight::json([
                'success' => true,
                'unread_count' => $unreadCount
            ]);

        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }
}