<!-- views/Backoffice/requetes_a_classifier.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Classifier les requêtes</title>
</head>
<body>
    <h2>Requêtes à classifier</h2>
    <a href="/Ticket/liste-Ticket">Liste des ticket a assigner</a>
    <a href="/Ticket/tickets-assignes">Liste des ticket</a>
    <?php if (!empty($requetes)): ?>
        <?php foreach ($requetes as $r): ?>
            <div style="border:1px solid gray; padding:10px; margin-bottom:15px;">
                <p><strong>Client :</strong> <?= $r['nom'] . ' ' . $r['prenom'] ?></p>
                <p><strong>Produit :</strong> <?= $r['nomProduit'] ?></p>
                <p><strong>Texte :</strong> <?= htmlspecialchars($r['sujet']) ?></p>

                <form method="POST" action="/Ticket/classifier-requete">
                    <input type="hidden" name="id_requete_client" value="<?= $r['id'] ?>">
                    
                    <label for="priorite">Priorité :</label>
                    <select name="priorite" required>
                        <option value="insignifiant">Insignifiant</option>
                        <option value="basse">Basse</option>
                        <option value="moyenne">Moyenne</option>
                        <option value="haute">Haute</option>
                    </select><br>

                    <label for="id_type">Type de demande :</label>
                    <select name="id_type" required>
                        <?php foreach ($types as $t): ?>
                            <option value="<?= $t['id_type'] ?>"><?= $t['nom_type'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label>Description du ticket :</label><br>
                    <textarea name="description" rows="4" cols="40" required><?= htmlspecialchars($r['sujet']) ?></textarea><br>

                    <button type="submit">Créer le ticket</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune requête à classifier.</p>
    <?php endif; ?>
</body>
</html>
