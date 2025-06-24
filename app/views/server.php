<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $ticketConnections;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->ticketConnections = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nouvelle connexion! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        if (!$data || !isset($data['type'])) {
            return;
        }

        $ticketId = $data['ticket_id'] ?? null;
        $userId = $data['user_id'] ?? null;
        $userType = $data['user_type'] ?? null;

        // Enregistrer la connexion pour ce ticket
        if ($ticketId && $userId && $userType) {
            $this->ticketConnections[$ticketId][$userType][$userId] = $from;
        }

        switch ($data['type']) {
            case 'message':
                $this->handleNewMessage($data, $from);
                break;
            case 'typing':
                $this->handleTyping($data);
                break;
            case 'read':
                $this->handleReadReceipt($data);
                break;
        }
    }

    protected function handleNewMessage($data, $from) {
        $ticketId = $data['ticket_id'];
        $message = $data['message'];
        $userType = $data['user_type'];
        $timestamp = date('Y-m-d H:i:s');

        // Sauvegarder en base de données (à implémenter)
        $this->saveMessageToDatabase($ticketId, $message, $userType);

        // Envoyer le message à toutes les parties concernées
        $recipientType = ($userType === 'client') ? 'agent' : 'client';
        
        $response = [
            'type' => 'message',
            'ticket_id' => $ticketId,
            'message' => $message,
            'user_type' => $userType,
            'timestamp' => $timestamp
        ];

        $this->broadcastToTicket($ticketId, $recipientType, $response);
    }

    protected function handleTyping($data) {
        $ticketId = $data['ticket_id'];
        $userType = $data['user_type'];
        $isTyping = $data['is_typing'];
        
        $recipientType = ($userType === 'client') ? 'agent' : 'client';
        
        $response = [
            'type' => 'typing',
            'ticket_id' => $ticketId,
            'user_type' => $userType,
            'is_typing' => $isTyping
        ];

        $this->broadcastToTicket($ticketId, $recipientType, $response);
    }

    protected function handleReadReceipt($data) {
        $ticketId = $data['ticket_id'];
        $userType = $data['user_type'];
        
        $recipientType = ($userType === 'client') ? 'agent' : 'client';
        
        $response = [
            'type' => 'read',
            'ticket_id' => $ticketId,
            'user_type' => $userType
        ];

        $this->broadcastToTicket($ticketId, $recipientType, $response);
    }

    protected function broadcastToTicket($ticketId, $recipientType, $message) {
        if (!isset($this->ticketConnections[$ticketId][$recipientType])) {
            return;
        }

        foreach ($this->ticketConnections[$ticketId][$recipientType] as $client) {
            $client->send(json_encode($message));
        }
    }

    protected function saveMessageToDatabase($ticketId, $message, $userType) {
        // Implémentez la logique de sauvegarde en base de données
        // Exemple avec PDO:
        /*
        $db = new PDO('mysql:host=localhost;dbname=your_db', 'user', 'pass');
        $stmt = $db->prepare("INSERT INTO messages (ticket_id, ".($userType === 'client' ? 'commentaire_client' : 'commentaire_agent').", timestamp) VALUES (?, ?, NOW())");
        $stmt->execute([$ticketId, $message]);
        */
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connexion {$conn->resourceId} déconnectée\n";

        // Nettoyer les références à cette connexion
        foreach ($this->ticketConnections as $ticketId => $userTypes) {
            foreach ($userTypes as $userType => $connections) {
                foreach ($connections as $key => $client) {
                    if ($client === $conn) {
                        unset($this->ticketConnections[$ticketId][$userType][$key]);
                    }
                }
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erreur: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Lancer le serveur
$server = IoServer::factory(
    new WsServer(new Chat()),
    8080
);

$server->run();