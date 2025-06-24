<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Tickets</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f5f5f5; }
        .selected { background-color: #d4edda; }
        .action-btn { margin: 5px; padding: 5px 10px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Liste des Tickets</h1>
    
    <?php if (!empty($tickets)): ?>
        <form id="ticketForm" method="get" action="/chat">
            <table>
                <thead>
                    <tr>
                        <th>Sélection</th>
                        <th>ID Ticket</th>
                        <th>Sujet</th>
                        <th>ID Client</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>
                                <input type="radio" name="selected_ticket"
                                       value="<?= htmlspecialchars($ticket['id_ticket']) ?>"
                                       data-client-id="<?= htmlspecialchars($ticket['id_client']) ?>"
                                       onchange="updateSelectedTicket(this)">
                            </td>
                            <td><?= htmlspecialchars($ticket['id_ticket']) ?></td>
                            <td><?= htmlspecialchars($ticket['sujet'] ?? '') ?></td>
                            <td><?= htmlspecialchars($ticket['id_client'] ?? '') ?></td>
                            <td><?= htmlspecialchars($ticket['date_creation'] ?? '') ?></td>
                            <td>
                                <button type="button" class="action-btn"
                                        onclick="openChat(<?= htmlspecialchars($ticket['id_ticket']) ?>, <?= htmlspecialchars($ticket['id_client']) ?>)">
                                    Ouvrir le chat
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <input type="hidden" id="selected_client_id" name="selected_client_id" value="">
        </form>
    <?php else: ?>
        <p>Aucun ticket trouvé.</p>
    <?php endif; ?>

    <script>
        function updateSelectedTicket(radio) {
            document.getElementById('selected_client_id').value = radio.getAttribute('data-client-id');
            
            // Mettre à jour les styles visuels
            const rows = document.querySelectorAll('tr');
            rows.forEach(row => row.classList.remove('selected'));
            radio.closest('tr').classList.add('selected');
        }

        function openChat(ticketId, clientId) {
            // Rediriger vers l'interface de chat
            window.location.href = `/chat?ticket_id=${ticketId}&client_id=${clientId}&user_type=agent`;
        }
    </script>
</body>
</html>