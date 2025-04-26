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


    <h2>Liste des Budgets</h2>
    <form action="addbudget" method="get">
        <button name="button" value="ajouter">Ajouter budget</button>
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
                    <th>Modifier</th>
                    <th>Supprimer</th>
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
                        <form action="addmodifier" method="get">
                            <td><input type="hidden" name="idBudget" value="<?= $budgets['idBudget'] ?>" >
                            <button type="submit">Modifier</button>
                               </td>
                        </form>
                        <form action="deletebudget" method="get">
                        <td><input type="hidden" name="idBudget" value="<?= $budgets['idBudget'] ?>" >
                            <button type="submit">supprimer</button>
                               </td>
                    </form>               
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucun budget trouvé.</p>
    <?php endif; ?>
    <br>

    <form action="csv" method="post" enctype="multipart/form-data">
    <!-- Importation CSV -->
    <label for="csv_file">Sélectionner un fichier CSV pour l'importation :</label>
    <br>
    <input type="file" name="csv_file" required>
    <button type="submit" name="csv" value="import">Importer</button>

    <!-- Exportation CSV -->

</form>
    
<br>
<br>
<form action="csv" method="post" >
<button type="submit" name="csv" value="export">Exporter</button>
</form>
  
</body>
</html>