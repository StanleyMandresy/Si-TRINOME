<!-- views/Backoffice/tickets_assignes.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Tickets assignés</title>
</head>
<body>
    <h2>Tickets qui vous sont assignés</h2>

    <?php if (!empty($tickets)): ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Produit</th>
                    <th>Priorité</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Chat</th>
                    <th>Executer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td>
                                <input type="radio" name="selected_ticket"
                                       value="<?= htmlspecialchars($t['id_ticket']) ?>"
                                       data-client-id="<?= htmlspecialchars($t['id_client']) ?>"
                                       onchange="updateSelectedTicket(this)">
                               <?= $t['id_ticket'] ?>
                                    </td>
                    
                        <td><?= $t['nom'] . ' ' . $t['prenom'] ?></td>
                        <td><?= $t['nomProduit'] ?></td>
                        <td><?= $t['priorite'] ?></td>
                        <td><?= htmlspecialchars($t['description']) ?></td>
                        <td>
                            <?php
                                $statuts = [0 => 'Reçu', 1 => 'En cours', 2 => 'Traité', 3 => 'Fermé'];
                                echo $statuts[$t['statut']] ?? 'Inconnu';
                            ?>
                        </td>
                           <td>
                                <button type="button" class="action-btn"
                                        onclick="openChat(<?= htmlspecialchars($t['id_ticket']) ?>, <?= htmlspecialchars($t['id_client']) ?>)">
                                    Ouvrir le chat
                                </button>
                            </td>
          <td>
    <?php if ($t['statut'] <= 2): ?>
        <form method="POST" action="/Ticket/traiter-ticket">
            <input type="hidden" name="id_ticket" value="<?= $t['id_ticket'] ?>">

            <label for="periode_<?= $t['id_ticket'] ?>">Période :</label>
            <select name="periode" id="periode_<?= $t['id_ticket'] ?>">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit">Traité</button>
        </form>
    <?php else: ?>
        Déjà traité
    <?php endif; ?>
</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun ticket assigné.</p>
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
