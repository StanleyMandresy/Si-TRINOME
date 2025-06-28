<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déposer un retour</title>
</head>
<body>
    <h1>Déposer un retour</h1>
    <form action="/retour" method="POST">
      
            <label for="typeCRM">Type de retour :</label>
            <select id="typeCRM" name="idTypeCRM" required>
                <?php foreach ($typeCRM as $type): ?>
                    <option value="<?= $type['idTypeCRM'] ?>"><?= htmlspecialchars($type['nomTypeCRM']) ?></option>
                <?php endforeach; ?>
            </select>
     

        <div>
            <label for="produit">Produit concerné :</label>
            <select id="produit" name="idProduit" required>
                <?php foreach ($produits as $produit): ?>
                    <option value="<?= $produit['idProduit'] ?>"><?= htmlspecialchars($produit['nomProduit']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="notes">Notes supplémentaires :</label>
            <textarea id="notes" name="notesSupplementaires"></textarea>
        </div>

        <button type="submit">Envoyer</button>
    </form>
    <a href="/Ticket">Creation de ticket</a>
</body>
</html>
