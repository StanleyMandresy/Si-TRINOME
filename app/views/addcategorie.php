<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Catégorie</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        form {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        label {
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <h2>Ajouter une Catégorie</h2>

    <form action="categorie" method="POST">
        <label for="nomCategorie">Nom de la Catégorie :</label>
        <input type="text" id="nomCategorie" name="nom" >

        <label for="idNature">Nature :</label>
        <select id="idNature" name="nature" >
            <option value="">Sélectionnez une nature</option>
            <?php foreach ($nature as $n) : ?>
                <option value="<?= $n['idNature'] ?>"><?= htmlspecialchars($n['nomNature']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="save" value="<?= $button ?>">Ajouter</button>
    </form>

</body>
</html>
