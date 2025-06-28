<!-- views/Backoffice/tickets_non_assignes.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Tickets non assignés</title>
</head>
<body>
    <h2>Tickets en attente d'assignation</h2>

    <?php if (!empty($tickets)): ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Produit</th>
                    <th>Description</th>
                    <th>Assigner</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><?= $t['id_ticket'] ?></td>
                        <td><?= $t['nom'] . ' ' . $t['prenom'] ?></td>
                        <td><?= $t['nomProduit'] ?></td>
                        <td><?= htmlspecialchars($t['description']) ?></td>
                        <td>
                            <form method="POST" action="/Ticket/assigner-ticket">
                                <input type="hidden" name="id_ticket" value="<?= $t['id_ticket'] ?>">
                                <select name="id_agent" required>
                                    <option value="">-- Agent --</option>
                                    <?php foreach ($agents as $a): ?>
                                        <option value="<?= $a['idUser'] ?>"><?= $a['Nom'] ?></option>
                                    <?php endforeach; ?>
                                </select><br>
                               Durée (HH:MM:SS) : <input type="time" name="duree" step="1" required>
                                Coût/horaire : <input type="number" step="0.01" name="coutHoraire" required><br>
                                <button type="submit">Assigner</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun ticket en attente.</p>
    <?php endif; ?>
</body>
</html>
