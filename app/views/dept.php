<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Departements</title>
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

    <h2>Liste des Departements</h2>
    <form action="adddept" method="post">

    <button name="button" value="ajouter">Ajouter</button>

    </form>

    <?php if (!empty($dept)) : ?>
        <table>
            <thead>
                <tr>
                    <th>NumeroDepartement</th>
                    <th>NomDepartement</th>
                    <th>Responsable</th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dept as $cat) : ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['NumeroDepartement']) ?></td>
                       
                        <td><?= htmlspecialchars($cat['NomDepartement']) ?></td>
                        <td><?= htmlspecialchars($cat['Responsable']) ?></td>
                        <form action="adddept" method="post">
                        <td><button name="button" value="<?=$cat['NumeroDepartement']?>">Modifier</button></td>
                        </form>
                        <form action="dept" method="post" >
                        <td><button name="delete" value="<?=$cat['NumeroDepartement']?>">Supprimer</button></td>
                        </form>               
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucune departement trouvée.</p>
    <?php endif; ?>
   
</body>
</html>
