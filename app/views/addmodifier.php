
<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter/Modifier un Budget</title>
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

    <h2>Modifier</h2>

    <form action="addmodifier" method="POST">
    
        <label for="idCategorie">Catégorie :</label>
        <select id="idCategorie" name="idCategorie" >
            <option value="">Sélectionnez une catégorie</option>
            <?php foreach ($data['categories'] as $c) : ?>
                <option value="<?= $c['idCategorie'] ?>"><?= htmlspecialchars($c['NomCategorie']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="idPeriode">Période :</label>
        <select id="idPeriode" name="idPeriode" >
            <option value="">Sélectionnez une période</option>
            <?php 
        $mois = [
            1 => "Janvier", 2 => "Février", 3 => "Mars", 4 => "Avril",
            5 => "Mai", 6 => "Juin", 7 => "Juillet", 8 => "Août",
            9 => "Septembre", 10 => "Octobre", 11 => "Novembre", 12 => "Décembre"
        ];
        
        foreach ($mois as $id => $nom) {
            echo "<option value='$id'>$nom</option>";
        }
    ?>
        </select>
        <label for="prevision">Prévision :</label>
        <input type="number" id="prevision" name="prevision" step="0.01" >

        <label for="realisation">Réalisation :</label>
        <input type="number" id="realisation" name="realisation" step="0.01">


        <label for="dateBudget">Date du Budget :</label>

        <input type="datetime-local" id="dateBudget" name="dateBudget" value="<?= date('Y-m-d\TH:i') ?>" >

        <button type="submit" name="save" value="<?= $_GET['idBudget']?>">Modifier</button>
    </form>

</body>
</html>
