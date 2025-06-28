<!-- views/form_requete.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Nouvelle Requête</title>
</head>
<body>
    <h2>Faire une nouvelle requête</h2>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/Ticket/save-requete">
        <label for="idProduit">Produit concerné :</label>
        <select name="idProduit" required>
            <option value="">-- Choisir un produit --</option>
            <?php foreach ($produits as $p): ?>
                <option value="<?= $p['idProduit'] ?>"><?= htmlspecialchars($p['nomProduit']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="sujet">Sujet / Problème :</label><br>
        <textarea name="sujet" rows="5" cols="40" required></textarea><br><br>

        <input type="submit" value="Envoyer la requête">
    </form>

    <br>
    <a href="/Ticket">Retour à mes requêtes</a>
</body>
</html>
