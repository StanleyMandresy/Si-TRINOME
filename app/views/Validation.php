<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Budgets</title>
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
        .positive {
            color: green;
        }
        .negative {
            color: red;
        }
    </style>
</head>
<body>

    <h2>Liste des Budgets</h2>
    <form action="addbudget" method="get">
        <button name="button" value="ajouter">Ajouter un budget</button>
    </form>
    

    <?php if (!empty($budgets)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Département</th>
                    <th>Catégorie</th>
                    <th>Prévision</th>
                    <th>Réalisation</th>
                    <th>Écart</th>
                    <th>Date</th>
                    <th>Valider</th>
                 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($budgets as $budgets) : ?>
                    <tr>
                        <td><?= htmlspecialchars($budgets['nomDepartement']) ?></td>
                        <td><?= htmlspecialchars($budgets['nomCategorie']) ?></td>
                        <td><?= number_format($budgets['Prevision'], 2, ',', ' ') ?></td>
                        <td><?= number_format($budgets['Realisation'], 2, ',', ' ') ?></td>
                        <td class="<?= ($budgets['Ecart'] >= 0) ? 'positive' : 'negative' ?>">
                            <?= number_format($budgets['Ecart'], 2, ',', ' ') ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($budgets['DateBudget'])) ?></td>
                        <form action="validation" method="post">
                            <td><input type="hidden" name="valide" value="<?= $budgets['idBudget'] ?>" >
                            <button type="submit">Valider</button>
                               </td>
                        </form>
                          
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucun budget trouvé.</p>
    <?php endif; ?>
   
</body>
</html>