<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau Budget</title>
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

    <h1 style="text-align: center;">Tableau de Budget</h1>
    <form action="budget"  method="POST">
        <label for="debut">Période de début :</label>
        <input type="number" name="debut" id="debut" value="<?= isset($_POST['debut']) ? $_POST['debut'] : 1 ?>" required>

        <label for="fin">Période de fin :</label>
        <input type="number" name="fin" id="fin" value="<?= isset($_POST['fin']) ? $_POST['fin'] : 1 ?>" required>

        <button type="submit">Afficher le budget</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Rubrique</th>
                <!-- Affichage dynamique des périodes -->
                <?php foreach ($tableau as $data) { ?>
                    <th colspan="3">Période <?php echo $data['periode']; ?></th>
                <?php } ?>
            </tr>
            <tr>
                <th></th>
                <!-- Affichage dynamique de Prévision, Réalisation et Écart pour chaque période -->
                <?php foreach ($tableau as $data) { ?>
                    <th>Prévision</th>
                    <th>Réalisation</th>
                    <th>Écart</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <!-- Solde Début -->
            <tr>
                <td><strong>Solde Début</strong></td>
                <?php foreach ($tableau as $data) { ?>
                    <td><?php echo $data['solde_debut']; ?></td>
                    <td><?php echo $data['solde_debut']; ?></td>
                    <td>0</td> <!-- Écart solde début est toujours 0 -->
                <?php } ?>
            </tr>

            <!-- Recette -->
            <tr>
                <td><strong>Recette</strong></td>
                <?php foreach ($tableau as $data) { ?>
                    <td><?php echo $data['recette_prevision']; ?></td>
                    <td><?php echo $data['recette_realisation']; ?></td>
                    <td><?php echo $data['ecart_recette']; ?></td>
                <?php } ?>
            </tr>

            <!-- Dépense -->
            <tr>
                <td><strong>Dépense</strong></td>
                <?php foreach ($tableau as $data) { ?>
                    <td><?php echo $data['depense_prevision']; ?></td>
                    <td><?php echo $data['depense_realisation']; ?></td>
                    <td><?php echo $data['ecart_depense']; ?></td>
                <?php } ?>
            </tr>

            <!-- Solde Fin -->
            <tr>
                <td><strong>Solde Fin</strong></td>
                <?php foreach ($tableau as $data) { ?>
                    <td><?php echo $data['solde_fin_prevision']; ?></td>
                    <td><?php echo $data['solde_fin_realisation']; ?></td>
                    <td></td> <!-- L'écart pour le solde fin est laissé vide -->
                <?php } ?>
            </tr>
        </tbody>
    </table>
    <br>
    <br>

    <?php if (!empty($tableauTotal)) { ?>
    <table>
        <thead>
            <tr>
                <th>Rubrique total</th>
                <!-- Affichage dynamique des périodes -->
                <?php foreach ($tableauTotal as $data) { ?>
                    <th colspan="3" scope="col">Période <?php echo $data['periode']; ?></th>
                <?php } ?>
            </tr>
            <tr>
                <th></th>
                <!-- Affichage dynamique de Prévision, Réalisation et Écart pour chaque période -->
                <?php foreach ($tableauTotal as $data) { ?>
                    <th>Prévision</th>
                    <th>Réalisation</th>
                    <th>Écart</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <!-- Solde Début -->
            <tr>
                <td><strong>Solde Début</strong></td>
                <?php foreach ($tableauTotal as $data) { ?>
                    <td><?php echo $data['solde_debut']; ?></td>
                    <td><?php echo $data['solde_debut']; ?></td>
                    <td>0</td> <!-- Écart solde début est toujours 0 -->
                <?php } ?>
            </tr>

            <!-- Recette -->
            <tr>
                <td><strong>Recette</strong></td>
                <?php foreach ($tableauTotal as $data) { ?>
                    <td><?php echo $data['recette_prevision'];?></td>
                    <td><?php echo $data['recette_realisation']; ?></td>
                    <td><?php echo $data['ecart_recette'];?></td>
                <?php } ?>
            </tr>

            <!-- Dépense -->
            <tr>
                <td><strong>Dépense</strong></td>
                <?php foreach ($tableauTotal as $data) { ?>
                    <td><?php echo $data['depense_prevision']; ?></td>
                    <td><?php echo $data['depense_realisation']; ?></td>
                    <td><?php echo $data['ecart_depense']; ?></td>
                <?php } ?>
            </tr>

            <!-- Solde Fin -->
            <tr>
                <td><strong>Solde Fin</strong></td>
                <?php foreach ($tableauTotal as $data) { ?>
                    <td><?php echo $data['solde_fin_prevision']; ?></td>
                    <td><?php echo $data['solde_fin_realisation']; ?></td>
                    <td></td> <!-- L'écart pour le solde fin est laissé vide -->
                <?php } ?>
            </tr>
        </tbody>
    </table>
<?php } ?>

<!-- Dans votre view.php -->
<a href="/exportPdf?debut=<?= isset($_POST['debut']) ? $_POST['debut'] : 1?>&fin=<?= isset($_POST['fin']) ? $_POST['fin'] : 1  ?>&id_departement=<?= 0?>">Exporter ce tableau en PDF</a>

<?php if (!empty($tableauTotal)) { ?>
    <a href="/exportPdf?debut=<?= isset($_POST['debut']) ? $_POST['debut'] : 1 ?>&fin=<?=isset($_POST['fin']) ? $_POST['fin'] : 1 ?>&total=1" style="margin-left: 20px;">Exporter le tableau consolidé en PDF</a>
<?php } ?>
</body>
</html>
