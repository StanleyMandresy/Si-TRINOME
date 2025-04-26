<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Catégories</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

    <h2>Liste des Catégories</h2>
    <form action="addcategorie" method="post">

    <button name="button" value="ajouter">Ajouter</button>

    </form>

    <?php if (!empty($categorie)) : ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Catégorie</th>
                    <th>Nature</th>
                    <th>Departement</th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorie as $cat) : ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['idCategorie']) ?></td>
                       
                        <td><?= htmlspecialchars($cat['NomCategorie']) ?></td>
                        <td><?= htmlspecialchars($cat['nomNature']) ?></td>
                        <td><?= htmlspecialchars($cat['idDepartement']) ?></td>
                        <form action="addcategorie" method="post">
                        <td><button name="button" value="<?=$cat['idCategorie']?>">Modifier</button></td>
                        </form>
                        <form action="categorie" method="post" >
                        <td><button name="delete" value="<?=$cat['idCategorie']?>">Supprimer</button></td>
                        </form>               
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucune catégorie trouvée.</p>
    <?php endif; ?>
   
</body>
</html>
