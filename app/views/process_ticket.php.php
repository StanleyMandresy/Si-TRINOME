<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_ticket'])) {
    $selectedTicketId = $_POST['selected_ticket'];
    $selectedClientId = $_POST['selected_client_id'] ?? null;
    
    // Stocker les IDs dans la session
    $_SESSION['selected_ticket_id'] = $selectedTicketId;
    $_SESSION['selected_client_id'] = $selectedClientId;
    
    // Vous pouvez aussi les stocker dans des cookies si nécessaire
    setcookie('selected_ticket_id', $selectedTicketId, time() + 86400, '/');
    setcookie('selected_client_id', $selectedClientId, time() + 86400, '/');
    
    // Rediriger vers la page de chat
    header('Location: /chat/show?ticket_id=' . urlencode($selectedTicketId) . '&client_id=' . urlencode($selectedClientId));
    exit;
} else {
    header('Location: ticket_list.php?error=no_ticket_selected');
    exit;
}