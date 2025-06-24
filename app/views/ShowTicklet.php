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
    </style>
</head>
<body>
    <h1>Liste des Tickets</h1>
    
    <?php if (!empty($tickets)): ?>
        <form id="ticketForm" method="post" action="process_ticket.php">
            <table>
                <thead>
                    <tr>
                        <th>Sélection</th>
                        <th>ID</th>
                        <th>Période</th>
                        <th>Montant</th>
                        <th>Date création</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>
                                <input type="radio" name="selected_ticket" 
                                       value="<?= htmlspecialchars($ticket['id_ticket']) ?>" 
                                       onchange="document.getElementById('ticketForm').submit();">
                            </td>
                            <td><?= htmlspecialchars($ticket['id_ticket']) ?></td>
                            <td><?= htmlspecialchars($ticket['periode_id'] ?? '') ?></td>
                            <td><?= htmlspecialchars($ticket['montant'] ?? '') ?></td>
                            <td><?= htmlspecialchars($ticket['date_creation'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    <?php else: ?>
        <p>Aucun ticket trouvé.</p>
    <?php endif; ?>

    <script>
        // Stocker l'ID du ticket dans le localStorage lorsqu'un ticket est sélectionné
        document.querySelectorAll('input[name="selected_ticket"]').forEach(radio => {
            radio.addEventListener('change', function() {
                localStorage.setItem('selectedTicketId', this.value);
            });
        });

        // Restaurer la sélection au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            const selectedId = localStorage.getItem('selectedTicketId');
            if (selectedId) {
                const radio = document.querySelector(`input[value="${selectedId}"]`);
                if (radio) {
                    radio.checked = true;
                    radio.closest('tr').classList.add('selected');
                }
            }
        });
    </script>
</body>
</html>