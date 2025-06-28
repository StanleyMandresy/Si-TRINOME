<!DOCTYPE html>
<html>
<head>
    <title>Mes Requêtes</title>
</head>
<body>
    <h2>Mes Requêtes et Tickets</h2>

    <h3>Vos requêtes :</h3>
    <a href="/Ticket/faire-requete">Faire une nouvelle requête</a>

    <?php if (!empty($requetes)): ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produit concerné</th>
                    <th>Sujet</th>
                   <th>Etat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requetes as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['nomProduit']) ?></td>
                        <td><?= htmlspecialchars($r['sujet']) ?></td>
                    <td>
                        <?= $r['classified'] ? 'Reçu' : 'Pas encore reçu' ?>
                    </td>
                            
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Vous n'avez pas encore fait de requête.</p>
    <?php endif; ?>


    <h3>Vos tickets :</h3>

    <?php if (!empty($tickets)): ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Choix</th>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Produit</th>
                    <th>Priorité</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Chat</th>
                    <th>Évaluation</th>
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
                        </td>
                        <td><?= $t['id_ticket'] ?></td>
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
                            <?php if ($t['statut'] == 2): ?>
                                <?php if (empty($t['evaluation_note'])): ?>
                                    <a href=/Ticket/evaluer-ticket?id=<?= $t['id_ticket'] ?>">Évaluer</a>
                                <?php else: ?>
                                    Évalué (<?= $t['evaluation_note'] ?> ★)
                                <?php endif; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Vous n'avez pas encore de ticket.</p>
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
            console.log("Ticket ID:", ticketId);
            console.log("Client ID:", clientId);
            window.location.href = `/chat?ticket_id=${ticketId}&client_id=${clientId}&user_type=client`;
        }
       
    </script>
</body>
</html>
