<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Dépôts </title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .validate-btn {
            background-color: #4CAF50;
            color: white;
        }
        .cancel-btn {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>

    <h2>Tableau des Dépôts avec Validation</h2>

    <table>
        <thead>
            <tr>
                <th>ID Dépôt</th>
                <th>ID Utilisateur</th>
                <th>Montant</th>
                <th>Validation</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($depot as $d) { ?>
                <tr>
                    <td><?php echo $d['idDepot']; ?></td>
                    <td><?php echo $d['idUsers']; ?></td>
                    <td><?php echo $d['montant']; ?></td>
                    <?php if($d['validations'] == false) { ?>
                        <form action="/pageadmin" method="post">
                        <td><button name="validation" value="<?php echo $d['idDepot']; ?>" class="validate-btn">Valider</button></td>
                        </form>
                        <?php } else { ?>
                        <td>Validée</td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>
